<?php

namespace App\Mail;

use App\Models\Promocion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromocionCreada extends Mailable
{
    use Queueable, SerializesModels;

    public $promocion;

    public function __construct(Promocion $promocion)
    {
        $this->promocion = $promocion;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Nueva Promoción Disponible: ' . $this->promocion->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.promocion-creada',
            with: [
                'promocion' => $this->promocion,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
