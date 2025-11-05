<?php
// database/seeders/FidelidadConfigSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FidelidadConfig;

class FidelidadConfigSeeder extends Seeder
{
    // En database/seeders/FidelidadConfigSeeder.php

public function run()
{
    $configuraciones = [
        // Configuración básica de puntos
        [
            'clave' => 'puntos_por_dolar',
            'valor' => '10', // Se guarda como string pero se castea a integer
            'tipo' => 'integer',
            'descripcion' => 'Puntos otorgados por cada $1 gastado en pedidos',
            'categoria' => 'puntos'
        ],
        [
            'clave' => 'activo',
            'valor' => '1', // Cambiar de 'true' a '1' para boolean
            'tipo' => 'boolean',
            'descripcion' => 'Estado del programa de fidelidad (activo/inactivo)',
            'categoria' => 'estado'
        ],
        // ... resto de configuraciones
    ];

    foreach ($configuraciones as $config) {
        FidelidadConfig::create($config);
    }
}
}