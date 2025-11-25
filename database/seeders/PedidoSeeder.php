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
    public function run(): void
    {
        $cajero  = User::where('email', 'cajero@gmail.com')->first();
        $cliente = User::where('email', 'cliente@gmail.com')->first();
        $mesa    = Mesa::first();
        $productos = Producto::limit(3)->get();

        if (!$cajero || !$cliente || !$mesa || $productos->isEmpty()) {
            $this->command->warn('⚠️  No se encontraron datos para pedidos de prueba.');
            return;
        }

        // ========= Pedido Mesa =========
        $pedidoMesa = Pedido::create([
            'tipo' => 'mesa',
            'cliente_id' => $cliente->id,
            'atendido_por' => $cajero->id,
            'mesa_id' => $mesa->id,
            'estado' => 'en_preparacion',
            'canal' => 'local',
        ]);

        PedidoItem::create([
            'pedido_id' => $pedidoMesa->id,
            'producto_id' => $productos[0]->id,
            'cantidad' => 2,
            'precio_unitario' => 20,
            'descuento_item' => 0,
            'subtotal_item' => 40,
            'estado_item' => 'en_preparacion',
            'destino' => 'cocina',
        ]);

        PedidoItem::create([
            'pedido_id' => $pedidoMesa->id,
            'producto_id' => $productos[1]->id,
            'cantidad' => 1,
            'precio_unitario' => 25,
            'descuento_item' => 0,
            'subtotal_item' => 25,
            'estado_item' => 'en_preparacion',
            'destino' => 'barra',
        ]);

        $pedidoMesa->token = Pedido::generarTokenPorTipo('mesa');
        $pedidoMesa->eta_minutes = $pedidoMesa->calcularEtaPorProductos();
        $pedidoMesa->started_at = now()->subMinutes(2);
        $pedidoMesa->save();

        // ========= Pedido Mostrador =========
        $pedidoMostrador = Pedido::create([
            'tipo' => 'mostrador',
            'cliente_id' => $cliente->id,
            'atendido_por' => $cajero->id,
            'estado' => 'preparado',
            'canal' => 'local',
        ]);

        PedidoItem::create([
            'pedido_id' => $pedidoMostrador->id,
            'producto_id' => $productos[2]->id,
            'cantidad' => 2,
            'precio_unitario' => 32,
            'descuento_item' => 0,
            'subtotal_item' => 64,
            'estado_item' => 'preparado',
            'destino' => 'barra',
        ]);

        $pedidoMostrador->token = Pedido::generarTokenPorTipo('mostrador');
        $pedidoMostrador->eta_minutes = $pedidoMostrador->calcularEtaPorProductos();
        $pedidoMostrador->ready_at = now()->subMinute();
        $pedidoMostrador->save();

        $this->command->info('✅ Pedidos de prueba con token/ETA creados.');
    }
}