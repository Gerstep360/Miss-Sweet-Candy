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
        $usuario = auth()->user();
        
        $notificaciones = Notificacion::where('usuario_destino_id', $usuario->id)
            ->orderByDesc('id')
            ->paginate(20);

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

        return view('notificaciones.show', compact('notificacion'));
    }

    // Marcar notificación como leída
    public function marcarLeida($id)
    {
        $notificacion = Notificacion::findOrFail($id);

        // Verificar que el usuario autenticado sea el destinatario
        if ($notificacion->usuario_destino_id !== auth()->id()) {
            abort(403, 'No tienes permiso para modificar esta notificación.');
        }

        $notificacion->marcarComoLeida();
        BitacoraController::registrar('notificacion_leida', 'notificacion', $notificacion->id);

        return redirect()->back()->with('success', 'Notificación marcada como leída.');
    }

    // Marcar todas las notificaciones del usuario como leídas
    public function marcarTodasLeidas()
    {
        $usuario = auth()->user();
        
        Notificacion::where('usuario_destino_id', $usuario->id)
            ->where('leido', false)
            ->update(['leido' => true]);

        BitacoraController::registrar('notificaciones_marcadas_leidas', 'notificacion', null);

        return redirect()->back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }

    // Obtener notificaciones no leídas (para AJAX/API)
    public function noLeidas()
    {
        $usuario = auth()->user();
        
        $notificaciones = Notificacion::where('usuario_destino_id', $usuario->id)
            ->where('leido', false)
            ->orderByDesc('id')
            ->get();

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
            ->where('activo', true)
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
}
