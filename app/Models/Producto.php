<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use App\Models\EspecialDelDia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; 

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
     * Alérgenos asociados a este producto
     */
    public function alergenos(): BelongsToMany
    {
        return $this->belongsToMany(Alergeno::class, 'alergeno_producto')
                    ->withPivot('nivel_presencia')
                    ->withTimestamps();
    }

    /**
     * Verificar si el producto contiene algún alérgeno específico
     */
    public function contieneAlergeno(string $nombreAlergeno): bool
    {
        return $this->alergenos()
                    ->where('nombre', $nombreAlergeno)
                    ->where('activo', true)
                    ->exists();
    }

    /**
     * Verificar si el producto contiene alérgenos que el cliente tiene registrados
     */
    public function esAptoParaCliente($clienteId): array
    {
        $cliente = \App\Models\User::find($clienteId);
        
        if (!$cliente || !$cliente->tienePerfil()) {
            return ['apto' => true, 'advertencias' => []];
        }

        $perfil = $cliente->perfil;
        $alergiasCliente = $perfil->alergias ?? [];
        $advertencias = [];

        // Obtener alérgenos del producto
        $alergenosProducto = $this->alergenos()
                                  ->where('activo', true)
                                  ->get();

        foreach ($alergiasCliente as $alergia) {
            $nombreAlergia = $alergia['nombre'] ?? '';
            $severidad = $alergia['severidad'] ?? 'leve';

            // Buscar coincidencias (comparación case-insensitive)
            $coincidencia = $alergenosProducto->first(function($alergeno) use ($nombreAlergia) {
                return stripos($alergeno->nombre, $nombreAlergia) !== false 
                    || stripos($nombreAlergia, $alergeno->nombre) !== false;
            });

            if ($coincidencia) {
                $advertencias[] = [
                    'alergeno' => $coincidencia->nombre,
                    'severidad' => $severidad,
                    'nivel_presencia' => $coincidencia->pivot->nivel_presencia,
                    'icono' => $coincidencia->icono,
                    'color' => $coincidencia->color,
                ];
            }
        }

        return [
            'apto' => empty($advertencias),
            'advertencias' => $advertencias,
        ];
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
