<?php
// app/Models/FidelidadConfig.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FidelidadConfig extends Model
{
    use HasFactory;

    protected $table = 'fidelidad_config';
    
    protected $fillable = [
        'clave', 
        'valor', 
        'tipo', 
        'descripcion', 
        'categoria'
    ];

    /**
     * Obtener configuración como array asociativo
     */
    public static function getConfig()
{
    return static::all()->mapWithKeys(function ($item) {
        return [$item->clave => $item->valor]; // Aquí se aplica el accessor getValorAttribute
    })->toArray();
}

    /**
     * Obtener un valor específico de configuración
     */
    public static function getValor($clave, $default = null)
{
    $config = static::where('clave', $clave)->first();
    return $config ? $config->valor : $default; // Se aplica el accessor automáticamente
}

    /**
     * Actualizar o crear configuración
     */
    public static function setValor($clave, $valor, $tipo = 'string', $descripcion = null)
    {
        return static::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valor,
                'tipo' => $tipo,
                'descripcion' => $descripcion
            ]
        );
    }

    /**
     * Cast automático de valores según el tipo
     */
    public function getValorAttribute($value)
    {
        return match($this->tipo) {
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => $value
        };
    }

    /**
     * Convertir valor para guardar en base de datos
     */
    public function setValorAttribute($value)
    {
        $this->attributes['valor'] = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
    }
}