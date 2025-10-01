<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;

class ProductoController extends BaseController
{
    use AuthorizesRequests;

    // Mostrar todos los productos
    public function index()
    {
        try {
            $this->authorize('ver-productos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $productos  = Producto::with('categoria')->orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        return view('admin.productos.index', compact('productos', 'categorias'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        try {
            $this->authorize('crear-productos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $categorias = Categoria::orderBy('nombre')->get();
        return view('admin.productos.create', compact('categorias'));
    }

    // Guardar nuevo producto
    public function store(Request $request)
    {
        try {
            $this->authorize('crear-productos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $request->validate([
            'nombre'       => 'required|string|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad'       => 'nullable|string|max:30',
            'precio'       => 'required|numeric|min:0',
            'imagen'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nombre', 'categoria_id', 'unidad', 'precio']);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('img/productos', 'public');
        }

        Producto::create($data);

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente');
    }

    // Mostrar un producto específico
    public function show($id)
    {
        try {
            $this->authorize('ver-productos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $producto = Producto::with('categoria')->findOrFail($id);
        return view('admin.productos.show', compact('producto'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        try {
            $this->authorize('editar-productos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $producto   = Producto::findOrFail($id);
        $categorias = Categoria::orderBy('nombre')->get();
        return view('admin.productos.edit', compact('producto', 'categorias'));
    }

    // Actualizar un producto
    public function update(Request $request, $id)
    {
        try {
            $this->authorize('editar-productos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre'       => 'required|string|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad'       => 'nullable|string|max:30',
            'precio'       => 'required|numeric|min:0',
            'imagen'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nombre', 'categoria_id', 'unidad', 'precio']);

        // Si hay nueva imagen, elimina la anterior y guarda la nueva
        if ($request->hasFile('imagen')) {
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('img/productos', 'public');
        }

        $producto->update($data);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente');
    }

    // Eliminar un producto
    public function destroy($id)
    {
        try {
            $this->authorize('eliminar-productos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $producto = Producto::findOrFail($id);

        // Elimina la imagen asociada si existe
        if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente');
    }
}
