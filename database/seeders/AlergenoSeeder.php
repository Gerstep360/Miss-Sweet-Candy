<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alergeno;

class AlergenoSeeder extends Seeder
{
    public function run(): void
    {
        $alergenos = Alergeno::alergenosComunes();

        foreach ($alergenos as $alergeno) {
            Alergeno::firstOrCreate(
                ['nombre' => $alergeno['nombre']],
                $alergeno
            );
        }

        $this->command->info('✅ Alérgenos comunes creados exitosamente');
    }
}
