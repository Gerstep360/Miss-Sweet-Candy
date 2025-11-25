<?php

namespace App\Listeners;

use App\Events\AccionAuditable;
use App\Models\Auditoria;
use Illuminate\Support\Facades\Log;

class RegistrarAuditoria
{
    /**
     * Handle the event.
     */
    public function handle(AccionAuditable $event): void
    {
        try {
            Auditoria::create([
                'usuario_id' => $event->usuarioId,
                'accion' => $event->accion,
                'entidad' => $event->entidad,
                'entidad_id' => $event->entidadId ?? 0,
                'ip' => $event->ip,
                'user_agent' => $event->userAgent,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // E1: Error al registrar en tabla de auditorías
            // Fallback: registrar en archivo de log
            Log::error('Error al registrar auditoría en BD', [
                'accion' => $event->accion,
                'entidad' => $event->entidad,
                'entidad_id' => $event->entidadId,
                'usuario_id' => $event->usuarioId,
                'error' => $e->getMessage(),
            ]);

            // Intentar reinsertar en segundo plano (3 reintentos)
            $this->reintentarRegistro($event, 3);
        }
    }

    /**
     * Reintenta el registro de auditoría
     */
    protected function reintentarRegistro(AccionAuditable $event, int $intentos): void
    {
        if ($intentos <= 0) {
            Log::critical('No se pudo registrar auditoría después de múltiples intentos', [
                'accion' => $event->accion,
                'entidad' => $event->entidad,
            ]);
            return;
        }

        try {
            Auditoria::create([
                'usuario_id' => $event->usuarioId,
                'accion' => $event->accion,
                'entidad' => $event->entidad,
                'entidad_id' => $event->entidadId ?? 0,
                'ip' => $event->ip,
                'user_agent' => $event->userAgent,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Esperar un poco antes de reintentar
            sleep(1);
            $this->reintentarRegistro($event, $intentos - 1);
        }
    }
}

