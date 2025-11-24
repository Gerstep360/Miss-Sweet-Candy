<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class BackupService
{
    protected $directorioBackups = 'backups';
    protected $configFile = 'backup_config.json';

    /**
     * Crea un backup según el tipo especificado
     */
    public function crearBackup(string $tipo): array
    {
        try {
            $timestamp = Carbon::now()->format('Ymd_His');
            $nombreArchivo = "backup_{$timestamp}_{$tipo}";

            switch ($tipo) {
                case 'completo':
                    return $this->crearBackupCompleto($nombreArchivo);
                case 'base_datos':
                    return $this->crearBackupBaseDatos($nombreArchivo);
                case 'archivos':
                    return $this->crearBackupArchivos($nombreArchivo);
                default:
                    throw new Exception("Tipo de backup no válido: {$tipo}");
            }
        } catch (Exception $e) {
            Log::error('Error al crear backup: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crea backup completo (base de datos + archivos)
     */
    protected function crearBackupCompleto(string $nombreBase): array
    {
        $dbBackup = $this->crearBackupBaseDatos($nombreBase . '_db');
        $filesBackup = $this->crearBackupArchivos($nombreBase . '_files');

        return [
            'archivo' => $nombreBase,
            'tipo' => 'completo',
            'base_datos' => $dbBackup['archivo'],
            'archivos' => $filesBackup['archivo'],
            'tamaño' => $dbBackup['tamaño'] + $filesBackup['tamaño'],
            'fecha' => Carbon::now(),
        ];
    }

    /**
     * Crea backup solo de base de datos
     */
    protected function crearBackupBaseDatos(string $nombreBase): array
    {
        $nombreArchivo = $nombreBase . '.sql';
        $rutaCompleta = $this->directorioBackups . '/' . $nombreArchivo;

        // Obtener configuración de base de datos
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        // Crear directorio si no existe
        if (!Storage::exists($this->directorioBackups)) {
            Storage::makeDirectory($this->directorioBackups);
        }

        // Ruta temporal para el dump
        $rutaTemporal = storage_path('app/temp_' . $nombreArchivo);

        // Ejecutar mysqldump
        $comando = sprintf(
            'mysqldump -h %s -P %s -u %s -p%s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($rutaTemporal)
        );

        exec($comando, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception('Error al ejecutar mysqldump: ' . implode("\n", $output));
        }

        // Comprimir el archivo SQL
        $rutaComprimida = $rutaTemporal . '.gz';
        $archivo = gzopen($rutaComprimida, 'w9');
        gzwrite($archivo, file_get_contents($rutaTemporal));
        gzclose($archivo);
        unlink($rutaTemporal);

        // Mover a storage
        $contenido = file_get_contents($rutaComprimida);
        Storage::put($rutaCompleta . '.gz', $contenido);
        unlink($rutaComprimida);

        // Calcular checksum
        $checksum = md5_file(storage_path('app/' . $rutaCompleta . '.gz'));

        // Guardar metadata
        $metadata = [
            'tipo' => 'base_datos',
            'fecha' => Carbon::now()->toIso8601String(),
            'checksum' => $checksum,
            'tamaño' => Storage::size($rutaCompleta . '.gz'),
        ];

        Storage::put($rutaCompleta . '.meta.json', json_encode($metadata));

        return [
            'archivo' => $nombreArchivo . '.gz',
            'tipo' => 'base_datos',
            'tamaño' => $metadata['tamaño'],
            'checksum' => $checksum,
            'fecha' => Carbon::now(),
        ];
    }

    /**
     * Crea backup solo de archivos críticos
     */
    protected function crearBackupArchivos(string $nombreBase): array
    {
        $nombreArchivo = $nombreBase . '.tar.gz';
        $rutaCompleta = $this->directorioBackups . '/' . $nombreArchivo;

        // Directorios a respaldar
        $directorios = [
            'storage/app/public',
            'config',
            '.env', // ⚠️ Cuidado con información sensible
        ];

        // Crear directorio si no existe
        if (!Storage::exists($this->directorioBackups)) {
            Storage::makeDirectory($this->directorioBackups);
        }

        // Ruta temporal
        $rutaTemporal = storage_path('app/temp_' . $nombreArchivo);

        // Crear archivo tar.gz
        $comando = sprintf(
            'cd %s && tar -czf %s %s 2>&1',
            escapeshellarg(base_path()),
            escapeshellarg($rutaTemporal),
            implode(' ', array_map('escapeshellarg', $directorios))
        );

        exec($comando, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception('Error al crear backup de archivos: ' . implode("\n", $output));
        }

        // Mover a storage
        $contenido = file_get_contents($rutaTemporal);
        Storage::put($rutaCompleta, $contenido);
        unlink($rutaTemporal);

        // Calcular checksum
        $checksum = md5_file(storage_path('app/' . $rutaCompleta));

        // Guardar metadata
        $metadata = [
            'tipo' => 'archivos',
            'fecha' => Carbon::now()->toIso8601String(),
            'checksum' => $checksum,
            'tamaño' => Storage::size($rutaCompleta),
        ];

        Storage::put($rutaCompleta . '.meta.json', json_encode($metadata));

        return [
            'archivo' => $nombreArchivo,
            'tipo' => 'archivos',
            'tamaño' => $metadata['tamaño'],
            'checksum' => $checksum,
            'fecha' => Carbon::now(),
        ];
    }

    /**
     * Lista todos los backups disponibles
     */
    public function listarBackups(): array
    {
        if (!Storage::exists($this->directorioBackups)) {
            return [];
        }

        $archivos = Storage::files($this->directorioBackups);
        $backups = [];

        foreach ($archivos as $archivo) {
            // Solo archivos de backup, no metadata
            if (str_ends_with($archivo, '.meta.json')) {
                continue;
            }

            $metadataPath = $archivo . '.meta.json';
            $metadata = Storage::exists($metadataPath)
                ? json_decode(Storage::get($metadataPath), true)
                : null;

            $backups[] = [
                'archivo' => basename($archivo),
                'ruta' => $archivo,
                'tipo' => $metadata['tipo'] ?? $this->detectarTipo($archivo),
                'tamaño' => Storage::size($archivo),
                'fecha' => $metadata['fecha'] ?? Carbon::createFromTimestamp(Storage::lastModified($archivo))->toIso8601String(),
                'checksum' => $metadata['checksum'] ?? null,
            ];
        }

        // Ordenar por fecha más reciente
        usort($backups, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });

        return $backups;
    }

    /**
     * Detecta el tipo de backup por el nombre del archivo
     */
    protected function detectarTipo(string $archivo): string
    {
        if (str_contains($archivo, '_db') || str_ends_with($archivo, '.sql.gz')) {
            return 'base_datos';
        }
        if (str_contains($archivo, '_files') || str_ends_with($archivo, '.tar.gz')) {
            return 'archivos';
        }
        return 'completo';
    }

    /**
     * Obtiene la ruta completa de un backup
     */
    public function obtenerRutaBackup(string $archivo): string
    {
        return $this->directorioBackups . '/' . basename($archivo);
    }

    /**
     * Elimina un backup
     */
    public function eliminarBackup(string $archivo): bool
    {
        $ruta = $this->obtenerRutaBackup($archivo);
        
        if (!Storage::exists($ruta)) {
            throw new Exception('Backup no encontrado');
        }

        Storage::delete($ruta);
        
        // Eliminar metadata si existe
        $metadataPath = $ruta . '.meta.json';
        if (Storage::exists($metadataPath)) {
            Storage::delete($metadataPath);
        }

        return true;
    }

    /**
     * Restaura desde un backup
     */
    public function restaurarBackup(string $archivo): bool
    {
        $ruta = $this->obtenerRutaBackup($archivo);
        
        if (!Storage::exists($ruta)) {
            throw new Exception('Backup no encontrado');
        }

        // Verificar integridad
        $metadataPath = $ruta . '.meta.json';
        if (Storage::exists($metadataPath)) {
            $metadata = json_decode(Storage::get($metadataPath), true);
            $checksumActual = md5_file(storage_path('app/' . $ruta));
            
            if ($checksumActual !== $metadata['checksum']) {
                throw new Exception('El archivo de backup está corrupto (checksum no coincide)');
            }
        }

        $tipo = $this->detectarTipo($archivo);

        switch ($tipo) {
            case 'base_datos':
                return $this->restaurarBaseDatos($ruta);
            case 'archivos':
                return $this->restaurarArchivos($ruta);
            default:
                throw new Exception('Tipo de backup no soportado para restauración automática');
        }
    }

    /**
     * Restaura la base de datos desde un backup
     */
    protected function restaurarBaseDatos(string $ruta): bool
    {
        // Descomprimir
        $rutaTemporal = storage_path('app/temp_restore.sql');
        $archivoComprimido = storage_path('app/' . $ruta);
        
        $archivo = gzopen($archivoComprimido, 'r');
        $contenido = '';
        while (!gzeof($archivo)) {
            $contenido .= gzread($archivo, 8192);
        }
        gzclose($archivo);
        
        file_put_contents($rutaTemporal, $contenido);

        // Obtener configuración de base de datos
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        // Ejecutar restauración
        $comando = sprintf(
            'mysql -h %s -P %s -u %s -p%s %s < %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($rutaTemporal)
        );

        exec($comando, $output, $returnCode);
        unlink($rutaTemporal);

        if ($returnCode !== 0) {
            throw new Exception('Error al restaurar base de datos: ' . implode("\n", $output));
        }

        return true;
    }

    /**
     * Restaura archivos desde un backup
     */
    protected function restaurarArchivos(string $ruta): bool
    {
        $rutaCompleta = storage_path('app/' . $ruta);
        
        // Extraer archivos
        $comando = sprintf(
            'cd %s && tar -xzf %s 2>&1',
            escapeshellarg(base_path()),
            escapeshellarg($rutaCompleta)
        );

        exec($comando, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception('Error al restaurar archivos: ' . implode("\n", $output));
        }

        return true;
    }

    /**
     * Obtiene la configuración de backups automáticos
     */
    public function obtenerConfiguracion(): array
    {
        if (!Storage::exists($this->configFile)) {
            return [
                'frecuencia' => 'diaria',
                'hora' => '02:00',
                'tipo' => 'completo',
                'retencion' => 30,
                'notificar_email' => false,
                'activo' => false,
            ];
        }

        return json_decode(Storage::get($this->configFile), true);
    }

    /**
     * Configura backups automáticos
     */
    public function configurarAutomatico(array $config): bool
    {
        $config['activo'] = true;
        Storage::put($this->configFile, json_encode($config));
        return true;
    }

    /**
     * Verifica espacio disponible
     */
    public function verificarEspacio(): array
    {
        $espacioTotal = disk_total_space(storage_path('app'));
        $espacioLibre = disk_free_space(storage_path('app'));
        $espacioUsado = $espacioTotal - $espacioLibre;
        $porcentajeUsado = ($espacioUsado / $espacioTotal) * 100;

        return [
            'total' => $espacioTotal,
            'libre' => $espacioLibre,
            'usado' => $espacioUsado,
            'porcentaje_usado' => round($porcentajeUsado, 2),
            'suficiente' => $porcentajeUsado < 90, // Alerta si > 90%
        ];
    }
}


