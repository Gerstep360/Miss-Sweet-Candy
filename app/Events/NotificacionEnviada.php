<?php

namespace App\Events;

use App\Models\Notificacion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificacionEnviada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Notificacion $notificacion) {}

    /**
     * Get the channels the event should broadcast on.
     * Usamos PrivateChannel para seguridad. Convención: App.Models.User.{id}
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->notificacion->usuario_destino_id),
        ];
    }

    /**
     * El nombre del evento para escuchar en el frontend (echo.listen('.notificacion.enviada'))
     */
    public function broadcastAs(): string
    {
        return 'notificacion.enviada';
    }

    /**
     * Los datos que se enviarán al frontend.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notificacion->id,
            'mensaje' => $this->notificacion->mensaje,
            'tipo' => $this->notificacion->tipo, // 'pedido', 'stock', etc.
            'leido' => false,
            'created_at' => $this->notificacion->created_at->toDateTimeString(),
            'tiempo_atras' => $this->notificacion->created_at->diffForHumans(),
            'url' => route('notificaciones.show', $this->notificacion->id), // Opcional: link directo
        ];
    }
}