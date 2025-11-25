<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BitacoraController extends Controller
{
    /**
     * Muestra la lista de auditorías (bitácora) con filtros avanzados
     */
    public function index(Request $request)
    {
        $query = Auditoria::with('usuario');

        // 🔍 Filtros
        if ($request->filled('usuario_id')) {
            $query->porUsuario($request->usuario_id);
        }

        if ($request->filled('accion')) {
            $query->porAccion($request->accion);
        }

        if ($request->filled('entidad')) {
            $query->porEntidad($request->entidad);
        }

        if ($request->filled('ip')) {
            $query->porIp($request->ip);
        }

        if ($request->filled('desde') && $request->filled('hasta')) {
            $desde = Carbon::parse($request->desde)->startOfDay();
            $hasta = Carbon::parse($request->hasta)->endOfDay();
            $query->porRangoFechas($desde, $hasta);
        }

        // Ordenar por fecha más reciente
        $query->orderByDesc('created_at');

        // Paginación
        $auditorias = $query->paginate(50);

        // 📊 Estadísticas rápidas
        $stats = [
            'total_hoy' => Auditoria::whereDate('created_at', today())->count(),
            'total_semana' => Auditoria::where('created_at', '>=', now()->startOfWeek())->count(),
            'usuarios_activos' => Auditoria::whereDate('created_at', today())->distinct('usuario_id')->count(),
        ];

        // Datos para filtros
        $usuarios = User::orderBy('name')->get();
        $acciones = Auditoria::distinct()->pluck('accion')->sort()->values();
        $entidades = Auditoria::distinct()->pluck('entidad')->sort()->values();

        return view('admin.bitacora.index', compact('auditorias', 'usuarios', 'acciones', 'entidades', 'stats'));
    }

    /**
     * Muestra el detalle de una acción de auditoría
     */
    public function show($id)
    {
        $auditoria = Auditoria::with('usuario')->findOrFail($id);

        // Obtener registros relacionados (mismo usuario, misma entidad, mismo día)
        $relacionados = Auditoria::where('id', '!=', $id)
            ->where(function($q) use ($auditoria) {
                $q->where('usuario_id', $auditoria->usuario_id)
                  ->orWhere('entidad', $auditoria->entidad)
                  ->orWhere('ip', $auditoria->ip);
            })
            ->whereDate('created_at', $auditoria->created_at->toDateString())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.bitacora.show', compact('auditoria', 'relacionados'));
    }

    /**
     * Exportar bitácora a Excel (stub - pendiente implementación)
     */
    public function exportarExcel(Request $request)
    {
        // TODO: Implementar usando Maatwebsite/Excel
        $query = Auditoria::with('usuario');
        
        // Aplicar los mismos filtros que index()
        if ($request->filled('usuario_id')) $query->porUsuario($request->usuario_id);
        if ($request->filled('accion')) $query->porAccion($request->accion);
        if ($request->filled('entidad')) $query->porEntidad($request->entidad);
        
        $auditorias = $query->limit(10000)->get();

        return response()->json([
            'message' => 'Exportación Excel pendiente de implementar',
            'registros' => $auditorias->count()
        ]);
    }

    /**
     * Exportar bitácora a PDF (stub - pendiente implementación)
     */
    public function exportarPdf(Request $request)
    {
        // TODO: Implementar usando DomPDF
        $query = Auditoria::with('usuario');
        
        // Aplicar filtros
        if ($request->filled('usuario_id')) $query->porUsuario($request->usuario_id);
        if ($request->filled('accion')) $query->porAccion($request->accion);
        
        $auditorias = $query->limit(10000)->get();

        return response()->json([
            'message' => 'Exportación PDF pendiente de implementar',
            'registros' => $auditorias->count()
        ]);
    }

    /**
     * Método estático para registrar acciones en la bitácora
     * ⚠️ MANTENER ESTE MÉTODO - Se usa en todo el código
     * Ahora usa eventos para registro automático
     */
    public static function registrar($accion, $entidad, $entidad_id = null, $usuario_id = null, $request = null)
    {
        // Disparar evento para registro automático
        event(new \App\Events\AccionAuditable(
            $accion,
            $entidad,
            $entidad_id,
            $usuario_id,
            $request ? self::obtenerIpReal($request) : null, // Use existing helper
            $request ? self::formatearUserAgent($request->userAgent()) : null, // Use existing helper
            $request
        ));
    }

    /**
     * Obtiene la IP real del cliente, considerando proxies
     */
    private static function obtenerIpReal($request)
    {
        // Cloudflare
        if ($ip = $request->header('CF-Connecting-IP')) {
            return $ip;
        }
        
        // Nginx proxy o similar
        if ($ip = $request->header('X-Real-IP')) {
            return $ip;
        }
        
        // Proxy estándar (puede tener múltiples IPs)
        if ($forwarded = $request->header('X-Forwarded-For')) {
            $ips = array_map('trim', explode(',', $forwarded));
            // La primera IP es la del cliente original
            $ip = $ips[0];
            if ($ip !== '127.0.0.1' && $ip !== '::1') {
                return $ip;
            }
        }
        
        // Obtener IP del servidor
        $ip = $request->ip();
        
        // Si es localhost, intentar obtener la IP de red local
        if ($ip === '127.0.0.1' || $ip === '::1') {
            // En Windows, intentar obtener la IP local
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $output = shell_exec('ipconfig | findstr /i "IPv4"');
                if ($output && preg_match('/(\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                    // Retornar la primera IP local encontrada
                    $localIp = $matches[1];
                    if ($localIp !== '127.0.0.1') {
                        return $localIp . ' (Local)';
                    }
                }
            } else {
                // En Linux/Mac
                $output = shell_exec("hostname -I | awk '{print $1}'");
                if ($output) {
                    $localIp = trim($output);
                    if ($localIp && $localIp !== '127.0.0.1') {
                        return $localIp . ' (Local)';
                    }
                }
            }
            
            return '127.0.0.1 (localhost)';
        }
        
        return $ip;
    }

    /**
     * Formatea el User Agent a texto legible
     */
    private static function formatearUserAgent($userAgent)
    {
        if (!$userAgent) {
            return 'Desconocido';
        }

        // Detectar navegador
        $navegador = 'Desconocido';
        $version = '';
        
        if (preg_match('/OPR\/(\d+)/', $userAgent, $matches)) {
            $navegador = 'Opera';
            $version = 'ver.' . $matches[1];
        } elseif (preg_match('/Edg\/(\d+)/', $userAgent, $matches)) {
            $navegador = 'Edge';
            $version = 'ver.' . $matches[1];
        } elseif (preg_match('/Chrome\/(\d+)/', $userAgent, $matches) && !strpos($userAgent, 'Edg')) {
            $navegador = 'Chrome';
            $version = 'ver.' . $matches[1];
        } elseif (preg_match('/Firefox\/(\d+)/', $userAgent, $matches)) {
            $navegador = 'Firefox';
            $version = 'ver.' . $matches[1];
        } elseif (preg_match('/Safari\/(\d+)/', $userAgent, $matches) && !strpos($userAgent, 'Chrome')) {
            $navegador = 'Safari';
            $version = 'ver.' . $matches[1];
        }

        // Detectar sistema operativo
        $so = 'Desconocido';
        
        if (preg_match('/Windows NT 10\.0/', $userAgent)) {
            $so = 'Windows 10/11';
        } elseif (preg_match('/Windows NT 6\.3/', $userAgent)) {
            $so = 'Windows 8.1';
        } elseif (preg_match('/Windows NT 6\.2/', $userAgent)) {
            $so = 'Windows 8';
        } elseif (preg_match('/Windows NT 6\.1/', $userAgent)) {
            $so = 'Windows 7';
        } elseif (preg_match('/Mac OS X/', $userAgent)) {
            $so = 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $so = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $so = 'Android';
        } elseif (preg_match('/iPhone|iPad/', $userAgent)) {
            $so = 'iOS';
        }

        return "$navegador $version en $so";
    }
}