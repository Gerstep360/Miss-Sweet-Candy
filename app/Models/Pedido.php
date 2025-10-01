<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'cliente_id',
        'atendido_por',
        'mesa_id',
        'modalidad',
        'estado',
        'programado_para',
        'direccion_entrega',
        'gps_lat',
        'gps_lng',
        'telefono_contacto',
        'canal',
        'notas',
    ];

    protected $casts = [
        'programado_para' => 'datetime',
        'gps_lat' => 'decimal:7',
        'gps_lng' => 'decimal:7',
    ];

    /**
     * Relación con el cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Relación con el usuario que atendió el pedido
     */
    public function atendidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atendido_por');
    }

    /**
     * Relación con la mesa (solo para pedidos de tipo 'mesa')
     */
    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    /**
     * Relación con los items del pedido
     */
    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    /**
     * Scopes para filtrar por tipo
     */
    public function scopeMesa($query)
    {
        return $query->where('tipo', 'mesa');
    }

    public function scopeMostrador($query)
    {
        return $query->where('tipo', 'mostrador');
    }

    public function scopeWeb($query)
    {
        return $query->where('tipo', 'web');
    }

    /**
     * Scopes para filtrar por estado
     */
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnPreparacion($query)
    {
        return $query->where('estado', 'en_preparacion');
    }

    public function scopePreparado($query)
    {
        return $query->where('estado', 'preparado');
    }

    public function scopeEntregado($query)
    {
        return $query->where('estado', 'entregado');
    }

    public function scopePagado($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeAnulado($query)
    {
        return $query->where('estado', 'anulado');
    }

    /**
     * Accesor para obtener el nombre del tipo
     */
    public function getTipoNombreAttribute(): string
    {
        return match($this->tipo) {
            'mesa' => 'Mesa',
            'mostrador' => 'Mostrador',
            'web' => 'Web',
            default => 'Desconocido'
        };
    }

    /**
     * Accesor para obtener el nombre del estado
     */
    public function getEstadoNombreAttribute(): string
    {
        return match($this->estado) {
            'pendiente' => 'Pendiente',
            'en_preparacion' => 'En Preparación',
            'preparado' => 'Preparado',
            'entregado' => 'Entregado',
            'pagado' => 'Pagado',
            'anulado' => 'Anulado',
            default => 'Desconocido'
        };
    }

    /**
     * Verificar si el pedido es de mesa
     */
    public function esMesa(): bool
    {
        return $this->tipo === 'mesa';
    }

    /**
     * Verificar si el pedido es de mostrador
     */
    public function esMostrador(): bool
    {
        return $this->tipo === 'mostrador';
    }

    /**
     * Verificar si el pedido es web
     */
    public function esWeb(): bool
    {
        return $this->tipo === 'web';
    }

    /**
     * Verificar si el pedido requiere entrega
     */
    public function requiereEntrega(): bool
    {
        return $this->esWeb() && $this->modalidad === 'entrega';
    }

    /**
     * Relación con los cobros en caja
     */
    public function cobros(): HasMany
    {
        return $this->hasMany(CobroCaja::class);
    }

    /**
     * Obtener el total del pedido
     */
    public function getTotalAttribute(): float
    {
        return $this->items->sum('subtotal_item');
    }

    /**
     * Verificar si el pedido está cobrado
     */
    public function estaCobrado(): bool
    {
        return $this->cobros()->where('estado', 'cobrado')->exists();
    }

    /**
     * Obtener el último cobro registrado
     */
    public function ultimoCobro(): ?CobroCaja
    {
        return $this->cobros()->latest()->first();
    }

    /**
     * Verificar si está listo para cobrar
     */
    public function listoParaCobrar(): bool
    {
        return in_array($this->estado, ['preparado', 'servido', 'retirado', 'entregado']) 
               && !$this->estaCobrado();
    }
}
