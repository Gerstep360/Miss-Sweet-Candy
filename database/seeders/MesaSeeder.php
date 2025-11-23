<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mesa;
class MesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Mesa::create(['nombre' => 'Mesa 1', 'estado' => 'libre', 'capacidad' => 4]);
        Mesa::create(['nombre' => 'Mesa 2', 'estado' => 'ocupada', 'capacidad' => 2]);
        Mesa::create(['nombre' => 'Mesa 3', 'estado' => 'reservada', 'capacidad' => 6]);
        Mesa::create(['nombre' => 'Mesa 4', 'estado' => 'libre', 'capacidad' => 4]);
        // Ejemplo de fusión: Mesa 5 fusionada con Mesa 1
        Mesa::create(['nombre' => 'Mesa 5', 'estado' => 'fusionada', 'fusion_id' => 1, 'capacidad' => 2]);
    }
}
