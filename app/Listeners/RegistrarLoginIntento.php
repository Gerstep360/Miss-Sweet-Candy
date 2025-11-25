<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use App\Models\LoginIntento;
use Illuminate\Support\Facades\Log;

class RegistrarLoginIntento
{
    /**
     * Handle login successful event.
     */
    public function handleLogin(Login $event): void
    {
        try {
            LoginIntento::create([
                'email' => $event->user->email,
                'exitoso' => true,
                'ip' => request()->ip(),
                'created_at' => now(),
            ]);

            // Registrar también en auditoría
            event(new \App\Events\AccionAuditable(
                'login',
                'Usuario',
                $event->user->id,
                $event->user->id
            ));
        } catch (\Exception $e) {
            Log::error('Error al registrar login exitoso', [
                'email' => $event->user->email ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle failed login event.
     */
    public function handleFailed(Failed $event): void
    {
        try {
            LoginIntento::create([
                'email' => $event->credentials['email'] ?? 'unknown',
                'exitoso' => false,
                'ip' => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error al registrar login fallido', [
                'email' => $event->credentials['email'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
        }
    }
}

