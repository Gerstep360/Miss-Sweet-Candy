<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CobroCaja;

class CobroCajaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CobroCaja::create([
            'pedido_mostrador_id' => null,
            'pedido_mesa_id' => 1,
            'importe' => 150.00,
            'metodo' => 'pos', // Cambiar 'tarjeta' por 'pos'
            'estado' => 'cobrado',
            'comprobante' => 'CAJA-0002',
            'cajero_id' => 1,
        ]);
    }
}