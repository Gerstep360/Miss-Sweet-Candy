<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginIntento extends Model
{
    public $timestamps = false;

    protected $table = 'login_intentos';

    protected $fillable = [
        'email',
        'exitoso',
        'ip',
        'created_at',
    ];

    protected $casts = [
        'exitoso' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con usuario (opcional, basado en email)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Accesor para formatear fecha
     */
    public function getCreatedAtFormattedAttribute(): ?string
    {
        return $this->created_at
            ? $this->created_at->format('d/m/Y H:i:s')
            : null;
    }

    /**
     * Scope para intentos exitosos
     */
    public function scopeExitosos($query)
    {
        return $query->where('exitoso', true);
    }

    /**
     * Scope para intentos fallidos
     */
    public function scopeFallidos($query)
    {
        return $query->where('exitoso', false);
    }

    /**
     * Scope para filtrar por email
     */
    public function scopePorEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope para filtrar por IP
     */
    public function scopePorIp($query, $ip)
    {
        return $query->where('ip', $ip);
    }
}

