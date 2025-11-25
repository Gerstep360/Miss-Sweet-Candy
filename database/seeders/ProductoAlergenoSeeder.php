<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Alergeno;

class ProductoAlergenoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener alérgenos
        $gluten = Alergeno::where('nombre', 'Gluten')->first();
        $lacteos = Alergeno::where('nombre', 'Lácteos')->first();
        $huevo = Alergeno::where('nombre', 'Huevo')->first();
        $frutosSecos = Alergeno::where('nombre', 'Frutos Secos')->first();
        $soja = Alergeno::where('nombre', 'Soja')->first();

        // Ejemplos de asignación (ajusta según tus productos reales)
        
        // Café con leche - contiene lácteos
        $producto = Producto::where('nombre', 'LIKE', '%Café con Leche%')->first();
        if ($producto && $lacteos) {
            $producto->alergenos()->syncWithoutDetaching([
                $lacteos->id => ['nivel_presencia' => 'contiene']
            ]);
        }

        // Cappuccino - contiene lácteos
        $producto = Producto::where('nombre', 'LIKE', '%Cappuccino%')->first();
        if ($producto && $lacteos) {
            $producto->alergenos()->syncWithoutDetaching([
                $lacteos->id => ['nivel_presencia' => 'contiene']
            ]);
        }

        // Pan dulce - contiene gluten, huevo, puede contener frutos secos
        $producto = Producto::where('nombre', 'LIKE', '%Pan%')->orWhere('nombre', 'LIKE', '%Croissant%')->first();
        if ($producto) {
            $alergenos = [];
            if ($gluten) $alergenos[$gluten->id] = ['nivel_presencia' => 'contiene'];
            if ($huevo) $alergenos[$huevo->id] = ['nivel_presencia' => 'contiene'];
            if ($frutosSecos) $alergenos[$frutosSecos->id] = ['nivel_presencia' => 'puede_contener'];
            
            if (!empty($alergenos)) {
                $producto->alergenos()->syncWithoutDetaching($alergenos);
            }
        }

        // Productos con soja (leches vegetales)
        $producto = Producto::where('nombre', 'LIKE', '%Soja%')->first();
        if ($producto && $soja) {
            $producto->alergenos()->syncWithoutDetaching([
                $soja->id => ['nivel_presencia' => 'contiene']
            ]);
        }

        $this->command->info('✅ Alérgenos asignados a productos de ejemplo');
        $this->command->warn('⚠️  Revisa y ajusta las asignaciones según tus productos reales');
    }
}
