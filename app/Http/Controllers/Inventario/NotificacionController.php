<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Notificacion;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Events\NotificacionEnviada; // IMPORTANTE: El evento para Reverb

class NotificacionController extends Controller
{
    use AuthorizesRequests;

    // Lista de notificaciones para el usuario autenticado
    public function index(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'No tienes permiso para ver las notificaciones.');
        }

        $usuario = auth()->user();
        $unread = Notificacion::where('usuario_destino_id', $usuario->id)
            ->where('leido', false)
            ->count();
        $notificaciones = Notificacion::where('usuario_destino_id', $usuario->id)
            ->orderByDesc('id')
            ->paginate(20);

        BitacoraController::registrar('notificaciones_listadas', 'notificacion', null, $usuario->id);

        return view('notificaciones.index', compact('notificaciones', 'unread'));
    }

    // Ver una notificación específica
    public function show($id)
    {
        $notificacion = Notificacion::findOrFail($id);

        if ($notificacion->usuario_destino_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver esta notificación.');
        }

        if (!$notificacion->leido) {
            $notificacion->marcarComoLeida();
            BitacoraController::registrar('notificacion_leida', 'notificacion', $notificacion->id);
        }

        BitacoraController::registrar('notificacion_vista', 'notificacion', $notificacion->id, auth()->id());

        return view('notificaciones.show', compact('notificacion'));
    }

    // Marcar notificación como leída
    public function marcarLeida($id)
    {
        $notificacion = Notificacion::findOrFail($id);

        if ($notificacion->usuario_destino_id !== auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            abort(403, 'No tienes permiso para modificar esta notificación.');
        }

        $notificacion->marcarComoLeida();
        BitacoraController::registrar('notificacion_leida', 'notificacion', $notificacion->id, auth()->id());

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notificación marcada como leída'
            ]);
        }

        return redirect()->back()->with('success', 'Notificación marcada como leída.');
    }

    // Marcar todas las notificaciones del usuario como leídas
    public function marcarTodasLeidas()
    {
        $usuario = auth()->user();
        if (!auth()->check()) {
            abort(403, 'No tienes permiso para modificar las notificaciones.');
        }
        
        Notificacion::where('usuario_destino_id', $usuario->id)
            ->where('leido', false)
            ->update(['leido' => true]);

        BitacoraController::registrar('notificaciones_marcadas_leidas', 'notificacion', null, $usuario->id);

        return redirect()->back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }

    // Obtener notificaciones no leídas (para AJAX/API tradicional si falla WebSocket)
    public function noLeidas()
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $usuario = auth()->user();

        $notificaciones = Notificacion::where('usuario_destino_id', $usuario->id)
            ->where('leido', false)
            ->orderByDesc('id')
            ->get();

        BitacoraController::registrar('notificaciones_consultadas', 'notificacion', null, $usuario->id);

        return response()->json([
            'count' => $notificaciones->count(),
            'notificaciones' => $notificaciones
        ]);
    }

    // ADMIN: Crear notificación (solo administradores)
    public function create()
    {
        $this->authorize('crear-notificacion');

        $usuarios = User::where('activo', true)->orderBy('name')->get();

        BitacoraController::registrar('notificacion_create_view', 'notificacion', null, auth()->id());
        
        return view('admin.notificaciones.create', compact('usuarios'));
    }

    // ADMIN: Guardar notificación
    public function store(Request $request)
    {
        $this->authorize('crear-notificacion');

        $request->validate([
            'tipo' => 'required|in:pedido,stock,reserva,sistema',
            'canal' => 'nullable|in:panel,email,push',
            'mensaje' => 'required|string|max:255',
            'usuario_destino_id' => 'required|exists:users,id',
        ]);

        $notificacion = Notificacion::create([
            'tipo' => $request->tipo,
            'canal' => $request->canal ?? 'panel',
            'mensaje' => $request->mensaje,
            'usuario_destino_id' => $request->usuario_destino_id,
            'rel_model' => $request->rel_model,
            'rel_id' => $request->rel_id,
            'leido' => false,
        ]);

        // 📡 BROADCAST A REVERB
        broadcast(new NotificacionEnviada($notificacion));

        BitacoraController::registrar('notificacion_creada', 'notificacion', $notificacion->id);

        return redirect()->route('admin.notificaciones.index')->with('success', 'Notificación creada correctamente.');
    }

    // ADMIN: Lista de todas las notificaciones (solo administradores)
    public function adminIndex(Request $request)
    {
        $this->authorize('ver-todas-notificaciones');

        $query = Notificacion::with('usuarioDestino');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('leido')) {
            $query->where('leido', $request->leido);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_destino_id', $request->usuario_id);
        }

        $notificaciones = $query->orderByDesc('id')->paginate(20);
        $usuarios = User::where('activo', true)->orderBy('name')->get();

        BitacoraController::registrar('notificaciones_listadas_admin', 'notificacion', null, auth()->id());

        return view('admin.notificaciones.index', compact('notificaciones', 'usuarios'));
    }

    // ==================================================================================
    // MÉTODOS ESTÁTICOS DE SISTEMA (Con Broadcasting)
    // ==================================================================================

    // Notificar producto agotado
    public static function notificarProductoAgotado($productoId, $stockAntes = null, $origen = 'sistema')
    {
        $producto = Producto::find($productoId);
        
        if (!$producto) return;

        BitacoraController::registrar('producto_agotado', 'producto', $productoId, auth()->check() ? auth()->id() : null);

        $usuariosANotificar = User::role(['administrador', 'cajero'])->get();
        $mensaje = "Producto agotado: {$producto->nombre}. Stock anterior: {$stockAntes}. Origen: {$origen}.";

        foreach ($usuariosANotificar as $usuario) {
            $notificacion = Notificacion::create([
                'tipo' => 'stock',
                'canal' => 'panel',
                'mensaje' => $mensaje,
                'usuario_destino_id' => $usuario->id,
                'rel_model' => 'producto',
                'rel_id' => $productoId,
                'leido' => false,
            ]);

            // 📡 BROADCAST
            broadcast(new NotificacionEnviada($notificacion));
        }
    }

    // Notificar a baristas nuevo pedido
    public static function notificarNuevoPedidoABarista($pedido)
    {
        $baristas = User::role('barista')->get();

        if ($baristas->isEmpty()) return;

        $cajero = $pedido->cajero ? $pedido->cajero->name : 'Sistema';
        $mesa = $pedido->mesa ? "Mesa {$pedido->mesa->numero}" : 'Para llevar';
        $itemsCount = $pedido->items->count();
        $mensaje = "🆕 Nuevo pedido #{$pedido->id} de {$cajero} - {$mesa} ({$itemsCount} productos)";

        foreach ($baristas as $barista) {
            $notificacion = Notificacion::create([
                'tipo' => 'pedido',
                'canal' => 'panel',
                'mensaje' => $mensaje,
                'usuario_destino_id' => $barista->id,
                'rel_model' => 'pedido',
                'rel_id' => $pedido->id,
                'leido' => false,
            ]);

            // 📡 BROADCAST
            broadcast(new NotificacionEnviada($notificacion));
        }

        BitacoraController::registrar('pedido_notificado_barista', 'pedido', $pedido->id, auth()->check() ? auth()->id() : null);
    }

    // Notificar pedido listo (al cajero creador o a todos)
    public static function notificarPedidoListo($pedido)
    {
        // 1. Notificar al cajero específico
        if ($pedido->cajero_id) {
            $cajero = User::find($pedido->cajero_id);
            
            if ($cajero && $cajero->activo) {
                $barista = $pedido->barista ? $pedido->barista->name : 'Barista';
                $mesa = $pedido->mesa ? "Mesa {$pedido->mesa->numero}" : 'Para llevar';
                $mensaje = "✅ Pedido #{$pedido->id} listo para entregar - {$mesa} (Preparado por {$barista})";

                $notificacion = Notificacion::create([
                    'tipo' => 'pedido',
                    'canal' => 'panel',
                    'mensaje' => $mensaje,
                    'usuario_destino_id' => $cajero->id,
                    'rel_model' => 'pedido',
                    'rel_id' => $pedido->id,
                    'leido' => false,
                ]);

                // 📡 BROADCAST
                broadcast(new NotificacionEnviada($notificacion));
            }
        }

        // 2. Notificar a otros cajeros/admins como respaldo
        $otrosCajeros = User::role(['cajero', 'administrador'])
            ->where('id', '!=', $pedido->cajero_id)
            ->get();

        if ($otrosCajeros->isNotEmpty()) {
            $mesa = $pedido->mesa ? "Mesa {$pedido->mesa->numero}" : 'Para llevar';
            $mensaje = "🔔 Pedido #{$pedido->id} listo para entregar - {$mesa}";

            foreach ($otrosCajeros as $cajero) {
                $notificacion = Notificacion::create([
                    'tipo' => 'pedido',
                    'canal' => 'panel',
                    'mensaje' => $mensaje,
                    'usuario_destino_id' => $cajero->id,
                    'rel_model' => 'pedido',
                    'rel_id' => $pedido->id,
                    'leido' => false,
                ]);

                // 📡 BROADCAST
                broadcast(new NotificacionEnviada($notificacion));
            }
        }

        BitacoraController::registrar('pedido_listo_notificado', 'pedido', $pedido->id, auth()->check() ? auth()->id() : null);
    }

    // Notificar cambios de estado generales
    public static function notificarCambioEstadoPedido($pedido, $estadoAnterior, $estadoNuevo)
    {
        $destinatarios = [];
        $mensaje = "";

        switch ($estadoNuevo) {
            case 'en_preparacion':
                $destinatarios = User::role('barista')->where('activo', true)->get();
                $mensaje = "👨‍🍳 Pedido #{$pedido->id} asignado para preparación";
                break;

            case 'listo':
                return; // Ya manejado por notificarPedidoListo

            case 'completado':
                if ($pedido->barista_id) {
                    $destinatarios = collect([User::find($pedido->barista_id)])->filter();
                    $mensaje = "✅ Pedido #{$pedido->id} entregado al cliente";
                }
                break;

            case 'cancelado':
                $usuarios = collect();
                if ($pedido->cajero_id) $usuarios->push(User::find($pedido->cajero_id));
                if ($pedido->barista_id) $usuarios->push(User::find($pedido->barista_id));
                $destinatarios = $usuarios->filter();
                $mensaje = "❌ Pedido #{$pedido->id} cancelado";
                break;

            default:
                return;
        }

        foreach ($destinatarios as $usuario) {
            if ($usuario && $usuario->activo) {
                $notificacion = Notificacion::create([
                    'tipo' => 'pedido',
                    'canal' => 'panel',
                    'mensaje' => $mensaje,
                    'usuario_destino_id' => $usuario->id,
                    'rel_model' => 'pedido',
                    'rel_id' => $pedido->id,
                    'leido' => false,
                ]);

                // 📡 BROADCAST
                broadcast(new NotificacionEnviada($notificacion));
            }
        }

        BitacoraController::registrar('pedido_cambio_estado', 'pedido', $pedido->id, auth()->check() ? auth()->id() : null, ['estado_anterior' => $estadoAnterior, 'estado_nuevo' => $estadoNuevo]);
    }
}