<?php

use App\Http\Controllers\CierreCajaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('cierres-caja')->name('cierres_caja.')->group(function () {
    
    // Listar cierres
    Route::get('/', [CierreCajaController::class, 'index'])
        ->name('index')
        ->middleware('permission:ver-cierres');

    // Crear nuevo cierre
    Route::get('/crear', [CierreCajaController::class, 'create'])
        ->name('create')
        ->middleware('permission:cerrar-caja');

    Route::post('/', [CierreCajaController::class, 'store'])
        ->name('store')
        ->middleware('permission:cerrar-caja');

    // Ver detalle de un cierre
    Route::get('/{id}', [CierreCajaController::class, 'show'])
        ->name('show')
        ->middleware('permission:ver-cierres');

    // Exportar a PDF
    Route::get('/{id}/exportar-pdf', [CierreCajaController::class, 'exportarPDF'])
        ->name('exportar-pdf')
        ->middleware('permission:ver-cierres');

    // Verificar si existe cierre hoy (API)
    Route::get('/api/verificar-hoy', [CierreCajaController::class, 'verificarCierreHoy'])
        ->name('verificar-hoy');

    // Anular cierre (solo admin)
    Route::post('/{id}/anular', [CierreCajaController::class, 'anular'])
        ->name('anular')
        ->middleware('permission:cerrar-caja');
});
