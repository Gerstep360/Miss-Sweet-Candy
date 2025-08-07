<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a Café Aroma - Configura tu cuenta',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-created',
            with: [
                'user' => $this->user,
                'activationUrl' => route('users.activate', $this->token),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}