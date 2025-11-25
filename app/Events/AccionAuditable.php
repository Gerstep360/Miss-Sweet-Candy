<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccionAuditable
{
    use Dispatchable, SerializesModels;

    public $accion;
    public $entidad;
    public $entidadId;
    public $usuarioId;
    public $ip;
    public $userAgent;
    public $request;

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $accion,
        string $entidad,
        $entidadId = null,
        $usuarioId = null,
        $ip = null,
        $userAgent = null,
        $request = null
    ) {
        $this->accion = $accion;
        $this->entidad = $entidad;
        $this->entidadId = $entidadId;
        $this->usuarioId = $usuarioId ?? (auth()->check() ? auth()->id() : null);
        $this->ip = $ip ?? request()->ip();
        $this->userAgent = $userAgent ?? request()->userAgent();
        $this->request = $request;
    }
}

