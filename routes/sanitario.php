<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SanitarioController;

Route::middleware(['auth'])->group(function () {
    Route::get('/sanitario', [SanitarioController::class, 'index'])->name('sanitario.index');
    Route::get('/sanitario/temperaturas', [SanitarioController::class, 'temperaturas'])->name('sanitario.temperaturas');
    Route::post('/sanitario/temperaturas', [SanitarioController::class, 'storeTemperatura'])->name('sanitario.temperaturas.store');
    
    Route::get('/sanitario/{lista}', [SanitarioController::class, 'show'])->name('sanitario.show');
    Route::get('/sanitario/{lista}/historial', [SanitarioController::class, 'historial'])->name('sanitario.historial');
    Route::post('/sanitario/{lista}', [SanitarioController::class, 'store'])->name('sanitario.store');
});
