<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FidelidadMovimiento extends Model
{
    use HasFactory;

    protected $table = 'fidelidad_movimientos';

    protected $fillable = [
        'cliente_id',
        'puntos',
        'tipo', 
        'descripcion',
        'origen_type',
        'origen_id'
    ];

    protected $casts = [
        'puntos' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con el cliente - CORREGIDA TEMPORAL: Usar User directamente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Relación polimórfica con el origen (pedido)
     */
    public function origen(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relación con el pedido - usando origen polimórfico
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'origen_id')
                    ->where('origen_type', Pedido::class);
    }

    /**
     * Accesor para el motivo (usa descripcion)
     */
    public function getMotivoAttribute(): string
    {
        return $this->descripcion;
    }

    /**
     * Mutador para el motivo (guarda en descripcion)
     */
    public function setMotivoAttribute($value)
    {
        $this->attributes['descripcion'] = $value;
    }

    /**
     * Scope para movimientos de acumulación
     */
    public function scopeAcumulacion($query)
    {
        return $query->where('tipo', 'acumulo');
    }

    /**
     * Scope para movimientos de canje
     */
    public function scopeCanje($query)
    {
        return $query->where('tipo', 'canje');
    }

    /**
     * Accesor para el nombre del tipo
     */
    public function getTipoNombreAttribute(): string
    {
        return match($this->tipo) {
            'acumulo' => 'Acumulación',
            'canje' => 'Canje',
            default => 'Desconocido'
        };
    }

    /**
     * Crear movimiento para un pedido - SIMPLIFICADO
     */
    public static function crearParaPedido($pedido, $puntos, $tipo = 'acumulo', $motivo = null)
    {
        return self::create([
            'cliente_id' => $pedido->cliente_id,
            'puntos' => $puntos,
            'tipo' => $tipo,
            'descripcion' => $motivo ?: "Acumulación por pedido #{$pedido->id}",
            'origen_type' => Pedido::class,
            'origen_id' => $pedido->id
        ]);
    }

    /**
     * Verificar si el cliente existe - SIMPLIFICADO
     */
    public static function verificarClientePerfil($clienteId)
    {
        // Solo verificar que el usuario existe
        return \App\Models\User::find($clienteId);
    }


    public function repararPuntosPedidos()
{
    $pedidos = Pedido::whereIn('estado', ['entregado', 'pagado'])->get();

    foreach ($pedidos as $pedido) {
        $clienteId = $pedido->cliente_id;

        // Evitar duplicados
        if (FidelidadMovimiento::where('pedido_id', $pedido->id)->exists()) continue;

        FidelidadMovimiento::create([
            'cliente_id' => $clienteId,
            'pedido_id' => $pedido->id,
            'puntos' => intval($pedido->total * 1), // o tu factor de puntos
            'tipo' => 'acumulo',
            'descripcion' => "Puntos por pedido #{$pedido->id}",
            'fecha' => now(),
        ]);
    }

    return "Puntos reparados";
}

}