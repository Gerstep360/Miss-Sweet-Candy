<?php

namespace App\Providers;

use App\Events\AccionAuditable;
use App\Listeners\RegistrarAuditoria;
use App\Listeners\RegistrarLoginIntento;
use App\Models\Producto;
use App\Observers\ProductoObserver;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Producto::observe(ProductoObserver::class);
        // Registrar listener para eventos de auditoría
        Event::listen(
            AccionAuditable::class,
            RegistrarAuditoria::class
        );

        // Registrar listeners para eventos de autenticación
        Event::listen(Login::class, [RegistrarLoginIntento::class, 'handleLogin']);
        Event::listen(Failed::class, [RegistrarLoginIntento::class, 'handleFailed']);

        // Registrar logout en auditoría
        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                event(new AccionAuditable(
                    'logout',
                    'Usuario',
                    $event->user->id,
                    $event->user->id
                ));
            }
        });
    }
}
