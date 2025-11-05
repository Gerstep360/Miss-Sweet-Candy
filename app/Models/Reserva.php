<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $fillable = [
        'cliente_id',
        'mesa_id', 
        'fecha',
        'hora',
        'numero_personas',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime:H:i',
    ];

    /**
     * Relación con el cliente (User)
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Relación con la mesa
     */
    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    /**
     * Scope para reservas activas
     */
    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['pendiente', 'confirmada']);
    }

    /**
     * Scope para reservas del cliente
     */
    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Scope para reservas de hoy
     */
    public function scopeDeHoy($query)
    {
        return $query->where('fecha', now()->toDateString());
    }

    /**
     * Scope para reservas por fecha
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    /**
     * Scope para reservas por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Verificar si la reserva está activa
     */
    public function estaActiva(): bool
    {
        return in_array($this->estado, ['pendiente', 'confirmada']);
    }

    /**
     * Verificar si la reserva es para hoy
     */
    public function esParaHoy(): bool
    {
        return $this->fecha->isToday();
    }

    /**
     * Verificar si la reserva es próxima (en los próximos 30 minutos)
     */
    public function esProxima(): bool
    {
        $horaReserva = \Carbon\Carbon::parse($this->hora);
        return $horaReserva->diffInMinutes(now()) <= 30 && $horaReserva->gt(now());
    }

    /**
     * Verificar si la reserva está atrasada
     */
    public function estaAtrasada(): bool
    {
        $horaReserva = \Carbon\Carbon::parse($this->hora);
        return $horaReserva->lt(now()) && $this->estado == 'confirmada';
    }
}