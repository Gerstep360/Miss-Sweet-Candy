<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'temporal_token',
        'password_set',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'temporal_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_set' => 'boolean',
        ];
    }

    protected $attributes = [
        'password_set' => true,
        'temporal_token' => null,
    ];

    // === NUEVAS RELACIONES AGREGADAS ===
    
    /**
     * Relación con autenticación de dos factores
     */
    public function twoFactor()
    {
        return $this->hasOne(UserTwoFactor::class, 'user_id');
    }

    /**
     * Relación con whitelist de IPs
     */
    public function ipWhitelists()
    {
        return $this->hasMany(IpWhitelist::class);
    }

    /**
     * Relación con intentos de login
     */
    public function loginAttempts()
    {
        return $this->hasMany(LoginIntento::class, 'email', 'email');
    }

    /**
     * Relación con auditorías
     */
    public function auditorias()
    {
        return $this->hasMany(Auditoria::class, 'usuario_id');
    }

    // === MÉTODOS EXISTENTES ===
    
    public function initials(): string
    {
        return strtoupper(
            Str::of($this->name)
                ->explode(' ')
                ->take(2)
                ->map(fn ($word) => Str::substr($word, 0, 1))
                ->implode('')
        );
    }

    public function needsPasswordSetup(): bool
    {
        return !$this->password_set && !is_null($this->temporal_token);
    }

    public function isFullyActive(): bool
    {
        return $this->password_set && is_null($this->temporal_token);
    }

    public function cobrosRealizados()
    {
        return $this->hasMany(\App\Models\CobroCaja::class, 'cajero_id');
    }

    public function pedidosAtendidos()
    {
        return $this->hasMany(\App\Models\Pedido::class, 'atendido_por');
    }

    public function misPedidos()
    {
        return $this->hasMany(\App\Models\Pedido::class, 'cliente_id');
    }

    // === NUEVOS MÉTODOS PARA SEGURIDAD ===
    

    /**
     * Obtener intentos de login recientes
     */
    public function getRecentLoginAttempts($limit = 10)
    {
        return $this->loginAttempts()
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Verificar si una IP está permitida para este usuario
     */
    public function isIpAllowed($ip): bool
    {
        if ($this->ipWhitelists->isEmpty()) {
            return true; // Si no hay whitelist, todas las IPs están permitidas
        }

        return IpWhitelist::isIpAllowed($this->id, $ip);
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->twoFactor !== null;
    }

    // Método para contar usuarios con 2FA por rol
    public static function getTwoFactorStatsByRole()
    {
        return \Spatie\Permission\Models\Role::withCount(['users', 'users as users_with_2fa_count' => function($query) {
            $query->whereHas('twoFactor');
        }])->get();
    }
    /**
     * Relación con el perfil del cliente
     */
    public function perfil()
    {
        return $this->hasOne(\App\Models\ClientePerfil::class, 'user_id');
    }

    /**
     * Verificar si el usuario tiene perfil de cliente
     */
    public function tienePerfil(): bool
    {
        return $this->perfil()->exists();
    }

    /**
     * Obtener o crear perfil del cliente
     */
    public function obtenerOCrearPerfil()
    {
        return $this->perfil()->firstOrCreate(['user_id' => $this->id]);
    }
}