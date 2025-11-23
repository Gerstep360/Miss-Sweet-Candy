<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TurneroController extends Controller
{
    /**
     * ========================================
     * VALIDACIÓN Y VERIFICACIÓN
     * ========================================
     */

    /**
     * Mostrar lista de tickets del usuario autenticado
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            // ✅ VALIDACIÓN: Usuario autenticado
            if (! auth()->check()) {
                Log::info('Turnero index: Usuario no autenticado intentó acceder');

                return redirect()->route('login')
                    ->with('info', 'Inicia sesión para ver tus pedidos');
            }

            $userId = auth()->id();

            // ✅ VALIDACIÓN: Verificar que el usuario existe
            if (! $userId) {
                Log::warning('Turnero index: ID de usuario inválido');

                return redirect()->route('login')
                    ->with('error', 'Sesión inválida. Por favor, inicia sesión nuevamente.');
            }

            // ✅ VERIFICACIÓN: Obtener pedidos activos del usuario
            $pedidos = Pedido::where('cliente_id', $userId)
                ->whereNotIn('estado', ['entregado', 'cancelado', 'anulado'])
                ->whereNotNull('token')
                ->orderByDesc('created_at')
                ->get();

            // ✅ LOG: Registro de acceso
            Log::info("Turnero index: Usuario {$userId} consultó sus pedidos", [
                'user_id' => $userId,
                'pedidos_count' => $pedidos->count(),
            ]);

            return view('turnero.cliente-index', compact('pedidos'));

        } catch (\Exception $e) {
            Log::error('Error en turnero index: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Error al cargar tus pedidos. Intenta nuevamente.');
        }
    }

    /**
     * Mostrar monitor público de turnos
     *
     * @return \Illuminate\View\View
     */
    public function monitor()
    {
        try {
            // ✅ LOG: Registro de acceso al monitor
            Log::info('Monitor de turnos accedido');

            return view('turnero.monitor');

        } catch (\Exception $e) {
            Log::error('Error en monitor: '.$e->getMessage());
            abort(500, 'Error al cargar el monitor');
        }
    }

    /**
     * API Feed para el monitor (con caché)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function feed()
    {
        try {
            // ✅ VALIDACIÓN: Solo pedidos con token válido
            $pendiente = Pedido::whereIn('estado', ['pendiente', 'confirmado'])
                ->whereNotNull('token')
                ->orderBy('created_at')
                ->get(['id', 'token', 'eta_minutes', 'estado', 'tipo', 'created_at']);

            $preparando = Pedido::where('estado', 'en_preparacion')
                ->whereNotNull('token')
                ->orderBy('started_at')
                ->get(['id', 'token', 'eta_minutes', 'estado', 'tipo', 'created_at', 'started_at']);

            $preparado = Pedido::where('estado', 'preparado')
                ->whereNotNull('token')
                ->orderBy('ready_at')
                ->get(['id', 'token', 'eta_minutes', 'estado', 'tipo', 'created_at', 'ready_at']);

            $data = compact('pendiente', 'preparando', 'preparado');

            // ✅ VERIFICACIÓN: Contar totales
            $total = $pendiente->count() + $preparando->count() + $preparado->count();

            // ✅ LOG: Solo registrar si hay cambios significativos
            if ($total > 0) {
                Log::debug("Feed actualizado: {$total} pedidos activos", [
                    'pendiente' => $pendiente->count(),
                    'preparando' => $preparando->count(),
                    'preparado' => $preparado->count(),
                ]);
            }

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('Error en feed API: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // ✅ RESPUESTA DE ERROR: JSON válido
            return response()->json([
                'error' => 'Error al obtener el feed',
                'message' => $e->getMessage(),
                'pendiente' => [],
                'preparando' => [],
                'preparado' => [],
            ], 500);
        }
    }

    /**
     * Mostrar seguimiento individual de un pedido por token
     *
     * @param  string  $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function cliente($token)
    {
        try {
            // ✅ VALIDACIÓN: Token no vacío
            if (empty($token)) {
                Log::warning('Turnero cliente: Token vacío recibido');
                abort(404, 'Token inválido');
            }

            // ✅ VALIDACIÓN: Formato de token (ej: A001, M023, W100)
            if (! preg_match('/^[A-Z][0-9]{3}$/', $token)) {
                Log::warning("Turnero cliente: Token con formato inválido: {$token}");
                abort(404, 'Formato de token inválido');
            }

            // ✅ VERIFICACIÓN: Buscar pedido por token
            $pedido = Pedido::where('token', $token)->first();

            if (! $pedido) {
                Log::warning("Turnero cliente: Token no encontrado: {$token}");
                abort(404, 'Pedido no encontrado. Verifica tu número de turno.');
            }

            // ✅ VALIDACIÓN: Pedido no debe estar cancelado/anulado
            if (in_array($pedido->estado, ['cancelado', 'anulado'])) {
                Log::info("Turnero cliente: Intento de acceso a pedido {$pedido->estado}: {$token}");

                return redirect()->route('turnos.turnero.index')
                    ->with('error', 'Este pedido ha sido '.$pedido->estado.'.');
            }

            // ✅ LOG: Registro de consulta
            Log::info("Turnero cliente: Consultado token {$token}", [
                'pedido_id' => $pedido->id,
                'estado' => $pedido->estado,
                'cliente_id' => $pedido->cliente_id,
            ]);

            return view('turnero.cliente', compact('pedido'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Turnero cliente: Token no encontrado (ModelNotFoundException): {$token}");
            abort(404, 'Pedido no encontrado');

        } catch (\Exception $e) {
            Log::error('Error en turnero cliente: '.$e->getMessage(), [
                'token' => $token,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Error al cargar el seguimiento. Intenta nuevamente.');
        }
    }
}
