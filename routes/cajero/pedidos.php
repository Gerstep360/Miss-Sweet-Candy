<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PedidoSelectorController;

/*
|--------------------------------------------------------------------------
| Panel de Cajero - Pedidos
|--------------------------------------------------------------------------
*/

// Operaciones de pedidos (Mesa, Mostrador, Web)
Route::middleware(['auth'])->prefix('cajero')->group(function () {
    Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');

    Route::get('pedidos/mesa/create', [PedidoController::class, 'createMesa'])->name('pedidos.mesa.create');
    Route::post('pedidos/mesa', [PedidoController::class, 'storeMesa'])->name('pedidos.mesa.store');

    Route::get('pedidos/mostrador/create', [PedidoController::class, 'createMostrador'])->name('pedidos.mostrador.create');
    Route::post('pedidos/mostrador', [PedidoController::class, 'storeMostrador'])->name('pedidos.mostrador.store');

    Route::get('pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::get('pedidos/{pedido}/edit', [PedidoController::class, 'edit'])->name('pedidos.edit');
    Route::put('pedidos/{pedido}', [PedidoController::class, 'update'])->name('pedidos.update');
    Route::delete('pedidos/{pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');

    Route::patch('pedidos/{pedido}/estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar-estado');
    Route::delete('pedidos/items/{item}/anular', [PedidoController::class, 'anularItem'])->name('pedidos.anular-item');

    // Selector de productos para pedidos
    Route::get('selector-productos', [PedidoSelectorController::class, 'index'])->name('selector.productos.index');
    Route::post('selector-productos/confirmar', [PedidoSelectorController::class, 'confirmar'])->name('selector.productos.confirmar');
});
