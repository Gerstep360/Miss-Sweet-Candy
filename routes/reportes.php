<?php

use App\Http\Controllers\ReporteVentasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('reportes')->name('reportes.')->group(function () {
    
    // Reporte principal de ventas
    Route::get('/ventas', [ReporteVentasController::class, 'index'])
        ->name('ventas.index')
        ->middleware('permission:ver-reportes');

    // Reporte por cajero
    Route::get('/ventas/por-cajero', [ReporteVentasController::class, 'porCajero'])
        ->name('ventas.por-cajero')
        ->middleware('permission:ver-reportes');

    // Reporte por producto
    Route::get('/ventas/por-producto', [ReporteVentasController::class, 'porProducto'])
        ->name('ventas.por-producto')
        ->middleware('permission:ver-reportes');

    // Reporte de ventas diarias
    Route::get('/ventas/diarias', [ReporteVentasController::class, 'ventasDiarias'])
        ->name('ventas.diarias')
        ->middleware('permission:ver-reportes');

    // Exportar a PDF
    Route::get('/ventas/exportar-pdf', [ReporteVentasController::class, 'exportarPDF'])
        ->name('ventas.exportar-pdf')
        ->middleware('permission:ver-reportes');

    // Exportar a Excel (CSV)
    Route::get('/ventas/exportar-excel', [ReporteVentasController::class, 'exportarExcel'])
        ->name('ventas.exportar-excel')
        ->middleware('permission:ver-reportes');
});
