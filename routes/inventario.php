<?php

use App\Http\Controllers\Inventario\InventarioController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    
    // Rutas de inventario con triple capa de seguridad
    Route::prefix('inventario')->name('inventario.')->group(function () {
        
        // Listar inventario (ver-inventario)
        Route::get('/', [InventarioController::class, 'index'])
            ->middleware('can:ver-inventario')
            ->name('index');
        
        // Ver detalle de inventario (ver-inventario)
        Route::get('/{id}', [InventarioController::class, 'show'])
            ->middleware('can:ver-inventario')
            ->name('show');
        
        // Alertas de stock bajo/crítico (ver-inventario)
        Route::get('/alertas/listado', [InventarioController::class, 'alertas'])
            ->middleware('can:ver-inventario')
            ->name('alertas');
        
        // Exportar reporte (ver-inventario)
        Route::get('/reporte/exportar', [InventarioController::class, 'exportar'])
            ->middleware('can:ver-inventario')
            ->name('exportar');
        
        // Formulario para ajustar stock (editar-inventario)
        Route::get('/{productoId}/editar-stock', [InventarioController::class, 'editStock'])
            ->middleware('can:editar-inventario')
            ->name('edit-stock');
        
        // Actualizar stock (editar-inventario)
        Route::post('/{productoId}/actualizar-stock', [InventarioController::class, 'updateStock'])
            ->middleware('can:editar-inventario')
            ->name('update-stock');
        
        // Formulario para editar umbrales (editar-inventario)
        Route::get('/{productoId}/editar-umbrales', [InventarioController::class, 'editUmbrales'])
            ->middleware('can:editar-inventario')
            ->name('edit-umbrales');
        
        // Actualizar umbrales (editar-inventario)
        Route::put('/{productoId}/actualizar-umbrales', [InventarioController::class, 'updateUmbrales'])
            ->middleware('can:editar-inventario')
            ->name('update-umbrales');
    });
});
