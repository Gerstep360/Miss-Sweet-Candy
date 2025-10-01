<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CobroCaja;
use App\Models\Pedido;

class CobroCajaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el primer pedido de mesa
        $pedidoMesa = Pedido::where('tipo', 'mesa')->first();
        
        if (!$pedidoMesa) {
            $this->command->warn('⚠️  No se encontró ningún pedido de mesa para crear el cobro.');
            return;
        }

        CobroCaja::create([
            'pedido_id' => $pedidoMesa->id,
            'importe' => 150.00,
            'metodo' => 'pos',
            'estado' => 'cobrado',
            'comprobante' => 'CAJA-0001',
            'cajero_id' => 1,
        ]);

        $this->command->info('✅ Cobro de caja creado exitosamente.');
    }
}
