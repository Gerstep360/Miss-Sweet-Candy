<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FidelidadMovimiento extends Model
{
    use HasFactory;

    protected $table = 'fidelidad_movimientos';

    protected $fillable = [
        'cliente_id',
        'pedido_id',
        'tipo',
        'puntos',
        'motivo'
    ];

    protected $casts = [
        'puntos' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con el cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Relación con el pedido
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Scope para movimientos de acumulación
     */
    public function scopeAcumulacion($query)
    {
        return $query->where('tipo', 'acumulo');
    }

    /**
     * Scope para movimientos de canje
     */
    public function scopeCanje($query)
    {
        return $query->where('tipo', 'canje');
    }

    /**
     * Accesor para el nombre del tipo
     */
    public function getTipoNombreAttribute(): string
    {
        return match($this->tipo) {
            'acumulo' => 'Acumulación',
            'canje' => 'Canje',
            default => 'Desconocido'
        };
    }
}