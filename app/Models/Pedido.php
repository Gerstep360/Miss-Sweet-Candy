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
        'token',
        'eta_minutes',
        'started_at',
        'ready_at',
        'delivered_at',
    ];

    protected $casts = [
        'programado_para' => 'datetime',
        'gps_lat' => 'decimal:7',
        'gps_lng' => 'decimal:7',
        'started_at' => 'datetime',
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // ==============================
    // HOOK: genera token ANTES de insertar
    // ==============================
    protected static function booted()
    {
        static::creating(function (Pedido $pedido) {
            if (empty($pedido->token)) {
                $pedido->token = self::generarTokenPorTipo($pedido->tipo);
            }

            // ETA puede quedar null y recalcularse después
            if ($pedido->eta_minutes === null) {
                $pedido->eta_minutes = 0;
            }
        });
    }

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
     * Relación con el feedback del pedido
     */
    public function feedback()
    {
        return $this->hasOne(Feedback::class);
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
     * Obtener el total con descuentos de promociones aplicadas
     */
    public function getTotalConPromocionesAttribute(): float
    {
        $subtotal = $this->total;
        $descuentoPromociones = $this->calcularDescuentoPromociones();
        
        return max(0, $subtotal - $descuentoPromociones);
    }

    /**
     * Calcular descuento total de promociones aplicables
     */
    public function calcularDescuentoPromociones(): float
    {
        $promocionService = app(\App\Services\PromocionService::class);
        $resultado = $promocionService->aplicarPromociones($this);
        
        return $resultado['descuento_total'] ?? 0;
    }

    /**
     * Obtener promociones aplicadas al pedido
     */
    public function getPromocionesAplicadas(): array
    {
        $promocionService = app(\App\Services\PromocionService::class);
        $resultado = $promocionService->aplicarPromociones($this);
        
        return $resultado['promociones'] ?? [];
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

    public static function generarTokenPorTipo(string $tipo): string
{
    $prefix = match($tipo) {
        'mesa' => 'M',
        'mostrador' => 'A',
        'web' => 'W',
        default => 'A',
    };

    // Busca el último token de ese tipo para continuar secuencia
    $last = self::where('tipo', $tipo)
        ->whereNotNull('token')
        ->latest('id')
        ->first();

    $num = 1;
    if ($last && preg_match('/\d+/', $last->token, $m)) {
        $num = (int)$m[0] + 1;
    }

    return $prefix . str_pad((string)$num, 3, '0', STR_PAD_LEFT);
}

public function calcularEtaPorProductos(): int
{
    // max(tiempo_item) como te expliqué
    $max = $this->items->map(function($it){
        $t = (int)($it->producto->prep_time_minutes ?? 1);
        return $t * (int)$it->cantidad;
    })->max() ?? 1;

    // penalidad por cola simple
    $enPrep = self::where('estado','en_preparacion')->count();
    $penalidad = $enPrep * 2;

    return $max + $penalidad;
}
}
