<?php

use App\Http\Controllers\Reportes\ReporteVentasController;
use App\Http\Controllers\Reportes\ReporteExportController;
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

    // =====================================================
    // CU30 - EXPORTAR REPORTES (PDF/EXCEL)
    // =====================================================

    // Vista principal de exportación
    Route::get('/exportar', [ReporteExportController::class, 'index'])
        ->name('exportar.index')
        ->middleware('permission:ver-reportes');

    // Formularios específicos
    Route::get('/exportar/inventario', [ReporteExportController::class, 'inventarioForm'])
        ->name('exportar.inventario.form')
        ->middleware('permission:ver-reportes');

    Route::get('/exportar/cobro-caja', [ReporteExportController::class, 'cobroCajaForm'])
        ->name('exportar.cobro-caja.form')
        ->middleware('permission:ver-reportes');

    Route::get('/exportar/arqueos', [ReporteExportController::class, 'arqueosForm'])
        ->name('exportar.arqueos.form')
        ->middleware('permission:ver-reportes');

    Route::get('/exportar/pedidos', [ReporteExportController::class, 'pedidosForm'])
        ->name('exportar.pedidos.form')
        ->middleware('permission:ver-reportes');

    Route::get('/exportar/promociones', [ReporteExportController::class, 'promocionesForm'])
        ->name('exportar.promociones.form')
        ->middleware('permission:ver-reportes');

    // Exportaciones - Inventario
    Route::post('/exportar/inventario/pdf', [ReporteExportController::class, 'inventarioPDF'])
        ->name('exportar.inventario.pdf')
        ->middleware('permission:ver-reportes');

    Route::post('/exportar/inventario/excel', [ReporteExportController::class, 'inventarioExcel'])
        ->name('exportar.inventario.excel')
        ->middleware('permission:ver-reportes');

    // Exportaciones - Cobro Caja
    Route::post('/exportar/cobro-caja/pdf', [ReporteExportController::class, 'cobroCajaPDF'])
        ->name('exportar.cobro-caja.pdf')
        ->middleware('permission:ver-reportes');

    Route::post('/exportar/cobro-caja/excel', [ReporteExportController::class, 'cobroCajaExcel'])
        ->name('exportar.cobro-caja.excel')
        ->middleware('permission:ver-reportes');

    // Exportaciones - Arqueos
    Route::post('/exportar/arqueos/pdf', [ReporteExportController::class, 'arqueosPDF'])
        ->name('exportar.arqueos.pdf')
        ->middleware('permission:ver-reportes');

    Route::post('/exportar/arqueos/excel', [ReporteExportController::class, 'arqueosExcel'])
        ->name('exportar.arqueos.excel')
        ->middleware('permission:ver-reportes');

    // Exportaciones - Pedidos
    Route::post('/exportar/pedidos/pdf', [ReporteExportController::class, 'pedidosPDF'])
        ->name('exportar.pedidos.pdf')
        ->middleware('permission:ver-reportes');

    Route::post('/exportar/pedidos/excel', [ReporteExportController::class, 'pedidosExcel'])
        ->name('exportar.pedidos.excel')
        ->middleware('permission:ver-reportes');

    // Exportaciones - Promociones
    Route::post('/exportar/promociones/pdf', [ReporteExportController::class, 'promocionesPDF'])
        ->name('exportar.promociones.pdf')
        ->middleware('permission:ver-reportes');

    Route::post('/exportar/promociones/excel', [ReporteExportController::class, 'promocionesExcel'])
        ->name('exportar.promociones.excel')
        ->middleware('permission:ver-reportes');
});
