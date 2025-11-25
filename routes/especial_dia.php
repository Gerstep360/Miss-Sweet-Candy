<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controller\Promociones\EspecialDelDiaController;

// Backend (admin)
Route::middleware(['web', 'auth'])
    ->prefix('admin/especiales-dia')
    ->as('especial_dia.')
    ->group(function () {
        Route::get('/',               [EspecialDelDiaController::class, 'index'])->name('index')->middleware('permission:ver-especiales');
        Route::get('/crear',          [EspecialDelDiaController::class, 'create'])->name('create')->middleware('permission:crear-especial');
        Route::post('/',              [EspecialDelDiaController::class, 'store'])->name('store')->middleware('permission:crear-especial');

        Route::get('/{especial}',     [EspecialDelDiaController::class, 'show'])->name('show')->middleware('permission:ver-especiales');
        Route::get('/{especial}/editar', [EspecialDelDiaController::class, 'edit'])->name('edit')->middleware('permission:editar-especial');
        Route::match(['put','patch'],'/{especial}', [EspecialDelDiaController::class, 'update'])->name('update')->middleware('permission:editar-especial');

        Route::delete('/{especial}',  [EspecialDelDiaController::class, 'destroy'])->name('destroy')->middleware('permission:eliminar-especial');
        Route::patch('/{especial}/toggle', [EspecialDelDiaController::class, 'toggle'])->name('toggle')->middleware('permission:activar-especial');
    });

// API (clientes autenticados: ver especial del día / semana)
// Usa OR en permisos (Spatie): ver-especiales | ver-menu-publico
Route::middleware(['web','auth','permission:ver-especiales|ver-menu-publico'])
    ->prefix('api')
    ->as('especial_dia.api.')
    ->group(function () {
        Route::get('/especiales-dia/hoy',    [EspecialDelDiaController::class, 'getEspecialHoy'])->name('hoy');
        Route::get('/especiales-dia/semana', [EspecialDelDiaController::class, 'getEspecialesSemana'])->name('semana');
    });
