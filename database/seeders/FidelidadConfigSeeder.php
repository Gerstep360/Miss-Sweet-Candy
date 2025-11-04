<?php
// database/seeders/FidelidadConfigSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FidelidadConfig;

class FidelidadConfigSeeder extends Seeder
{
    public function run()
    {
        $configuraciones = [
            // Configuración básica de puntos
            [
                'clave' => 'puntos_por_dolar',
                'valor' => '10',
                'tipo' => 'integer',
                'descripcion' => 'Puntos otorgados por cada $1 gastado en pedidos',
                'categoria' => 'puntos'
            ],
            [
                'clave' => 'activo',
                'valor' => 'true',
                'tipo' => 'boolean',
                'descripcion' => 'Estado del programa de fidelidad (activo/inactivo)',
                'categoria' => 'estado'
            ],

            // Configuración de antigüedad
            [
                'clave' => 'puntos_por_antiguedad_meses',
                'valor' => '12',
                'tipo' => 'integer',
                'descripcion' => 'Meses requeridos para bonificación por antigüedad',
                'categoria' => 'antiguedad'
            ],
            [
                'clave' => 'puntos_antiguedad_base',
                'valor' => '50',
                'tipo' => 'integer',
                'descripcion' => 'Puntos base otorgados por antigüedad cada período',
                'categoria' => 'antiguedad'
            ],

            // Multiplicadores
            [
                'clave' => 'multiplicador_fin_semana',
                'valor' => '1.5',
                'tipo' => 'float',
                'descripcion' => 'Multiplicador de puntos los fines de semana (sábado y domingo)',
                'categoria' => 'multiplicadores'
            ],

            // Recompensas predefinidas
            [
                'clave' => 'recompensa_descuento_10_puntos',
                'valor' => '100',
                'tipo' => 'integer',
                'descripcion' => 'Puntos requeridos para canjear 10% de descuento',
                'categoria' => 'recompensas'
            ],
            [
                'clave' => 'recompensa_descuento_20_puntos',
                'valor' => '200',
                'tipo' => 'integer',
                'descripcion' => 'Puntos requeridos para canjear 20% de descuento',
                'categoria' => 'recompensas'
            ],
            [
                'clave' => 'recompensa_producto_gratis_puntos',
                'valor' => '150',
                'tipo' => 'integer',
                'descripcion' => 'Puntos requeridos para canjear producto gratis',
                'categoria' => 'recompensas'
            ]
        ];

        foreach ($configuraciones as $config) {
            FidelidadConfig::create($config);
        }

        $this->command->info('Configuración de fidelidad insertada correctamente.');
    }
}