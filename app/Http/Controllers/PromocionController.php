<?php
// app/Http/Controllers/PromocionController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promocion;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Controllers\BitacoraController;

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

    // Guardar nueva promoción
    public function store(Request $request)
    {
        try {
            $this->authorize('crear-promociones');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $request->validate([
            'nombre' => 'required|string|max:120',
            'tipo' => 'required|in:porcentaje,monto_fijo,2x1,combo',
            'aplica_sobre' => 'required|in:item,pedido',
            'valor' => 'required|numeric|min:0',
            'tope_descuento' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio',
            'prioridad' => 'required|integer|min:1',
            'dias_semana' => 'nullable|array',
            'dias_semana.*' => 'in:lun,mar,mie,jue,vie,sab,dom',
            'productos' => 'nullable|array',
            'categorias' => 'nullable|array',
        ]);

        // Crear promoción
        $promocion = Promocion::create([
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
        if ($request->productos) {
            $productosData = [];
            foreach ($request->productos as $productoId) {
                $productosData[$productoId] = ['cantidad_requerida' => 1];
            }
            $promocion->productos()->sync($productosData);
        }

        // Sincronizar categorías
        if ($request->categorias) {
            $categoriasData = [];
            foreach ($request->categorias as $categoriaId) {
                $categoriasData[$categoriaId] = ['cantidad_requerida' => 1];
            }
            $promocion->categorias()->sync($categoriasData);
        }

        BitacoraController::registrar('creado', 'Promocion', $promocion->id);
        return redirect()->route('promociones.index')->with('success', 'Promoción creada correctamente');
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
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio',
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
}