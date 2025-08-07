<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoMesa extends Model
{
    protected $fillable = [
        'mesa_id',
        'cliente_id',
        'atendido_por',
        'estado',      // pendiente, enviado, anulado, servido, etc.
        'notas',       // observaciones
    ];

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function atendidoPor()
    {
        return $this->belongsTo(User::class, 'atendido_por');
    }

    public function items()
    {
        return $this->hasMany(PedidoMesaItem::class, 'pedido_mesa_id');
    }

    // Calcula el subtotal dinámicamente sumando los ítems del pedido
    public function getSubtotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->cantidad * $item->precio;
        });
    }
}
