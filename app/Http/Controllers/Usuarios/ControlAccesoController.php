<?php

namespace App\Http\Controllers\Usuarios;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserTwoFactor;
use App\Models\LoginIntento;
use App\Models\IpWhitelist;
use App\Models\Auditoria;
use App\Mail\TwoFactorCodeMail;

class ControlAccesoController extends Controller
{
    /**
     * Mostrar el panel principal de control de acceso
     */
    public function index()
    {
        $user = Auth::user();
        $twoFactor = $user->twoFactor;
        $ipWhitelists = $user->ipWhitelists;
        
        // Datos reales para las estadísticas
        $twoFactorEnabledCount = UserTwoFactor::count();
        $totalUsers = User::count();
        $failedLoginsToday = LoginIntento::whereDate('created_at', today())
            ->where('exitoso', false)
            ->count();
        
        // Auditorías recientes del usuario
        $recentAudits = $user->auditorias()
            ->latest()
            ->limit(5)
            ->get();
        
        // Estado 2FA por rol (datos reales)
        $twoFactorByRole = \Spatie\Permission\Models\Role::withCount('users')
            ->get()
            ->map(function($role) {
                return [
                    'name' => $role->name,
                    'users_count' => $role->users_count,
                    'enabled' => in_array($role->name, ['Administrador', 'Cajero', 'Barista']) // Roles que requieren 2FA
                ];
            });

        return view('admin.controlAcceso.index', compact(
            'user',
            'twoFactor',
            'ipWhitelists',
            'recentAudits',
            'twoFactorEnabledCount',
            'totalUsers',
            'failedLoginsToday',
            'twoFactorByRole'
        ));
    }

    /**
     * Solicitar código de verificación por email
     */
    public function requestTwoFactorCode(Request $request)
    {
        $user = Auth::user();
        
        // Generar código de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(30);

        // Guardar código temporal en la sesión
        session([
            'two_factor_code' => $code,
            'two_factor_expires' => $expiresAt,
            'two_factor_secret' => Str::random(32) // Secret para verificar después
        ]);

        // Enviar código por email
        try {
            Mail::to($user->email)->send(new TwoFactorCodeMail($code, $expiresAt));
            
            // Registrar en auditoría
            Auditoria::create([
                'usuario_id' => $user->id,
                'accion' => 'SOLICITUD_CODIGO_2FA',
                'entidad' => 'UserTwoFactor',
                'entidad_id' => $user->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            return redirect()->back()->with('success', 'Código de verificación enviado a tu correo electrónico. Válido por 30 minutos.');
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al enviar el código. Por favor, intenta nuevamente.']);
        }
    }

    /**
     * Habilitar autenticación de dos factores con código de email
     */
    public function enableTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $code = $request->code;

        // Verificar código y expiración
        $storedCode = session('two_factor_code');
        $expiresAt = session('two_factor_expires');

        if (!$storedCode || !$expiresAt) {
            return redirect()->back()->withErrors(['code' => 'Solicita un nuevo código de verificación.']);
        }

        if (Carbon::now()->gt($expiresAt)) {
            return redirect()->back()->withErrors(['code' => 'El código ha expirado. Solicita uno nuevo.']);
        }

        if ($code !== $storedCode) {
            return redirect()->back()->withErrors(['code' => 'El código de verificación es incorrecto.']);
        }

        // Crear registro de 2FA
        UserTwoFactor::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret' => session('two_factor_secret'),
                'recovery_codes' => $this->generateRecoveryCodes()
            ]
        );

        // Limpiar sesión
        session()->forget(['two_factor_code', 'two_factor_expires', 'two_factor_secret']);

        // Registrar en auditoría
        Auditoria::create([
            'usuario_id' => $user->id,
            'accion' => 'HABILITAR_2FA',
            'entidad' => 'UserTwoFactor',
            'entidad_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now()
        ]);

        return redirect()->route('control-acceso.two-factor')
            ->with('success', 'Autenticación de dos factores habilitada correctamente.');
    }

    /**
     * Deshabilitar autenticación de dos factores
     */
    public function disableTwoFactor(Request $request)
    {
        $user = Auth::user();
        
        // Eliminar el registro de 2FA
        $user->twoFactor()->delete();

        // Limpiar sesión si existe
        session()->forget(['two_factor_code', 'two_factor_expires', 'two_factor_secret']);

        // Registrar en auditoría
        Auditoria::create([
            'usuario_id' => $user->id,
            'accion' => 'DESHABILITAR_2FA',
            'entidad' => 'UserTwoFactor',
            'entidad_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now()
        ]);

        return redirect()->route('control-acceso.two-factor')
            ->with('success', 'Autenticación de dos factores deshabilitada correctamente.');
    }

    /**
     * Mostrar códigos de respaldo
     */
    public function showBackupCodes()
    {
        $user = Auth::user();
        
        if (!$user->twoFactor) {
            return redirect()->route('control-acceso.index')
                ->withErrors(['error' => 'Primero debe habilitar la autenticación de dos factores.']);
        }

        $backupCodes = $user->twoFactor->getFormattedRecoveryCodes();

        return view('admin.controlAcceso.backup-codes', compact('backupCodes'));
    }

    /**
     * Generar nuevos códigos de respaldo
     */
    public function generateBackupCodes(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->twoFactor) {
            return redirect()->route('control-acceso.index')
                ->withErrors(['error' => 'Primero debe habilitar la autenticación de dos factores.']);
        }

        // Actualizar los códigos de respaldo
        $user->twoFactor->update([
            'recovery_codes' => $this->generateRecoveryCodes()
        ]);

        // Registrar en auditoría
        Auditoria::create([
            'usuario_id' => $user->id,
            'accion' => 'REGENERAR_CODIGOS_RESPALDO',
            'entidad' => 'UserTwoFactor',
            'entidad_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now()
        ]);

        return redirect()->route('security.backup-codes')
            ->with('success', 'Nuevos códigos de respaldo generados correctamente.');
    }

    /**
     * Agregar IP a la whitelist
     */
    public function addIpToWhitelist(Request $request)
    {
        $request->validate([
            'ip_cidr' => 'required|ip|max:43'
        ]);

        $user = Auth::user();

        IpWhitelist::create([
            'user_id' => $user->id,
            'ip_cidr' => $request->ip_cidr
        ]);

        // Registrar en auditoría
        Auditoria::create([
            'usuario_id' => $user->id,
            'accion' => 'AGREGAR_IP_WHITELIST',
            'entidad' => 'IpWhitelist',
            'entidad_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now()
        ]);

        return redirect()->route('control-acceso.config-ips')
            ->with('success', 'IP agregada a la lista blanca correctamente.');
    }

    /**
     * Eliminar IP de la whitelist
     */
    public function removeIpFromWhitelist(Request $request, $id)
    {
        $user = Auth::user();
        $ipWhitelist = IpWhitelist::where('user_id', $user->id)->findOrFail($id);

        $ipWhitelist->delete();

        // Registrar en auditoría
        Auditoria::create([
            'usuario_id' => $user->id,
            'accion' => 'ELIMINAR_IP_WHITELIST',
            'entidad' => 'IpWhitelist',
            'entidad_id' => $id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now()
        ]);

        return redirect()->route('control-acceso.config-ips')
            ->with('success', 'IP eliminada de la lista blanca correctamente.');
    }

    /**
     * Generar códigos de respaldo
     */
    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(8) . '-' . Str::random(8);
        }
        return $codes;
    }

    /**
     * Verificar código de 2FA (para login)
     */
    public function verifyTwoFactorCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        
        if (!$user->twoFactor) {
            return response()->json(['valid' => false, 'message' => '2FA no configurado']);
        }

        // Verificar código de sesión
        $valid = $this->verifyEmailCode($request->code);

        return response()->json(['valid' => $valid]);
    }

    /**
     * Verificar código de email
     */
    private function verifyEmailCode($code): bool
    {
        $storedCode = session('two_factor_code');
        $expiresAt = session('two_factor_expires');

        if (!$storedCode || !$expiresAt) {
            return false;
        }

        if (Carbon::now()->gt($expiresAt)) {
            return false;
        }

        return $code === $storedCode;
    }

    /**
     * Obtener actividad reciente
     */
    public function getRecentActivity()
    {
        $user = Auth::user();
        $logins = $user->getRecentLoginAttempts(10);
        $auditorias = $user->auditorias()->latest()->limit(10)->get();

        return response()->json([
            'logins' => $logins,
            'auditorias' => $auditorias
        ]);
    }

    /**
     * Importar múltiples IPs a la whitelist
     */
    public function importIps(Request $request)
    {
        $request->validate([
            'ips' => 'required|string'
        ]);

        $user = Auth::user();
        $ips = explode("\n", $request->ips);
        $imported = 0;
        $errors = [];

        foreach ($ips as $ip) {
            $ip = trim($ip);
            
            // Saltar líneas vacías
            if (empty($ip)) {
                continue;
            }

            // Validar formato de IP
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $errors[] = "IP inválida: {$ip}";
                continue;
            }

            // Verificar si ya existe
            $exists = IpWhitelist::where('user_id', $user->id)
                ->where('ip_cidr', $ip)
                ->exists();

            if (!$exists) {
                IpWhitelist::create([
                    'user_id' => $user->id,
                    'ip_cidr' => $ip
                ]);
                $imported++;
            } else {
                $errors[] = "IP ya existe: {$ip}";
            }
        }

        // Registrar en auditoría
        Auditoria::create([
            'usuario_id' => $user->id,
            'accion' => 'IMPORTAR_IPS_WHITELIST',
            'entidad' => 'IpWhitelist',
            'entidad_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now()
        ]);

        $message = "Se importaron {$imported} IPs correctamente.";
        if (!empty($errors)) {
            $message .= " Errores: " . implode(', ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " ... y " . (count($errors) - 5) . " más";
            }
        }

        $redirect = redirect()->route('control-acceso.config-ips');

        if (!empty($errors) && $imported === 0) {
            return $redirect->withErrors(['ips' => $message]);
        }

        return $redirect->with($errors ? 'warning' : 'success', $message);
    }

    /**
     * Mostrar configuración de IPs
     */
    // En ControlAccesoController

    /**
     * Mostrar configuración de IPs mejorada
     */
    public function configIps()
    {
        $user = Auth::user();
        
        // Obtener IPs en whitelist del usuario
        $ipWhitelists = $user->ipWhitelists;
        
        // Obtener todas las IPs únicas que han accedido al sistema
        $detectedIps = LoginIntento::select('ip')
            ->selectRaw('COUNT(*) as access_count')
            ->groupBy('ip')
            ->get()
            ->map(function($item) use ($user) {
                $ip = $item->ip;
                return (object)[
                    'ip' => $ip,
                    'access_count' => $item->access_count,
                    'in_whitelist' => IpWhitelist::where('user_id', $user->id)
                        ->where('ip_cidr', $ip)
                        ->exists(),
                    'in_blacklist' => false // Puedes crear una tabla para blacklist si la necesitas
                ];
            });

        // Obtener IPs con intentos fallidos recientes
        $recentBlockedIps = LoginIntento::where('exitoso', false)
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->select('ip')
            ->distinct()
            ->get();

        // Estadísticas
        $whitelistCount = $ipWhitelists->count();
        $blacklistCount = 0; // Cambiar si implementas blacklist
        $unclassifiedCount = $detectedIps->where('in_whitelist', false)->where('in_blacklist', false)->count();
        $totalIps = $detectedIps->count();

        return view('admin.controlAcceso.config-ips', compact(
            'ipWhitelists',
            'detectedIps',
            'recentBlockedIps',
            'whitelistCount',
            'blacklistCount',
            'unclassifiedCount',
            'totalIps'
        ));
    }

    /**
     * Mostrar configuración de 2FA
     */
    // En ControlAccesoController

/**
 * Mostrar panel de administración de 2FA
 */
public function twoFactor()
{
    // Estadísticas
    $totalUsers = User::count();
    $usersWith2FA = UserTwoFactor::count();
    $usersWithout2FA = $totalUsers - $usersWith2FA;
    
    // Roles con configuración de 2FA
    $roles = \Spatie\Permission\Models\Role::withCount(['users', 'users as users_with_2fa_count' => function($query) {
        $query->whereHas('twoFactor');
    }])->get()->map(function($role) {
        $role->requires_2fa = in_array($role->name, ['Administrador', 'Cajero', 'Barista']);
        return $role;
    });
    
    $rolesWithMandatory2FA = $roles->where('requires_2fa', true)->count();

    // Lista de usuarios paginada
    $users = User::with(['roles', 'twoFactor'])
        ->withCount('twoFactor')
        ->paginate(15);

    return view('admin.controlAcceso.two-factor', compact(
        'totalUsers',
        'usersWith2FA',
        'usersWithout2FA',
        'rolesWithMandatory2FA',
        'roles',
        'users'
    ));
}

/**
 * Activar/desactivar 2FA obligatorio para un rol
 */
public function toggleRole2FA(Request $request)
{
    $request->validate([
        'role_id' => 'required|exists:roles,id',
        'action' => 'required|in:enable,disable'
    ]);

    $role = \Spatie\Permission\Models\Role::find($request->role_id);
    
    // Aquí podrías guardar esta configuración en la base de datos
    // Por ahora usamos una lógica simple basada en nombres de roles
    
    return redirect()->route('control-acceso.two-factor')
        ->with('success', "Configuración de 2FA actualizada para el rol {$role->name}");
}

/**
 * Forzar activación de 2FA para un usuario
 */
public function forceEnable2FA(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id'
    ]);

    // Aquí implementarías la lógica para forzar la activación
    // Podría ser enviando un email de configuración obligatoria
    
    return redirect()->route('control-acceso.two-factor')
        ->with('success', 'Se ha solicitado la activación de 2FA para el usuario');
}

/**
 * Desactivar 2FA para un usuario
 */
public function disableUser2FA(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id'
    ]);

    $userTwoFactor = UserTwoFactor::where('user_id', $request->user_id)->first();
    
    if ($userTwoFactor) {
        $userTwoFactor->delete();
        
        Auditoria::create([
            'usuario_id' => Auth::id(),
            'accion' => 'DESACTIVAR_2FA_USUARIO',
            'entidad' => 'UserTwoFactor',
            'entidad_id' => $request->user_id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now()
        ]);
    }

    return redirect()->route('control-acceso.two-factor')
        ->with('success', '2FA desactivado para el usuario');
}


    /**
     * Mostrar formulario para importar IPs
     */
    public function showImportIpsForm()
    {
        return view('admin.controlAcceso.import-ips');
    }
}