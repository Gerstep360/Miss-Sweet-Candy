<?php

namespace App\Services;

use App\Models\Auditoria;
use App\Models\LoginIntento;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnomaliaService
{
    /**
     * Detecta anomalías en el sistema
     */
    public function detectarAnomalias(): array
    {
        $anomalias = [];

        // 1. Múltiples intentos de login fallidos desde la misma IP
        $anomalias = array_merge($anomalias, $this->detectarIntentosLoginFallidos());

        // 2. Acciones inusuales en horarios no laborales
        $anomalias = array_merge($anomalias, $this->detectarActividadHorarioNoLaboral());

        // 3. Eliminaciones masivas de registros
        $anomalias = array_merge($anomalias, $this->detectarEliminacionesMasivas());

        // 4. Accesos desde IPs no autorizadas
        $anomalias = array_merge($anomalias, $this->detectarIpsNoAutorizadas());

        // 5. Cambios en datos críticos
        $anomalias = array_merge($anomalias, $this->detectarCambiosDatosCriticos());

        // 6. Actividad inusual de un usuario
        $anomalias = array_merge($anomalias, $this->detectarActividadInusualUsuario());

        return $anomalias;
    }

    /**
     * Detecta múltiples intentos de login fallidos desde la misma IP
     */
    protected function detectarIntentosLoginFallidos(): array
    {
        $anomalias = [];
        $limite = 5; // Intentos fallidos permitidos
        $ventanaTiempo = 15; // minutos

        $intentos = LoginIntento::fallidos()
            ->where('created_at', '>=', Carbon::now()->subMinutes($ventanaTiempo))
            ->select('ip', DB::raw('COUNT(*) as intentos'))
            ->groupBy('ip')
            ->having('intentos', '>', $limite)
            ->get();

        foreach ($intentos as $intento) {
            $anomalias[] = [
                'tipo' => 'intentos_login_fallidos',
                'severidad' => 'alto',
                'descripcion' => "Múltiples intentos de login fallidos desde IP {$intento->ip} ({$intento->intentos} intentos en {$ventanaTiempo} minutos)",
                'ip' => $intento->ip,
                'detalles' => [
                    'intentos' => $intento->intentos,
                    'ventana_tiempo' => $ventanaTiempo,
                ],
                'fecha' => Carbon::now(),
            ];
        }

        return $anomalias;
    }

    /**
     * Detecta actividad en horarios no laborales
     */
    protected function detectarActividadHorarioNoLaboral(): array
    {
        $anomalias = [];
        
        // Horario laboral: 8:00 - 22:00 (configurable)
        $horaInicio = 8;
        $horaFin = 22;

        $actividad = Auditoria::where(function($query) use ($horaInicio, $horaFin) {
            $query->whereRaw('HOUR(created_at) < ?', [$horaInicio])
                  ->orWhereRaw('HOUR(created_at) >= ?', [$horaFin]);
        })
        ->where('created_at', '>=', Carbon::now()->subDay())
        ->whereIn('accion', ['eliminar', 'delete', 'destroy', 'eliminado', 'editar', 'update'])
        ->get();

        if ($actividad->count() > 10) {
            $anomalias[] = [
                'tipo' => 'actividad_horario_no_laboral',
                'severidad' => 'medio',
                'descripcion' => "Actividad inusual detectada fuera del horario laboral ({$actividad->count()} acciones)",
                'detalles' => [
                    'acciones' => $actividad->count(),
                    'horario_laboral' => "{$horaInicio}:00 - {$horaFin}:00",
                ],
                'fecha' => Carbon::now(),
            ];
        }

        return $anomalias;
    }

    /**
     * Detecta eliminaciones masivas de registros
     */
    protected function detectarEliminacionesMasivas(): array
    {
        $anomalias = [];
        $limite = 10; // Eliminaciones en un período corto

        $eliminaciones = Auditoria::eliminaciones()
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->select('usuario_id', 'entidad', DB::raw('COUNT(*) as total'))
            ->groupBy('usuario_id', 'entidad')
            ->having('total', '>', $limite)
            ->with('usuario')
            ->get();

        foreach ($eliminaciones as $eliminacion) {
            $anomalias[] = [
                'tipo' => 'eliminaciones_masivas',
                'severidad' => 'alto',
                'descripcion' => "Eliminaciones masivas detectadas: {$eliminacion->total} registros de {$eliminacion->entidad} eliminados por " . ($eliminacion->usuario->name ?? 'Usuario desconocido'),
                'usuario_id' => $eliminacion->usuario_id,
                'entidad' => $eliminacion->entidad,
                'detalles' => [
                    'total_eliminaciones' => $eliminacion->total,
                    'periodo' => '1 hora',
                ],
                'fecha' => Carbon::now(),
            ];
        }

        return $anomalias;
    }

    /**
     * Detecta accesos desde IPs no autorizadas
     */
    protected function detectarIpsNoAutorizadas(): array
    {
        $anomalias = [];
        
        // Obtener IPs conocidas/confiables (puede venir de configuración)
        $ipsConocidas = config('auditoria.ips_autorizadas', []);
        
        if (empty($ipsConocidas)) {
            // Si no hay configuración, obtener IPs más frecuentes de los últimos 30 días
            $ipsConocidas = Auditoria::where('created_at', '>=', Carbon::now()->subDays(30))
                ->select('ip', DB::raw('COUNT(*) as total'))
                ->groupBy('ip')
                ->orderByDesc('total')
                ->limit(10)
                ->pluck('ip')
                ->toArray();
        }

        // Buscar IPs nuevas en las últimas 24 horas
        $ipsNuevas = Auditoria::where('created_at', '>=', Carbon::now()->subDay())
            ->whereNotIn('ip', $ipsConocidas)
            ->whereNotNull('ip')
            ->select('ip', DB::raw('COUNT(*) as total'))
            ->groupBy('ip')
            ->get();

        foreach ($ipsNuevas as $ipNueva) {
            if ($ipNueva->total > 5) { // Más de 5 acciones desde IP nueva
                $anomalias[] = [
                    'tipo' => 'ip_no_autorizada',
                    'severidad' => 'medio',
                    'descripcion' => "Acceso desde IP no reconocida: {$ipNueva->ip} ({$ipNueva->total} acciones)",
                    'ip' => $ipNueva->ip,
                    'detalles' => [
                        'acciones' => $ipNueva->total,
                        'periodo' => '24 horas',
                    ],
                    'fecha' => Carbon::now(),
                ];
            }
        }

        return $anomalias;
    }

    /**
     * Detecta cambios en datos críticos
     */
    protected function detectarCambiosDatosCriticos(): array
    {
        $anomalias = [];
        
        // Entidades críticas
        $entidadesCriticas = ['Usuario', 'Role', 'Permission', 'Producto'];
        
        // Acciones críticas
        $accionesCriticas = ['editar', 'update', 'eliminar', 'delete', 'destroy'];

        $cambios = Auditoria::whereIn('entidad', $entidadesCriticas)
            ->whereIn('accion', $accionesCriticas)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->with('usuario')
            ->get();

        foreach ($cambios as $cambio) {
            $anomalias[] = [
                'tipo' => 'cambio_dato_critico',
                'severidad' => 'alto',
                'descripcion' => "Cambio en dato crítico: {$cambio->accion} en {$cambio->entidad} #{$cambio->entidad_id} por " . ($cambio->usuario->name ?? 'Usuario desconocido'),
                'usuario_id' => $cambio->usuario_id,
                'entidad' => $cambio->entidad,
                'entidad_id' => $cambio->entidad_id,
                'detalles' => [
                    'accion' => $cambio->accion,
                    'ip' => $cambio->ip,
                ],
                'fecha' => $cambio->created_at,
            ];
        }

        return $anomalias;
    }

    /**
     * Detecta actividad inusual de un usuario
     */
    protected function detectarActividadInusualUsuario(): array
    {
        $anomalias = [];
        $limiteAcciones = 100; // Acciones en una hora

        $actividad = Auditoria::where('created_at', '>=', Carbon::now()->subHour())
            ->select('usuario_id', DB::raw('COUNT(*) as total'))
            ->groupBy('usuario_id')
            ->having('total', '>', $limiteAcciones)
            ->with('usuario')
            ->get();

        foreach ($actividad as $act) {
            $anomalias[] = [
                'tipo' => 'actividad_inusual_usuario',
                'severidad' => 'medio',
                'descripcion' => "Actividad inusual detectada: {$act->total} acciones en 1 hora por " . ($act->usuario->name ?? 'Usuario desconocido'),
                'usuario_id' => $act->usuario_id,
                'detalles' => [
                    'acciones' => $act->total,
                    'periodo' => '1 hora',
                ],
                'fecha' => Carbon::now(),
            ];
        }

        return $anomalias;
    }

    /**
     * Obtiene anomalías con filtros
     */
    public function obtenerAnomalias(array $filtros = []): array
    {
        // Por ahora, ejecutamos detección en tiempo real
        // En producción, podrías almacenar anomalías en una tabla
        $anomalias = $this->detectarAnomalias();

        // Aplicar filtros
        if (isset($filtros['severidad'])) {
            $anomalias = array_filter($anomalias, function($a) use ($filtros) {
                return $a['severidad'] === $filtros['severidad'];
            });
        }

        if (isset($filtros['tipo'])) {
            $anomalias = array_filter($anomalias, function($a) use ($filtros) {
                return $a['tipo'] === $filtros['tipo'];
            });
        }

        // Ordenar por severidad y fecha
        usort($anomalias, function($a, $b) {
            $severidadOrden = ['critico' => 4, 'alto' => 3, 'medio' => 2, 'bajo' => 1];
            $ordenA = $severidadOrden[$a['severidad']] ?? 0;
            $ordenB = $severidadOrden[$b['severidad']] ?? 0;
            
            if ($ordenA !== $ordenB) {
                return $ordenB - $ordenA;
            }
            
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });

        return array_values($anomalias);
    }

    /**
     * Registra una anomalía en el log del sistema
     */
    public function registrarAnomalia(array $anomalia): void
    {
        Log::warning('Anomalía detectada', $anomalia);
        
        // TODO: Opcionalmente, guardar en una tabla de anomalías
        // Anomalia::create($anomalia);
    }
}

