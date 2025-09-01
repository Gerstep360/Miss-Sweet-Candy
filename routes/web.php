<?php
// filepath: routes/web.php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\PedidoMesaController;
use App\Http\Controllers\PedidoMostradorController;
use App\Http\Controllers\CobroCajaController;
use App\Http\Controllers\MenuPublicoController;
use App\Http\Controllers\EspecialDelDiaController;

use App\Models\Horario;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\CobroCaja;

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard completo (con ETag privado para respuestas 304)
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('cache.headers:private;etag')
        ->name('dashboard');
    
    // Fragmento de órdenes (útil para AJAX/htmx)
    Route::get('dashboard/orders-fragment', [DashboardController::class, 'ordersFragment'])
        ->middleware('cache.headers:private;etag')
        ->name('dashboard.orders.fragment');
    
    // Establecer producto especial
    Route::post('/dashboard/set-special-product/{product}', [DashboardController::class, 'setSpecialProduct'])
        ->middleware('can:editar-productos')
        ->name('dashboard.set-special-product');
});

/*
|--------------------------------------------------------------------------
| Configuraciones de Usuario
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

/*
|--------------------------------------------------------------------------
| Panel de Administración
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:administrador'])->prefix('admin')->group(function () {
    // Gestión de usuarios y permisos
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class);
    
    // Configuración del negocio
    Route::resource('horarios', HorarioController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('categorias', CategoriaController::class);
    
    // Especiales del día
    Route::resource('especial_dia', EspecialDelDiaController::class);
    Route::post('especial_dia/{especiale}/toggle', [EspecialDelDiaController::class, 'toggleActive'])
        ->name('especial_dia.toggle');
});

/*
|--------------------------------------------------------------------------
| Panel de Cajero
|--------------------------------------------------------------------------
*/

// Gestión de mesas (requiere permiso específico)
Route::middleware(['auth', 'permission:ver-mesas'])->prefix('cajero')->group(function () {
    Route::resource('mesas', MesaController::class);
    Route::get('mesas/{mesa}/factura', [MesaController::class, 'factura'])->name('mesas.factura');
});

// Operaciones de caja y pedidos
Route::middleware(['auth'])->prefix('cajero')->group(function () {
    // Pedidos para mesas
    Route::resource('pedido-mesas', PedidoMesaController::class);
    Route::post('pedido-mesas/{pedido}/completar', [PedidoMesaController::class, 'completar'])
        ->name('pedido-mesas.completar');
    Route::post('pedido-mesas/{pedido}/procesar', [PedidoMesaController::class, 'procesar'])
        ->name('pedido-mesas.procesar');
    
    // Pedidos para mostrador
    Route::resource('pedido_mostrador', PedidoMostradorController::class);
    Route::post('pedido_mostrador/{pedido}/procesar', [PedidoMostradorController::class, 'procesar'])
        ->name('pedido_mostrador.procesar');
    Route::post('pedido_mostrador/{pedido}/confirmar-retiro', [PedidoMostradorController::class, 'confirmarRetiro'])
        ->name('pedido_mostrador.confirmarRetiro');
    
    // Selector de pedidos
    Route::get('pedido/selector', [App\Http\Controllers\PedidoSelectorController::class, 'index'])
        ->name('pedido.selector');
    Route::post('pedido/selector/confirmar', [App\Http\Controllers\PedidoSelectorController::class, 'confirmar'])
        ->name('pedido.selector.confirmar');
    
    // Cobros y facturación
    Route::get('cobros', [CobroCajaController::class, 'index'])->name('cobro_caja.index');
    Route::get('cobros/{tipo}/{id}/create', [CobroCajaController::class, 'create'])->name('cobro_caja.create');
    Route::post('cobros', [CobroCajaController::class, 'store'])->name('cobro_caja.store');
    Route::get('cobros/{tipo}/{id}', [CobroCajaController::class, 'show'])->name('cobro_caja.show');
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    // Productos para el dashboard
    Route::get('/productos', function () {
        return \App\Models\Producto::with('categoria')
            ->select('id', 'nombre', 'precio', 'imagen', 'categoria_id')
            ->get()
            ->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio,
                    'imagen_url' => $producto->imagen_url,
                    'categoria' => $producto->categoria
                ];
            });
    });
    
    // Especiales del día
    Route::get('/especial-hoy', [EspecialDelDiaController::class, 'getEspecialHoy']);
    Route::get('/especiales-semana', [EspecialDelDiaController::class, 'getEspecialesSemana']);
});

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';