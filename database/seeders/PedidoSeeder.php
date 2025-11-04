<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\User;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\FidelidadMovimiento;

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

        // PEDIDOS COMPLETADOS (para acumular puntos)

        // Pedido de Mesa COMPLETADO
        $pedidoMesaCompletado = Pedido::create([
            'tipo' => 'mesa',
            'cliente_id' => $cliente->id,
            'atendido_por' => $cajero->id,
            'mesa_id' => $mesa->id,
            'estado' => 'pagado', // ✅ ESTADO COMPLETADO
            'canal' => 'local',
            'created_at' => now()->subDays(5), // Hace 5 días
        ]);

        PedidoItem::create([
            'pedido_id' => $pedidoMesaCompletado->id,
            'producto_id' => $productos[0]->id,
            'cantidad' => 2,
            'precio_unitario' => 50.00,
            'descuento_item' => 0.00,
            'subtotal_item' => 100.00,
            'estado_item' => 'entregado',
            'destino' => 'cocina',
        ]);

        // Pedido de Mostrador COMPLETADO
        $pedidoMostradorCompletado = Pedido::create([
            'tipo' => 'mostrador',
            'cliente_id' => $cliente->id,
            'atendido_por' => $cajero->id,
            'estado' => 'entregado', // ✅ ESTADO COMPLETADO
            'canal' => 'local',
            'created_at' => now()->subDays(3), // Hace 3 días
        ]);

        PedidoItem::create([
            'pedido_id' => $pedidoMostradorCompletado->id,
            'producto_id' => $productos[1]->id,
            'cantidad' => 1,
            'precio_unitario' => 75.00,
            'descuento_item' => 0.00,
            'subtotal_item' => 75.00,
            'estado_item' => 'entregado',
            'destino' => 'barra',
        ]);

        // PEDIDOS NO COMPLETADOS (para mostrar en el sistema)

        // Pedido de Mesa en preparación
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
            'precio_unitario' => 50.00,
            'descuento_item' => 0.00,
            'subtotal_item' => 100.00,
            'estado_item' => 'preparado',
            'destino' => 'cocina',
            'notas' => 'Sin cebolla',
        ]);

        // Pedido Web - Retiro pendiente
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

        $this->command->info('✅ Pedidos de prueba creados exitosamente.');

        // ACUMULAR PUNTOS AUTOMÁTICAMENTE PARA LOS PEDIDOS COMPLETADOS
        $this->acumularPuntosParaPedidosCompletados($cliente->id);
    }

    /**
     * Acumular puntos para pedidos completados
     */
    private function acumularPuntosParaPedidosCompletados($clienteId)
    {
        $pedidosCompletados = Pedido::where('cliente_id', $clienteId)
            ->whereIn('estado', ['entregado', 'servido', 'retirado', 'pagado'])
            ->get();

        foreach ($pedidosCompletados as $pedido) {
            try {
                // Calcular puntos basado en el total del pedido
                $totalPedido = $pedido->total;
                $puntos = intval($totalPedido * 1); // 1 punto por dólar (configuración por defecto)

                // Verificar que no exista ya un movimiento para este pedido
                $existeMovimiento = FidelidadMovimiento::where('pedido_id', $pedido->id)
                    ->where('tipo', 'acumulo')
                    ->exists();

                if (!$existeMovimiento && $puntos > 0) {
                    FidelidadMovimiento::create([
                        'cliente_id' => $clienteId,
                        'pedido_id' => $pedido->id,
                        'tipo' => 'acumulo',
                        'puntos' => $puntos,
                        'motivo' => "Acumulación por pedido #{$pedido->id}",
                        'created_at' => $pedido->created_at,
                    ]);

                    $this->command->info("✅ Puntos acumulados para pedido #{$pedido->id}: {$puntos} puntos");
                }
            } catch (\Exception $e) {
                $this->command->error("❌ Error acumulando puntos para pedido #{$pedido->id}: " . $e->getMessage());
            }
        }
    }
}