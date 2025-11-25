<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Usuarios\AlergenoController;

/*
|--------------------------------------------------------------------------
| Rutas de Gestión de Alérgenos
|--------------------------------------------------------------------------
| CU22 - Gestión de alérgenos para productos
| Solo ADMINISTRADOR puede gestionar alérgenos
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Gestión de alérgenos (ADMIN)
    Route::middleware(['can:ver-alergenos'])->group(function () {
        Route::get('/alergenos', [AlergenoController::class, 'index'])->name('alergenos.index');
        Route::get('/alergenos/{alergeno}/productos', [AlergenoController::class, 'productos'])->name('alergenos.productos');
    });

    Route::middleware(['can:crear-alergenos'])->group(function () {
        Route::get('/alergenos/crear', [AlergenoController::class, 'create'])->name('alergenos.create');
        Route::post('/alergenos', [AlergenoController::class, 'store'])->name('alergenos.store');
    });

    Route::middleware(['can:editar-alergenos'])->group(function () {
        Route::get('/alergenos/{alergeno}/editar', [AlergenoController::class, 'edit'])->name('alergenos.edit');
        Route::put('/alergenos/{alergeno}', [AlergenoController::class, 'update'])->name('alergenos.update');
        Route::post('/alergenos/{alergeno}/toggle', [AlergenoController::class, 'toggleActivo'])->name('alergenos.toggle');
    });

    Route::middleware(['can:eliminar-alergenos'])->group(function () {
        Route::delete('/alergenos/{alergeno}', [AlergenoController::class, 'destroy'])->name('alergenos.destroy');
    });

    // Gestionar alérgenos de productos específicos
    Route::middleware(['can:gestionar-alergenos-productos'])->group(function () {
        Route::get('/productos/{producto}/alergenos', [AlergenoController::class, 'gestionarProducto'])->name('productos.alergenos.gestionar');
        Route::post('/productos/{producto}/alergenos', [AlergenoController::class, 'actualizarProducto'])->name('productos.alergenos.actualizar');
    });
});
