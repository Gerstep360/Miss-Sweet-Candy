<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspecialDelDiaController;
use App\Models\Producto;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    // Productos para el dashboard
    Route::get('/productos', function () {
        return Producto::with('categoria')
            ->select('id', 'nombre', 'precio', 'imagen', 'categoria_id')
            ->get()
            ->map(function ($producto) {
                return [
                    'id'         => $producto->id,
                    'nombre'     => $producto->nombre,
                    'precio'     => $producto->precio,
                    'imagen_url' => $producto->imagen_url,
                    'categoria'  => $producto->categoria,
                ];
            });
    });

    // Especiales del día
    Route::get('/especial-hoy', [EspecialDelDiaController::class, 'getEspecialHoy']);
    Route::get('/especiales-semana', [EspecialDelDiaController::class, 'getEspecialesSemana']);
});
