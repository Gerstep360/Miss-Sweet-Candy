<?php
// routes/web.php
use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/api/especial-hoy', [WelcomeController::class, 'especialHoy'])->name('api.especial-hoy');
Route::get('/api/estado', [WelcomeController::class, 'estado'])->name('api.estado');
Route::get('/home', fn() => redirect()->route('dashboard'))->name('home');