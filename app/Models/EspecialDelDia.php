<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EspecialDelDia extends Model
{
    use HasFactory;

    protected $table = 'especial_del_dia';

    protected $fillable = [
        'producto_id',
        'dia_semana',
        'fecha_especifica',
        'precio_especial',
        'descuento_porcentaje',
        'descripcion_especial',
        'activo',
        'fecha_inicio',
        'fecha_fin',
        'prioridad'
    ];

    protected $casts = [
        'fecha_especifica' => 'date',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'precio_especial' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeParaHoy($query)
    {
        $hoy = Carbon::now('America/La_Paz');
        $diaSemana = $this->getNombreDiaEs($hoy->dayOfWeek);
        
        return $query->where(function ($q) use ($hoy, $diaSemana) {
            // Especiales por día de la semana
            $q->where('dia_semana', $diaSemana)
              ->whereNull('fecha_especifica');
        })->orWhere(function ($q) use ($hoy) {
            // Especiales por fecha específica
            $q->where('fecha_especifica', $hoy->toDateString());
        })->orWhere(function ($q) use ($hoy) {
            // Especiales por rango de fechas
            $q->whereNotNull('fecha_inicio')
              ->whereNotNull('fecha_fin')
              ->where('fecha_inicio', '<=', $hoy)
              ->where('fecha_fin', '>=', $hoy);
        });
    }

    public function scopeParaDia($query, $dia)
    {
        if (is_string($dia)) {
            return $query->where('dia_semana', strtolower($dia));
        }
        
        if ($dia instanceof Carbon) {
            $diaSemana = $this->getNombreDiaEs($dia->dayOfWeek);
            return $query->where('dia_semana', $diaSemana)
                        ->orWhere('fecha_especifica', $dia->toDateString());
        }
        
        return $query;
    }

    public function scopeVigente($query)
    {
        $ahora = Carbon::now('America/La_Paz');
        
        return $query->where(function ($q) use ($ahora) {
            $q->whereNull('fecha_inicio')
              ->whereNull('fecha_fin');
        })->orWhere(function ($q) use ($ahora) {
            $q->where('fecha_inicio', '<=', $ahora)
              ->where('fecha_fin', '>=', $ahora);
        });
    }

    // Métodos auxiliares
    public function getPrecioFinal()
    {
        if ($this->precio_especial) {
            return $this->precio_especial;
        }
        
        if ($this->descuento_porcentaje && $this->producto) {
            $descuento = ($this->producto->precio * $this->descuento_porcentaje) / 100;
            return $this->producto->precio - $descuento;
        }
        
        return $this->producto?->precio ?? 0;
    }

    public function getDescripcionCompleta()
    {
        return $this->descripcion_especial ?? $this->producto?->descripcion;
    }

    public function tieneDescuento()
    {
        return $this->precio_especial || $this->descuento_porcentaje;
    }

    public function getDescuentoMonto()
    {
        if (!$this->producto) return 0;
        
        return $this->producto->precio - $this->getPrecioFinal();
    }

    private function getNombreDiaEs($dayOfWeek)
    {
        $dias = [
            0 => 'domingo',
            1 => 'lunes', 
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado'
        ];
        
        return $dias[$dayOfWeek] ?? 'lunes';
    }

    // Método estático para obtener el especial de hoy
    public static function getEspecialHoy()
    {
        return static::activo()
                    ->vigente()
                    ->paraHoy()
                    ->with(['producto.categoria'])
                    ->orderBy('prioridad', 'desc')
                    ->first();
    }

    // Método estático para obtener especiales de la semana
    public static function getEspecialesSemana()
    {
        $especiales = [];
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        
        foreach ($dias as $dia) {
            $especial = static::activo()
                            ->vigente()
                            ->paraDia($dia)
                            ->with(['producto.categoria'])
                            ->orderBy('prioridad', 'desc')
                            ->first();
            
            if ($especial) {
                $especiales[$dia] = $especial;
            }
        }
        
        return $especiales;
    }
}