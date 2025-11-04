<?php

namespace App\Http\Controllers;

use App\Models\FidelidadMovimiento;
use App\Models\FidelidadConfig;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\BitacoraController;

class FidelidadController extends BaseController
{
    use AuthorizesRequests;

    private $configFidelidad;

    public function __construct()
    {
        $this->configFidelidad = FidelidadConfig::getConfig();
    }

    /**
     * Display a listing of puntos for clients
     */
    public function index(Request $request)
    {
        $query = FidelidadMovimiento::with(['cliente', 'pedido'])
            ->select('cliente_id')
            ->selectRaw('SUM(CASE WHEN tipo = "acumulo" THEN puntos ELSE 0 END) as puntos_acumulados')
            ->selectRaw('SUM(CASE WHEN tipo = "canje" THEN puntos ELSE 0 END) as puntos_canjeados')
            ->selectRaw('SUM(CASE WHEN tipo = "acumulo" THEN puntos ELSE -puntos END) as puntos_actuales')
            ->groupBy('cliente_id');

        // Filtros de seguridad
        if (auth()->user()->hasRole('cliente')) {
            $query->where('cliente_id', auth()->id());
        } elseif ($request->get('cliente_id') && auth()->user()->can('gestionar-fidelidad')) {
            $query->where('cliente_id', $request->get('cliente_id'));
        }

        $puntosClientes = $query->paginate(15);
        $clientes = User::role('cliente')->get();

        // Calcular puntos del usuario actual para la tarjeta principal
        $misPuntos = $this->calcularPuntosCliente(auth()->id());

        BitacoraController::registrar('ver lista', 'Fidelidad', null);
        return view('fidelidad.index', compact('puntosClientes', 'clientes', 'misPuntos'));
    }

    /**
     * Show the form for managing fidelidad configuration
     */
    public function config()
    {
        $this->authorize('gestionar-fidelidad');
        
        $config = $this->configFidelidad;
        BitacoraController::registrar('ver configuracion', 'FidelidadConfig', null);
        
        return view('fidelidad.config', compact('config'));
    }

    /**
     * Update fidelidad configuration
     */
    public function updateConfig(Request $request)
    {
        $this->authorize('gestionar-fidelidad');

        $validated = $request->validate([
            'puntos_por_dolar' => 'required|numeric|min:0',
            'puntos_por_antiguedad_meses' => 'nullable|integer|min:0',
            'puntos_antiguedad_base' => 'nullable|integer|min:0',
            'multiplicador_fin_semana' => 'nullable|numeric|min:1',
            'activo' => 'sometimes|boolean',
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

    /**
     * Show available rewards for redemption
     */
    public function recompensas()
    {
        $recompensas = $this->obtenerRecompensasConfiguradas();
        $programaActivo = $this->isProgramaActivo();

        // Para clientes, calcular sus puntos actuales
        $puntosCliente = 0;
        if (auth()->user()->hasRole('cliente')) {
            $puntosCliente = $this->calcularPuntosCliente(auth()->id());
        }

        BitacoraController::registrar('ver recompensas', 'Recompensa', null);
        return view('fidelidad.recompensas', compact('recompensas', 'programaActivo', 'puntosCliente'));
    }

    /**
     * Redeem a reward
     */
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

        // Validación de seguridad
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
                $puntosFaltantes = $recompensa['puntos_requeridos'] - $puntosCliente;
                throw new \Exception("No tienes puntos suficientes. Te faltan {$puntosFaltantes} puntos.");
            }

            FidelidadMovimiento::create([
                'cliente_id' => $clienteId,
                'tipo' => 'canje',
                'puntos' => $recompensa['puntos_requeridos'],
                'motivo' => "Canje de recompensa: {$recompensa['nombre']}",
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
            \Log::error("Error al canjear recompensa: {$e->getMessage()}");
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show client points history
     */
    public function historial(Request $request, User $cliente = null)
    {
        // Determinar cliente
        if (auth()->user()->hasRole('cliente')) {
            $cliente = auth()->user();
        } elseif (!$cliente) {
            $cliente = auth()->user();
        }

        // Validar permisos
        if ($cliente->id != auth()->id() && !auth()->user()->can('gestionar-fidelidad')) {
            abort(403, 'No tienes permiso para ver el historial de otros clientes.');
        }

        $movimientos = FidelidadMovimiento::with(['pedido'])
            ->where('cliente_id', $cliente->id)
            ->orderBy('id', 'desc')
            ->paginate(15);

        $puntosTotales = $this->calcularPuntosCliente($cliente->id);

        // CÁLCULOS ADICIONALES QUE LA VISTA NECESITA
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

    /**
     * Calculate client points - MÉTODO UNIFICADO Y CORREGIDO
     */
    public function calcularPuntosCliente($clienteId): int
    {
        try {
            $result = FidelidadMovimiento::where('cliente_id', $clienteId)
                ->selectRaw('SUM(CASE WHEN tipo = "acumulo" THEN puntos WHEN tipo = "canje" THEN -puntos ELSE 0 END) as puntos_totales')
                ->first();

            $puntos = $result->puntos_totales ?? 0;

            return max(0, $puntos);

        } catch (\Exception $e) {
            \Log::error("Error calculando puntos para cliente {$clienteId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * API: Acumular puntos automáticamente cuando un pedido se completa
     */
    public function acumularPuntosPorPedido(Pedido $pedido)
    {
        // Validaciones
        if (!$pedido->cliente_id) {
            throw new \Exception('Cliente no identificado para acumular puntos.');
        }

        if (!$this->configFidelidad['activo']) {
            throw new \Exception('El programa de fidelidad no está activo.');
        }

        if (!in_array($pedido->estado, ['entregado', 'servido', 'retirado', 'pagado'])) {
            throw new \Exception('El pedido no está completado para acumular puntos.');
        }

        // Verificar que no se hayan acumulado puntos ya
        if (FidelidadMovimiento::where('pedido_id', $pedido->id)->where('tipo', 'acumulo')->exists()) {
            throw new \Exception('Ya se acumularon puntos para este pedido.');
        }

        DB::beginTransaction();

        try {
            $puntos = $this->calcularPuntosPedido($pedido);

            FidelidadMovimiento::create([
                'cliente_id' => $pedido->cliente_id,
                'pedido_id' => $pedido->id,
                'tipo' => 'acumulo',
                'puntos' => $puntos,
                'motivo' => "Acumulación por pedido #{$pedido->id}"
            ]);

            DB::commit();

            BitacoraController::registrar('acumular puntos', 'FidelidadMovimiento', null, "Pedido #{$pedido->id}");
            return $puntos;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calculate points for a completed order
     */
    private function calcularPuntosPedido(Pedido $pedido): int
    {
        $totalPedido = $pedido->total;
        $puntos = intval($totalPedido * $this->configFidelidad['puntos_por_dolar']);

        // Puntos por antigüedad
        if (($this->configFidelidad['puntos_por_antiguedad_meses'] ?? 0) > 0 && 
            ($this->configFidelidad['puntos_antiguedad_base'] ?? 0) > 0) {
            
            $antiguedadMeses = $pedido->cliente->created_at->diffInMonths(now());
            $puntosAntiguedad = intval($antiguedadMeses / $this->configFidelidad['puntos_por_antiguedad_meses']) * $this->configFidelidad['puntos_antiguedad_base'];
            $puntos += $puntosAntiguedad;
        }

        // Multiplicador por fin de semana
        if (($this->configFidelidad['multiplicador_fin_semana'] ?? 1) > 1 && now()->isWeekend()) {
            $puntos = intval($puntos * $this->configFidelidad['multiplicador_fin_semana']);
        }

        return max(0, $puntos);
    }

    /**
     * Vista para que los cajeros gestionen puntos de clientes
     */
    public function puntosCajero()
    {
        $this->authorize('gestionar-fidelidad');
        
        $recompensas = $this->getRecompensasDisponibles();
        
        BitacoraController::registrar('acceder', 'PuntosCajero', null);
        return view('fidelidad.puntosCajero', compact('recompensas'));
    }

    /**
     * Buscar clientes para el cajero (para AJAX) - CORREGIDO
     */
    public function buscarCliente(Request $request)
    {
        $this->authorize('gestionar-fidelidad');

        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $clientes = User::role('cliente')
            ->with(['misPedidos']) // RELACIÓN CORREGIDA - usar misPedidos en lugar de pedidos
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
                  // Eliminada la búsqueda por teléfono ya que no existe en el modelo
            })
            ->limit(10)
            ->get()
            ->map(function($cliente) {
                // USAR EL MÉTODO UNIFICADO para consistencia
                $puntosTotales = $this->calcularPuntosCliente($cliente->id);
                
                return [
                    'id' => $cliente->id,
                    'name' => $cliente->name,
                    'email' => $cliente->email,
                    'telefono' => 'No registrado', // No existe teléfono en el modelo User
                    'direccion' => 'No registrada', // No existe dirección en el modelo User
                    'puntos_totales' => $puntosTotales,
                    'total_pedidos' => $cliente->misPedidos->count() ?? 0, // RELACIÓN CORREGIDA
                    'cliente_desde' => $cliente->created_at->format('M Y'),
                    'initials' => $cliente->initials() // Usar el método initials del modelo
                ];
            });

        return response()->json($clientes);
    }

    /**
     * Obtener historial de un cliente específico
     */
    public function getHistorialCliente($clienteId)
    {
        $this->authorize('gestionar-fidelidad');

        $movimientos = FidelidadMovimiento::with('pedido')
            ->where('cliente_id', $clienteId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function($movimiento) {
                return [
                    'id' => $movimiento->id,
                    'tipo' => $movimiento->tipo,
                    'puntos' => $movimiento->puntos,
                    'descripcion' => $movimiento->motivo,
                    'fecha' => $movimiento->created_at->format('d/m/Y H:i'),
                    'pedido_id' => $movimiento->pedido_id
                ];
            });

        return response()->json($movimientos);
    }

    /**
     * Manual points adjustment (for administrators)
     */
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
            FidelidadMovimiento::create($validated);
            DB::commit();

            $cliente = User::find($validated['cliente_id']);
            BitacoraController::registrar('ajustar puntos manualmente', 'FidelidadMovimiento', null, "Cliente: {$cliente->name}");
            
            return back()->with('success', 'Puntos ajustados manualmente exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al ajustar puntos: ' . $e->getMessage()]);
        }
    }

    /**
     * Get client points (API endpoint)
     */
    public function getPuntosCliente(User $cliente = null)
    {
        if (auth()->user()->hasRole('cliente')) {
            $cliente = auth()->user();
        } elseif (!$cliente) {
            return response()->json(['error' => 'Cliente no especificado'], 400);
        }

        $puntos = $this->calcularPuntosCliente($cliente->id);

        return response()->json([
            'cliente_id' => $cliente->id,
            'cliente_nombre' => $cliente->name,
            'puntos_actuales' => $puntos
        ]);
    }

    /**
     * Check if fidelidad program is active
     */
    public function isProgramaActivo()
    {
        return $this->configFidelidad['activo'] ?? false;
    }

    /**
     * Obtener recompensas configuradas
     */
    private function obtenerRecompensasConfiguradas()
    {
        $recompensas = [
            [
                'id' => 1,
                'nombre' => 'Descuento 10%',
                'descripcion' => '10% de descuento en tu próxima compra',
                'puntos_requeridos' => (int) FidelidadConfig::getValor('recompensa_descuento_10_puntos', 100),
                'tipo' => 'descuento',
                'activo' => true
            ],
            [
                'id' => 2,
                'nombre' => 'Descuento 20%',
                'descripcion' => '20% de descuento en tu próxima compra',
                'puntos_requeridos' => (int) FidelidadConfig::getValor('recompensa_descuento_20_puntos', 200),
                'tipo' => 'descuento',
                'activo' => true
            ],
            [
                'id' => 3,
                'nombre' => 'Producto Gratis',
                'descripcion' => 'Producto de cortesía del menú',
                'puntos_requeridos' => (int) FidelidadConfig::getValor('recompensa_producto_gratis_puntos', 150),
                'tipo' => 'producto',
                'activo' => true
            ]
        ];
        
        return array_filter($recompensas, function($recompensa) {
            return $recompensa['activo'] === true && $recompensa['puntos_requeridos'] > 0;
        });
    }

    /**
     * Obtener recompensa por ID
     */
    private function obtenerRecompensaPorId($recompensaId)
    {
        $recompensas = [
            1 => [
                'nombre' => 'Descuento 10%', 
                'puntos_requeridos' => (int) FidelidadConfig::getValor('recompensa_descuento_10_puntos', 100),
                'tipo' => 'descuento',
                'activo' => true
            ],
            2 => [
                'nombre' => 'Descuento 20%', 
                'puntos_requeridos' => (int) FidelidadConfig::getValor('recompensa_descuento_20_puntos', 200),
                'tipo' => 'descuento',
                'activo' => true
            ],
            3 => [
                'nombre' => 'Producto Gratis', 
                'puntos_requeridos' => (int) FidelidadConfig::getValor('recompensa_producto_gratis_puntos', 150),
                'tipo' => 'producto',
                'activo' => true
            ]
        ];

        return $recompensas[$recompensaId] ?? null;
    }

    /**
     * Obtener recompensas disponibles para cajero
     */
    private function getRecompensasDisponibles()
    {
        return [
            [
                'id' => 'desc_10',
                'nombre' => '10% de Descuento',
                'descripcion' => 'Descuento del 10% en tu próximo pedido',
                'puntos_requeridos' => (int) ($this->configFidelidad['recompensa_descuento_10_puntos'] ?? 100),
                'tipo' => 'descuento'
            ],
            [
                'id' => 'desc_20',
                'nombre' => '20% de Descuento',
                'descripcion' => 'Descuento del 20% en tu próximo pedido',
                'puntos_requeridos' => (int) ($this->configFidelidad['recompensa_descuento_20_puntos'] ?? 200),
                'tipo' => 'descuento'
            ]
        ];
    }
}