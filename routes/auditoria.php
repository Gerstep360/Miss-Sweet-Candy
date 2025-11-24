<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditoriaController;

/*
|--------------------------------------------------------------------------
| Rutas de Auditoría (CU26)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('admin/auditoria')->name('auditoria.')->group(function () {
    // Listado principal con filtros
    Route::get('/', [AuditoriaController::class, 'index'])->name('index');
    
    // Detalle de auditoría
    Route::get('/{id}', [AuditoriaController::class, 'show'])->name('show');
    
    // Exportación
    Route::get('/exportar/pdf', [AuditoriaController::class, 'exportarPdf'])->name('exportar.pdf');
    Route::get('/exportar/excel', [AuditoriaController::class, 'exportarExcel'])->name('exportar.excel');
    
    // Gestión de backups
    Route::get('/backups', [AuditoriaController::class, 'backups'])->name('backups');
    Route::post('/backup/crear', [AuditoriaController::class, 'crearBackup'])->name('backup.crear');
    Route::get('/backup/{archivo}/descargar', [AuditoriaController::class, 'descargarBackup'])->name('backup.descargar');
    Route::delete('/backup/{archivo}/eliminar', [AuditoriaController::class, 'eliminarBackup'])->name('backup.eliminar');
    Route::post('/backup/{archivo}/restaurar', [AuditoriaController::class, 'restaurarBackup'])->name('backup.restaurar');
    Route::post('/backup/configurar', [AuditoriaController::class, 'configurarBackups'])->name('backup.configurar');
    
    // Detección de anomalías
    Route::get('/anomalias', [AuditoriaController::class, 'anomalias'])->name('anomalias');
    Route::post('/anomalias/detectar', [AuditoriaController::class, 'detectarAnomalias'])->name('anomalias.detectar');
    
    // Intentos de login
    Route::get('/intentos-login', [AuditoriaController::class, 'intentosLogin'])->name('intentos-login');
});

