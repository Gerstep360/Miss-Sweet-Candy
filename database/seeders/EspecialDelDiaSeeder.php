<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EspecialDelDia;
use App\Models\Producto;
use Carbon\Carbon;

class EspecialDelDiaSeeder extends Seeder
{
    public function run()
    {
        // Obtener algunos productos para los especiales
        $productos = Producto::limit(7)->get();
        
        if ($productos->count() < 7) {
            $this->command->warn('Se necesitan al menos 7 productos para crear especiales de la semana');
            return;
        }

        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        $especiales = [
            'lunes' => [
                'descuento_porcentaje' => 15,
                'descripcion_especial' => '¡Lunes de descuento! Perfecto para empezar la semana con energía.'
            ],
            'martes' => [
                'precio_especial' => 25.00,
                'descripcion_especial' => 'Martes especial con precio fijo. ¡No te lo pierdas!'
            ],
            'miercoles' => [
                'descuento_porcentaje' => 20,
                'descripcion_especial' => 'Mitad de semana, mitad de precio... ¡casi!'
            ],
            'jueves' => [
                'precio_especial' => 30.00,
                'descripcion_especial' => 'Jueves de antojo con precio especial.'
            ],
            'viernes' => [
                'descuento_porcentaje' => 10,
                'descripcion_especial' => '¡Viernes de celebración! Termina la semana con sabor.'
            ],
            'sabado' => [
                'precio_especial' => 35.00,
                'descripcion_especial' => 'Sábado especial para compartir en familia.'
            ],
            'domingo' => [
                'descuento_porcentaje' => 25,
                'descripcion_especial' => 'Domingo de relajación con descuento especial.'
            ]
        ];

        foreach ($dias as $index => $dia) {
            $producto = $productos[$index];
            $data = $especiales[$dia];
            
            EspecialDelDia::create([
                'producto_id' => $producto->id,
                'dia_semana' => $dia,
                'descuento_porcentaje' => $data['descuento_porcentaje'] ?? null,
                'precio_especial' => $data['precio_especial'] ?? null,
                'descripcion_especial' => $data['descripcion_especial'],
                'activo' => true,
                'prioridad' => 1
            ]);
        }

        // Crear algunos especiales por fecha específica (promociones temporales)
        if ($productos->count() >= 10) {
            EspecialDelDia::create([
                'producto_id' => $productos[7]->id,
                'fecha_especifica' => Carbon::now('America/La_Paz')->addDays(1)->toDateString(),
                'descuento_porcentaje' => 30,
                'descripcion_especial' => '¡Promoción especial de mañana! 30% de descuento.',
                'activo' => true,
                'prioridad' => 2
            ]);

            EspecialDelDia::create([
                'producto_id' => $productos[8]->id,
                'fecha_inicio' => Carbon::now('America/La_Paz'),
                'fecha_fin' => Carbon::now('America/La_Paz')->addWeek(),
                'precio_especial' => 20.00,
                'descripcion_especial' => 'Promoción semanal con precio especial.',
                'activo' => true,
                'prioridad' => 3
            ]);
        }

        $this->command->info('Se crearon especiales del día para toda la semana');
    }
}
