<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PedidoMesa;
use App\Models\Mesa;
use App\Models\User;
class PedidoMesaSeeder extends Seeder
{
    public function run(): void
    {
        $mesa = Mesa::first();
        $cliente = User::where('name', 'Cliente')->first(); // usuario genérico
        $cajero = User::first(); // quien atiende

        PedidoMesa::create([
            'mesa_id' => $mesa->id,
            'cliente_id' => $cliente ? $cliente->id : null,
            'atendido_por' => $cajero->id,
            'estado' => 'pendiente',
            'notas' => 'Pedido de ejemplo',
        ]);
    }
}
