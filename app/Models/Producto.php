<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'nombre', 'unidad', 'precio', 'imagen'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

        // Método optimizado para obtener la URL de la imagen
    public function getImagenUrlAttribute()
    {
        $ruta = $this->imagen ? 'storage/' . $this->imagen : 'storage/img/none/none.png';

        // Verifica si el archivo existe en public/storage
        if ($this->imagen && !File::exists(public_path($ruta))) {
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
