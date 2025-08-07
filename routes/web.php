<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

#Horario
use App\Http\Controllers\HorarioController;
use App\Models\Horario;

#productos
use App\Http\Controllers\ProductoController;
use App\Models\Producto;
use App\Http\Controllers\CategoriaController;
use App\Models\Categoria;

#mesa
use App\Http\Controllers\MesaController;
use App\Models\Mesa;

#pedidos
use App\Http\Controllers\PedidoMesaController;
use App\Http\Controllers\PedidoMostradorController;

#CobroCaja
use App\Http\Controllers\CobroCajaController;
use App\Models\CobroCaja;

#Menu
use App\Http\Controllers\MenuPublicoController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;



Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'role:administrador'])->prefix('admin')->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class);
    Route::resource('horarios', HorarioController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('categorias', CategoriaController::class);
    // ...otras rutas de administración
});
Route::middleware(['auth', 'permission:ver-mesas'])->prefix('cajero')->group(function () {
    Route::resource('mesas', MesaController::class);
    Route::get('mesas/{mesa}/factura', [MesaController::class, 'factura'])->name('mesas.factura');
});
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

//pedidos

Route::middleware(['auth'])->prefix('cajero')->group(function () {
    Route::resource('pedido-mesas', PedidoMesaController::class);
    Route::get('pedido/selector', [App\Http\Controllers\PedidoSelectorController::class, 'index'])->name('pedido.selector');
    Route::post('pedido/selector/confirmar', [App\Http\Controllers\PedidoSelectorController::class, 'confirmar'])->name('pedido.selector.confirmar');

    Route::post('pedido-mesas/{pedido}/completar', [PedidoMesaController::class, 'completar'])
        ->name('pedido-mesas.completar');
    Route::post('pedido-mesas/{pedido}/procesar', [PedidoMesaController::class, 'procesar'])
        ->name('pedido-mesas.procesar');

    Route::resource('pedido_mostrador', PedidoMostradorController::class);

    Route::post('pedido_mostrador/{pedido}/procesar', [PedidoMostradorController::class, 'procesar'])
        ->name('pedido_mostrador.procesar');
    Route::post('pedido_mostrador/{pedido}/confirmar-retiro', [PedidoMostradorController::class, 'confirmarRetiro'])
        ->name('pedido_mostrador.confirmarRetiro');

    Route::get('cobros', [CobroCajaController::class, 'index'])->name('cobro_caja.index');
    Route::get('cobros/{tipo}/{id}/create', [CobroCajaController::class, 'create'])->name('cobro_caja.create');
    Route::post('cobros', [CobroCajaController::class, 'store'])->name('cobro_caja.store');
    Route::get('cobros/{tipo}/{id}', [CobroCajaController::class, 'show'])->name('cobro_caja.show');
});


Route::get('/menu', [MenuPublicoController::class, 'index'])->name('menu.publico');

// Rutas para activación de cuenta (sin middleware auth)
Route::get('/activate/{token}', [UserController::class, 'activateAccount'])->name('users.activate');
Route::post('/set-password/{token}', [UserController::class, 'setPassword'])->name('users.set-password');
// ...existing code...


Route::get('/', function () {
    $horarios = Horario::orderByRaw("FIELD(dia, 'lunes','martes','miércoles','jueves','viernes','sábado','domingo')")->get();
    return view('welcome', compact('horarios'));
})->name('home');
require __DIR__.'/auth.php';
