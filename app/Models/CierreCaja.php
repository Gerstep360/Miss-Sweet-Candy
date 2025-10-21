<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CierreCaja extends Model
{
    public $timestamps = false;

    protected $table = 'cierres_caja';

    protected $fillable = [
        'cajero_id',
        'inicio',
        'fin',
        'total_sistema',
        'total_declarado',
        'diferencia',
        'observaciones',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
        'total_sistema' => 'decimal:2',
        'total_declarado' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];

    /**
     * Relación con el cajero
     */
    public function cajero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }

    /**
     * Relación con los detalles del cierre (por método de pago)
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(CierreCajaDetalle::class, 'cierre_caja_id');
    }

    /**
     * Verificar si hay diferencia (faltante o sobrante)
     */
    public function tieneDiferencia(): bool
    {
        return abs($this->diferencia) > 0.01;
    }

    /**
     * Verificar si es un faltante
     */
    public function esFaltante(): bool
    {
        return $this->diferencia < -0.01;
    }

    /**
     * Verificar si es un sobrante
     */
    public function esSobrante(): bool
    {
        return $this->diferencia > 0.01;
    }

    /**
     * Verificar si está cuadrado (sin diferencia)
     */
    public function estaCuadrado(): bool
    {
        return !$this->tieneDiferencia();
    }

    /**
     * Obtener el tipo de diferencia en texto
     */
    public function getTipoDiferenciaAttribute(): string
    {
        if ($this->estaCuadrado()) {
            return 'Cuadrado';
        }
        return $this->esFaltante() ? 'Faltante' : 'Sobrante';
    }

    /**
     * Obtener el color del badge según la diferencia
     */
    public function getColorDiferenciaAttribute(): string
    {
        if ($this->estaCuadrado()) {
            return 'green';
        }
        return $this->esFaltante() ? 'red' : 'amber';
    }

    /**
     * Obtener la duración del turno en horas
     */
    public function getDuracionTurnoAttribute(): float
    {
        return $this->inicio->diffInHours($this->fin, true);
    }

    /**
     * Scope para cierres de hoy
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fin', today());
    }

    /**
     * Scope para cierres de un cajero específico
     */
    public function scopeDeCajero($query, int $cajeroId)
    {
        return $query->where('cajero_id', $cajeroId);
    }

    /**
     * Scope para cierres en un rango de fechas
     */
    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fin', [$inicio, $fin]);
    }

    /**
     * Scope para cierres con diferencias
     */
    public function scopeConDiferencias($query)
    {
        return $query->where(function ($q) {
            $q->where('diferencia', '>', 0.01)
              ->orWhere('diferencia', '<', -0.01);
        });
    }

    /**
     * Scope para cierres cuadrados
     */
    public function scopeCuadrados($query)
    {
        return $query->whereBetween('diferencia', [-0.01, 0.01]);
    }
}
