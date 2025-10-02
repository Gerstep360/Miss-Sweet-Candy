<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Controllers\BitacoraController;
class CategoriaController extends BaseController
{
    use AuthorizesRequests;

    // Mostrar todas las categorías
    public function index()
    {
        try {
            $this->authorize('ver-categorias');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $categorias = Categoria::orderBy('nombre')->get();
        BitacoraController::registrar('ver', 'Categoria', null);
        return view('admin.categorias.index', compact('categorias'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        try {
            $this->authorize('crear-categorias');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }
        BitacoraController::registrar('crear', 'Categoria', null);

        return view('admin.categorias.create');
    }

    // Guardar nueva categoría
    public function store(Request $request)
    {
        try {
            $this->authorize('crear-categorias');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre',
        ]);

        Categoria::create([
            'nombre' => $request->nombre,
        ]);
        BitacoraController::registrar('creado', 'Categoria', null);
        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente');
    }

    // Mostrar una categoría específica
    public function show($id)
    {
        try {
            $this->authorize('ver-categorias');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $categoria = Categoria::findOrFail($id);
        BitacoraController::registrar('ver', 'Categoria', $categoria->id);
        return view('admin.categorias.show', compact('categoria'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        try {
            $this->authorize('editar-categorias');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $categoria = Categoria::findOrFail($id);
        BitacoraController::registrar('editar', 'Categoria', $categoria->id);
        return view('admin.categorias.edit', compact('categoria'));
    }

    // Actualizar una categoría
    public function update(Request $request, $id)
    {
        try {
            $this->authorize('editar-categorias');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre,' . $categoria->id,
        ]);

        $categoria->update([
            'nombre' => $request->nombre,
        ]);
        BitacoraController::registrar('actualizado', 'Categoria', $categoria->id);
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente');
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        try {
            $this->authorize('eliminar-categorias');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $categoria = Categoria::findOrFail($id);
        $categoria->delete();
        BitacoraController::registrar('eliminado', 'Categoria', $categoria->id);
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente');
    }
}
