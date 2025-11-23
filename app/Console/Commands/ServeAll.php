<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServeAll extends Command
{
    // Comando corto y claro (puedes cambiarlo a "app:serve-all" si prefieres)
    protected $signature = 'serve:all
        {--ip= : IP para HTTP (por defecto se intenta detectar; fallback 0.0.0.0)}
        {--port=8000 : Puerto HTTP}
        {--ws-ip= : IP para Reverb (por defecto usa la misma IP de HTTP)}
        {--ws-port=8080 : Puerto WS (Reverb)}';

    protected $description = 'Levanta HTTP (serve) y Reverb en un solo comando';
    protected $aliases = ['go']; // opcional: ahora también puedes usar "php artisan go"

    public function handle(): int
    {
        // 1) Detectar IP local (si no se puede, usar 0.0.0.0)
        $detectedIp = @gethostbyname(gethostname());
        if (!filter_var($detectedIp, FILTER_VALIDATE_IP) || str_starts_with($detectedIp, '127.')) {
            $detectedIp = '0.0.0.0';
        }

        // 2) Opciones (con defaults)
        $ip     = $this->option('ip') ?: $detectedIp;   // HTTP
        $port   = (string)($this->option('port') ?? '8000');
        $wsIp   = $this->option('ws-ip') ?: $ip;        // Reverb usa misma IP si no se indica
        $wsPort = (string)($this->option('ws-port') ?? '8080');

        $this->info("HTTP → http://{$ip}:{$port}");
        $this->info("WS   → ws://{$wsIp}:{$wsPort}");

        // 3) Procesos (usar PHP_BINARY por si el binario no es "php")
        $http = Process::fromShellCommandline(
            PHP_BINARY." artisan serve --host={$ip} --port={$port}",
            base_path()
        );
        $ws = Process::fromShellCommandline(
            PHP_BINARY." artisan reverb:start --host={$wsIp} --port={$wsPort}",
            base_path()
        );

        foreach ([$http, $ws] as $p) {
            $p->setTimeout(null);
            $p->start(function ($type, $buffer) { $this->output->write($buffer); });
        }

        // 4) Mantener ambos vivos; si uno cae, intentamos parar el otro
        while ($http->isRunning() && $ws->isRunning()) {
            usleep(250000);
        }

        // Parar el que quede vivo (graceful)
        if ($http->isRunning()) { $http->stop(1); }
        if ($ws->isRunning())   { $ws->stop(1); }

        return ($http->isSuccessful() && $ws->isSuccessful()) ? self::SUCCESS : self::FAILURE;
    }
}
