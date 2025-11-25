<?php

namespace App\Http\Controllers\Clientes;

use App\Models\FidelidadMovimiento;
use App\Models\FidelidadConfig;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Usuarios\BitacoraController;

class FidelidadController extends BaseController
{
    use AuthorizesRequests;

    private $configFidelidad;

    public function __construct()
    {
        $this->configFidelidad = FidelidadConfig::getConfig();
    }

    /** Listado de puntos por cliente */
    public function index(Request $request)
    {
        $query = FidelidadMovimiento::with(['cliente'])
            ->select('cliente_id')
            ->selectRaw('SUM(CASE WHEN tipo = "acumulo" THEN puntos ELSE 0 END) as puntos_acumulados')
            ->selectRaw('SUM(CASE WHEN tipo = "canje" THEN puntos ELSE 0 END) as puntos_canjeados')
            ->selectRaw('SUM(CASE WHEN tipo = "acumulo" THEN puntos ELSE -puntos END) as puntos_actuales')
            ->groupBy('cliente_id');

        if (auth()->user()->hasRole('cliente')) {
            $query->where('cliente_id', auth()->id());
        } elseif ($request->get('cliente_id') && auth()->user()->can('gestionar-fidelidad')) {
            $query->where('cliente_id', $request->get('cliente_id'));
        }

        $puntosClientes = $query->paginate(15);
        $clientes = User::role('cliente')->get();
        $misPuntos = $this->calcularPuntosCliente(auth()->id());

        BitacoraController::registrar('ver lista', 'Fidelidad', null);
        return view('fidelidad.index', compact('puntosClientes', 'clientes', 'misPuntos'));
    }

    public function canjearPuntosCajero(Request $request)
{
    $this->authorize('gestionar-fidelidad');

    $validated = $request->validate([
        'cliente_id' => 'required|exists:users,id',
        'puntos' => 'required|integer|min:1',
        'descripcion' => 'required|string|max:200'
    ]);

    DB::beginTransaction();
    try {
        FidelidadMovimiento::create([
            'cliente_id' => $validated['cliente_id'],
            'puntos' => $validated['puntos'],
            'tipo' => 'canje',
            'descripcion' => $validated['descripcion'],
            'origen_type' => null,
            'origen_id' => null
        ]);
        
        DB::commit();

        $cliente = User::find($validated['cliente_id']);
        BitacoraController::registrar('canjear puntos desde cajero', 'FidelidadMovimiento', null, "Cliente: {$cliente->name}");
        
        return response()->json(['success' => true, 'message' => 'Puntos canjeados exitosamente.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Error al canjear puntos: ' . $e->getMessage()], 500);
    }
}

    /** Vista y actualización de configuración */
    public function config()
    {
        $this->authorize('gestionar-fidelidad');
        $config = $this->configFidelidad;
        BitacoraController::registrar('ver configuracion', 'FidelidadConfig', null);
        return view('fidelidad.config', compact('config'));
    }

    public function updateConfig(Request $request)
    {
        $this->authorize('gestionar-fidelidad');

        $validated = $request->validate([
            'puntos_por_dolar' => 'required|numeric|min:0',
            'puntos_por_antiguedad_meses' => 'nullable|integer|min:0',
            'puntos_antiguedad_base' => 'nullable|integer|min:0',
            'multiplicador_fin_semana' => 'nullable|numeric|min:1',
            'activo' => 'sometimes|boolean',
            'recompensa_descuento_10_puntos' => 'nullable|integer|min:0',
            'recompensa_descuento_20_puntos' => 'nullable|integer|min:0',
            'recompensa_producto_gratis_puntos' => 'nullable|integer|min:0',
        ]);

        foreach ($validated as $clave => $valor) {
            FidelidadConfig::setValor($clave, $valor, $this->determinarTipo($valor));
        }

        $this->configFidelidad = FidelidadConfig::getConfig();

        BitacoraController::registrar('actualizar configuracion', 'FidelidadConfig', null);
        return back()->with('success', 'Configuración de fidelidad actualizada exitosamente.');
    }

    private function determinarTipo($valor)
    {
        if (is_bool($valor)) return 'boolean';
        if (is_int($valor)) return 'integer';
        if (is_float($valor)) return 'float';
        return 'string';
    }

    /** Recompensas y canje */
    public function recompensas()
    {
        $recompensas = $this->obtenerRecompensasConfiguradas();
        $programaActivo = $this->isProgramaActivo();

        $puntosCliente = auth()->user()->hasRole('cliente') ? $this->calcularPuntosCliente(auth()->id()) : 0;

        BitacoraController::registrar('ver recompensas', 'Recompensa', null);
        return view('fidelidad.recompensas', compact('recompensas', 'programaActivo', 'puntosCliente'));
    }

    public function canjearRecompensa(Request $request, $recompensaId)
    {
        if (!$this->isProgramaActivo()) {
            return back()->withErrors(['error' => 'El programa de fidelidad no está activo actualmente.']);
        }

        $recompensa = $this->obtenerRecompensaPorId($recompensaId);
        if (!$recompensa) {
            return back()->withErrors(['error' => 'Recompensa no válida o no disponible.']);
        }

        $clienteId = auth()->user()->hasRole('cliente') ? auth()->id() : $request->cliente_id;

        if (auth()->user()->hasRole('cliente') && $clienteId !== auth()->id()) {
            abort(403, 'No tienes permiso para canjear recompensas para otros clientes.');
        }

        DB::beginTransaction();
        try {
            $puntosCliente = $this->calcularPuntosCliente($clienteId);
            $cliente = User::findOrFail($clienteId);

            if (!$cliente->hasRole('cliente')) {
                throw new \Exception('El usuario especificado no es un cliente válido.');
            }

            if ($puntosCliente < $recompensa['puntos_requeridos']) {
                $faltan = $recompensa['puntos_requeridos'] - $puntosCliente;
                throw new \Exception("No tienes puntos suficientes. Te faltan {$faltan} puntos.");
            }

            FidelidadMovimiento::create([
                'cliente_id' => $clienteId,
                'puntos' => $recompensa['puntos_requeridos'],
                'tipo' => 'canje',
                'descripcion' => "Canje de recompensa: {$recompensa['nombre']}",
                'origen_type' => null,
                'origen_id' => null
            ]);

            DB::commit();

            BitacoraController::registrar(
                'canjear recompensa',
                'Recompensa',
                $recompensaId,
                "Cliente: {$cliente->name}, Recompensa: {$recompensa['nombre']}"
            );

            return back()->with('success', "¡Recompensa '{$recompensa['nombre']}' canjeada exitosamente!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /** Historial y cálculo de puntos */
    public function historial(Request $request, User $cliente = null)
    {
        if (auth()->user()->hasRole('cliente')) {
            $cliente = auth()->user();
        } elseif (!$cliente) {
            $cliente = auth()->user();
        }

        if ($cliente->id != auth()->id() && !auth()->user()->can('gestionar-fidelidad')) {
            abort(403, 'No tienes permiso para ver el historial de otros clientes.');
        }

        $movimientos = FidelidadMovimiento::with(['origen'])
            ->where('cliente_id', $cliente->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $puntosTotales = $this->calcularPuntosCliente($cliente->id);
        $puntosAcumulados = FidelidadMovimiento::where('cliente_id', $cliente->id)
            ->where('tipo', 'acumulo')
            ->sum('puntos');
        $puntosCanjeados = FidelidadMovimiento::where('cliente_id', $cliente->id)
            ->where('tipo', 'canje')
            ->sum('puntos');

        BitacoraController::registrar('ver historial', 'FidelidadMovimiento', null);

        return view('fidelidad.historial', compact(
            'movimientos', 
            'cliente', 
            'puntosTotales',
            'puntosAcumulados',
            'puntosCanjeados'
        ));
    }

    public function calcularPuntosCliente($clienteId): int
    {
        $result = FidelidadMovimiento::where('cliente_id', $clienteId)
            ->selectRaw('SUM(CASE WHEN tipo = "acumulo" THEN puntos WHEN tipo = "canje" THEN -puntos ELSE 0 END) as puntos_totales')
            ->first();

        return max(0, $result->puntos_totales ?? 0);
    }

    /** Acumulación de puntos por pedido - CORREGIDO */
    public function acumularPuntosPorPedido(Pedido $pedido): int
    {
        if (!$this->pedidoPuedeAcumularPuntos($pedido)) {
            return 0;
        }

        DB::beginTransaction();
        try {
            $puntos = $this->calcularPuntosPedido($pedido);
            
            if ($puntos <= 0) {
                DB::rollBack();
                return 0;
            }

            // Crear el movimiento usando la relación polimórfica
            $movimiento = FidelidadMovimiento::create([
                'cliente_id' => $pedido->cliente_id,
                'puntos' => $puntos,
                'tipo' => 'acumulo',
                'descripcion' => "Acumulación por pedido #{$pedido->id}",
                'origen_type' => Pedido::class,
                'origen_id' => $pedido->id,
            ]);

            DB::commit();
            return $puntos;
        } catch (\Exception $e) {
            DB::rollBack();
            return 0;
        }
    }

    public function pedidoPuedeAcumularPuntos(Pedido $pedido): bool
    {
        // 1. Verificar que el programa esté activo
        if (!$this->isProgramaActivo()) {
            return false;
        }

        // 2. Verificar que el pedido tenga cliente
        if (!$pedido->cliente_id) {
            return false;
        }

        // 3. Verificar que el cliente exista y tenga rol cliente
        $cliente = User::find($pedido->cliente_id);
        if (!$cliente || !$cliente->hasRole('cliente')) {
            return false;
        }

        // 4. Solo permitir estados específicos que SÍ acumulan
        $estadosQueAcumulan = ['entregado', 'pagado', 'cancelado'];
        if (!in_array($pedido->estado, $estadosQueAcumulan)) {
            return false;
        }

        // 5. Verificar que no exista ya un movimiento para este pedido
        $existeMovimiento = FidelidadMovimiento::where('origen_type', Pedido::class)
            ->where('origen_id', $pedido->id)
            ->where('tipo', 'acumulo')
            ->exists();

        return !$existeMovimiento;
    }

    public function calcularPuntosPedido(Pedido $pedido): int
    {
        $totalPedido = $pedido->total ?? 0;
        
        // Calcular puntos base
        $puntos = intval($totalPedido * ($this->configFidelidad['puntos_por_dolar'] ?? 0));

        return max(0, $puntos);
    }

    public function repararPuntosPedidos()
    {
        $this->authorize('gestionar-fidelidad');

        $pedidosSinPuntos = Pedido::whereIn('estado', ['entregado', 'pagado', 'cancelado'])
            ->whereDoesntHave('movimientosFidelidad')
            ->whereNotNull('cliente_id')
            ->with('cliente')
            ->get();

        $puntosAcumulados = 0;
        $pedidosProcesados = 0;

        foreach ($pedidosSinPuntos as $pedido) {
            $puntos = $this->acumularPuntosPorPedido($pedido);
            if ($puntos > 0) {
                $puntosAcumulados += $puntos;
                $pedidosProcesados++;
            }
        }

        BitacoraController::registrar('reparar puntos', 'Fidelidad', null, "Pedidos: {$pedidosProcesados}, Puntos: {$puntosAcumulados}");
        return back()->with('success', "Se acumularon {$puntosAcumulados} puntos para {$pedidosProcesados} pedidos.");
    }

    /** Métodos para cajero */
    public function puntosCajero()
    {
        $this->authorize('gestionar-fidelidad');
        $recompensas = $this->obtenerRecompensasConfiguradas();
        BitacoraController::registrar('acceder', 'PuntosCajero', null);
        return view('fidelidad.puntosCajero', compact('recompensas'));
    }

    public function buscarCliente(Request $request)
    {
        $this->authorize('gestionar-fidelidad');

        $query = $request->get('q');
        if (!$query || strlen($query) < 2) return response()->json([]);

        $clientes = User::role('cliente')
            ->with('misPedidos')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function($cliente) {
                return [
                    'id' => $cliente->id,
                    'name' => $cliente->name,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono ?? 'No registrado',
                    'direccion' => $cliente->direccion ?? 'No registrada',
                    'puntos_totales' => $this->calcularPuntosCliente($cliente->id),
                    'total_pedidos' => $cliente->misPedidos->count() ?? 0,
                    'cliente_desde' => $cliente->created_at->format('M Y'),
                    'initials' => $cliente->initials()
                ];
            });

        return response()->json($clientes);
    }

    public function getHistorialCliente($clienteId)
    {
        $this->authorize('gestionar-fidelidad');

        $movimientos = FidelidadMovimiento::with(['origen'])
            ->where('cliente_id', $clienteId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function($movimiento) {
                return [
                    'id' => $movimiento->id,
                    'tipo' => $movimiento->tipo,
                    'puntos' => $movimiento->puntos,
                    'descripcion' => $movimiento->descripcion,
                    'fecha' => $movimiento->created_at->format('d/m/Y H:i'),
                    'pedido_id' => $movimiento->origen_type === Pedido::class ? $movimiento->origen_id : null
                ];
            });

        return response()->json($movimientos);
    }

    public function ajustarPuntos(Request $request)
    {
        $this->authorize('gestionar-fidelidad');

        $validated = $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'tipo' => 'required|in:acumulo,canje',
            'puntos' => 'required|integer|min:1',
            'motivo' => 'required|string|max:200'
        ]);

        DB::beginTransaction();
        try {
            FidelidadMovimiento::create([
                'cliente_id' => $validated['cliente_id'],
                'puntos' => $validated['puntos'],
                'tipo' => $validated['tipo'],
                'descripcion' => $validated['motivo'],
                'origen_type' => null,
                'origen_id' => null
            ]);
            DB::commit();

            $cliente = User::find($validated['cliente_id']);
            BitacoraController::registrar('ajustar puntos manualmente', 'FidelidadMovimiento', null, "Cliente: {$cliente->name}");
            return back()->with('success', 'Puntos ajustados manualmente exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al ajustar puntos: ' . $e->getMessage()]);
        }
    }

    public function getPuntosCliente(User $cliente = null)
    {
        if (auth()->user()->hasRole('cliente')) {
            $cliente = auth()->user();
        } elseif (!$cliente) {
            return response()->json(['error' => 'Cliente no especificado'], 400);
        }

        return response()->json([
            'cliente_id' => $cliente->id,
            'cliente_nombre' => $cliente->name,
            'puntos_actuales' => $this->calcularPuntosCliente($cliente->id)
        ]);
    }

    /** Métodos auxiliares privados */
    private function obtenerRecompensasConfiguradas()
    {
        return [
            ['id'=>1, 'nombre'=>'10% de descuento','tipo'=>'descuento','descripcion'=>'Se necesitan 10 puntos para acceder', 'puntos_requeridos'=>$this->configFidelidad['recompensa_descuento_10_puntos'] ?? 100],
            ['id'=>2, 'nombre'=>'20% de descuento','tipo'=>'descuento','descripcion'=>'Se necesitan 10 puntos para acceder', 'puntos_requeridos'=>$this->configFidelidad['recompensa_descuento_20_puntos'] ?? 200],
            ['id'=>3, 'nombre'=>'Producto gratis','tipo'=>'producto','descripcion'=>'Se necesitan 10 puntos para acceder','puntos_requeridos'=>$this->configFidelidad['recompensa_producto_gratis_puntos'] ?? 500]
        ];
    }

    private function obtenerRecompensaPorId($id)
    {
        return collect($this->obtenerRecompensasConfiguradas())->firstWhere('id', $id);
    }

    public function isProgramaActivo(): bool
    {
        $activo = $this->configFidelidad['activo'] ?? false;
        return (bool) $activo;
    }

    private function getRecompensasDisponibles()
    {
        return $this->obtenerRecompensasConfiguradas();
    }

    public function debugConfig()
    {
        $config = $this->configFidelidad;
        
        foreach ($config as $clave => $valor) {
        }
        
        return response()->json($config);
    }
}