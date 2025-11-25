<?php

use App\Http\Controllers\Inventario\NotificacionController;
use Illuminate\Support\Facades\Route;

// Rutas de notificaciones para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::prefix('notificaciones')->name('notificaciones.')->group(function () {
        // Ver mis notificaciones
        Route::get('/', [NotificacionController::class, 'index'])->name('index');
        Route::get('/{id}', [NotificacionController::class, 'show'])->name('show');
        
        // Marcar como leída
        Route::post('/{id}/marcar-leida', [NotificacionController::class, 'marcarLeida'])->name('marcar-leida');
        Route::post('/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasLeidas'])->name('marcar-todas-leidas');
        
        // API para obtener no leídas (AJAX)
        Route::get('/api/no-leidas', [NotificacionController::class, 'noLeidas'])->name('no-leidas');
    });
});
