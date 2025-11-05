<?php

use App\Http\Controllers\Cajero\ReservaCajeroController;
use Illuminate\Support\Facades\Route;

// Rutas para Gestión de Reservas (Cajero)
Route::middleware(['auth', 'can:gestionar-reservas-cajero'])->group(function () {
    Route::prefix('cajero/reservas')->name('cajero.reservas.')->group(function () {
        // Listado principal
        Route::get('/', [ReservaCajeroController::class, 'index'])->name('index');
        
        // Crear reserva manual
        Route::get('/crear', [ReservaCajeroController::class, 'create'])->name('create');
        Route::post('/', [ReservaCajeroController::class, 'store'])->name('store');
        
        // Detalles
        Route::get('/{reserva}', [ReservaCajeroController::class, 'show'])->name('show');
        
        // Acciones
        Route::post('/{reserva}/confirmar-llegada', [ReservaCajeroController::class, 'confirmarLlegada'])
            ->name('confirmar-llegada');
        Route::post('/{reserva}/cancelar', [ReservaCajeroController::class, 'cancelar'])
            ->name('cancelar');
        Route::post('/{reserva}/no-show', [ReservaCajeroController::class, 'marcarNoShow'])
            ->name('marcar-no-show');
    });
});