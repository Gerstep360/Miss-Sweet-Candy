<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Usuarios\UserController;
use App\Http\Controllers\Usuarios\RoleController;
use App\Http\Controllers\Usuarios\PermissionController;
use App\Http\Controllers\VentasYCaja\HorarioController;
use App\Http\Controllers\Inventario\ProductoController;
use App\Http\Controllers\Inventario\CategoriaController;
use App\Http\Controller\Promociones\EspecialDelDiaController;
use App\Http\Controller\Promociones\PromocionController;
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // ===== Usuarios =====
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/',           'index')->middleware('permission:ver-usuarios')->name('index');
        Route::get('/create',     'create')->middleware('permission:crear-usuarios')->name('create');
        Route::post('/',          'store')->middleware('permission:crear-usuarios')->name('store');
        Route::get('/{user}',     'show')->middleware('permission:ver-usuarios')->name('show');
        Route::get('/{user}/edit','edit')->middleware('permission:editar-usuarios')->name('edit');
        Route::put('/{user}',     'update')->middleware('permission:editar-usuarios')->name('update');
        Route::delete('/{user}',  'destroy')->middleware('permission:eliminar-usuarios')->name('destroy');
    });

    // ===== Roles =====
    Route::controller(RoleController::class)->prefix('roles')->name('roles.')->group(function () {
        Route::get('/',            'index')->middleware('permission:ver-roles')->name('index');
        Route::get('/create',      'create')->middleware('permission:crear-roles')->name('create');
        Route::post('/',           'store')->middleware('permission:crear-roles')->name('store');
        Route::get('/{role}',      'show')->middleware('permission:ver-roles')->name('show');
        Route::get('/{role}/edit', 'edit')->middleware('permission:editar-roles')->name('edit');
        Route::put('/{role}',      'update')->middleware('permission:editar-roles')->name('update');
        Route::delete('/{role}',   'destroy')->middleware('permission:eliminar-roles')->name('destroy');
    });

    // ===== Permisos (permiso maestro) =====
    Route::controller(PermissionController::class)->prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/',                 'index')->middleware('permission:gestionar-permisos')->name('index');
        Route::get('/create',           'create')->middleware('permission:gestionar-permisos')->name('create');
        Route::post('/',                'store')->middleware('permission:gestionar-permisos')->name('store');
        Route::get('/{permission}',     'show')->middleware('permission:gestionar-permisos')->name('show');
        Route::get('/{permission}/edit','edit')->middleware('permission:gestionar-permisos')->name('edit');
        Route::put('/{permission}',     'update')->middleware('permission:gestionar-permisos')->name('update');
        Route::delete('/{permission}',  'destroy')->middleware('permission:gestionar-permisos')->name('destroy');
    });

    // ===== Categorías =====
    Route::controller(CategoriaController::class)->prefix('categorias')->name('categorias.')->group(function () {
        Route::get('/',                 'index')->middleware('permission:ver-categorias')->name('index');
        Route::get('/create',           'create')->middleware('permission:crear-categorias')->name('create');
        Route::post('/',                'store')->middleware('permission:crear-categorias')->name('store');
        Route::get('/{categoria}',      'show')->middleware('permission:ver-categorias')->name('show');
        Route::get('/{categoria}/edit', 'edit')->middleware('permission:editar-categorias')->name('edit');
        Route::put('/{categoria}',      'update')->middleware('permission:editar-categorias')->name('update');
        Route::delete('/{categoria}',   'destroy')->middleware('permission:eliminar-categorias')->name('destroy');
    });

    // ===== Productos =====
    Route::controller(ProductoController::class)->prefix('productos')->name('productos.')->group(function () {
        Route::get('/',               'index')->middleware('permission:ver-productos')->name('index');
        Route::get('/create',         'create')->middleware('permission:crear-productos')->name('create');
        Route::post('/',              'store')->middleware('permission:crear-productos')->name('store');
        Route::get('/{producto}',     'show')->middleware('permission:ver-productos')->name('show');
        Route::get('/{producto}/edit','edit')->middleware('permission:editar-productos')->name('edit');
        Route::put('/{producto}',     'update')->middleware('permission:editar-productos')->name('update');
        Route::delete('/{producto}',  'destroy')->middleware('permission:eliminar-productos')->name('destroy');
    });

    // ===== Horarios =====
    Route::controller(HorarioController::class)->prefix('horarios')->name('horarios.')->group(function () {
        Route::get('/',             'index')->middleware('permission:ver-horarios')->name('index');
        Route::get('/create',       'create')->middleware('permission:configurar-horarios')->name('create');
        Route::post('/',            'store')->middleware('permission:configurar-horarios')->name('store');
        Route::get('/{horario}',    'show')->middleware('permission:ver-horarios')->name('show');
        Route::get('/{horario}/edit','edit')->middleware('permission:configurar-horarios')->name('edit');
        Route::put('/{horario}',    'update')->middleware('permission:configurar-horarios')->name('update');
        Route::delete('/{horario}', 'destroy')->middleware('permission:configurar-horarios')->name('destroy');
    });

    // ===== Especial del día (usa permisos de productos) =====
    Route::controller(EspecialDelDiaController::class)->prefix('especial_dia')->name('especial_dia.')->group(function () {
        Route::get('/',                    'index')->middleware('permission:ver-productos')->name('index');
        Route::get('/create',              'create')->middleware('permission:crear-productos')->name('create');
        Route::post('/',                   'store')->middleware('permission:crear-productos')->name('store');
        Route::get('/{especiale}',         'show')->middleware('permission:ver-productos')->name('show');
        Route::get('/{especiale}/edit',    'edit')->middleware('permission:editar-productos')->name('edit');
        Route::put('/{especiale}',         'update')->middleware('permission:editar-productos')->name('update');
        Route::delete('/{especiale}',      'destroy')->middleware('permission:eliminar-productos')->name('destroy');

        Route::post('/{especiale}/toggle', 'toggleActive')
            ->middleware('permission:editar-productos')
            ->name('toggle');
    });

    // Rutas para control de acceso
   Route::controller(\App\Http\Controllers\Usuarios\ControlAccesoController::class)
    ->prefix('control-acceso')
    ->name('control-acceso.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/config-ips', 'configIps')->name('config-ips');
        Route::get('/two-factor', 'twoFactor')->name('two-factor');
        Route::post('/request-two-factor-code', 'requestTwoFactorCode')->name('request-two-factor-code');
        Route::post('/enable-two-factor', 'enableTwoFactor')->name('enable-two-factor');
        Route::post('/disable-two-factor', 'disableTwoFactor')->name('disable-two-factor');
        Route::get('/backup-codes', 'showBackupCodes')->name('backup-codes');
        Route::post('/generate-backup-codes', 'generateBackupCodes')->name('generate-backup-codes');
        Route::post('/add-ip-whitelist', 'addIpToWhitelist')->name('add-ip-whitelist');
        Route::delete('/remove-ip-whitelist/{id}', 'removeIpFromWhitelist')->name('remove-ip-whitelist');
        Route::post('/verify-code', 'verifyTwoFactorCode')->name('verify-code');
        Route::get('/recent-activity', 'getRecentActivity')->name('recent-activity');
        Route::post('/import-ips', 'importIps')->name('import-ips');
        Route::get('/bitacora', 'bitacora')->name('bitacora');
         Route::get('/import-ips', 'showImportIpsForm')->name('import-ips-form');
         Route::post('/add-ip-manual', 'addIpManual')->name('add-ip-manual');
        Route::post('/add-to-whitelist', 'addToWhitelist')->name('add-to-whitelist');
        Route::post('/add-to-blacklist', 'addToBlacklist')->name('add-to-blacklist');
        Route::get('/export-ips', 'exportIps')->name('export-ips');
        Route::post('/toggle-role-2fa', 'toggleRole2FA')->name('toggle-role-2fa');
        Route::post('/force-enable-2fa', 'forceEnable2FA')->name('force-enable-2fa');
        Route::post('/disable-user-2fa', 'disableUser2FA')->name('disable-user-2fa');
    });

});
