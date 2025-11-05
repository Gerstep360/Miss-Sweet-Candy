<?php

namespace App\Mail;

use App\Models\Promocion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromocionPorExpirar extends Mailable
{
    use Queueable, SerializesModels;

    public $promocion;
    public $diasRestantes;

    public function __construct(Promocion $promocion, int $diasRestantes)
    {
        $this->promocion = $promocion;
        $this->diasRestantes = $diasRestantes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⏰ Promoción por Expirar: ' . $this->promocion->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.promocion-por-expirar',
            with: [
                'promocion' => $this->promocion,
                'diasRestantes' => $this->diasRestantes,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
