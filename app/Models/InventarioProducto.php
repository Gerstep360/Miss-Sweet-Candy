<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioProducto extends Model
{
    use HasFactory;

    protected $table = 'inventario_productos';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'stock_actual',
        'stock_minimo',
        'punto_reposicion',
        'ubicacion',
    ];

    protected $casts = [
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'punto_reposicion' => 'integer',
    ];

    /**
     * Relación con Producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Calcula el estado del stock: OK, BAJO, CRÍTICO
     */
    public function getEstadoStockAttribute(): string
    {
        if ($this->stock_actual <= 0) {
            return 'CRÍTICO';
        }

        if ($this->stock_actual <= $this->stock_minimo) {
            return 'BAJO';
        }

        return 'OK';
    }

    /**
     * Verifica si el stock está en estado crítico
     */
    public function esCritico(): bool
    {
        return $this->stock_actual <= 0;
    }

    /**
     * Verifica si el stock está bajo
     */
    public function esBajo(): bool
    {
        return $this->stock_actual > 0 && $this->stock_actual <= $this->stock_minimo;
    }

    /**
     * Verifica si el stock está OK
     */
    public function esOk(): bool
    {
        return $this->stock_actual > $this->stock_minimo;
    }

    /**
     * Verifica si requiere alerta (BAJO o CRÍTICO)
     */
    public function requiereAlerta(): bool
    {
        return $this->esCritico() || $this->esBajo();
    }

    /**
     * Incrementa el stock
     */
    public function incrementarStock(int $cantidad): bool
    {
        $this->stock_actual += $cantidad;
        return $this->save();
    }

    /**
     * Decrementa el stock
     */
    public function decrementarStock(int $cantidad): bool
    {
        $this->stock_actual -= $cantidad;
        
        if ($this->stock_actual < 0) {
            $this->stock_actual = 0;
        }
        
        return $this->save();
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopePorEstado($query, string $estado)
    {
        switch (strtoupper($estado)) {
            case 'CRÍTICO':
                return $query->where('stock_actual', '<=', 0);
            case 'BAJO':
                return $query->whereRaw('stock_actual > 0 AND stock_actual <= stock_minimo');
            case 'OK':
                return $query->whereRaw('stock_actual > stock_minimo');
            default:
                return $query;
        }
    }

    /**
     * Scope para productos con stock crítico
     */
    public function scopeCriticos($query)
    {
        return $query->where('stock_actual', '<=', 0);
    }

    /**
     * Scope para productos con stock bajo
     */
    public function scopeBajos($query)
    {
        return $query->whereRaw('stock_actual > 0 AND stock_actual <= stock_minimo');
    }

    /**
     * Scope para productos con alertas (BAJO o CRÍTICO)
     */
    public function scopeConAlertas($query)
    {
        return $query->whereRaw('stock_actual <= stock_minimo');
    }
}
