<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\InventarioProducto;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la tabla antes de sembrar
        InventarioProducto::truncate();

        // Obtener todos los productos
        $productos = Producto::all();

        if ($productos->isEmpty()) {
            $this->command->warn('⚠️  No hay productos para crear el inventario. Ejecuta primero ProductoSeeder.');
            return;
        }

        $inventarioData = [];

        foreach ($productos as $producto) {
            // Configurar valores de inventario según el tipo de producto
            $stockConfig = $this->getStockConfig($producto->nombre, $producto->categoria->nombre);
            
            $inventarioData[] = [
                'producto_id' => $producto->id,
                'stock_actual' => $stockConfig['stock_actual'],
                'stock_minimo' => $stockConfig['stock_minimo'],
                'punto_reposicion' => $stockConfig['punto_reposicion'],
                'ubicacion' => $stockConfig['ubicacion'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insertar en lote
        InventarioProducto::insert($inventarioData);

        $this->command->info("✅ Inventario creado para {$productos->count()} productos.");
    }

    /**
     * Configuración de stock por tipo de producto
     */
    private function getStockConfig(string $nombreProducto, string $categoria): array
    {
        // Configuración base por categoría
        $configBase = [
            'Café' => [
                'stock_actual' => rand(50, 100),
                'stock_minimo' => 20,
                'punto_reposicion' => 30,
                'ubicacion' => 'Almacén Principal - Estante A'
            ],
            'Latte' => [
                'stock_actual' => rand(40, 80),
                'stock_minimo' => 15,
                'punto_reposicion' => 25,
                'ubicacion' => 'Almacén Principal - Estante B'
            ],
            'Masitas' => [
                'stock_actual' => rand(30, 60),
                'stock_minimo' => 10,
                'punto_reposicion' => 20,
                'ubicacion' => 'Refrigerador - Nivel 1'
            ],
            'Minitorta' => [
                'stock_actual' => rand(20, 40),
                'stock_minimo' => 5,
                'punto_reposicion' => 15,
                'ubicacion' => 'Refrigerador - Nivel 2'
            ],
            'Torta' => [
                'stock_actual' => rand(15, 30),
                'stock_minimo' => 3,
                'punto_reposicion' => 10,
                'ubicacion' => 'Refrigerador - Nivel 3'
            ],
            'Postre' => [
                'stock_actual' => rand(25, 50),
                'stock_minimo' => 8,
                'punto_reposicion' => 15,
                'ubicacion' => 'Refrigerador - Nivel 4'
            ],
            'Salteña' => [
                'stock_actual' => rand(40, 80),
                'stock_minimo' => 12,
                'punto_reposicion' => 25,
                'ubicacion' => 'Congelador - Sección A'
            ],
            'Helado' => [
                'stock_actual' => rand(35, 70),
                'stock_minimo' => 10,
                'punto_reposicion' => 20,
                'ubicacion' => 'Congelador - Sección B'
            ],
            'default' => [
                'stock_actual' => rand(20, 50),
                'stock_minimo' => 5,
                'punto_reposicion' => 15,
                'ubicacion' => 'Almacén General'
            ]
        ];

        return $configBase[$categoria] ?? $configBase['default'];
    }
}