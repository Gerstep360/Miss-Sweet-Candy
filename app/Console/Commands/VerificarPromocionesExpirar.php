<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Promocion;
use App\Models\User;
use App\Models\Notificacion;
use App\Mail\PromocionPorExpirar;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class VerificarPromocionesExpirar extends Command
{
    protected $signature = 'promociones:verificar-expiracion';
    protected $description = 'Verifica promociones próximas a expirar y envía notificaciones';

    public function handle()
    {
        $this->info('Verificando promociones próximas a expirar...');

        $hoy = Carbon::now('America/La_Paz');
        $tresDias = $hoy->copy()->addDays(3);

        // Buscar promociones que expiran en 3 días o menos
        $promociones = Promocion::where('activo', true)
            ->whereNotNull('fecha_fin')
            ->whereBetween('fecha_fin', [$hoy->startOfDay(), $tresDias->endOfDay()])
            ->get();

        if ($promociones->isEmpty()) {
            $this->info('No hay promociones próximas a expirar.');
            return 0;
        }

        $this->info("Encontradas {$promociones->count()} promociones próximas a expirar.");

        foreach ($promociones as $promocion) {
            $diasRestantes = max(0, $hoy->diffInDays($promocion->fecha_fin, false));
            
            $this->info("- {$promocion->nombre}: {$diasRestantes} días restantes");

            // Crear notificación
            $this->crearNotificacion($promocion, $diasRestantes);

            // Enviar email
            $this->enviarEmail($promocion, $diasRestantes);
        }

        $this->info('✓ Verificación completada.');
        return 0;
    }

    private function crearNotificacion(Promocion $promocion, int $diasRestantes)
    {
        $mensaje = "La promoción '{$promocion->nombre}' expira en {$diasRestantes} " . 
                   ($diasRestantes == 1 ? 'día' : 'días');

        // Notificar a administradores y cajeros
        $usuarios = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['administrador', 'cajero']);
        })->get();

        foreach ($usuarios as $usuario) {
            // Verificar si ya existe notificación reciente (últimas 24 horas)
            $existeNotificacion = Notificacion::where('user_id', $usuario->id)
                ->where('tipo', 'promocion_expira')
                ->where('mensaje', 'like', "%{$promocion->nombre}%")
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->exists();

            if (!$existeNotificacion) {
                Notificacion::create([
                    'user_id' => $usuario->id,
                    'tipo' => 'promocion_expira',
                    'titulo' => '⏰ Promoción Por Expirar',
                    'mensaje' => $mensaje,
                    'leido' => false,
                    'url' => route('promociones.show', $promocion->id),
                ]);
            }
        }
    }

    private function enviarEmail(Promocion $promocion, int $diasRestantes)
    {
        try {
            $usuarios = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['administrador', 'cajero']);
            })->get();

            foreach ($usuarios as $usuario) {
                if ($usuario->email) {
                    Mail::to($usuario->email)->send(new PromocionPorExpirar($promocion, $diasRestantes));
                }
            }
        } catch (\Exception $e) {
            $this->error('Error al enviar email: ' . $e->getMessage());
        }
    }
}
