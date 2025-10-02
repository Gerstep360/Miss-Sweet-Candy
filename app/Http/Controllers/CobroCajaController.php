<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\CobroCaja;
use App\Models\Pedido;
use Carbon\Carbon;
use App\Http\Controllers\BitacoraController;
class CobroCajaController extends Controller
{
    /**
     * Mostrar todos los pedidos pendientes de cobro
     */
    public function index(Request $request)
    {
        $tipo = $request->get('tipo'); // mesa, mostrador, web
        $estado = $request->get('estado'); // pendientes, pagados

        // Query base para pedidos pendientes de cobro
        $queryPendientes = Pedido::with(['cliente', 'mesa', 'items.producto', 'atendidoPor'])
            ->whereIn('estado', ['preparado', 'servido', 'retirado', 'entregado'])
            ->whereDoesntHave('cobros', function($query) {
                $query->where('estado', 'cobrado');
            });

        // Query base para pedidos ya cobrados (con cobros en estado 'cobrado')
        $queryPagados = Pedido::with(['cliente', 'mesa', 'items.producto', 'cobros.cajero'])
            ->whereHas('cobros', function($query) {
                $query->where('estado', 'cobrado');
            });

        // Filtrar por tipo si se especifica
        if ($tipo && in_array($tipo, ['mesa', 'mostrador', 'web'])) {
            $queryPendientes->where('tipo', $tipo);
            $queryPagados->where('tipo', $tipo);
        }

        $pedidosPendientes = $queryPendientes->latest()->get();
        $pedidosPagados = $queryPagados->latest()->take(50)->get();
        BitacoraController::registrar('ver', 'CobroCaja', null);
        return view('admin.cobro_caja.index', compact('pedidosPendientes', 'pedidosPagados', 'tipo', 'estado'));
    }

    /**
     * Mostrar detalles de un pedido para cobrar
     */
    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'mesa', 'items.producto', 'atendidoPor', 'cobros.cajero']);

        // Verificar si ya tiene cobro registrado
        $cobroExistente = $pedido->cobros()->where('estado', 'cobrado')->first();
        BitacoraController::registrar('ver', 'CobroCaja', $pedido->id);
        return view('admin.cobro_caja.show', compact('pedido', 'cobroExistente'));
    }

    /**
     * Mostrar formulario de cobro para un pedido
     */
    public function create(Pedido $pedido)
    {
        // Verificar que el pedido esté en un estado válido para cobrar
        if (!in_array($pedido->estado, ['preparado', 'servido', 'retirado', 'entregado'])) {
            return back()->with('error', 'Este pedido no está listo para ser cobrado.');
        }

        // Verificar si ya tiene un cobro registrado
        $cobroExistente = $pedido->cobros()->where('estado', 'cobrado')->first();
        if ($cobroExistente) {
            return back()->with('error', 'Este pedido ya ha sido cobrado.');
        }

        $pedido->load(['cliente', 'mesa', 'items.producto']);
        BitacoraController::registrar('crear', 'CobroCaja', $pedido->id);
        return view('admin.cobro_caja.create', compact('pedido'));
    }

    /**
     * Registrar el cobro y actualizar el estado del pedido
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'importe' => 'required|numeric|min:0',
            'metodo' => 'required|in:efectivo,pos,qr',
            'qr_proveedor' => 'required_if:metodo,qr|nullable|string|max:50',
            'qr_referencia' => 'nullable|string|max:120',
            'notas' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $pedido = Pedido::with('items')->findOrFail($validated['pedido_id']);

            // Verificar estado del pedido
            if (!in_array($pedido->estado, ['preparado', 'servido', 'retirado', 'entregado'])) {
                throw new \Exception('Este pedido no está listo para ser cobrado.');
            }

            // Verificar si ya tiene un cobro
            if ($pedido->cobros()->where('estado', 'cobrado')->exists()) {
                throw new \Exception('Este pedido ya ha sido cobrado.');
            }

            // Calcular el total real del pedido
            $totalPedido = $pedido->items->sum('subtotal_item');

            // Verificar que el importe sea correcto
            if (abs($validated['importe'] - $totalPedido) > 0.01) {
                throw new \Exception('El importe no coincide con el total del pedido.');
            }

            // Determinar estado QR si aplica
            $qrEstado = null;
            if ($validated['metodo'] === 'qr') {
                $qrEstado = 'pendiente'; // Por defecto pendiente, se debe confirmar manualmente
            }

            // Crear el cobro
            $cobro = CobroCaja::create([
                'pedido_id' => $pedido->id,
                'importe' => $validated['importe'],
                'metodo' => $validated['metodo'],
                'estado' => 'cobrado',
                'comprobante' => $this->generarComprobante($pedido),
                'cajero_id' => Auth::id(),
                'qr_tx_id' => $validated['metodo'] === 'qr' ? 'QR-' . now()->format('YmdHis') . '-' . $pedido->id : null,
                'qr_estado' => $qrEstado,
                'qr_proveedor' => $validated['qr_proveedor'] ?? null,
                'qr_referencia' => $validated['qr_referencia'] ?? null,
            ]);

            // Actualizar estado del pedido según el tipo
            $nuevoEstado = match($pedido->tipo) {
                'mesa' => 'servido',
                'mostrador' => 'retirado',
                'web' => $pedido->modalidad === 'entrega' ? 'entregado' : 'retirado',
                default => 'servido'
            };

            $pedido->update(['estado' => $nuevoEstado]);

            // Liberar mesa si aplica y el pedido está completado
            if ($pedido->tipo === 'mesa' && $pedido->mesa) {
                $pedido->mesa->update(['estado' => 'libre']);
            }

            // Marcar todos los items como completados
            $pedido->items()->update(['estado_item' => $nuevoEstado]);

            DB::commit();

            return redirect()
                ->route('cobro_caja.comprobante', $cobro)
                ->with('success', 'Cobro registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
        BitacoraController::registrar('creado', 'CobroCaja', $pedido->id);
    }

    /**
     * Mostrar comprobante de cobro
     */
    public function comprobante(CobroCaja $cobro)
    {
        $cobro->load(['pedido.cliente', 'pedido.mesa', 'pedido.items.producto', 'cajero']);
        BitacoraController::registrar('ver comprobante', 'CobroCaja', $cobro->id);
        return view('admin.cobro_caja.comprobante', compact('cobro'));
    }

    /**
     * Cancelar un cobro (solo si es del mismo día)
     */
    public function cancelar(Request $request, CobroCaja $cobro)
    {
        $validated = $request->validate([
            'motivo' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Verificar que sea del mismo día
            if (!$cobro->created_at->isToday()) {
                throw new \Exception('Solo se pueden cancelar cobros del día actual.');
            }

            // Verificar que no esté ya cancelado
            if ($cobro->estaCancelado()) {
                throw new \Exception('Este cobro ya está cancelado.');
            }

            // Cancelar el cobro
            $cobro->update([
                'estado' => 'cancelado',
                'qr_referencia' => ($cobro->qr_referencia ?? '') . ' | Motivo cancelación: ' . $validated['motivo']
            ]);

            // Revertir estado del pedido a 'preparado' para que pueda ser cobrado nuevamente
            $cobro->pedido->update(['estado' => 'preparado']);

            DB::commit();
            BitacoraController::registrar('cancelado', 'CobroCaja', $cobro->id);
            return back()->with('success', 'Cobro cancelado exitosamente. El pedido está disponible para un nuevo cobro.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Confirmar pago QR (cambiar estado de pendiente a aprobado)
     */
    public function confirmarQr(CobroCaja $cobro)
    {
        if (!$cobro->esQr()) {
            return back()->with('error', 'Este cobro no es por QR.');
        }

        if (!$cobro->qrPendiente()) {
            return back()->with('error', 'Este pago QR ya fue procesado.');
        }

        DB::beginTransaction();

        try {
            $cobro->update(['qr_estado' => 'aprobado']);

            DB::commit();

            return back()->with('success', 'Pago QR confirmado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al confirmar el pago QR: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar pago QR
     */
    public function rechazarQr(Request $request, CobroCaja $cobro)
    {
        $validated = $request->validate([
            'motivo' => 'required|string|max:255',
        ]);

        if (!$cobro->esQr()) {
            return back()->with('error', 'Este cobro no es por QR.');
        }

        if (!$cobro->qrPendiente()) {
            return back()->with('error', 'Este pago QR ya fue procesado.');
        }

        DB::beginTransaction();

        try {
            $cobro->update([
                'qr_estado' => 'rechazado',
                'qr_referencia' => ($cobro->qr_referencia ?? '') . ' | Motivo rechazo: ' . $validated['motivo']
            ]);

            // Revertir estado del pedido
            $cobro->pedido->update(['estado' => 'preparado']);

            DB::commit();

            return back()->with('warning', 'Pago QR rechazado. El pedido está disponible para un nuevo cobro.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al rechazar el pago QR: ' . $e->getMessage());
        }
    }

    /**
     * Reporte de cobros del día
     */
    public function reporteDiario(Request $request)
    {
        $fecha = $request->input('fecha') 
            ? Carbon::parse($request->input('fecha'))
            : Carbon::today();

        // Obtener cobros del día
        $cobros = CobroCaja::with(['pedido.cliente', 'cajero'])
            ->whereDate('created_at', $fecha)
            ->where('estado', 'cobrado')
            ->orderBy('created_at', 'asc')
            ->get();

        // Calcular totales por método
        $totalGeneral = $cobros->sum('importe');
        $totalEfectivo = $cobros->where('metodo', 'efectivo')->sum('importe');
        $totalPos = $cobros->where('metodo', 'pos')->sum('importe');
        $totalQr = $cobros->where('metodo', 'qr')->sum('importe');

        // Contar transacciones por método
        $cantidadEfectivo = $cobros->where('metodo', 'efectivo')->count();
        $cantidadPos = $cobros->where('metodo', 'pos')->count();
        $cantidadQr = $cobros->where('metodo', 'qr')->count();
        BitacoraController::registrar('ver reporte', 'CobroCaja', null);
        return view('admin.cobro_caja.reporte_diario', compact(
            'cobros',
            'fecha',
            'totalGeneral',
            'totalEfectivo',
            'totalPos',
            'totalQr',
            'cantidadEfectivo',
            'cantidadPos',
            'cantidadQr'
        ));
    }

    /**
     * Generar número de comprobante único
     */
    private function generarComprobante(Pedido $pedido): string
    {
        $prefijo = match($pedido->tipo) {
            'mesa' => 'MESA',
            'mostrador' => 'MOST',
            'web' => 'WEB',
            default => 'CAJA'
        };

        return $prefijo . '-' . now()->format('Ymd') . '-' . str_pad($pedido->id, 6, '0', STR_PAD_LEFT);
    }
}