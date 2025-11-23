<?php

namespace App\Events;

use App\Models\Pedido;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PedidoActualizado implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public Pedido $pedido) {}

    public function broadcastOn(): Channel
    {
        return new Channel('turnero'); // canal público
    }

    public function broadcastAs(): string
    {
        return 'pedido.actualizado';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->pedido->id,
            'token' => $this->pedido->token,
            'estado' => $this->pedido->estado,
            'eta' => $this->pedido->eta_minutes,
        ];
    }
}
