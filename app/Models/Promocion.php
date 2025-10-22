<?php
// app/Models/Promocion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Promocion extends Model
{
    // AGREGAR ESTA LÍNEA - Especificar el nombre de la tabla
    protected $table = 'promociones';

        // ⚡⚡⚡ AGREGAR ESTA LÍNEA - DESHABILITAR TIMESTAMPS ⚡⚡⚡
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'tipo',
        'aplica_sobre', 
        'valor',
        'tope_descuento',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'dias_semana',
        'prioridad',
        'activo'
    ];

    protected $casts = [
        'dias_semana' => 'array',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor' => 'decimal:2',
        'tope_descuento' => 'decimal:2',
        'activo' => 'boolean'
    ];

    // Relación con productos
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'promocion_productos')
                    ->withPivot('cantidad_requerida');
    }

    // Relación con categorías
    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'promocion_productos')
                    ->withPivot('cantidad_requerida');
    }

    // Scope para promociones activas
    public function scopeActivas($query)
    {
        return $query->where('activo', true)
                    ->where(function($q) {
                        $q->whereNull('fecha_inicio')
                          ->orWhere('fecha_inicio', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>=', now());
                    });
    }

    // Método para verificar si está vigente
    public function getEstaVigenteAttribute()
    {
        if (!$this->activo) return false;
        
        $hoy = now();
        $diaSemana = strtolower($hoy->format('D'));
        $diasMap = ['mon' => 'lun', 'tue' => 'mar', 'wed' => 'mie', 'thu' => 'jue', 'fri' => 'vie', 'sat' => 'sab', 'sun' => 'dom'];
        $diaActual = $diasMap[$diaSemana] ?? '';
        
        // Verificar día de la semana
        if ($this->dias_semana && !in_array($diaActual, $this->dias_semana)) {
            return false;
        }

        // Verificar fechas
        if ($this->fecha_inicio && $hoy->lt($this->fecha_inicio)) return false;
        if ($this->fecha_fin && $hoy->gt($this->fecha_fin)) return false;

        // Verificar horarios
        $horaActual = $hoy->format('H:i:s');
        if ($this->hora_inicio && $horaActual < $this->hora_inicio) return false;
        if ($this->hora_fin && $horaActual > $this->hora_fin) return false;

        return true;
    }
}