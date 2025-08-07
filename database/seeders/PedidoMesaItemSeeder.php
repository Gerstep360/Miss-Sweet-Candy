<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PedidoMesaItem;
use App\Models\PedidoMesa;
use App\Models\Producto;
class PedidoMesaItemSeeder extends Seeder
{
    public function run(): void
    {
        $pedido = PedidoMesa::first();
        $producto = Producto::first();

        PedidoMesaItem::create([
            'pedido_mesa_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio' => $producto->precio,
        ]);
    }
}
