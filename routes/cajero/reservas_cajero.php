<?php

use App\Http\Controllers\Cajero\ReservaCajeroController;
use Illuminate\Support\Facades\Route;

// Rutas para Gestión de Reservas (Cajero) - SIN CONFLICTOS
Route::middleware(['auth', 'can:gestionar-reservas-cajero'])->group(function () {
    
    // Ruta principal del cajero
    Route::get('/cajero/gestion-reservas', [ReservaCajeroController::class, 'index'])
        ->name('cajero.gestion-reservas.index');
    
    // Crear reserva manual
    Route::get('/cajero/gestion-reservas/crear', [ReservaCajeroController::class, 'create'])
        ->name('cajero.gestion-reservas.create');
    Route::post('/cajero/gestion-reservas', [ReservaCajeroController::class, 'store'])
        ->name('cajero.gestion-reservas.store');
    
    // Detalles
    Route::get('/cajero/gestion-reservas/{reserva}', [ReservaCajeroController::class, 'show'])
        ->name('cajero.gestion-reservas.show');
    
    // Acciones
    Route::post('/cajero/gestion-reservas/{reserva}/confirmar-llegada', [ReservaCajeroController::class, 'confirmarLlegada'])
        ->name('cajero.gestion-reservas.confirmar-llegada');
    Route::post('/cajero/gestion-reservas/{reserva}/cancelar', [ReservaCajeroController::class, 'cancelar'])
        ->name('cajero.gestion-reservas.cancelar');
    Route::post('/cajero/gestion-reservas/{reserva}/no-show', [ReservaCajeroController::class, 'marcarNoShow'])
        ->name('cajero.gestion-reservas.marcar-no-show');
});