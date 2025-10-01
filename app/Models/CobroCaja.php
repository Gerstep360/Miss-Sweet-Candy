<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CobroCaja extends Model
{
    protected $fillable = [
        'pedido_id',
        'importe',
        'metodo',
        'estado',
        'comprobante',
        'cajero_id',
        'qr_tx_id',
        'qr_estado',
        'qr_proveedor',
        'qr_referencia',
    ];

    protected $casts = [
        'importe' => 'decimal:2',
    ];

    /**
     * Relación con el pedido
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Relación con el cajero (usuario que realizó el cobro)
     */
    public function cajero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }

    /**
     * Verificar si el cobro es por efectivo
     */
    public function esEfectivo(): bool
    {
        return $this->metodo === 'efectivo';
    }

    /**
     * Verificar si el cobro es por POS
     */
    public function esPos(): bool
    {
        return $this->metodo === 'pos';
    }

    /**
     * Verificar si el cobro es por QR
     */
    public function esQr(): bool
    {
        return $this->metodo === 'qr';
    }

    /**
     * Verificar si el cobro está activo (cobrado)
     */
    public function estaCobrado(): bool
    {
        return $this->estado === 'cobrado';
    }

    /**
     * Verificar si el cobro fue cancelado
     */
    public function estaCancelado(): bool
    {
        return $this->estado === 'cancelado';
    }

    /**
     * Verificar si el pago QR está pendiente
     */
    public function qrPendiente(): bool
    {
        return $this->esQr() && $this->qr_estado === 'pendiente';
    }

    /**
     * Verificar si el pago QR fue aprobado
     */
    public function qrAprobado(): bool
    {
        return $this->esQr() && $this->qr_estado === 'aprobado';
    }

    /**
     * Verificar si el pago QR fue rechazado
     */
    public function qrRechazado(): bool
    {
        return $this->esQr() && $this->qr_estado === 'rechazado';
    }

    /**
     * Obtener el nombre del método de pago formateado
     */
    public function getNombreMetodoAttribute(): string
    {
        return match($this->metodo) {
            'efectivo' => 'Efectivo',
            'pos' => 'Tarjeta (POS)',
            'qr' => 'QR/Transferencia',
            default => 'Desconocido'
        };
    }

    /**
     * Obtener el badge HTML del estado
     */
    public function getBadgeEstadoAttribute(): string
    {
        return match($this->estado) {
            'cobrado' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400">Cobrado</span>',
            'cancelado' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-500/20 text-red-400">Cancelado</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-zinc-500/20 text-zinc-400">Desconocido</span>'
        };
    }

    /**
     * Obtener el badge HTML del estado QR
     */
    public function getBadgeQrEstadoAttribute(): string
    {
        if (!$this->esQr() || !$this->qr_estado) {
            return '';
        }

        return match($this->qr_estado) {
            'pendiente' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-500/20 text-amber-400">Pendiente</span>',
            'aprobado' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400">Aprobado</span>',
            'rechazado' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-500/20 text-red-400">Rechazado</span>',
            default => ''
        };
    }

    /**
     * Scope para filtrar solo cobros activos
     */
    public function scopeCobrados($query)
    {
        return $query->where('estado', 'cobrado');
    }

    /**
     * Scope para filtrar solo cobros cancelados
     */
    public function scopeCancelados($query)
    {
        return $query->where('estado', 'cancelado');
    }

    /**
     * Scope para filtrar por método de pago
     */
    public function scopePorMetodo($query, string $metodo)
    {
        return $query->where('metodo', $metodo);
    }

    /**
     * Scope para cobros QR pendientes
     */
    public function scopeQrPendientes($query)
    {
        return $query->where('metodo', 'qr')
                     ->where('qr_estado', 'pendiente');
    }

    /**
     * Scope para cobros del día actual
     */
    public function scopeDelDia($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope para cobros de un cajero específico
     */
    public function scopeDeCajero($query, int $cajeroId)
    {
        return $query->where('cajero_id', $cajeroId);
    }

    
}