<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientePerfil extends Model
{
    protected $table = 'clientes_perfil';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'telefono',
        'direccion',
        'alergias',
        'preferencias',
        'acepta_marketing',
    ];

    protected $casts = [
        'alergias' => 'array',
        'preferencias' => 'array',
        'acepta_marketing' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Verificar si el cliente tiene alergias registradas
     */
    public function tieneAlergias(): bool
    {
        return !empty($this->alergias) && count($this->alergias) > 0;
    }

    /**
     * Verificar si el cliente tiene preferencias alimentarias
     */
    public function tienePreferencias(): bool
    {
        return !empty($this->preferencias) && count($this->preferencias) > 0;
    }

    /**
     * Obtener alergias graves
     */
    public function getAlergiasGravesAttribute()
    {
        if (!$this->tieneAlergias()) {
            return [];
        }

        return collect($this->alergias)->filter(function($alergia) {
            return isset($alergia['severidad']) && $alergia['severidad'] === 'grave';
        })->values()->toArray();
    }

    /**
     * Obtener alergias moderadas
     */
    public function getAlergiasModераdasAttribute()
    {
        if (!$this->tieneAlergias()) {
            return [];
        }

        return collect($this->alergias)->filter(function($alergia) {
            return isset($alergia['severidad']) && $alergia['severidad'] === 'moderado';
        })->values()->toArray();
    }

    /**
     * Obtener alergias leves
     */
    public function getAlergiasLevesAttribute()
    {
        if (!$this->tieneAlergias()) {
            return [];
        }

        return collect($this->alergias)->filter(function($alergia) {
            return isset($alergia['severidad']) && $alergia['severidad'] === 'leve';
        })->values()->toArray();
    }

    /**
     * Verificar si tiene alergias graves
     */
    public function tieneAlergiasGraves(): bool
    {
        return count($this->alergias_graves) > 0;
    }

    /**
     * Obtener color de alerta según severidad
     */
    public static function getColorSeveridad(string $severidad): string
    {
        return match($severidad) {
            'grave' => 'red',
            'moderado' => 'orange',
            'leve' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Obtener icono según severidad
     */
    public static function getIconoSeveridad(string $severidad): string
    {
        return match($severidad) {
            'grave' => '🚨',
            'moderado' => '⚠️',
            'leve' => '⚡',
            default => 'ℹ️'
        };
    }

    /**
     * Obtener badge de preferencia alimentaria
     */
    public static function getBadgePreferencia(string $preferencia): array
    {
        return match($preferencia) {
            'vegetariano' => ['label' => 'Vegetariano', 'color' => 'green', 'icon' => '🥗'],
            'vegano' => ['label' => 'Vegano', 'color' => 'emerald', 'icon' => '🌱'],
            'sin_gluten' => ['label' => 'Sin Gluten', 'color' => 'amber', 'icon' => '🌾'],
            'sin_lactosa' => ['label' => 'Sin Lactosa', 'color' => 'blue', 'icon' => '🥛'],
            'sin_azucar' => ['label' => 'Sin Azúcar', 'color' => 'purple', 'icon' => '🍬'],
            'bajo_sodio' => ['label' => 'Bajo Sodio', 'color' => 'cyan', 'icon' => '🧂'],
            default => ['label' => $preferencia, 'color' => 'gray', 'icon' => '📋']
        };
    }

    /**
     * Lista de preferencias alimentarias disponibles
     */
    public static function preferenciasDisponibles(): array
    {
        return [
            'vegetariano' => 'Vegetariano (sin carne ni pescado)',
            'vegano' => 'Vegano (sin productos animales)',
            'sin_gluten' => 'Sin Gluten (celíaco)',
            'sin_lactosa' => 'Sin Lactosa (intolerancia)',
            'sin_azucar' => 'Sin Azúcar (diabetes)',
            'bajo_sodio' => 'Bajo Sodio (hipertensión)',
        ];
    }

    /**
     * Lista de niveles de severidad
     */
    public static function nivelesSeveridad(): array
    {
        return [
            'leve' => 'Leve (molestias menores)',
            'moderado' => 'Moderado (requiere atención)',
            'grave' => 'Grave (riesgo vital)',
        ];
    }
}
