<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoMesaItem extends Model
{
    protected $fillable = [
        'pedido_mesa_id',
        'producto_id',
        'cantidad',
        'precio', // precio unitario del producto
    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoMesa::class, 'pedido_mesa_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
