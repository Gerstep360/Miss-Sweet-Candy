<?php

namespace App\Http\Controllers;

use App\Mail\PromocionCreada;
use App\Models\Categoria;
use App\Models\Notificacion;
use App\Models\Producto;
use App\Models\Promocion;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PromocionController extends BaseController
{
    use AuthorizesRequests;

    // Mostrar todas las promociones
    public function index()
    {
        try {
            $this->authorize('ver-promociones');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $promociones = Promocion::orderBy('prioridad')->orderBy('nombre')->get();
        BitacoraController::registrar('ver lista', 'Promocion', null);

        return view('admin.promociones.index', compact('promociones'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        try {
            $this->authorize('crear-promociones');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $productos = Producto::orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        BitacoraController::registrar('crear', 'Promocion', null);

        return view('admin.promociones.create', compact('productos', 'categorias'));
    }

    // ========================================
    // CREAR NUEVA PROMOCIÓN
    // ========================================
    public function store(Request $request)
    {
        try {
            $this->authorize('crear-promociones');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        // Validar datos del formulario
        $datosValidados = $request->validate([
            'nombre' => 'required|string|max:120',
            'tipo' => 'required|in:porcentaje,monto_fijo,2x1,combo',
            'aplica_sobre' => 'required|in:item,pedido',
            'valor' => 'required|numeric|min:0',
            'tope_descuento' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i',
            'prioridad' => 'required|integer|min:1|max:10',
            'dias_semana' => 'nullable|array',
            'dias_semana.*' => 'in:lun,mar,mie,jue,vie,sab,dom',
            'productos' => 'nullable|array',
            'productos.*' => 'exists:productos,id',
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categorias,id',
            'activo' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            // 1. Crear la promoción
            $promocion = Promocion::create([
                'nombre' => $datosValidados['nombre'],
                'tipo' => $datosValidados['tipo'],
                'aplica_sobre' => $datosValidados['aplica_sobre'],
                'valor' => $datosValidados['valor'],
                'tope_descuento' => $datosValidados['tope_descuento'] ?? null,
                'fecha_inicio' => $datosValidados['fecha_inicio'] ?? null,
                'fecha_fin' => $datosValidados['fecha_fin'] ?? null,
                'hora_inicio' => $datosValidados['hora_inicio'] ?? null,
                'hora_fin' => $datosValidados['hora_fin'] ?? null,
                'prioridad' => $datosValidados['prioridad'],
                'dias_semana' => $datosValidados['dias_semana'] ?? null,
                'activo' => $request->has('activo'),
            ]);

            // 2. Asociar productos si los hay
            if (! empty($datosValidados['productos'])) {
                foreach ($datosValidados['productos'] as $productoId) {
                    $promocion->productos()->attach($productoId, ['cantidad_requerida' => 1]);
                }
            }

            // 3. Asociar categorías si las hay
            if (! empty($datosValidados['categorias'])) {
                foreach ($datosValidados['categorias'] as $categoriaId) {
                    $promocion->categorias()->attach($categoriaId, ['cantidad_requerida' => 1]);
                }
            }

            // 4. Notificar a usuarios (solo si está activa)
            if ($promocion->activo) {
                $this->notificarNuevaPromocion($promocion);
                $this->enviarEmailNuevaPromocion($promocion);
            }

            DB::commit();

            BitacoraController::registrar('creado', 'Promocion', $promocion->id);

            return redirect()
                ->route('promociones.index')
                ->with('success', '✅ Promoción creada exitosamente'.($promocion->activo ? ' y notificada a los usuarios.' : '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear promoción: '.$e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', '❌ Error al crear la promoción: '.$e->getMessage());
        }
    }

    // Mostrar una promoción específica
    public function show($id)
    {
        try {
            $this->authorize('ver-promociones');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $promocion = Promocion::with(['productos', 'categorias'])->findOrFail($id);
        BitacoraController::registrar('ver', 'Promocion', $promocion->id);

        return view('admin.promociones.show', compact('promocion'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        try {
            $this->authorize('editar-promociones');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $promocion = Promocion::with(['productos', 'categorias'])->findOrFail($id);
        $productos = Producto::orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        BitacoraController::registrar('editar', 'Promocion', $promocion->id);

        return view('admin.promociones.edit', compact('promocion', 'productos', 'categorias'));
    }

    // Actualizar una promoción
    public function update(Request $request, $id)
    {
        try {
            $this->authorize('editar-promociones');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $promocion = Promocion::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:120',
            'tipo' => 'required|in:porcentaje,monto_fijo,2x1,combo',
            'aplica_sobre' => 'required|in:item,pedido',
            'valor' => 'required|numeric|min:0',
            'tope_descuento' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
            'prioridad' => 'required|integer|min:1',
            'dias_semana' => 'nullable|array',
            'dias_semana.*' => 'in:lun,mar,mie,jue,vie,sab,dom',
            'productos' => 'nullable|array',
            'categorias' => 'nullable|array',
        ]);

        // Actualizar promoción
        $promocion->update([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'aplica_sobre' => $request->aplica_sobre,
            'valor' => $request->valor,
            'tope_descuento' => $request->tope_descuento,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'prioridad' => $request->prioridad,
            'dias_semana' => $request->dias_semana,
            'activo' => $request->has('activo'),
        ]);

        // Sincronizar productos
        $productosData = [];
        if ($request->productos) {
            foreach ($request->productos as $productoId) {
                $productosData[$productoId] = ['cantidad_requerida' => 1];
            }
        }
        $promocion->productos()->sync($productosData);

        // Sincronizar categorías
        $categoriasData = [];
        if ($request->categorias) {
            foreach ($request->categorias as $categoriaId) {
                $categoriasData[$categoriaId] = ['cantidad_requerida' => 1];
            }
        }
        $promocion->categorias()->sync($categoriasData);

        BitacoraController::registrar('actualizado', 'Promocion', $promocion->id);

        return redirect()->route('promociones.index')->with('success', 'Promoción actualizada correctamente');
    }

    // Eliminar una promoción
    public function destroy($id)
    {
        try {
            $this->authorize('eliminar-promociones');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $promocion = Promocion::findOrFail($id);
        $promocion->delete();

        BitacoraController::registrar('eliminado', 'Promocion', $promocion->id);

        return redirect()->route('promociones.index')->with('success', 'Promoción eliminada correctamente');
    }

    /**
     * ========================================
     * NOTIFICACIONES Y EMAILS
     * ========================================
     */

    /**
     * Notificar a usuarios sobre nueva promoción
     */
    private function notificarNuevaPromocion(Promocion $promocion)
    {
        try {
            // Construir mensaje de notificación
            $mensaje = $this->construirMensajePromocion($promocion);

            // Obtener usuarios activos
            $usuarios = User::where('activo', true)->get();

            // Crear notificación para cada usuario
            foreach ($usuarios as $usuario) {
                Notificacion::create([
                    'user_id' => $usuario->id,
                    'tipo' => 'promocion',
                    'titulo' => '🎉 Nueva Promoción Disponible',
                    'mensaje' => $mensaje,
                    'leido' => false,
                    'url' => route('promociones.show', $promocion->id),
                ]);
            }

            Log::info("Notificaciones creadas para promoción: {$promocion->nombre}");

        } catch (\Exception $e) {
            Log::error('Error al crear notificaciones: '.$e->getMessage());
        }
    }

    /**
     * Enviar email a TODOS los usuarios sobre nueva promoción
     */
    private function enviarEmailNuevaPromocion(Promocion $promocion)
    {
        try {
            // Obtener TODOS los usuarios con email VERIFICADO
            $usuarios = User::whereNotNull('email_verified_at') // Solo verificados
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();

            $emailsEnviados = 0;
            $emailsNoVerificados = 0;

            // Enviar email a cada usuario
            foreach ($usuarios as $usuario) {
                try {
                    Mail::to($usuario->email)->send(new PromocionCreada($promocion));
                    $emailsEnviados++;
                } catch (\Exception $e) {
                    Log::warning("Error al enviar email a {$usuario->email}: ".$e->getMessage());
                }
            }

            // Contar usuarios sin verificar (para debug)
            $emailsNoVerificados = User::whereNull('email_verified_at')
                ->whereNotNull('email')
                ->count();

            Log::info("📧 Emails de promoción enviados: {$emailsEnviados} usuarios verificados para '{$promocion->nombre}'");
            if ($emailsNoVerificados > 0) {
                Log::info("⚠️ {$emailsNoVerificados} usuarios no recibieron el email (email no verificado)");
            }

        } catch (\Exception $e) {
            Log::error('Error al enviar emails de promoción: '.$e->getMessage());
        }
    }

    /**
     * Construir mensaje descriptivo de la promoción
     */
    private function construirMensajePromocion(Promocion $promocion): string
    {
        $mensaje = "Nueva promoción: {$promocion->nombre}";

        // Agregar información del descuento
        if ($promocion->tipo === 'porcentaje') {
            $mensaje .= " - {$promocion->valor}% de descuento";
        } elseif ($promocion->tipo === 'monto_fijo') {
            $mensaje .= ' - $'.number_format($promocion->valor, 2).' de descuento';
        } elseif ($promocion->tipo === '2x1') {
            $mensaje .= ' - ¡Paga 1 y lleva 2!';
        } elseif ($promocion->tipo === 'combo') {
            $mensaje .= ' - Combo especial';
        }

        // Agregar vigencia si está definida
        if ($promocion->fecha_fin) {
            $mensaje .= ' válida hasta el '.$promocion->fecha_fin->format('d/m/Y');
        }

        return $mensaje;
    }
}
