<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $fillable = [
        'nombre',
        'estado', // libre, ocupada, reservada, fusionada
        'fusion_id', // id de la mesa principal si está fusionada
        'capacidad',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function fusionadas()
{
    return $this->hasMany(Mesa::class, 'fusion_id');
}

    public function mesaFusionada()
    {
        return $this->belongsTo(Mesa::class, 'fusion_id');
    }
}
