<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginIntento extends Model
{
    use HasFactory;

    protected $table = 'login_intentos';
    public $timestamps = false;
    
    protected $fillable = [
        'email',
        'exitoso',
        'ip'
    ];

    protected $casts = [
        'exitoso' => 'boolean',
        'created_at' => 'datetime'
    ];

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
     * Scope para un email específico
     */
    public function scopePorEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Relación con usuario (basado en email)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}