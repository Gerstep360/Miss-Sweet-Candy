<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EspecialDelDia;
use App\Models\Producto;
use Carbon\Carbon;

class EspecialDelDiaSeeder extends Seeder
{
    public function run(): void
    {
        $productos = Producto::all();
        
        if ($productos->isEmpty()) {
            $this->command->warn('No hay productos en la base de datos. Ejecuta ProductoSeeder primero.');
            return;
        }

        $especiales = [];

        // 1. ESPECIALES POR DÍA DE LA SEMANA (7 días)
        $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        $productosUsados = [];

        foreach ($diasSemana as $index => $dia) {
            // Seleccionar un producto que no se haya usado
            $producto = $productos->whereNotIn('id', $productosUsados)->random();
            $productosUsados[] = $producto->id;

            // Tipos de descuento variados
            $tipoDescuento = rand(1, 3);
            
            switch ($tipoDescuento) {
                case 1: // Descuento porcentual
                    $descuentoPorcentaje = rand(10, 40);
                    $precioFijo = null;
                    $descripcion = "¡{$descuentoPorcentaje}% de descuento todos los {$dia}s!";
                    break;
                case 2: // Precio fijo
                    $precioFijo = round($producto->precio * rand(60, 80) / 100, 2);
                    $descuentoPorcentaje = null;
                    $descripcion = "Precio especial de {$dia}: Bs {$precioFijo}";
                    break;
                case 3: // 2x1 o combo
                    $descuentoPorcentaje = 50;
                    $precioFijo = null;
                    $descripcion = "¡2x1 en {$producto->nombre} todos los {$dia}s!";
                    break;
            }

            $especiales[] = [
                'producto_id' => $producto->id,
                'dia_semana' => $dia,
                'fecha_especifica' => null,
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'descuento_porcentaje' => $descuentoPorcentaje,
                'precio_fijo' => $precioFijo,
                'descripcion_especial' => $descripcion,
                'activo' => true,
            ];
        }

        // 2. ESPECIALES POR FECHA ESPECÍFICA (próximos eventos)
        $fechasEspeciales = [
            [
                'fecha' => Carbon::now()->addDays(5),
                'descripcion' => 'Aniversario del local',
            ],
            [
                'fecha' => Carbon::now()->addDays(15),
                'descripcion' => 'Día del Amor y la Amistad',
            ],
            [
                'fecha' => Carbon::now()->addDays(30),
                'descripcion' => 'Fin de mes - Liquidación',
            ],
        ];

        foreach ($fechasEspeciales as $evento) {
            $producto = $productos->random();
            
            $especiales[] = [
                'producto_id' => $producto->id,
                'dia_semana' => null,
                'fecha_especifica' => $evento['fecha']->format('Y-m-d'),
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'descuento_porcentaje' => rand(20, 50),
                'precio_fijo' => null,
                'descripcion_especial' => $evento['descripcion'] . ' - ¡No te lo pierdas!',
                'activo' => true,
            ];
        }

        // 3. ESPECIALES POR RANGO DE FECHAS (temporadas)
        $rangosFechas = [
            [
                'inicio' => Carbon::now(),
                'fin' => Carbon::now()->addDays(14),
                'descripcion' => 'Promoción de Temporada',
            ],
            [
                'inicio' => Carbon::now()->addDays(20),
                'fin' => Carbon::now()->addDays(35),
                'descripcion' => 'Festival del Café',
            ],
        ];

        foreach ($rangosFechas as $rango) {
            $producto = $productos->random();
            
            $especiales[] = [
                'producto_id' => $producto->id,
                'dia_semana' => null,
                'fecha_especifica' => null,
                'fecha_inicio' => $rango['inicio']->format('Y-m-d'),
                'fecha_fin' => $rango['fin']->format('Y-m-d'),
                'descuento_porcentaje' => null,
                'precio_fijo' => round($producto->precio * 0.75, 2),
                'descripcion_especial' => $rango['descripcion'] . ' - Oferta válida del ' . 
                    $rango['inicio']->format('d/m') . ' al ' . $rango['fin']->format('d/m'),
                'activo' => true,
            ];
        }

        // 4. ESPECIALES INACTIVOS (históricos)
        for ($i = 0; $i < 5; $i++) {
            $producto = $productos->random();
            $diasPasados = rand(30, 90);
            
            $especiales[] = [
                'producto_id' => $producto->id,
                'dia_semana' => null,
                'fecha_especifica' => Carbon::now()->subDays($diasPasados)->format('Y-m-d'),
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'descuento_porcentaje' => rand(15, 35),
                'precio_fijo' => null,
                'descripcion_especial' => 'Promoción pasada - Ya no disponible',
                'activo' => false,
            ];
        }

        // Insertar todos los especiales
        foreach ($especiales as $especial) {
            EspecialDelDia::create($especial);
        }

        $this->command->info('✅ Especiales del día creados: ' . count($especiales));
        $this->command->info('📅 Por día de semana: 7');
        $this->command->info('📆 Por fecha específica: ' . count($fechasEspeciales));
        $this->command->info('🗓️  Por rango de fechas: ' . count($rangosFechas));
        $this->command->info('🚫 Inactivos (históricos): 5');
    }
}
