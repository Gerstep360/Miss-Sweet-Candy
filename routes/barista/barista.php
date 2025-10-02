<?php
use App\Http\Controllers\BaristaController;
Route::middleware(['auth', 'permission:gestionar-pedidos-barista'])->prefix('barista')->name('barista.')->group(function () {
    Route::get('pedidos', [BaristaController::class, 'index'])->name('pedidos.index');
    Route::get('pedidos/{pedido}', [BaristaController::class, 'show'])->name('pedidos.show');
    Route::post('pedidos/{pedido}/preparar', [BaristaController::class, 'preparar'])->name('pedidos.preparar');
    Route::post('pedidos/{pedido}/servir', [BaristaController::class, 'servir'])->name('pedidos.servir');
});