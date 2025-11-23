<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;

/*
|--------------------------------------------------------------------------
| Rutas de Feedback
|--------------------------------------------------------------------------
|
| Rutas para gestionar el feedback de clientes y ver estadísticas
|
*/

Route::middleware(['auth'])->group(function () {
    
    // Rutas para CLIENTES - Crear y ver su propio feedback
    Route::get('/feedback/crear', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show');

    // Rutas para ADMINISTRADORES - Ver estadísticas y gestionar
    Route::prefix('admin/feedback')->group(function () {
        Route::get('/', [FeedbackController::class, 'index'])->name('feedback.index');
        Route::get('/estadisticas', [FeedbackController::class, 'estadisticas'])->name('feedback.estadisticas');
    });
});
