<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BitacoraController;
class ImagenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'imagen' => 'required|image|max:2048',
        ]);

        $ruta = $request->file('imagen')->store('img/productos', 'public');

        // Aquí puedes guardar la ruta en la base de datos si lo necesitas
        BitacoraController::registrar('subido imagen', 'Producto', null);
        return back()->with('success', 'Imagen subida correctamente')->with('ruta', $ruta);
    }

    public function update(Request $request, $producto)
    {
        $request->validate([
            'imagen' => 'required|image|max:2048',
        ]);

        $producto = Producto::findOrFail($producto);

        // Elimina la imagen anterior si existe
        if ($producto->imagen && \Storage::disk('public')->exists($producto->imagen)) {
            \Storage::disk('public')->delete($producto->imagen);
        }

        // Guarda la nueva imagen
        $ruta = $request->file('imagen')->store('img/productos', 'public');
        $producto->imagen = $ruta;
        $producto->save();
        BitacoraController::registrar('actualizado imagen', 'Producto', $producto->id);
        return back()->with('success', 'Imagen actualizada correctamente')->with('ruta', $ruta);
    }
}
