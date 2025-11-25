<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class BackupAutomatico extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:automatico';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta backups automáticos según la configuración';

    protected $backupService;

    /**
     * Create a new command instance.
     */
    public function __construct(BackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = $this->backupService->obtenerConfiguracion();

        // Verificar si los backups automáticos están activos
        if (!($config['activo'] ?? false)) {
            $this->info('Backups automáticos desactivados.');
            return 0;
        }

        // Verificar espacio disponible
        $espacio = $this->backupService->verificarEspacio();
        if (!$espacio['suficiente']) {
            $this->error('Espacio insuficiente para crear backup. Espacio usado: ' . round($espacio['porcentaje_usado'], 2) . '%');
            Log::critical('Backup automático cancelado: espacio insuficiente', $espacio);
            
            // TODO: Enviar notificación al administrador
            return 1;
        }

        try {
            $this->info('Iniciando backup automático...');
            $resultado = $this->backupService->crearBackup($config['tipo'] ?? 'completo');
            
            $this->info("Backup creado exitosamente: {$resultado['archivo']}");
            $this->info("Tamaño: " . $this->formatearTamaño($resultado['tamaño']));

            // Limpiar backups antiguos según retención
            $this->limpiarBackupsAntiguos($config['retencion'] ?? 30);

            // Enviar notificación si está configurado
            if ($config['notificar_email'] ?? false) {
                $this->enviarNotificacion($resultado);
            }

            // Registrar en auditoría
            event(new \App\Events\AccionAuditable(
                'backup automatico',
                'Backup',
                null,
                null
            ));

            return 0;
        } catch (\Exception $e) {
            $this->error('Error al crear backup: ' . $e->getMessage());
            Log::error('Error en backup automático', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // TODO: Enviar notificación de error al administrador
            return 1;
        }
    }

    /**
     * Limpia backups antiguos según la política de retención
     */
    protected function limpiarBackupsAntiguos(int $retencion): void
    {
        $backups = $this->backupService->listarBackups();
        $fechaLimite = Carbon::now()->subDays($retencion);

        $eliminados = 0;
        foreach ($backups as $backup) {
            $fechaBackup = Carbon::parse($backup['fecha']);
            if ($fechaBackup->lt($fechaLimite)) {
                try {
                    $this->backupService->eliminarBackup($backup['archivo']);
                    $eliminados++;
                } catch (\Exception $e) {
                    $this->warn("No se pudo eliminar backup antiguo: {$backup['archivo']}");
                }
            }
        }

        if ($eliminados > 0) {
            $this->info("Se eliminaron {$eliminados} backups antiguos.");
        }
    }

    /**
     * Envía notificación por email
     */
    protected function enviarNotificacion(array $resultado): void
    {
        // TODO: Implementar envío de email
        // Mail::to(config('mail.admin_email'))->send(new BackupCompletado($resultado));
        $this->info('Notificación de backup completado (email pendiente de configurar)');
    }

    /**
     * Formatea el tamaño en bytes a formato legible
     */
    protected function formatearTamaño(int $bytes): string
    {
        $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($unidades) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $unidades[$i];
    }
}
