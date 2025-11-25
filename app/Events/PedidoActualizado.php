<?php

namespace App\Events;

use App\Models\Pedido;
use Illuminate\Broadcasting\Channel;
// 1. CAMBIA ESTO: De ShouldBroadcast a ShouldBroadcastNow
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; 
use Illuminate\Queue\SerializesModels;

// 2. IMPLEMENTA LA NUEVA INTERFAZ
class PedidoActualizado implements ShouldBroadcastNow 
{
    use SerializesModels;

    public function __construct(public Pedido $pedido) {}

    public function broadcastOn(): Channel
    {
        return new Channel('turnero');
    }

    public function broadcastAs(): string
    {
        return 'pedido.actualizado';
    }

    public function broadcastWith(): array
    {
        return [
            'id'          => $this->pedido->id,
            'token'       => $this->pedido->token,
            'estado'      => $this->pedido->estado,
            'eta'         => $this->pedido->eta_minutes,
            'tipo'        => $this->pedido->tipo,
            'created_at'  => $this->pedido->created_at,
            'updated_at'  => $this->pedido->updated_at,
        ];
    }
}