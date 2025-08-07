<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoMostradorItem extends Model
{
    protected $fillable = [
        'pedido_mostrador_id',
        'producto_id',
        'cantidad',
        'precio',
    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoMostrador::class, 'pedido_mostrador_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
