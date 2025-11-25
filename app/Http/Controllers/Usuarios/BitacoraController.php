<?php

namespace App\Http\Controllers\Usuarios;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    // Muestra la lista de auditorías (bitácora)
    public function index(Request $request)
    {
        if (!auth()->user()->can('ver-bitacora')) {
            abort(403, 'No tienes permiso para ver la bitácora.');
        }
        $auditorias = Auditoria::with('usuario')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.bitacora.index', compact('auditorias'));
    }

    // Muestra el detalle de una acción de auditoría
    public function show($id)
    {
        if (!auth()->user()->can('ver-bitacora')) {
            abort(403, 'No tienes permiso para ver la bitácora.');
        }
        $auditoria = Auditoria::with('usuario')->findOrFail($id);

        return view('admin.bitacora.show', compact('auditoria'));
    }

    // Método estático para registrar acciones en la bitácora
    public static function registrar($accion, $entidad, $entidad_id = null, $usuario_id = null, $request = null)
    {
        $req = $request ?? request();
        
        Auditoria::create([
            'usuario_id' => $usuario_id ?? (auth()->check() ? auth()->id() : null),
            'accion'     => $accion,
            'entidad'    => $entidad,
            'entidad_id' => $entidad_id ?? 0,
            'ip'         => self::obtenerIpReal($req),
            'user_agent' => self::formatearUserAgent($req->userAgent()),
            'created_at' => now(),
        ]);
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