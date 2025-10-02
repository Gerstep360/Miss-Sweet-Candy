<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MesaController;

/*
|--------------------------------------------------------------------------
| Panel de Cajero - Mesas
|--------------------------------------------------------------------------
*/

// Gestión de mesas (requiere permiso específico)
Route::middleware(['auth', 'permission:ver-mesas'])->prefix('cajero')->group(function () {
    Route::resource('mesas', MesaController::class);
    Route::get('mesas/{mesa}/factura', [MesaController::class, 'factura'])->name('mesas.factura');
});
