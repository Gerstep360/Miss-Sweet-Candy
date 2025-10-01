<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'temporal_token',
        'password_set',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'temporal_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_set' => 'boolean',
        ];
    }

    /**
     * The attributes that should have default values.
     */
    protected $attributes = [
        'password_set' => true,
        'temporal_token' => null,
    ];

    /**
     * Get the user's initials
     */
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

    /**
     * Verificar si el usuario necesita establecer contraseña
     */
    public function needsPasswordSetup(): bool
    {
        return !$this->password_set && !is_null($this->temporal_token);
    }

    /**
     * Verificar si el usuario está completamente activo
     */
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
}