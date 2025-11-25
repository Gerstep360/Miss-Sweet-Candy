<?php

use App\Http\Controllers\Personal\TurnoCajaController;
use Illuminate\Support\Facades\Route;

// Rutas protegidas con autenticación y permisos
Route::middleware(['auth'])->group(function () {
    
    // API para obtener estado del turno (sin permisos especiales, cualquier usuario autenticado)
    Route::get('/turnos-caja/api/estado', [TurnoCajaController::class, 'estado'])
        ->name('turnos_caja.api.estado');
    
    // Rutas para cajeros y administradores
    Route::middleware('permission:iniciar-turno')->group(function () {
        Route::get('/turnos-caja/iniciar', [TurnoCajaController::class, 'formIniciar'])
            ->name('turnos_caja.iniciar.form');
        
        Route::post('/turnos-caja', [TurnoCajaController::class, 'iniciar'])
            ->name('turnos_caja.iniciar');
    });
    
    Route::middleware('permission:cerrar-turno')->group(function () {
        Route::post('/turnos-caja/cerrar', [TurnoCajaController::class, 'cerrar'])
            ->name('turnos_caja.cerrar');
    });
    
    // Historial de turnos (requiere permiso de ver cierres o ser admin)
    Route::middleware('permission:ver-cierres')->group(function () {
        Route::get('/turnos-caja', [TurnoCajaController::class, 'index'])
            ->name('turnos_caja.index');
        
        Route::get('/turnos-caja/{turno}', [TurnoCajaController::class, 'show'])
            ->name('turnos_caja.show');
    });
});
