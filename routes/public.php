<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuPublicoController;
use App\Http\Controllers\UserController;
use App\Models\Horario;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Página de inicio
Route::get('/', function () {
    $horarios = Horario::orderByRaw("FIELD(dia, 'lunes','martes','miércoles','jueves','viernes','sábado','domingo')")->get();
    return view('welcome', compact('horarios'));
})->name('home');

// Menú público
Route::get('/menu', [MenuPublicoController::class, 'index'])->name('menu.publico');

// Activación de cuentas (sin autenticación)
Route::get('/activate/{token}', [UserController::class, 'activateAccount'])->name('users.activate');
Route::post('/set-password/{token}', [UserController::class, 'setPassword'])->name('users.set-password');
