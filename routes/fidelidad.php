<?php

use App\Http\Controllers\Clientes\FidelidadController;
use Illuminate\Support\Facades\Route;

// Grupo de rutas para el sistema de fidelidad
Route::prefix('fidelidad')->name('fidelidad.')->middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard principal de fidelidad
    Route::get('/', [FidelidadController::class, 'index'])
        ->name('index');

    // Configuración del sistema (solo administradores)
    Route::middleware('can:gestionar-fidelidad')->group(function () {
        Route::get('/config', [FidelidadController::class, 'config'])
            ->name('config');
        
        Route::post('/config', [FidelidadController::class, 'updateConfig'])
            ->name('update-config');
        
        // Ajuste manual de puntos
        Route::post('/ajustar-puntos', [FidelidadController::class, 'ajustarPuntos'])
            ->name('ajustar-puntos');
    });

    // Rutas para cajeros (y administradores)
    Route::middleware(['can:puntosCajero'])->group(function () {
        Route::get('/puntos-cajero', [FidelidadController::class, 'puntosCajero'])
            ->name('puntos-cajero');
        
        Route::get('/buscar-cliente', [FidelidadController::class, 'buscarCliente'])
            ->name('buscar-cliente');
        
        Route::get('/historial-cliente/{clienteId}', [FidelidadController::class, 'getHistorialCliente'])
            ->name('historial-cliente');
        
        Route::post('/canjear-puntos-cajero', [FidelidadController::class, 'canjearPuntosCajero'])
            ->name('canjear-puntos-cajero');
    });

    // Recompensas (accesible para todos los usuarios autenticados)
    Route::get('/recompensas', [FidelidadController::class, 'recompensas'])
        ->name('recompensas');
    
    Route::post('/recompensas/{recompensaId}/canjear', [FidelidadController::class, 'canjearRecompensa'])
        ->name('canjear-recompensa');

    // Historial de puntos
    Route::get('/historial', [FidelidadController::class, 'historial'])->name('historial');
    
    // Historial específico de cliente (solo administradores)
    Route::get('/historial/{cliente}', [FidelidadController::class, 'historial'])
        ->name('historial.cliente')
        ->middleware('can:gestionar-fidelidad');

    // API endpoints para consultas
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/puntos', [FidelidadController::class, 'getPuntosCliente'])
            ->name('puntos');
        
        // Puntos específicos de cliente (solo administradores)
        Route::get('/puntos/{cliente}', [FidelidadController::class, 'getPuntosCliente'])
            ->name('puntos.cliente')
            ->middleware('can:gestionar-fidelidad');
    });
});

// Ruta interna para acumulación automática desde PedidoController
Route::post('/fidelidad/internal/acumular-puntos/{pedido}', [FidelidadController::class, 'acumularPuntosPorPedido'])
    ->name('fidelidad.internal.acumular-puntos')
    ->middleware('auth');