<?php
// app/Models/Promocion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Promocion extends Model
{
    // AGREGAR ESTA LÍNEA - Especificar el nombre de la tabla
    protected $table = 'promociones';

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
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor' => 'decimal:2',
        'tope_descuento' => 'decimal:2',
        'activo' => 'boolean'
    ];

    // Mutator: Convertir array a string para MySQL SET
    public function setDiasSemanaAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['dias_semana'] = implode(',', $value);
        } else {
            $this->attributes['dias_semana'] = $value;
        }
    }

    // Accessor: Convertir string de MySQL SET a array
    public function getDiasSemanaAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        return explode(',', $value);
    }

    // Mutator: Limpiar formato de hora_inicio (convertir a H:i si viene con AM/PM)
    public function setHoraInicioAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['hora_inicio'] = null;
            return;
        }
        
        // Si viene con formato AM/PM, convertir a formato 24h
        if (stripos($value, 'a.m.') !== false || stripos($value, 'p.m.') !== false || stripos($value, 'am') !== false || stripos($value, 'pm') !== false) {
            $value = str_replace([' a. m.', ' p. m.', ' a.m.', ' p.m.', 'a.m.', 'p.m.'], ['AM', 'PM', 'AM', 'PM', 'AM', 'PM'], $value);
            try {
                $time = \Carbon\Carbon::createFromFormat('h:i A', trim($value));
                $this->attributes['hora_inicio'] = $time->format('H:i');
            } catch (\Exception $e) {
                $this->attributes['hora_inicio'] = $value;
            }
        } else {
            // Ya viene en formato correcto H:i
            $this->attributes['hora_inicio'] = $value;
        }
    }

    // Mutator: Limpiar formato de hora_fin (convertir a H:i si viene con AM/PM)
    public function setHoraFinAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['hora_fin'] = null;
            return;
        }
        
        // Si viene con formato AM/PM, convertir a formato 24h
        if (stripos($value, 'a.m.') !== false || stripos($value, 'p.m.') !== false || stripos($value, 'am') !== false || stripos($value, 'pm') !== false) {
            $value = str_replace([' a. m.', ' p. m.', ' a.m.', ' p.m.', 'a.m.', 'p.m.'], ['AM', 'PM', 'AM', 'PM', 'AM', 'PM'], $value);
            try {
                $time = \Carbon\Carbon::createFromFormat('h:i A', trim($value));
                $this->attributes['hora_fin'] = $time->format('H:i');
            } catch (\Exception $e) {
                $this->attributes['hora_fin'] = $value;
            }
        } else {
            // Ya viene en formato correcto H:i
            $this->attributes['hora_fin'] = $value;
        }
    }

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
        $fechaHoy = $hoy->toDateString();
        $horaActual = $hoy->format('H:i:s');
        
        // Verificar fechas (solo si están definidas)
        if ($this->fecha_inicio) {
            if ($fechaHoy < $this->fecha_inicio->toDateString()) {
                return false;
            }
        }
        
        if ($this->fecha_fin) {
            if ($fechaHoy > $this->fecha_fin->toDateString()) {
                return false;
            }
        }

        // Verificar horarios (solo si están definidos)
        if ($this->hora_inicio && $this->hora_fin) {
            if ($horaActual < $this->hora_inicio || $horaActual > $this->hora_fin) {
                return false;
            }
        }
        
        // Verificar día de la semana (solo si hay días específicos)
        if (!empty($this->dias_semana) && count($this->dias_semana) > 0) {
            $diaSemana = strtolower($hoy->format('D'));
            $diasMap = [
                'mon' => 'lun', 
                'tue' => 'mar', 
                'wed' => 'mie', 
                'thu' => 'jue', 
                'fri' => 'vie', 
                'sat' => 'sab', 
                'sun' => 'dom'
            ];
            $diaActual = $diasMap[$diaSemana] ?? '';
            
            if (!in_array($diaActual, $this->dias_semana)) {
                return false;
            }
        }

        return true;
    }
}