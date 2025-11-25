<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $expiresAt;
    public $minutesValid;

    /**
     * Create a new message instance.
     */
    public function __construct($code, $expiresAt)
    {
        $this->code = $code;
        $this->expiresAt = $expiresAt;
        $this->minutesValid = 30;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Código de Verificación - Autenticación de Dos Factores')
                    ->markdown('emails.two-factor-code')
                    ->with([
                        'code' => $this->code,
                        'expiresAt' => $this->expiresAt,
                        'minutesValid' => $this->minutesValid,
                    ]);
    }
}