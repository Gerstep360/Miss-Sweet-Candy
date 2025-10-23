<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspecialDelDiaController;
use App\Models\Producto;
use App\Models\Notificacion;

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
    
    // Notificaciones
    Route::middleware('auth')->group(function () {
        Route::get('/notificaciones/count', function () {
            $count = Notificacion::where('usuario_destino_id', auth()->id())
                ->where('leido', false)
                ->count();
            
            return response()->json(['count' => $count]);
        });
    });
});
