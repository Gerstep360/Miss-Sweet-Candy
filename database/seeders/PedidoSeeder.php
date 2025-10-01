<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\User;
use App\Models\Mesa;
use App\Models\Producto;

class PedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios y productos
        $cajero = User::where('email', 'cajero@gmail.com')->first();
        $cliente = User::where('email', 'cliente@gmail.com')->first();
        $mesa = Mesa::first();
        $productos = Producto::limit(3)->get();

        if (!$cajero || !$cliente || !$mesa || $productos->isEmpty()) {
            $this->command->warn('⚠️  No se encontraron los datos necesarios para crear pedidos de prueba.');
            return;
        }

        // Pedido de Mesa
        $pedidoMesa = Pedido::create([
            'tipo' => 'mesa',
            'cliente_id' => $cliente->id,
            'atendido_por' => $cajero->id,
            'mesa_id' => $mesa->id,
            'estado' => 'en_preparacion',
            'canal' => 'local',
        ]);

        // Items del pedido de mesa
        PedidoItem::create([
            'pedido_id' => $pedidoMesa->id,
            'producto_id' => $productos[0]->id,
            'cantidad' => 2,
            'precio_unitario' => 50.00,
            'descuento_item' => 0.00,
            'subtotal_item' => 100.00,
            'estado_item' => 'enviado',
            'destino' => 'cocina',
            'notas' => 'Sin cebolla',
        ]);

        PedidoItem::create([
            'pedido_id' => $pedidoMesa->id,
            'producto_id' => $productos[1]->id,
            'cantidad' => 1,
            'precio_unitario' => 50.00,
            'descuento_item' => 0.00,
            'subtotal_item' => 50.00,
            'estado_item' => 'enviado',
            'destino' => 'barra',
        ]);

        // Pedido de Mostrador
        $pedidoMostrador = Pedido::create([
            'tipo' => 'mostrador',
            'cliente_id' => $cliente->id,
            'atendido_por' => $cajero->id,
            'estado' => 'preparado',
            'canal' => 'local',
        ]);

        // Item del pedido de mostrador
        PedidoItem::create([
            'pedido_id' => $pedidoMostrador->id,
            'producto_id' => $productos[2]->id,
            'cantidad' => 2,
            'precio_unitario' => 40.00,
            'descuento_item' => 5.00,
            'subtotal_item' => 75.00,
            'estado_item' => 'preparado',
            'destino' => 'barra',
        ]);

        // Pedido Web - Retiro
        $pedidoWebRetiro = Pedido::create([
            'tipo' => 'web',
            'cliente_id' => $cliente->id,
            'modalidad' => 'click_collect',
            'estado' => 'pendiente',
            'canal' => 'web',
        ]);

        PedidoItem::create([
            'pedido_id' => $pedidoWebRetiro->id,
            'producto_id' => $productos[0]->id,
            'cantidad' => 3,
            'precio_unitario' => 40.00,
            'descuento_item' => 0.00,
            'subtotal_item' => 120.00,
            'estado_item' => 'pendiente',
            'destino' => 'cocina',
        ]);

        // Pedido Web - Entrega
        $pedidoWebEntrega = Pedido::create([
            'tipo' => 'web',
            'cliente_id' => $cliente->id,
            'modalidad' => 'delivery',
            'estado' => 'pendiente',
            'direccion_entrega' => 'Calle Principal #123, Colonia Centro',
            'gps_lat' => -34.603722,
            'gps_lng' => -58.381592,
            'canal' => 'web',
        ]);

        PedidoItem::create([
            'pedido_id' => $pedidoWebEntrega->id,
            'producto_id' => $productos[1]->id,
            'cantidad' => 4,
            'precio_unitario' => 50.00,
            'descuento_item' => 10.00,
            'subtotal_item' => 190.00,
            'estado_item' => 'pendiente',
            'destino' => 'cocina',
            'notas' => 'Entregar antes de las 14:00',
        ]);

        $this->command->info('✅ Pedidos de prueba creados exitosamente.');
    }
}
