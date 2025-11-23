<?php

use App\Http\Controllers\HistorialPedidosClienteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Cliente
|--------------------------------------------------------------------------
|
| CU23: Historial de Pedidos del Cliente
| Rutas para que los clientes gestionen su historial de pedidos
|
*/

Route::middleware(['auth'])->prefix('cliente')->name('cliente.')->group(function () {
    
    // CU23: Historial de Pedidos del Cliente
    Route::prefix('historial')->name('historial.')->group(function () {
        
        // Listar historial de pedidos (ver-mis-pedidos)
        Route::get('/', [HistorialPedidosClienteController::class, 'index'])
            ->middleware('can:ver-mis-pedidos')
            ->name('index');
        
        // Ver detalle de un pedido (ver-mis-pedidos)
        Route::get('/{id}', [HistorialPedidosClienteController::class, 'show'])
            ->middleware('can:ver-mis-pedidos')
            ->name('show');
        
        // Estadísticas del cliente (ver-mis-pedidos)
        Route::get('/estadisticas/resumen', [HistorialPedidosClienteController::class, 'estadisticas'])
            ->middleware('can:ver-mis-pedidos')
            ->name('estadisticas');
        
        // Buscar en historial (ver-mis-pedidos)
        Route::get('/buscar/pedidos', [HistorialPedidosClienteController::class, 'buscar'])
            ->middleware('can:ver-mis-pedidos')
            ->name('buscar');
        
        // Exportar historial (ver-mis-pedidos)
        Route::get('/exportar/reporte', [HistorialPedidosClienteController::class, 'exportar'])
            ->middleware('can:ver-mis-pedidos')
            ->name('exportar');
        
        // Formulario para reordenar pedido (crear-pedidos-web)
        Route::get('/{id}/reordenar', [HistorialPedidosClienteController::class, 'reordenar'])
            ->middleware('can:crear-pedidos-web')
            ->name('reordenar');
        
        // Procesar reorden de pedido (crear-pedidos-web)
        Route::post('/{id}/reordenar', [HistorialPedidosClienteController::class, 'storeReorden'])
            ->middleware('can:crear-pedidos-web')
            ->name('store-reorden');
    });
});
