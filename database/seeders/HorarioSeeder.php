<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Horario; 

class HorarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $dias = [
            'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'
        ];
        foreach ($dias as $dia) {
            Horario::create([
                'dia' => $dia,
                'abre' => '06:00:00',
                'cierra' => '22:00:00',
            ]);
        }
    }
}
