<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CierreCajaDetalle extends Model
{
    public $timestamps = false;

    protected $table = 'cierres_caja_detalle';

    protected $fillable = [
        'cierre_caja_id',
        'metodo',
        'monto_sistema',
        'monto_declarado',
    ];

    protected $casts = [
        'monto_sistema' => 'decimal:2',
        'monto_declarado' => 'decimal:2',
    ];

    /**
     * Relación con el cierre de caja principal
     */
    public function cierreCaja(): BelongsTo
    {
        return $this->belongsTo(CierreCaja::class, 'cierre_caja_id');
    }

    /**
     * Calcular la diferencia para este método de pago
     */
    public function getDiferenciaAttribute(): float
    {
        return $this->monto_declarado - $this->monto_sistema;
    }

    /**
     * Verificar si tiene diferencia
     */
    public function tieneDiferencia(): bool
    {
        return abs($this->diferencia) > 0.01;
    }

    /**
     * Verificar si es faltante
     */
    public function esFaltante(): bool
    {
        return $this->diferencia < -0.01;
    }

    /**
     * Verificar si es sobrante
     */
    public function esSobrante(): bool
    {
        return $this->diferencia > 0.01;
    }

    /**
     * Obtener el nombre del método de pago
     */
    public function getNombreMetodoAttribute(): string
    {
        return match($this->metodo) {
            'efectivo' => 'Efectivo',
            'pos' => 'Tarjeta (POS)',
            'qr' => 'QR/Transferencia',
            default => 'Desconocido'
        };
    }

    /**
     * Obtener el icono del método de pago
     */
    public function getIconoMetodoAttribute(): string
    {
        return match($this->metodo) {
            'efectivo' => '💵',
            'pos' => '💳',
            'qr' => '📱',
            default => '❓'
        };
    }
}
