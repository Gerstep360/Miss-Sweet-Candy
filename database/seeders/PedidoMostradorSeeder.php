<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PedidoMostrador;
use App\Models\PedidoMostradorItem;
use App\Models\User;
use App\Models\Producto;
use App\Models\Categoria;
class PedidoMostradorSeeder extends Seeder
{
    public function run(): void
    {
        $cliente = User::where('name', 'Cliente')->first();
        $cajero = User::first();
        $producto = Producto::first();

        $pedido = PedidoMostrador::create([
            'cliente_id' => $cliente ? $cliente->id : null,
            'atendido_por' => $cajero->id,
            'estado' => 'pendiente',
            'notas' => 'Pedido para llevar de ejemplo',
        ]);

        PedidoMostradorItem::create([
            'pedido_mostrador_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio' => $producto->precio,
        ]);
    }
}
