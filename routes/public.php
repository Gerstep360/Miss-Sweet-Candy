<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentasYCaja\MenuPublicoController;
use App\Http\Controllers\Usuarios\UserController;
use App\Models\Horario;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Página de inicio
// Menú público
Route::get('/menu', [MenuPublicoController::class, 'index'])->name('menu.publico');

// Activación de cuentas (sin autenticación)
Route::get('/activate/{token}', [UserController::class, 'activateAccount'])->name('users.activate');
Route::post('/set-password/{token}', [UserController::class, 'setPassword'])->name('users.set-password');
