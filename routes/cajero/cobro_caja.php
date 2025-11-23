<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CobroCajaController;

/*
|--------------------------------------------------------------------------
| Panel de Cajero - Cobro de Caja
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('cajero')->name('cobro_caja.')->group(function () {
    Route::get('/cobros', [CobroCajaController::class, 'index'])->name('index');
    Route::get('/reporte-diario', [CobroCajaController::class, 'reporteDiario'])->name('reporte_diario');

    Route::get('/cobros/{pedido}/cobrar', [CobroCajaController::class, 'create'])->name('create');
    Route::get('/cobros/{pedido}', [CobroCajaController::class, 'show'])->name('show');
    Route::post('/cobros', [CobroCajaController::class, 'store'])->name('store');

    Route::get('/comprobante/{cobro}', [CobroCajaController::class, 'comprobante'])->name('comprobante');
    Route::get('/comprobante/{cobro}/pdf', [CobroCajaController::class, 'comprobantePdf'])->name('comprobante.pdf');

    Route::post('/cobros/{cobro}/cancelar', [CobroCajaController::class, 'cancelar'])->name('cancelar');
    Route::post('/cobros/{cobro}/confirmar-qr', [CobroCajaController::class, 'confirmarQr'])->name('confirmar_qr');
    Route::post('/cobros/{cobro}/rechazar-qr', [CobroCajaController::class, 'rechazarQr'])->name('rechazar_qr');
});
