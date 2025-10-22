<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventarioProducto;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        $movimientosCreados = 0;
        $inventariosCreados = 0;

        foreach ($productos as $producto) {
            // Stock inicial aleatorio
            $stockActual = rand(20, 100);
            
            // Crear UN SOLO registro en inventario_productos por producto
            InventarioProducto::updateOrCreate(
                ['producto_id' => $producto->id],
                [
                    'stock_actual' => $stockActual,
                    'stock_minimo' => rand(5, 15),
                    'punto_reposicion' => rand(20, 30),
                    'ubicacion' => 'Almacén Principal',
                ]
            );
            $inventariosCreados++;

            // Registrar movimiento inicial en movimientos_producto
            DB::table('movimientos_producto')->insert([
                'producto_id' => $producto->id,
                'tipo' => 'entrada',
                'cantidad' => $stockActual,
                'saldo_anterior' => 0,
                'saldo_nuevo' => $stockActual,
                'motivo' => 'Stock inicial',
                'usuario_id' => $adminUser->id,
                'created_at' => Carbon::now()->subDays(60),
            ]);
            $movimientosCreados++;

            // Simular movimientos de los últimos 60 días
            for ($i = 59; $i >= 0; $i--) {
                $fecha = Carbon::now()->subDays($i);
                
                // Entradas (compras/producción) - 2-3 veces por semana
                if ($i % 3 == 0 && rand(0, 10) > 3) {
                    $cantidad = rand(10, 50);
                    $saldoAnterior = $stockActual;
                    $stockActual += $cantidad;
                    
                    $motivos = [
                        'Compra de mercadería',
                        'Producción diaria',
                        'Reposición de stock',
                        'Pedido a proveedor',
                    ];
                    
                    DB::table('movimientos_producto')->insert([
                        'producto_id' => $producto->id,
                        'tipo' => 'entrada',
                        'cantidad' => $cantidad,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_nuevo' => $stockActual,
                        'motivo' => $motivos[array_rand($motivos)],
                        'usuario_id' => $adminUser->id,
                        'created_at' => $fecha->copy()->setTime(rand(8, 10), rand(0, 59)),
                    ]);
                    $movimientosCreados++;
                }

                // Salidas (ventas) - diariamente
                if (rand(0, 10) > 2) {
                    $cantidad = rand(3, 15);
                    
                    // Evitar stock negativo
                    if ($stockActual - $cantidad < 0) {
                        $cantidad = max(1, $stockActual);
                    }
                    
                    $saldoAnterior = $stockActual;
                    $stockActual -= $cantidad;
                    
                    DB::table('movimientos_producto')->insert([
                        'producto_id' => $producto->id,
                        'tipo' => 'venta',
                        'cantidad' => $cantidad,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_nuevo' => $stockActual,
                        'motivo' => 'Venta',
                        'usuario_id' => $adminUser->id,
                        'created_at' => $fecha->copy()->setTime(rand(12, 19), rand(0, 59)),
                    ]);
                    $movimientosCreados++;
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
                    
                    $saldoAnterior = $stockActual;
                    
                    if ($esPositivo) {
                        $stockActual += $cantidad;
                        $tipo = 'entrada';
                    } else {
                        if ($stockActual - $cantidad < 0) {
                            $cantidad = max(1, $stockActual);
                        }
                        $stockActual -= $cantidad;
                        $tipo = 'ajuste';
                    }
                    
                    DB::table('movimientos_producto')->insert([
                        'producto_id' => $producto->id,
                        'tipo' => $tipo,
                        'cantidad' => $cantidad,
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_nuevo' => $stockActual,
                        'motivo' => $motivos[array_rand($motivos)],
                        'usuario_id' => $adminUser->id,
                        'created_at' => $fecha->copy()->setTime(rand(8, 20), rand(0, 59)),
                    ]);
                    $movimientosCreados++;
                }
            }

            // Actualizar stock final en inventario_productos
            DB::table('inventario_productos')
                ->where('producto_id', $producto->id)
                ->update(['stock_actual' => max(0, $stockActual)]);
        }

        $this->command->info('✅ Registros de inventario creados: ' . $inventariosCreados);
        $this->command->info('✅ Movimientos de producto creados: ' . $movimientosCreados);
    }
}
