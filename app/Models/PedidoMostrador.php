<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoMostrador extends Model
{
    protected $fillable = [
        'cliente_id',      // opcional
        'atendido_por',    // cajero que atiende
        'estado',          // pendiente, enviado, anulado, retirado
        'notas',           // observaciones
    ];

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
        return $this->hasMany(PedidoMostradorItem::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->cantidad * $item->precio;
        });
    }
}