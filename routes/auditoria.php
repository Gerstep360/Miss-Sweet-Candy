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
    
    // Exportación (antes de /{id} para evitar conflictos)
    Route::get('/exportar/pdf', [AuditoriaController::class, 'exportarPdf'])->name('exportar.pdf');
    Route::get('/exportar/excel', [AuditoriaController::class, 'exportarExcel'])->name('exportar.excel');
    
    // Gestión de backups (antes de /{id})
    Route::get('/backups', [AuditoriaController::class, 'backups'])->name('backups');
    Route::post('/backup/crear', [AuditoriaController::class, 'crearBackup'])->name('backup.crear');
    Route::get('/backup/{archivo}/descargar', [AuditoriaController::class, 'descargarBackup'])->name('backup.descargar');
    Route::delete('/backup/{archivo}/eliminar', [AuditoriaController::class, 'eliminarBackup'])->name('backup.eliminar');
    Route::post('/backup/{archivo}/restaurar', [AuditoriaController::class, 'restaurarBackup'])->name('backup.restaurar');
    Route::post('/backup/configurar', [AuditoriaController::class, 'configurarBackups'])->name('backup.configurar');
    
    // Detección de anomalías (antes de /{id})
    Route::get('/anomalias', [AuditoriaController::class, 'anomalias'])->name('anomalias');
    Route::post('/anomalias/detectar', [AuditoriaController::class, 'detectarAnomalias'])->name('anomalias.detectar');
    
    // Intentos de login (antes de /{id})
    Route::get('/intentos-login', [AuditoriaController::class, 'intentosLogin'])->name('intentos-login');
    
    // Detalle de auditoría - DEBE ESTAR AL FINAL para no capturar las rutas específicas
    Route::get('/{id}', [AuditoriaController::class, 'show'])->name('show');
});
