<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use App\Models\EspecialDelDia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne; 

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'nombre', 'unidad', 'precio', 'imagen'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /** Especial vigente para HOY (activo + vigente + coincide hoy/fecha/rango) */
    public function especialVigente(): HasOne
    {
        return $this->hasOne(EspecialDelDia::class, 'producto_id')
            ->activo()
            ->vigente()
            ->paraHoy();
    }

    /** Precio “vigente” (aplica especial si existe; si no, el normal) */
    public function getPrecioVigenteAttribute(): float
    {
        $esp = $this->relationLoaded('especialVigente')
            ? $this->especialVigente
            : $this->especialVigente()->first();

        return $esp ? (float) $esp->getPrecioFinal() : (float) $this->precio;
    }

    /** ¿Tiene oferta hoy? */
    public function getTieneOfertaAttribute(): bool
    {
        $esp = $this->relationLoaded('especialVigente')
            ? $this->especialVigente
            : $this->especialVigente()->first();

        return (bool) $esp?->tieneDescuento();
    }

    /** % de descuento (si aplica) */
    public function getPorcentajeOfertaAttribute(): int
    {
        $base = (float) $this->precio;
        $vig  = (float) $this->precio_vigente;
        return $base > 0 ? (int) round(100 * max(0, $base - $vig) / $base) : 0;
    }

    /** Ahorro absoluto (si aplica) */
    public function getAhorroOfertaAttribute(): float
    {
        $base = (float) $this->precio;
        $vig  = (float) $this->precio_vigente;
        return max(0, $base - $vig);
    }

    // === Tu accessor de imagen intacto ===
    public function getImagenUrlAttribute()
    {
        $ruta = $this->imagen ? 'storage/' . $this->imagen : 'storage/img/none/none.png';
        if ($this->imagen && !\Illuminate\Support\Facades\File::exists(public_path($ruta))) {
            return asset('storage/img/none/none.png');
        }
        return asset($ruta);
    }

    public function pedidoItems()
    {
        return $this->hasMany(\App\Models\PedidoItem::class);
    }

    /**
     * Relación con Inventario (1:1)
     */
    public function inventario()
    {
        return $this->hasOne(InventarioProducto::class, 'producto_id');
    }

    /**
     * Obtiene el stock actual del producto
     */
    public function getStockActualAttribute()
    {
        return $this->inventario ? $this->inventario->stock_actual : 0;
    }

    /**
     * Obtiene el estado del stock
     */
    public function getEstadoStockAttribute()
    {
        return $this->inventario ? $this->inventario->estado_stock : 'SIN_INVENTARIO';
    }
}
