<?php

namespace App\Http\Controllers;

use App\Models\FidelidadMovimiento;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\BitacoraController;

class FidelidadController extends BaseController
{
    use AuthorizesRequests;

    // Configuración hardcodeada (podría moverse a la base de datos después)
    private $configFidelidad = [
        'activo' => true,
        'puntos_por_dolar' => 10, // 10 puntos por cada $1 gastado
        'puntos_por_antiguedad_meses' => 12, // Cada 12 meses
        'puntos_antiguedad_base' => 100, // 100 puntos por cada año
        'multiplicador_fin_semana' => 1.5, // 50% más los fines de semana
    ];

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

        // 🔒 SEGURIDAD: Si es cliente, solo ver sus propios puntos
        if (auth()->user()->hasRole('cliente')) {
            $query->where('cliente_id', auth()->id());
        }

        // Filtrar por cliente si se especifica (para administradores)
        if ($request->get('cliente_id') && auth()->user()->can('gestionar-fidelidad')) {
            $query->where('cliente_id', $request->get('cliente_id'));
        }

        $puntosClientes = $query->paginate(15);
        $clientes = User::role('cliente')->get();

        BitacoraController::registrar('ver lista', 'Fidelidad', null);
        return view('fidelidad.index', compact('puntosClientes', 'clientes'));
    }

    /**
     * Show the form for managing fidelidad configuration (versión simple)
     */
    public function config()
    {
        // 🔒 Solo administradores pueden gestionar la configuración
        try {
            $this->authorize('gestionar-fidelidad');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        $config = $this->configFidelidad;

        BitacoraController::registrar('ver configuracion', 'FidelidadConfig', null);
        return view('fidelidad.config', compact('config'));
    }

    /**
     * Update fidelidad configuration (versión simple)
     */
    public function updateConfig(Request $request)
    {
        // 🔒 Solo administradores pueden gestionar la configuración
        try {
            $this->authorize('gestionar-fidelidad');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        $validated = $request->validate([
            'puntos_por_dolar' => 'required|numeric|min:0',
            'puntos_por_antiguedad_meses' => 'nullable|integer|min:0',
            'puntos_antiguedad_base' => 'nullable|integer|min:0',
            'multiplicador_fin_semana' => 'nullable|numeric|min:1',
        ]);

        // En una versión futura, podrías guardar esto en la base de datos
        // Por ahora, actualizamos la configuración en memoria (sesión)
        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $this->configFidelidad)) {
                $this->configFidelidad[$key] = $value;
            }
        }

        BitacoraController::registrar('actualizar configuracion', 'FidelidadConfig', null);
        return back()->with('success', 'Configuración de fidelidad actualizada exitosamente.');
    }

    /**
     * Show available rewards for redemption (versión simple)
     */
    public function recompensas()
    {
        // Recompensas hardcodeadas
        $recompensas = [
            [
                'id' => 1,
                'nombre' => 'Descuento 10%',
                'descripcion' => '10% de descuento en tu próxima compra',
                'puntos_requeridos' => 100,
                'tipo' => 'descuento',
                'valor_descuento' => 10,
                'activo' => true
            ],
            [
                'id' => 2,
                'nombre' => 'Descuento 20%',
                'descripcion' => '20% de descuento en tu próxima compra',
                'puntos_requeridos' => 200,
                'tipo' => 'descuento',
                'valor_descuento' => 20,
                'activo' => true
            ],
            [
                'id' => 3,
                'nombre' => 'Producto Gratis',
                'descripcion' => 'Producto de cortesía del menú',
                'puntos_requeridos' => 150,
                'tipo' => 'producto',
                'activo' => true
            ]
        ];
        
        $puntosCliente = 0;
        $puntosTotales = 0;
        $movimientos = collect();

        if (auth()->user()->hasRole('cliente')) {
            $puntosCliente = $this->calcularPuntosCliente(auth()->id());
            $puntosTotales = $puntosCliente;
            
            // 🔥 CORRECCIÓN: Cambiar latest() por orderBy('id', 'desc')
            $movimientos = FidelidadMovimiento::where('cliente_id', auth()->id())
                ->orderBy('id', 'desc')  // ← CORREGIDO
                ->get();
        }

        BitacoraController::registrar('ver recompensas', 'Recompensa', null);
        return view('fidelidad.recompensas', compact('recompensas', 'puntosCliente', 'movimientos', 'puntosTotales'));
    }

    /**
     * Redeem a reward (versión simple)
     */
    public function canjearRecompensa(Request $request, $recompensaId)
    {
        // Recompensas hardcodeadas
        $recompensas = [
            1 => ['nombre' => 'Descuento 10%', 'puntos_requeridos' => 100],
            2 => ['nombre' => 'Descuento 20%', 'puntos_requeridos' => 200],
            3 => ['nombre' => 'Producto Gratis', 'puntos_requeridos' => 150]
        ];

        if (!isset($recompensas[$recompensaId])) {
            return back()->withErrors(['error' => 'Recompensa no válida.']);
        }

        $recompensa = $recompensas[$recompensaId];
        $validated = $request->validate([
            'cliente_id' => auth()->user()->hasRole('cliente') ? 'nullable' : 'required|exists:users,id',
        ]);

        $clienteId = $validated['cliente_id'] ?? auth()->id();

        // 🔒 SEGURIDAD: Cliente solo puede canjear para sí mismo
        if (auth()->user()->hasRole('cliente') && $clienteId !== auth()->id()) {
            abort(403, 'No tienes permiso para canjear recompensas para otros clientes.');
        }

        DB::beginTransaction();

        try {
            // Calcular puntos del cliente
            $puntosCliente = $this->calcularPuntosCliente($clienteId);

            // Verificar que tenga puntos suficientes
            if ($puntosCliente < $recompensa['puntos_requeridos']) {
                throw new \Exception('No tienes puntos suficientes para canjear esta recompensa.');
            }

            // Crear movimiento de canje
            FidelidadMovimiento::create([
                'cliente_id' => $clienteId,
                'tipo' => 'canje',
                'puntos' => $recompensa['puntos_requeridos'],
                'motivo' => "Canje de recompensa: {$recompensa['nombre']}"
            ]);

            DB::commit();

            BitacoraController::registrar('canjear recompensa', 'Recompensa', $recompensaId);
            return back()->with('success', "Recompensa '{$recompensa['nombre']}' canjeada exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show client points history
     */
    public function historial(Request $request, User $cliente = null)
    {
        // 🔒 SEGURIDAD: Cliente solo puede ver su propio historial
        if (auth()->user()->hasRole('cliente')) {
            $cliente = auth()->user();
        } elseif (!$cliente) {
            // Si es admin y no se especifica cliente, usar el usuario actual
            if (auth()->user()->can('gestionar-fidelidad')) {
                $cliente = auth()->user();
            } else {
                abort(403, 'No tienes permiso para ver este historial.');
            }
        }

        // Verificar que el admin tenga permiso para ver este cliente
        if ($cliente->id != auth()->id() && !auth()->user()->can('gestionar-fidelidad')) {
            abort(403, 'No tienes permiso para ver el historial de otros clientes.');
        }

        $movimientos = FidelidadMovimiento::with(['pedido'])
            ->where('cliente_id', $cliente->id)
            ->orderBy('id', 'desc') // 🔥 CORREGIDO
            ->paginate(15);

        $puntosTotales = $this->calcularPuntosCliente($cliente->id);

        // Cálculos optimizados para el resumen
        $puntosAcumulados = $movimientos->where('tipo', 'acumulo')->sum('puntos');
        $puntosCanjeados = $movimientos->where('tipo', 'canje')->sum('puntos');

        BitacoraController::registrar('ver historial', 'FidelidadMovimiento', null);
        return view('fidelidad.historial', compact('movimientos', 'cliente', 'puntosTotales', 'puntosAcumulados', 'puntosCanjeados'));
    }

    /**
     * Calculate client points
     */
    private function calcularPuntosCliente($clienteId): int
    {
        return FidelidadMovimiento::where('cliente_id', $clienteId)
            ->selectRaw('SUM(CASE WHEN tipo = "acumulo" THEN puntos ELSE -puntos END) as puntos_totales')
            ->value('puntos_totales') ?? 0;
    }

    /**
     * API: Acumular puntos automáticamente cuando un pedido se completa
     * Este método se puede llamar desde el PedidoController cuando un pedido cambia a estado "entregado" o "pagado"
     */
    public function acumularPuntosPorPedido(Pedido $pedido)
    {
        // Verificar pre-condiciones del caso de uso
        if (!$pedido->cliente_id) {
            throw new \Exception('Cliente no identificado para acumular puntos.');
        }

        if (!$this->configFidelidad['activo']) {
            throw new \Exception('El programa de fidelidad no está activo.');
        }

        // Verificar que el pedido esté en estado completado
        if (!in_array($pedido->estado, ['entregado', 'servido', 'retirado', 'pagado'])) {
            throw new \Exception('El pedido no está completado para acumular puntos.');
        }

        // Verificar que no se hayan acumulado puntos ya para este pedido
        $existeMovimiento = FidelidadMovimiento::where('pedido_id', $pedido->id)
            ->where('tipo', 'acumulo')
            ->exists();

        if ($existeMovimiento) {
            throw new \Exception('Ya se acumularon puntos para este pedido.');
        }

        DB::beginTransaction();

        try {
            $puntos = $this->calcularPuntosPedido($pedido);

            // Crear movimiento de acumulación
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
     * Calculate points for a completed order based on configuration
     */
    private function calcularPuntosPedido(Pedido $pedido): int
    {
        $totalPedido = $pedido->total;
        
        // Puntos base por monto gastado
        $puntos = intval($totalPedido * $this->configFidelidad['puntos_por_dolar']);

        // Puntos por antigüedad del cliente
        if ($this->configFidelidad['puntos_por_antiguedad_meses'] > 0 && 
            $this->configFidelidad['puntos_antiguedad_base'] > 0) {
            
            $antiguedadMeses = $pedido->cliente->created_at->diffInMonths(now());
            $puntosAntiguedad = intval($antiguedadMeses / $this->configFidelidad['puntos_por_antiguedad_meses']) * $this->configFidelidad['puntos_antiguedad_base'];
            $puntos += $puntosAntiguedad;
        }

        // Multiplicador por fin de semana
        if ($this->configFidelidad['multiplicador_fin_semana'] > 1 && now()->isWeekend()) {
            $puntos = intval($puntos * $this->configFidelidad['multiplicador_fin_semana']);
        }

        return max(0, $puntos);
    }

    /**
     * Vista para que los cajeros gestionen puntos de clientes
     */
    public function puntosCajero()
    {
                // 🔒 Solo administradores pueden gestionar la configuración
        try {
            $this->authorize('gestionar-fidelidad');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, 'No tienes permiso para gestionar la configuración de fidelidad.');
        }
        $recompensas = $this->getRecompensasDisponibles();
        
        BitacoraController::registrar('acceder', 'PuntosCajero', null);
        return view('fidelidad.puntosCajero', compact('recompensas'));
    }

    /**
     * Buscar clientes para el cajero (para AJAX)
     */
    public function buscarCliente(Request $request)
    {
        // 🔒 Solo cajeros y admin pueden buscar clientes
        try {
            $this->authorize('gestionar-fidelidad');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, 'No tienes permiso para gestionar la configuración de fidelidad.');
        }

        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $clientes = User::role('cliente')
            ->with(['clientePerfil', 'fidelidadMovimientos', 'pedidos'])
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhereHas('clientePerfil', function($q2) use ($query) {
                      $q2->where('telefono', 'LIKE', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function($cliente) {
                // Calcular puntos totales del cliente
                $puntosAcumulados = $cliente->fidelidadMovimientos
                    ->where('tipo', 'acumulo')
                    ->sum('puntos');
                    
                $puntosCanjeados = $cliente->fidelidadMovimientos
                    ->where('tipo', 'canje')
                    ->sum('puntos');
                    
                $puntosTotales = $puntosAcumulados - $puntosCanjeados;
                
                return [
                    'id' => $cliente->id,
                    'name' => $cliente->name,
                    'email' => $cliente->email,
                    'telefono' => $cliente->clientePerfil->telefono ?? 'No registrado',
                    'direccion' => $cliente->clientePerfil->direccion ?? 'No registrada',
                    'puntos_totales' => $puntosTotales,
                    'puntos_acumulados' => $puntosAcumulados,
                    'puntos_canjeados' => $puntosCanjeados,
                    'total_pedidos' => $cliente->pedidos->count() ?? 0,
                    'cliente_desde' => $cliente->created_at->format('M Y')
                ];
            });

        return response()->json($clientes);
    }

    /**
     * Obtener historial de un cliente específico
     */
    public function getHistorialCliente($clienteId)
    {
        // 🔒 Solo administradores pueden gestionar la configuración
        try {
            $this->authorize('gestionar-fidelidad');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, 'No tienes permiso para gestionar la configuración de fidelidad.');
        }

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
                    'pedido_id' => $movimiento->pedido_id,
                    'recompensa_canjeada' => $movimiento->tipo === 'canje' ? 'Canje de puntos' : null
                ];
            });

        return response()->json($movimientos);
    }

    /**
     * Canjear puntos desde el panel de cajero
     */
    public function canjearPuntosCajero(Request $request)
    {
        // 🔒 Solo cajeros y admin pueden canjear puntos
        try {
            $this->authorize('gestionar-fidelidad');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para realizar esta acción'
            ], 403);
        }

        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'puntos' => 'required|integer|min:1',
            'descripcion' => 'required|string|max:255',
            'recompensa_id' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $cliente = User::findOrFail($request->cliente_id);
            
            // Verificar que el cliente tenga suficientes puntos
            $puntosTotales = $this->calcularPuntosCliente($cliente->id);
            
            if ($puntosTotales < $request->puntos) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene suficientes puntos. Puntos disponibles: ' . $puntosTotales
                ], 422);
            }

            // Crear movimiento de canje
            $movimiento = FidelidadMovimiento::create([
                'cliente_id' => $cliente->id,
                'puntos' => $request->puntos,
                'tipo' => 'canje',
                'motivo' => $request->descripcion,
                'origen_id' => auth()->id(),
                'origen_type' => User::class,
            ]);

            // Aplicar recompensa si se especifica
            if ($request->recompensa_id) {
                $this->aplicarRecompensa($request->recompensa_id, $cliente, $request->puntos);
            }

            DB::commit();

            // Obtener datos actualizados del cliente
            $clienteData = $this->getDatosClienteParaCajero($cliente->id);
            $historial = $this->getHistorialCliente($cliente->id)->getData();

            BitacoraController::registrar('canjear puntos cajero', 'FidelidadMovimiento', $movimiento->id, "Cliente: {$cliente->name}");

            return response()->json([
                'success' => true,
                'message' => 'Puntos canjeados exitosamente',
                'cliente' => $clienteData,
                'historial' => $historial
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al canjear puntos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos completos de un cliente para el cajero
     */
    private function getDatosClienteParaCajero($clienteId)
    {
        $cliente = User::with(['clientePerfil', 'pedidos'])->find($clienteId);
        
        if (!$cliente) {
            return null;
        }

        $puntosAcumulados = FidelidadMovimiento::where('cliente_id', $clienteId)
            ->where('tipo', 'acumulo')
            ->sum('puntos');
            
        $puntosCanjeados = FidelidadMovimiento::where('cliente_id', $clienteId)
            ->where('tipo', 'canje')
            ->sum('puntos');
            
        $puntosTotales = $puntosAcumulados - $puntosCanjeados;

        return [
            'id' => $cliente->id,
            'name' => $cliente->name,
            'email' => $cliente->email,
            'telefono' => $cliente->clientePerfil->telefono ?? 'No registrado',
            'direccion' => $cliente->clientePerfil->direccion ?? 'No registrada',
            'cliente_desde' => $cliente->created_at->format('M Y'),
            'total_pedidos' => $cliente->pedidos->count(),
            'puntos_totales' => $puntosTotales,
            'puntos_acumulados' => $puntosAcumulados,
            'puntos_canjeados' => $puntosCanjeados
        ];
    }

    /**
     * Obtener recompensas disponibles
     */
    private function getRecompensasDisponibles()
    {
        return [
            [
                'id' => 'desc_10',
                'nombre' => '10% de Descuento',
                'descripcion' => 'Descuento del 10% en tu próximo pedido',
                'puntos_requeridos' => 100,
                'tipo' => 'descuento',
                'valor_descuento' => 10,
                'activo' => true
            ],
            [
                'id' => 'desc_20',
                'nombre' => '20% de Descuento',
                'descripcion' => 'Descuento del 20% en tu próximo pedido',
                'puntos_requeridos' => 200,
                'tipo' => 'descuento',
                'valor_descuento' => 20,
                'activo' => true
            ],
            [
                'id' => 'cafe_gratis',
                'nombre' => 'Café Gratis',
                'descripcion' => 'Café especial de la casa gratis',
                'puntos_requeridos' => 50,
                'tipo' => 'producto',
                'activo' => true
            ],
            [
                'id' => 'postre_gratis',
                'nombre' => 'Postre Gratis',
                'descripcion' => 'Postre del día gratis',
                'puntos_requeridos' => 80,
                'tipo' => 'producto',
                'activo' => true
            ]
        ];
    }

    /**
     * Aplicar recompensa (lógica básica)
     */
    private function aplicarRecompensa($recompensaId, $cliente, $puntos)
    {
        // Aquí puedes implementar la lógica específica para cada recompensa
        // Por ejemplo: generar cupones, aplicar descuentos automáticos, etc.
        
        \Log::info("Recompensa aplicada por cajero: {$recompensaId} para cliente {$cliente->name} por {$puntos} puntos");
        
        // Por ahora solo registramos en el log
        // En una implementación completa, aquí generarías cupones o aplicarías descuentos
    }

    /**
     * Manual points adjustment (for administrators)
     */
    public function ajustarPuntos(Request $request)
    {
        // 🔒 Solo administradores pueden ajustar puntos manualmente
        try {
            $this->authorize('gestionar-fidelidad');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

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
        // 🔒 SEGURIDAD: Cliente solo puede consultar sus propios puntos
        if (auth()->user()->hasRole('cliente')) {
            $cliente = auth()->user();
        } elseif (!$cliente && auth()->user()->can('gestionar-fidelidad')) {
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
        return $this->configFidelidad['activo'];
    }
}