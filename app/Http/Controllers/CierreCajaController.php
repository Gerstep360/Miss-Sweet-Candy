<?php

namespace App\Http\Controllers;

use App\Models\CierreCaja;
use App\Models\CierreCajaDetalle;
use App\Models\CobroCaja;
use App\Models\TurnoCaja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CierreCajaController extends Controller
{

    /**
     * Listar todos los cierres de caja
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('ver-cierres')) {
            abort(403, 'No tienes permiso para ver los cierres de caja.');
        }

        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $cajeroId = $request->input('cajero_id', 'todos');
        $estado = $request->input('estado', 'todos'); // cuadrados, con_diferencias, todos

        $query = CierreCaja::with(['cajero', 'detalles'])
            ->whereBetween('fin', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ]);

        if ($cajeroId !== 'todos') {
            $query->where('cajero_id', $cajeroId);
        }

        if ($estado === 'cuadrados') {
            $query->cuadrados();
        } elseif ($estado === 'con_diferencias') {
            $query->conDiferencias();
        }

        $cierres = $query->orderBy('fin', 'desc')->paginate(15);

        // Estadísticas del período
        $estadisticas = $this->calcularEstadisticas($fechaInicio, $fechaFin, $cajeroId);

        // Cajeros para el filtro
        $cajeros = User::role(['cajero', 'administrador'])->orderBy('name')->get();

        // Registrar en bitácora
        BitacoraController::registrar('Ver', 'CierreCaja');

        return view('cierres_caja.index', compact(
            'cierres',
            'estadisticas',
            'cajeros',
            'fechaInicio',
            'fechaFin',
            'cajeroId',
            'estado'
        ));
    }

    /**
     * Mostrar formulario para crear un nuevo cierre
     */
    public function create()
    {
        if (!auth()->user()->can('cerrar-caja')) {
            abort(403, 'No tienes permiso para crear cierres de caja.');
        }

        // Verificar que existe un turno activo
        $turnoActivo = TurnoCaja::turnoActivo();
        
        if (!$turnoActivo) {
            return redirect()->route('dashboard')->with('error', 'No hay ningún turno activo. Debes iniciar un turno primero.');
        }

        // Verificar que el turno es del cajero actual
        if ($turnoActivo->cajero_id !== Auth::id()) {
            return redirect()->route('dashboard')->with('error', 
                "{$turnoActivo->cajero->name} está de turno actualmente. Solo él puede realizar el cierre de caja."
            );
        }

        // Verificar si el cajero ya tiene un cierre para este turno
        if ($turnoActivo->cierre) {
            return redirect()->route('cierres_caja.show', $turnoActivo->cierre->id)
                ->with('info', 'Ya existe un cierre de caja para este turno.');
        }

        $inicio = $turnoActivo->inicio;
        $fin = now();

        // Calcular totales del sistema por método de pago
        $totalesSistema = $this->calcularTotalesSistema(Auth::id(), $inicio, $fin);

        // Registrar en bitácora
        BitacoraController::registrar('Crear formulario', 'CierreCaja');

        return view('cierres_caja.create', compact('inicio', 'fin', 'totalesSistema', 'turnoActivo'));
    }

    /**
     * Guardar el nuevo cierre de caja
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('cerrar-caja')) {
            abort(403, 'No tienes permiso para registrar cierres de caja.');
        }

        $request->validate([
            'inicio' => 'required|date',
            'fin' => 'required|date|after:inicio',
            'efectivo_declarado' => 'required|numeric|min:0',
            'pos_declarado' => 'required|numeric|min:0',
            'qr_declarado' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:255',
        ], [
            'inicio.required' => 'La fecha de inicio es obligatoria.',
            'fin.required' => 'La fecha de fin es obligatoria.',
            'fin.after' => 'La fecha de fin debe ser posterior a la de inicio.',
            'efectivo_declarado.required' => 'El monto de efectivo es obligatorio.',
            'pos_declarado.required' => 'El monto de POS es obligatorio.',
            'qr_declarado.required' => 'El monto de QR es obligatorio.',
        ]);

        // Verificar que existe un turno activo
        $turnoActivo = TurnoCaja::turnoActivo();
        
        if (!$turnoActivo) {
            return back()->withErrors([
                'mensaje' => 'No hay ningún turno activo.'
            ])->withInput();
        }

        // Verificar que el turno es del cajero actual
        if ($turnoActivo->cajero_id !== Auth::id()) {
            return back()->withErrors([
                'mensaje' => 'Solo puedes cerrar tu propio turno.'
            ])->withInput();
        }

        // Verificar que el turno no tenga un cierre ya registrado
        if ($turnoActivo->cierre) {
            return back()->withErrors([
                'mensaje' => 'Este turno ya tiene un cierre registrado.'
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            // Calcular totales del sistema
            $inicio = Carbon::parse($request->inicio);
            $fin = Carbon::parse($request->fin);
            $totalesSistema = $this->calcularTotalesSistema(Auth::id(), $inicio, $fin);

            // Montos declarados
            $efectivoDeclarado = floatval($request->efectivo_declarado);
            $posDeclarado = floatval($request->pos_declarado);
            $qrDeclarado = floatval($request->qr_declarado);

            $totalDeclarado = $efectivoDeclarado + $posDeclarado + $qrDeclarado;
            $totalSistema = $totalesSistema['efectivo'] + $totalesSistema['pos'] + $totalesSistema['qr'];
            $diferencia = $totalDeclarado - $totalSistema;

            // Crear el cierre de caja
            $cierre = CierreCaja::create([
                'turno_id' => $turnoActivo->id,
                'cajero_id' => Auth::id(),
                'inicio' => $inicio,
                'fin' => $fin,
                'total_sistema' => $totalSistema,
                'total_declarado' => $totalDeclarado,
                'diferencia' => $diferencia,
                'observaciones' => $request->observaciones,
            ]);

            // Crear los detalles por método de pago
            CierreCajaDetalle::create([
                'cierre_caja_id' => $cierre->id,
                'metodo' => 'efectivo',
                'monto_sistema' => $totalesSistema['efectivo'],
                'monto_declarado' => $efectivoDeclarado,
            ]);

            CierreCajaDetalle::create([
                'cierre_caja_id' => $cierre->id,
                'metodo' => 'pos',
                'monto_sistema' => $totalesSistema['pos'],
                'monto_declarado' => $posDeclarado,
            ]);

            CierreCajaDetalle::create([
                'cierre_caja_id' => $cierre->id,
                'metodo' => 'qr',
                'monto_sistema' => $totalesSistema['qr'],
                'monto_declarado' => $qrDeclarado,
            ]);

            // Cerrar el turno
            $turnoActivo->cerrarTurno($request->observaciones);

            DB::commit();

            // Registrar en bitácora
            BitacoraController::registrar(
                'Crear',
                'CierreCaja',
                $cierre->id
            );

            return redirect()->route('cierres_caja.show', $cierre->id)
                ->with('success', 'Cierre de caja registrado exitosamente. Tu turno ha finalizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'mensaje' => 'Error al registrar el cierre: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Mostrar un cierre de caja específico
     */
    public function show($id)
    {
        if (!auth()->user()->can('ver-cierres')) {
            abort(403, 'No tienes permiso para ver los cierres de caja.');
        }

        $cierre = CierreCaja::with(['cajero', 'detalles'])->findOrFail($id);

        // Verificar permisos (solo puede ver su propio cierre o ser admin)
        if (!Auth::user()->hasRole('administrador') && $cierre->cajero_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver este cierre de caja.');
        }

        // Obtener cobros del período
        $cobros = CobroCaja::with(['pedido.mesa'])
            ->where('cajero_id', $cierre->cajero_id)
            ->where('estado', 'cobrado')
            ->whereBetween('created_at', [$cierre->inicio, $cierre->fin])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCobros = $cobros->count();

        // Registrar en bitácora
        BitacoraController::registrar('Ver detalle', 'CierreCaja', $cierre->id);

        return view('cierres_caja.show', compact('cierre', 'cobros', 'totalCobros'));
    }

    /**
     * Exportar cierre a PDF
     */
    public function exportarPDF($id)
    {
        if (!auth()->user()->can('ver-cierres')) {
            abort(403, 'No tienes permiso para exportar cierres de caja.');
        }

        $cierre = CierreCaja::with(['cajero', 'detalles'])->findOrFail($id);

        // Verificar permisos
        if (!Auth::user()->hasRole('administrador') && $cierre->cajero_id !== Auth::id()) {
            abort(403, 'No tienes permiso para exportar este cierre de caja.');
        }

        // Obtener cobros del período
        $cobros = CobroCaja::with(['pedido.mesa'])
            ->where('cajero_id', $cierre->cajero_id)
            ->where('estado', 'cobrado')
            ->whereBetween('created_at', [$cierre->inicio, $cierre->fin])
            ->orderBy('created_at', 'asc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cierres_caja.pdf', compact('cierre', 'cobros'));
        $pdf->setPaper('letter', 'portrait');

        $nombreArchivo = 'cierre-caja-' . $cierre->id . '-' . $cierre->fin->format('Y-m-d') . '.pdf';

        // Registrar en bitácora
        BitacoraController::registrar('Exportar PDF', 'CierreCaja', $cierre->id);

        return $pdf->download($nombreArchivo);
    }

    /**
     * Calcular totales del sistema por método de pago
     */
    private function calcularTotalesSistema($cajeroId, $inicio, $fin)
    {
        $cobros = CobroCaja::where('cajero_id', $cajeroId)
            ->where('estado', 'cobrado')
            ->whereBetween('created_at', [$inicio, $fin])
            ->get();

        return [
            'efectivo' => $cobros->where('metodo', 'efectivo')->sum('importe'),
            'pos' => $cobros->where('metodo', 'pos')->sum('importe'),
            'qr' => $cobros->where('metodo', 'qr')->sum('importe'),
            'total' => $cobros->sum('importe'),
            'cantidad_cobros' => $cobros->count(),
        ];
    }

    /**
     * Calcular estadísticas del período
     */
    private function calcularEstadisticas($fechaInicio, $fechaFin, $cajeroId)
    {
        $query = CierreCaja::whereBetween('fin', [
            Carbon::parse($fechaInicio)->startOfDay(),
            Carbon::parse($fechaFin)->endOfDay()
        ]);

        if ($cajeroId !== 'todos') {
            $query->where('cajero_id', $cajeroId);
        }

        $cierres = $query->get();

        return [
            'total_cierres' => $cierres->count(),
            'total_sistema' => $cierres->sum('total_sistema'),
            'total_declarado' => $cierres->sum('total_declarado'),
            'total_diferencias' => $cierres->sum('diferencia'),
            'cierres_cuadrados' => $cierres->filter(fn($c) => $c->estaCuadrado())->count(),
            'cierres_con_diferencias' => $cierres->filter(fn($c) => $c->tieneDiferencia())->count(),
            'total_faltantes' => $cierres->filter(fn($c) => $c->esFaltante())->sum('diferencia'),
            'total_sobrantes' => $cierres->filter(fn($c) => $c->esSobrante())->sum('diferencia'),
        ];
    }

    /**
     * Verificar si existe un cierre para hoy
     */
    public function verificarCierreHoy()
    {
        $cierreHoy = CierreCaja::where('cajero_id', Auth::id())
            ->whereDate('fin', today())
            ->first();

        return response()->json([
            'existe' => $cierreHoy !== null,
            'cierre' => $cierreHoy ? [
                'id' => $cierreHoy->id,
                'total_sistema' => $cierreHoy->total_sistema,
                'total_declarado' => $cierreHoy->total_declarado,
                'diferencia' => $cierreHoy->diferencia,
            ] : null
        ]);
    }

    /**
     * Anular un cierre de caja (solo administrador)
     */
    public function anular(Request $request, $id)
    {
        if (!Auth::user()->hasRole('administrador')) {
            abort(403, 'Solo los administradores pueden anular cierres de caja.');
        }

        $cierre = CierreCaja::findOrFail($id);

        $request->validate([
            'motivo_anulacion' => 'required|string|max:255',
        ], [
            'motivo_anulacion.required' => 'Debe indicar el motivo de la anulación.',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar observaciones con el motivo de anulación
            $cierre->observaciones = ($cierre->observaciones ? $cierre->observaciones . ' | ' : '') 
                . 'ANULADO: ' . $request->motivo_anulacion 
                . ' (por ' . Auth::user()->name . ' el ' . now()->format('d/m/Y H:i') . ')';
            $cierre->save();

            // Marcar como anulado cambiando la diferencia a 0 y agregando nota
            // (No hay campo estado en la migración, así que usamos observaciones)

            DB::commit();

            // Registrar en bitácora
            BitacoraController::registrar(
                'Anular',
                'CierreCaja',
                $cierre->id
            );

            return redirect()->route('cierres_caja.index')
                ->with('success', 'Cierre de caja anulado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'mensaje' => 'Error al anular el cierre: ' . $e->getMessage()
            ]);
        }
    }
}
