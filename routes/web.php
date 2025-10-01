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
use App\Http\Controllers\PedidoController;
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
    // Pedidos unificados (Mesa, Mostrador, Web)
    Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('pedidos/mesa/create', [PedidoController::class, 'createMesa'])->name('pedidos.mesa.create');
    Route::post('pedidos/mesa', [PedidoController::class, 'storeMesa'])->name('pedidos.mesa.store');
    Route::get('pedidos/mostrador/create', [PedidoController::class, 'createMostrador'])->name('pedidos.mostrador.create');
    Route::post('pedidos/mostrador', [PedidoController::class, 'storeMostrador'])->name('pedidos.mostrador.store');
    Route::get('pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::get('pedidos/{pedido}/edit', [PedidoController::class, 'edit'])->name('pedidos.edit');
    Route::put('pedidos/{pedido}', [PedidoController::class, 'update'])->name('pedidos.update');
    Route::delete('pedidos/{pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');
    Route::patch('pedidos/{pedido}/estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar-estado');
    Route::delete('pedidos/items/{item}/anular', [PedidoController::class, 'anularItem'])->name('pedidos.anular-item');
    
    // Selector de productos para pedidos
    Route::get('selector-productos', [\App\Http\Controllers\PedidoSelectorController::class, 'index'])->name('selector.productos.index');
    Route::post('selector-productos/confirmar', [\App\Http\Controllers\PedidoSelectorController::class, 'confirmar'])->name('selector.productos.confirmar');
    
});
Route::middleware(['auth'])->prefix('cajero')->name('cobro_caja.')->group(function () {
    Route::get('/cobros', [CobroCajaController::class, 'index'])->name('index');
    Route::get('/reporte-diario', [CobroCajaController::class, 'reporteDiario'])->name('reporte_diario');
    Route::get('/cobros/{pedido}/cobrar', [CobroCajaController::class, 'create'])->name('create');
    Route::get('/cobros/{pedido}', [CobroCajaController::class, 'show'])->name('show');
    Route::post('/cobros', [CobroCajaController::class, 'store'])->name('store');
    Route::get('/comprobante/{cobro}', [CobroCajaController::class, 'comprobante'])->name('comprobante');
    Route::get('/comprobante/{cobro}/pdf', [CobroCajaController::class, 'comprobantePdf'])->name('comprobante.pdf');
    Route::post('/cobros/{cobro}/cancelar', [CobroCajaController::class, 'cancelar'])->name('cancelar');
    Route::post('/cobros/{cobro}/confirmar-qr', [CobroCajaController::class, 'confirmarQr'])->name('confirmar_qr');
    Route::post('/cobros/{cobro}/rechazar-qr', [CobroCajaController::class, 'rechazarQr'])->name('rechazar_qr');
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
require __DIR__.'/error/403.php';
require __DIR__.'/auth.php';