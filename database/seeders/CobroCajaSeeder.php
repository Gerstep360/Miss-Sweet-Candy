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
            'pedido_id' => 1,
            'importe' => 100.00,
            'metodo' => 'efectivo',
            'estado' => 'cobrado',
            'comprobante' => 'CAJA-0001',
            'cajero_id' => 1,
        ]);
    }
}
