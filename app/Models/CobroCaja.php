<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CobroCaja extends Model
{
    protected $fillable = [
        'pedido_mostrador_id',
        'pedido_mesa_id',
        'importe',
        'metodo',
        'estado',
        'comprobante',
        'cajero_id',
    ];

    public function pedidoMostrador()
    {
        return $this->belongsTo(PedidoMostrador::class, 'pedido_mostrador_id');
    }

    public function pedidoMesa()
    {
        return $this->belongsTo(PedidoMesa::class, 'pedido_mesa_id');
    }

    public function cajero()
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }
}
