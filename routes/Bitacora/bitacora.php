<?php
use App\Http\Controllers\BitacoraController;

Route::middleware(['auth', 'verified', 'can:ver-bitacora'])->group(function () {
    Route::get('/admin/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    Route::get('/admin/bitacora/{id}', [BitacoraController::class, 'show'])->name('bitacora.show');
});
