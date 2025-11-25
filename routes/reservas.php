<?php

use App\Http\Controllers\ReservaController;
use Illuminate\Support\Facades\Route;

// Rutas para Reservas (Cliente)
Route::middleware(['auth'])->group(function () {
    Route::prefix('reservas')->name('reservas.')->group(function () {
        Route::get('/', [ReservaController::class, 'index'])->name('index');
        Route::get('/crear', [ReservaController::class, 'create'])->name('create');
        Route::post('/verificar-disponibilidad', [ReservaController::class, 'verificarDisponibilidad'])->name('verificar-disponibilidad');
        Route::post('/', [ReservaController::class, 'store'])->name('store');
        Route::get('/{reserva}', [ReservaController::class, 'show'])->name('show');
        Route::get('/{reserva}/editar', [ReservaController::class, 'edit'])->name('edit');
        Route::put('/{reserva}', [ReservaController::class, 'update'])->name('update');
        Route::delete('/{reserva}', [ReservaController::class, 'destroy'])->name('destroy');
    });
});