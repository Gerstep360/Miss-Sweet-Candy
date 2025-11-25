<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clientes\ClientePerfilController;

/*
|--------------------------------------------------------------------------
| Rutas de Perfil de Cliente (CU22)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Rutas para el cliente (gestionar su propio perfil)
    Route::middleware('permission:ver-mi-perfil')->group(function () {
        Route::get('/perfil', [ClientePerfilController::class, 'show'])
            ->name('perfil.show');
    });

    Route::middleware('permission:editar-mi-perfil')->group(function () {
        Route::get('/perfil/editar', [ClientePerfilController::class, 'edit'])
            ->name('perfil.edit');
        
        Route::put('/perfil', [ClientePerfilController::class, 'update'])
            ->name('perfil.update');
    });

    // Rutas para cajeros (consultar perfiles de clientes)
    Route::middleware('permission:consultar-perfil-cliente')->group(function () {
        // Vista de tabla con todos los clientes
        Route::get('/clientes/perfiles', [ClientePerfilController::class, 'consultarTodos'])
            ->name('perfil.consultar.todos');
        
        Route::get('/clientes/{userId}/perfil', [ClientePerfilController::class, 'consultarVista'])
            ->name('clientes.perfil.ver');
        
        Route::get('/api/clientes/{userId}/perfil', [ClientePerfilController::class, 'consultar'])
            ->name('api.clientes.perfil');
        
        Route::get('/api/clientes/buscar', [ClientePerfilController::class, 'buscarClientes'])
            ->name('api.clientes.buscar');
    });
});
