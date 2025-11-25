<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SanitarioLista;
use App\Models\SanitarioItem;

class SanitarioSeeder extends Seeder
{
    public function run()
    {
        // Evitar duplicados si ya se corrió
        if (SanitarioLista::count() > 0) return;

        // 1. Limpieza Apertura
        $lista1 = SanitarioLista::create([
            'nombre' => 'Limpieza Apertura',
            'descripcion' => 'Protocolo de limpieza antes de abrir al público.'
        ]);

        SanitarioItem::create(['lista_id' => $lista1->id, 'texto' => 'Pisos barridos y trapeados', 'tipo' => 'check']);
        SanitarioItem::create(['lista_id' => $lista1->id, 'texto' => 'Mesas y sillas limpias', 'tipo' => 'check']);
        SanitarioItem::create(['lista_id' => $lista1->id, 'texto' => 'Baños limpios y con insumos', 'tipo' => 'check']);
        SanitarioItem::create(['lista_id' => $lista1->id, 'texto' => 'Foto del salón ordenado', 'tipo' => 'foto']);

        // 2. Recepción de Productos
        $lista2 = SanitarioLista::create([
            'nombre' => 'Recepción de Productos',
            'descripcion' => 'Verificación de estado de productos al recibir proveedores.'
        ]);
        
        SanitarioItem::create(['lista_id' => $lista2->id, 'texto' => 'Empaques íntegros', 'tipo' => 'check']);
        SanitarioItem::create(['lista_id' => $lista2->id, 'texto' => 'Fechas de vencimiento vigentes', 'tipo' => 'check']);
        SanitarioItem::create(['lista_id' => $lista2->id, 'texto' => 'Temperatura de productos refrigerados (°C)', 'tipo' => 'numero']);
        SanitarioItem::create(['lista_id' => $lista2->id, 'texto' => 'Observaciones', 'tipo' => 'texto']);
    }
}
