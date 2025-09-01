<?php

namespace Database\Factories;

use App\Models\EspecialDelDia;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class EspecialDelDiaFactory extends Factory
{
    protected $model = EspecialDelDia::class;

    public function definition()
    {
        $producto = Producto::inRandomOrder()->first();
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        
        return [
            'producto_id' => $producto?->id ?? Producto::factory(),
            'dia_semana' => $this->faker->randomElement($dias),
            'descuento_porcentaje' => $this->faker->optional(0.7)->randomFloat(2, 5, 30),
            'precio_especial' => $this->faker->optional(0.3)->randomFloat(2, 15, 50),
            'descripcion_especial' => $this->faker->sentence(),
            'activo' => $this->faker->boolean(80),
            'prioridad' => $this->faker->numberBetween(1, 5)
        ];
    }

    public function activo()
    {
        return $this->state(['activo' => true]);
    }

    public function inactivo()
    {
        return $this->state(['activo' => false]);
    }

    public function conDescuentoPorcentaje($porcentaje = 15)
    {
        return $this->state([
            'descuento_porcentaje' => $porcentaje,
            'precio_especial' => null
        ]);
    }

    public function conPrecioEspecial($precio)
    {
        return $this->state([
            'precio_especial' => $precio,
            'descuento_porcentaje' => null
        ]);
    }

    public function paraHoy()
    {
        $hoy = Carbon::now('America/La_Paz');
        $dias = [
            0 => 'domingo', 1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado'
        ];
        
        return $this->state([
            'dia_semana' => $dias[$hoy->dayOfWeek]
        ]);
    }
}