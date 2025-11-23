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
        $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        
        // Rastrear qué combinaciones producto-dia ya están usadas
        $usados = [];

        // 1. ESPECIALES POR DÍA DE LA SEMANA (7 días) - UN PRODUCTO DIFERENTE POR DÍA
        foreach ($diasSemana as $dia) {
            $producto = $productos->random();
            $usados[$producto->id][] = $dia;

            // Tipos de descuento variados
            $tipoDescuento = rand(1, 3);
            
            switch ($tipoDescuento) {
                case 1: // Descuento porcentual
                    $descuentoPorcentaje = rand(10, 40);
                    $precioEspecial = null;
                    $descripcion = "¡{$descuentoPorcentaje}% de descuento todos los {$dia}s!";
                    break;
                case 2: // Precio fijo
                    $precioEspecial = round($producto->precio * rand(60, 80) / 100, 2);
                    $descuentoPorcentaje = null;
                    $descripcion = "Precio especial de {$dia}: Bs {$precioEspecial}";
                    break;
                case 3: // 2x1 o combo
                    $descuentoPorcentaje = 50;
                    $precioEspecial = null;
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
                'precio_especial' => $precioEspecial,
                'descripcion_especial' => $descripcion,
                'activo' => true,
                'prioridad' => 1,
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
            // Buscar un producto y día que no esté usado
            $producto = $this->getProductoConDiaDisponible($productos, $evento['fecha'], $usados);
            $diaSemana = $this->getDiaSemana($evento['fecha']);
            $usados[$producto->id][] = $diaSemana;
            
            $especiales[] = [
                'producto_id' => $producto->id,
                'dia_semana' => $diaSemana,
                'fecha_especifica' => $evento['fecha']->format('Y-m-d'),
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'descuento_porcentaje' => rand(20, 50),
                'precio_especial' => null,
                'descripcion_especial' => $evento['descripcion'] . ' - ¡No te lo pierdas!',
                'activo' => true,
                'prioridad' => 2,
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
            // Buscar un producto y día que no esté usado
            $producto = $this->getProductoConDiaDisponible($productos, $rango['inicio'], $usados);
            $diaSemana = $this->getDiaSemana($rango['inicio']);
            $usados[$producto->id][] = $diaSemana;
            
            $especiales[] = [
                'producto_id' => $producto->id,
                'dia_semana' => $diaSemana,
                'fecha_especifica' => null,
                'fecha_inicio' => $rango['inicio']->format('Y-m-d H:i:s'),
                'fecha_fin' => $rango['fin']->format('Y-m-d H:i:s'),
                'descuento_porcentaje' => null,
                'precio_especial' => round($producto->precio * 0.75, 2),
                'descripcion_especial' => $rango['descripcion'] . ' - Oferta válida del ' . 
                    $rango['inicio']->format('d/m') . ' al ' . $rango['fin']->format('d/m'),
                'activo' => true,
                'prioridad' => 3,
            ];
        }

        // 4. ESPECIALES INACTIVOS (históricos)
        for ($i = 0; $i < 5; $i++) {
            $diasPasados = rand(30, 90);
            $fechaPasada = Carbon::now()->subDays($diasPasados);
            
            // Buscar un producto y día que no esté usado
            $producto = $this->getProductoConDiaDisponible($productos, $fechaPasada, $usados);
            $diaSemana = $this->getDiaSemana($fechaPasada);
            $usados[$producto->id][] = $diaSemana;
            
            $especiales[] = [
                'producto_id' => $producto->id,
                'dia_semana' => $diaSemana,
                'fecha_especifica' => $fechaPasada->format('Y-m-d'),
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'descuento_porcentaje' => rand(15, 35),
                'precio_especial' => null,
                'descripcion_especial' => 'Promoción pasada - Ya no disponible',
                'activo' => false,
                'prioridad' => 1,
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
    
    /**
     * Obtiene un producto con un día disponible que no viole el constraint UNIQUE
     */
    private function getProductoConDiaDisponible($productos, Carbon $fecha, array &$usados)
    {
        $diaSemana = $this->getDiaSemana($fecha);
        $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        
        // Intentar encontrar un producto que no tenga este día usado
        $productosDisponibles = $productos->shuffle();
        
        foreach ($productosDisponibles as $producto) {
            if (!isset($usados[$producto->id]) || !in_array($diaSemana, $usados[$producto->id])) {
                return $producto;
            }
        }
        
        // Si todos tienen el día usado, buscar un producto con cualquier día disponible
        foreach ($productosDisponibles as $producto) {
            $diasUsados = $usados[$producto->id] ?? [];
            $diasLibres = array_diff($diasSemana, $diasUsados);
            
            if (!empty($diasLibres)) {
                // Cambiar el día de la fecha al primer día disponible
                return $producto;
            }
        }
        
        // Último recurso: devolver un producto aleatorio (podría fallar pero es improbable con 17 productos)
        return $productos->random();
    }

    /**
     * Convierte una fecha Carbon al nombre del día de semana en español
     */
    private function getDiaSemana(Carbon $fecha): string
    {
        $dias = [
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
        ];
        
        return $dias[$fecha->dayOfWeek];
    }
}
