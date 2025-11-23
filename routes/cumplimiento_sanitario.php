<?php

use App\Http\Controllers\CumplimientoSanitarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Cumplimiento Sanitario (SENASAG)
|--------------------------------------------------------------------------
|
| CU25: Cumplimiento Sanitario
| Gestión de registros de limpieza, control de temperaturas y evidencias
| para cumplir con normativas SENASAG
|
*/

Route::middleware(['auth'])->prefix('cumplimiento-sanitario')->name('cumplimiento-sanitario.')->group(function () {
    
    // Dashboard principal (ver-cumplimiento-sanitario)
    Route::get('/', [CumplimientoSanitarioController::class, 'index'])
        ->middleware('can:ver-cumplimiento-sanitario')
        ->name('index');
    
    // ========================================
    // REGISTROS DE LIMPIEZA
    // ========================================
    
    // Formulario para registrar limpieza (registrar-cumplimiento-sanitario)
    Route::get('/limpieza/crear', [CumplimientoSanitarioController::class, 'createLimpieza'])
        ->middleware('can:registrar-cumplimiento-sanitario')
        ->name('limpieza.create');
    
    // Registrar limpieza (registrar-cumplimiento-sanitario)
    Route::post('/limpieza', [CumplimientoSanitarioController::class, 'storeLimpieza'])
        ->middleware('can:registrar-cumplimiento-sanitario')
        ->name('limpieza.store');
    
    // Ver detalle de limpieza (ver-cumplimiento-sanitario)
    Route::get('/limpieza/{id}', [CumplimientoSanitarioController::class, 'showLimpieza'])
        ->middleware('can:ver-cumplimiento-sanitario')
        ->name('limpieza.show');
    
    // ========================================
    // CONTROL DE TEMPERATURAS
    // ========================================
    
    // Formulario para registrar temperatura (registrar-cumplimiento-sanitario)
    Route::get('/temperatura/crear', [CumplimientoSanitarioController::class, 'createTemperatura'])
        ->middleware('can:registrar-cumplimiento-sanitario')
        ->name('temperatura.create');
    
    // Registrar temperatura (registrar-cumplimiento-sanitario)
    Route::post('/temperatura', [CumplimientoSanitarioController::class, 'storeTemperatura'])
        ->middleware('can:registrar-cumplimiento-sanitario')
        ->name('temperatura.store');
    
    // Ver detalle de temperatura (ver-cumplimiento-sanitario)
    Route::get('/temperatura/{id}', [CumplimientoSanitarioController::class, 'showTemperatura'])
        ->middleware('can:ver-cumplimiento-sanitario')
        ->name('temperatura.show');
    
    // ========================================
    // HISTORIAL Y CONSULTAS
    // ========================================
    
    // Historial de registros con filtros (ver-cumplimiento-sanitario)
    Route::get('/historial', [CumplimientoSanitarioController::class, 'historial'])
        ->middleware('can:ver-cumplimiento-sanitario')
        ->name('historial');
    
    // Alertas activas (ver-cumplimiento-sanitario)
    Route::get('/alertas', [CumplimientoSanitarioController::class, 'alertas'])
        ->middleware('can:ver-cumplimiento-sanitario')
        ->name('alertas');
    
    // ========================================
    // REPORTES Y EXPORTACIÓN
    // ========================================
    
    // Generar reporte SENASAG (generar-reportes-sanitarios)
    Route::get('/reporte-senasag', [CumplimientoSanitarioController::class, 'reporteSenasag'])
        ->middleware('can:generar-reportes-sanitarios')
        ->name('reporte-senasag');
    
    // Exportar registros (generar-reportes-sanitarios)
    Route::get('/exportar', [CumplimientoSanitarioController::class, 'exportar'])
        ->middleware('can:generar-reportes-sanitarios')
        ->name('exportar');
    
    // ========================================
    // GESTIÓN DE EVIDENCIAS
    // ========================================
    
    // Eliminar evidencia (registrar-cumplimiento-sanitario)
    Route::delete('/evidencia/{id}', [CumplimientoSanitarioController::class, 'eliminarEvidencia'])
        ->middleware('can:registrar-cumplimiento-sanitario')
        ->name('evidencia.eliminar');
});
