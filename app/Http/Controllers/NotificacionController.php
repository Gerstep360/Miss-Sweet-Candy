<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NotificacionController extends Controller
{
    use AuthorizesRequests;

    // Lista de notificaciones para el usuario autenticado
    public function index(Request $request)
    {
        // Asegurar usuario autenticado
        if (!auth()->check()) {
            abort(403, 'No tienes permiso para ver las notificaciones.');
        }

        $usuario = auth()->user();

        $notificaciones = Notificacion::where('usuario_destino_id', $usuario->id)
            ->orderByDesc('id')
            ->paginate(20);

        // Registrar en bitácora que el usuario consultó su lista de notificaciones
        BitacoraController::registrar('notificaciones_listadas', 'notificacion', null, $usuario->id);

        return view('notificaciones.index', compact('notificaciones'));
    }

    // Ver una notificación específica
    public function show($id)
    {
        $notificacion = Notificacion::findOrFail($id);

        // Verificar que el usuario autenticado sea el destinatario
        if ($notificacion->usuario_destino_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver esta notificación.');
        }

        // Marcar como leída si no lo está
        if (!$notificacion->leido) {
            $notificacion->marcarComoLeida();
            BitacoraController::registrar('notificacion_leida', 'notificacion', $notificacion->id);
        }

        // Registrar en bitácora que el usuario vio la notificación
        BitacoraController::registrar('notificacion_vista', 'notificacion', $notificacion->id, auth()->id());

        return view('notificaciones.show', compact('notificacion'));
    }

    // Marcar notificación como leída
    public function marcarLeida($id)
    {
        $notificacion = Notificacion::findOrFail($id);

        // Verificar que el usuario autenticado sea el destinatario
        if ($notificacion->usuario_destino_id !== auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            abort(403, 'No tienes permiso para modificar esta notificación.');
        }

        $notificacion->marcarComoLeida();
        BitacoraController::registrar('notificacion_leida', 'notificacion', $notificacion->id, auth()->id());

        // Si es una petición AJAX, retornar JSON
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

    // Obtener notificaciones no leídas (para AJAX/API)
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

        // Registrar en bitácora (consulta AJAX de notificaciones no leídas)
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

        // Registrar en bitácora que se abrió la vista para crear notificación
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

        BitacoraController::registrar('notificacion_creada', 'notificacion', $notificacion->id);

        return redirect()->route('admin.notificaciones.index')->with('success', 'Notificación creada correctamente.');
    }

    // ADMIN: Lista de todas las notificaciones (solo administradores)
    public function adminIndex(Request $request)
    {
        $this->authorize('ver-todas-notificaciones');

        $query = Notificacion::with('usuarioDestino');

        // Filtros
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

        // Registrar en bitácora que el admin consultó las notificaciones
        BitacoraController::registrar('notificaciones_listadas_admin', 'notificacion', null, auth()->id());

        return view('admin.notificaciones.index', compact('notificaciones', 'usuarios'));
    }

    // Método estático para crear notificación de producto agotado
    public static function notificarProductoAgotado($productoId, $stockAntes = null, $origen = 'sistema')
    {
        $producto = Producto::find($productoId);
        
        if (!$producto) {
            return;
        }

        // Registrar en bitácora/auditoría
        BitacoraController::registrar(
            'producto_agotado',
            'producto',
            $productoId,
            auth()->check() ? auth()->id() : null
        );

        // Obtener administradores y cajeros para notificar
        $usuariosANotificar = User::role(['administrador', 'cajero'])
          
            ->get();

        $mensaje = "Producto agotado: {$producto->nombre}. Stock anterior: {$stockAntes}. Origen: {$origen}.";

        foreach ($usuariosANotificar as $usuario) {
            Notificacion::create([
                'tipo' => 'stock',
                'canal' => 'panel',
                'mensaje' => $mensaje,
                'usuario_destino_id' => $usuario->id,
                'rel_model' => 'producto',
                'rel_id' => $productoId,
                'leido' => false,
            ]);
        }
    }

    /**
     * Notificar a baristas cuando hay un nuevo pedido
     * Flujo: Cajero crea pedido → Barista recibe notificación
     */
    public static function notificarNuevoPedidoABarista($pedido)
    {
        // Obtener todos los baristas activos
        $baristas = User::role('barista')
            ->get();

        if ($baristas->isEmpty()) {
            return;
        }

        $cajero = $pedido->cajero ? $pedido->cajero->name : 'Sistema';
        $mesa = $pedido->mesa ? "Mesa {$pedido->mesa->numero}" : 'Para llevar';
        $itemsCount = $pedido->items->count();

        $mensaje = "🆕 Nuevo pedido #{$pedido->id} de {$cajero} - {$mesa} ({$itemsCount} productos)";

        foreach ($baristas as $barista) {
            Notificacion::create([
                'tipo' => 'pedido',
                'canal' => 'panel',
                'mensaje' => $mensaje,
                'usuario_destino_id' => $barista->id,
                'rel_model' => 'pedido',
                'rel_id' => $pedido->id,
                'leido' => false,
            ]);
        }

        // Registrar en bitácora
        BitacoraController::registrar(
            'pedido_notificado_barista',
            'pedido',
            $pedido->id,
            auth()->check() ? auth()->id() : null
        );
    }

    /**
     * Notificar al cajero cuando el barista completa el pedido
     * Flujo: Barista completa pedido → Cajero recibe notificación para entrega
     */
    public static function notificarPedidoListo($pedido)
    {
        // Notificar al cajero que creó el pedido
        if ($pedido->cajero_id) {
            $cajero = User::find($pedido->cajero_id);
            
            if ($cajero && $cajero->activo) {
                $barista = $pedido->barista ? $pedido->barista->name : 'Barista';
                $mesa = $pedido->mesa ? "Mesa {$pedido->mesa->numero}" : 'Para llevar';
                
                $mensaje = "✅ Pedido #{$pedido->id} listo para entregar - {$mesa} (Preparado por {$barista})";

                Notificacion::create([
                    'tipo' => 'pedido',
                    'canal' => 'panel',
                    'mensaje' => $mensaje,
                    'usuario_destino_id' => $cajero->id,
                    'rel_model' => 'pedido',
                    'rel_id' => $pedido->id,
                    'leido' => false,
                ]);
            }
        }

        // También notificar a todos los cajeros activos (por si el cajero original no está disponible)
        $otrosCajeros = User::role(['cajero', 'administrador'])
            ->where('id', '!=', $pedido->cajero_id)
            ->get();

        if ($otrosCajeros->isNotEmpty()) {
            $mesa = $pedido->mesa ? "Mesa {$pedido->mesa->numero}" : 'Para llevar';
            $mensaje = "🔔 Pedido #{$pedido->id} listo para entregar - {$mesa}";

            foreach ($otrosCajeros as $cajero) {
                Notificacion::create([
                    'tipo' => 'pedido',
                    'canal' => 'panel',
                    'mensaje' => $mensaje,
                    'usuario_destino_id' => $cajero->id,
                    'rel_model' => 'pedido',
                    'rel_id' => $pedido->id,
                    'leido' => false,
                ]);
            }
        }

        // Registrar en bitácora
        BitacoraController::registrar(
            'pedido_listo_notificado',
            'pedido',
            $pedido->id,
            auth()->check() ? auth()->id() : null
        );
    }

    /**
     * Notificar cuando se actualiza el estado de un pedido
     */
    public static function notificarCambioEstadoPedido($pedido, $estadoAnterior, $estadoNuevo)
    {
        $destinatarios = [];

        // Determinar quién debe ser notificado según el cambio de estado
        switch ($estadoNuevo) {
            case 'en_preparacion':
                // Notificar a baristas
                $destinatarios = User::role('barista')->where('activo', true)->get();
                $mensaje = "👨‍🍳 Pedido #{$pedido->id} asignado para preparación";
                break;

            case 'listo':
                // Notificar a cajeros (ya lo hace notificarPedidoListo)
                return; // No duplicar notificaciones

            case 'completado':
                // Notificar al barista que lo preparó (confirmación)
                if ($pedido->barista_id) {
                    $destinatarios = collect([User::find($pedido->barista_id)])->filter();
                    $mensaje = "✅ Pedido #{$pedido->id} entregado al cliente";
                }
                break;

            case 'cancelado':
                // Notificar a todos los involucrados
                $usuarios = collect();
                if ($pedido->cajero_id) $usuarios->push(User::find($pedido->cajero_id));
                if ($pedido->barista_id) $usuarios->push(User::find($pedido->barista_id));
                $destinatarios = $usuarios->filter();
                $mensaje = "❌ Pedido #{$pedido->id} cancelado";
                break;

            default:
                return;
        }

        // Crear notificaciones
        foreach ($destinatarios as $usuario) {
            if ($usuario && $usuario->activo) {
                Notificacion::create([
                    'tipo' => 'pedido',
                    'canal' => 'panel',
                    'mensaje' => $mensaje,
                    'usuario_destino_id' => $usuario->id,
                    'rel_model' => 'pedido',
                    'rel_id' => $pedido->id,
                    'leido' => false,
                ]);
            }
        }

        // Registrar en bitácora
        BitacoraController::registrar(
            'pedido_cambio_estado',
            'pedido',
            $pedido->id,
            auth()->check() ? auth()->id() : null,
            ['estado_anterior' => $estadoAnterior, 'estado_nuevo' => $estadoNuevo]
        );
    }
}
