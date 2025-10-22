<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventarioProducto;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;

class InventarioProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = Producto::all();
        
        if ($productos->isEmpty()) {
            $this->command->warn('No hay productos en la base de datos. Ejecuta ProductoSeeder primero.');
            return;
        }

        $adminUser = User::role('administrador')->first();
        
        if (!$adminUser) {
            $this->command->warn('No hay usuario administrador. Ejecuta UserSeeder primero.');
            return;
        }

        $inventarios = [];
        $movimientos = [];

        foreach ($productos as $producto) {
            // Stock inicial (hace 60 días)
            $stockInicial = rand(20, 100);
            
            $inventarios[] = [
                'producto_id' => $producto->id,
                'cantidad' => $stockInicial,
                'tipo_movimiento' => 'entrada',
                'motivo' => 'Stock inicial',
                'usuario_id' => $adminUser->id,
            ];

            // Simular movimientos de los últimos 60 días
            $stockActual = $stockInicial;
            
            for ($i = 59; $i >= 0; $i--) {
                $fecha = Carbon::now()->subDays($i);
                
                // Entradas (compras/producción) - 2-3 veces por semana
                if ($i % 3 == 0 && rand(0, 10) > 3) {
                    $cantidad = rand(10, 50);
                    $stockActual += $cantidad;
                    
                    $motivos = [
                        'Compra de mercadería',
                        'Producción diaria',
                        'Reposición de stock',
                        'Pedido a proveedor',
                    ];
                    
                    $inventarios[] = [
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'tipo_movimiento' => 'entrada',
                        'motivo' => $motivos[array_rand($motivos)],
                        'usuario_id' => $adminUser->id,
                    ];
                }

                // Salidas (ventas) - diariamente
                if (rand(0, 10) > 2) {
                    $cantidad = rand(3, 15);
                    
                    // Evitar stock negativo
                    if ($stockActual - $cantidad < 0) {
                        $cantidad = max(1, $stockActual);
                    }
                    
                    $stockActual -= $cantidad;
                    
                    $inventarios[] = [
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'tipo_movimiento' => 'salida',
                        'motivo' => 'Venta',
                        'usuario_id' => $adminUser->id,
                    ];
                }

                // Ajustes (merma, corrección) - ocasionalmente
                if (rand(0, 10) > 8) {
                    $cantidad = rand(1, 5);
                    $esPositivo = rand(0, 1);
                    
                    $motivos = [
                        'Merma por vencimiento',
                        'Producto dañado',
                        'Ajuste de inventario',
                        'Corrección de stock',
                        'Degustación',
                    ];
                    
                    if ($esPositivo) {
                        $stockActual += $cantidad;
                        $tipo = 'entrada';
                    } else {
                        if ($stockActual - $cantidad < 0) {
                            $cantidad = max(1, $stockActual);
                        }
                        $stockActual -= $cantidad;
                        $tipo = 'salida';
                    }
                    
                    $inventarios[] = [
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'tipo_movimiento' => $tipo,
                        'motivo' => $motivos[array_rand($motivos)],
                        'usuario_id' => $adminUser->id,
                    ];
                }
            }

            // Actualizar stock actual del producto
            $producto->update(['stock' => max(0, $stockActual)]);
        }

        // Insertar todos los movimientos
        foreach ($inventarios as $inventario) {
            InventarioProducto::create($inventario);
        }

        $this->command->info('✅ Movimientos de inventario creados: ' . count($inventarios));
        $this->command->info('📦 Productos con stock actualizado: ' . $productos->count());
    }
}
