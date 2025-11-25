<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentasYCaja\TurneroController;

Route::prefix('turnos')->name('turnos.')->group(function () {
    // Lista de tickets del usuario
    Route::get('/mis-pedidos', [TurneroController::class, 'index'])->name('turnero.index');
    
    // Monitor público
    Route::get('/monitor/cola', [TurneroController::class, 'monitor'])->name('turnero.monitor');
    
    // Seguimiento individual por token
    Route::get('/cola/{token}', [TurneroController::class, 'cliente'])->name('turnero.cliente');
    
    // API feed para el monitor
    Route::get('/feed', [TurneroController::class, 'feed']);
});