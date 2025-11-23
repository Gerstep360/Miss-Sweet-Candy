<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'descuento_item',
        'subtotal_item',
        'estado_item',
        'destino',
        'notas',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento_item' => 'decimal:2',
        'subtotal_item' => 'decimal:2',
    ];

    /**
     * Relación con el pedido
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Relación con el producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Scopes para filtrar por estado
     */
    public function scopePendiente($query)
    {
        return $query->where('estado_item', 'pendiente');
    }

    public function scopeEnviado($query)
    {
        return $query->where('estado_item', 'enviado');
    }

    public function scopePreparado($query)
    {
        return $query->where('estado_item', 'preparado');
    }

    public function scopeServido($query)
    {
        return $query->where('estado_item', 'servido');
    }

    public function scopeRetirado($query)
    {
        return $query->where('estado_item', 'retirado');
    }

    public function scopeEntregado($query)
    {
        return $query->where('estado_item', 'entregado');
    }

    public function scopeAnulado($query)
    {
        return $query->where('estado_item', 'anulado');
    }

    /**
     * Scopes para filtrar por destino
     */
    public function scopeBarra($query)
    {
        return $query->where('destino', 'barra');
    }

    public function scopeCocina($query)
    {
        return $query->where('destino', 'cocina');
    }

    /**
     * Accesor para obtener el nombre del estado
     */
    public function getEstadoNombreAttribute(): string
    {
        return match($this->estado_item) {
            'pendiente' => 'Pendiente',
            'enviado' => 'Enviado',
            'preparado' => 'Preparado',
            'servido' => 'Servido',
            'retirado' => 'Retirado',
            'entregado' => 'Entregado',
            'anulado' => 'Anulado',
            default => 'Desconocido'
        };
    }

    /**
     * Accesor para obtener el nombre del destino
     */
    public function getDestinoNombreAttribute(): ?string
    {
        return match($this->destino) {
            'barra' => 'Barra',
            'cocina' => 'Cocina',
            default => null
        };
    }

    /**
     * Calcular el subtotal del item
     */
    public function calcularSubtotal(): float
    {
        $subtotal = $this->cantidad * $this->precio_unitario;
        return $subtotal - ($this->descuento_item ?? 0);
    }
}
