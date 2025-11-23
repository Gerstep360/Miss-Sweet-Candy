<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard completo (con ETag privado para respuestas 304)
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('cache.headers:private;etag')
        ->name('dashboard');

    // Fragmento de órdenes (útil para AJAX/htmx)
    Route::get('dashboard/orders-fragment', [DashboardController::class, 'ordersFragment'])
        ->middleware('cache.headers:private;etag')
        ->name('dashboard.orders.fragment');

    // Establecer producto especial
    Route::post('/dashboard/set-special-product/{product}', [DashboardController::class, 'setSpecialProduct'])
        ->middleware('can:editar-productos')
        ->name('dashboard.set-special-product');
});
