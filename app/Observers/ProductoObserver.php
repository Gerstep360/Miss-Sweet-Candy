<?php

namespace App\Observers;

use App\Models\Producto;
use App\Models\InventarioProducto;

class ProductoObserver
{
    /**
     * Handle the Producto "created" event.
     */
    public function created(Producto $producto): void
    {
        // Crear automáticamente el registro de inventario cuando se crea un producto
        InventarioProducto::create([
            'producto_id' => $producto->id,
            'stock_actual' => 0,
            'stock_minimo' => 5, // Valor por defecto
            'punto_reposicion' => 10, // Valor por defecto
            'ubicacion' => null,
        ]);
    }

    /**
     * Handle the Producto "deleted" event.
     */
    public function deleted(Producto $producto): void
    {
        // Eliminar el inventario asociado cuando se elimina el producto
        $producto->inventario?->delete();
    }
}
