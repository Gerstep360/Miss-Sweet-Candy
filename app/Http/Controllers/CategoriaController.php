<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
class CategoriaController extends Controller
{
    // Mostrar todas las categorías
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('admin.categorias.index', compact('categorias'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('admin.categorias.create');
    }

    // Guardar nueva categoría
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre',
        ]);

        Categoria::create([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente');
    }

    // Mostrar una categoría específica
    public function show($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('admin.categorias.show', compact('categoria'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('admin.categorias.edit', compact('categoria'));
    }

    // Actualizar una categoría
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre,' . $categoria->id,
        ]);

        $categoria->update([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente');
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente');
    }
}
