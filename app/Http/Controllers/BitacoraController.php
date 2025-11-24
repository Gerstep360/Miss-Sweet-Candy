<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    // Muestra la lista de auditorías (bitácora)
    public function index(Request $request)
    {
        $auditorias = Auditoria::with('usuario')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.bitacora.index', compact('auditorias'));
    }

    // Muestra el detalle de una acción de auditoría
    public function show($id)
    {
        $auditoria = Auditoria::with('usuario')->findOrFail($id);

        return view('admin.bitacora.show', compact('auditoria'));
    }

    // Método estático para registrar acciones en la bitácora
    // Ahora usa eventos para registro automático
    public static function registrar($accion, $entidad, $entidad_id = null, $usuario_id = null, $request = null)
    {
        // Disparar evento para registro automático
        event(new \App\Events\AccionAuditable(
            $accion,
            $entidad,
            $entidad_id,
            $usuario_id,
            $request ? $request->ip() : null,
            $request ? $request->userAgent() : null,
            $request
        ));
    }
}