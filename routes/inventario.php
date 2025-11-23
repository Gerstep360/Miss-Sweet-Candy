<?php

use App\Http\Controllers\InventarioController;
use App\Http\Controllers\InventarioProductoTerminadoController;
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

    // CU15: Rutas de Inventario de Producto Terminado
    Route::prefix('inventario/producto-terminado')->name('inventario.producto-terminado.')->group(function () {
        
        // Listar productos terminados en inventario (ver-inventario)
        Route::get('/', [InventarioProductoTerminadoController::class, 'index'])
            ->middleware('can:ver-inventario')
            ->name('index');
        
        // Ver detalle de producto terminado (ver-inventario)
        Route::get('/{id}', [InventarioProductoTerminadoController::class, 'show'])
            ->middleware('can:ver-inventario')
            ->name('show');
        
        // Formulario para registrar producción (editar-inventario)
        Route::get('/produccion/crear', [InventarioProductoTerminadoController::class, 'create'])
            ->middleware('can:editar-inventario')
            ->name('create');
        
        // Registrar producción (editar-inventario)
        Route::post('/produccion', [InventarioProductoTerminadoController::class, 'store'])
            ->middleware('can:editar-inventario')
            ->name('store');
        
        // Formulario para registrar merma (editar-inventario)
        Route::get('/{productoId}/merma/crear', [InventarioProductoTerminadoController::class, 'createMerma'])
            ->middleware('can:editar-inventario')
            ->name('create-merma');
        
        // Registrar merma (editar-inventario)
        Route::post('/{productoId}/merma', [InventarioProductoTerminadoController::class, 'storeMerma'])
            ->middleware('can:editar-inventario')
            ->name('store-merma');
        
        // Formulario para ajuste manual (editar-inventario)
        Route::get('/{productoId}/ajuste/crear', [InventarioProductoTerminadoController::class, 'createAjuste'])
            ->middleware('can:editar-inventario')
            ->name('create-ajuste');
        
        // Realizar ajuste manual (editar-inventario)
        Route::post('/{productoId}/ajuste', [InventarioProductoTerminadoController::class, 'storeAjuste'])
            ->middleware('can:editar-inventario')
            ->name('store-ajuste');
        
        // Reporte de rotación (ver-inventario)
        Route::get('/reporte/rotacion', [InventarioProductoTerminadoController::class, 'reporteRotacion'])
            ->middleware('can:ver-inventario')
            ->name('reporte-rotacion');
        
        // Exportar reporte (ver-inventario)
        Route::get('/reporte/exportar', [InventarioProductoTerminadoController::class, 'exportar'])
            ->middleware('can:ver-inventario')
            ->name('exportar');
    });
});
