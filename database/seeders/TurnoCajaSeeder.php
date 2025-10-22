<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TurnoCaja;
use App\Models\User;
use Carbon\Carbon;

class TurnoCajaSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener cajeros
        $cajeros = User::role('cajero')->get();

        if ($cajeros->isEmpty()) {
            $this->command->warn('No hay cajeros en la base de datos. Ejecuta UserSeeder primero.');
            return;
        }

        $turnos = [];

        // Turnos cerrados de los últimos 30 días
        for ($i = 30; $i >= 1; $i--) {
            $fecha = Carbon::now()->subDays($i);
            
            // Turno mañana (08:00 - 14:00)
            if (rand(0, 10) > 2) { // 80% de probabilidad
                $cajero = $cajeros->random();
                $inicio = $fecha->copy()->setTime(8, 0, 0);
                $fin = $fecha->copy()->setTime(14, rand(0, 30), rand(0, 59));
                
                $turnos[] = [
                    'cajero_id' => $cajero->id,
                    'inicio' => $inicio,
                    'fin' => $fin,
                    'monto_inicial' => rand(50000, 100000) / 100, // 500-1000 Bs
                    'estado' => 'cerrado',
                ];
            }

            // Turno tarde (14:00 - 20:00)
            if (rand(0, 10) > 1) { // 90% de probabilidad
                $cajero = $cajeros->random();
                $inicio = $fecha->copy()->setTime(14, 0, 0);
                $fin = $fecha->copy()->setTime(20, rand(0, 30), rand(0, 59));
                
                $turnos[] = [
                    'cajero_id' => $cajero->id,
                    'inicio' => $inicio,
                    'fin' => $fin,
                    'monto_inicial' => rand(50000, 100000) / 100,
                    'estado' => 'cerrado',
                ];
            }
        }

        // Turno activo de hoy (si es horario laboral)
        $horaActual = Carbon::now()->hour;
        if ($horaActual >= 8 && $horaActual < 20 && rand(0, 10) > 3) {
            $cajero = $cajeros->random();
            $inicio = Carbon::now()->setTime(rand(8, 14), rand(0, 59), 0);
            
            $turnos[] = [
                'cajero_id' => $cajero->id,
                'inicio' => $inicio,
                'fin' => null,
                'monto_inicial' => rand(50000, 100000) / 100,
                'estado' => 'activo',
            ];
        }

        foreach ($turnos as $turno) {
            TurnoCaja::create($turno);
        }

        $this->command->info('✅ Turnos de caja creados: ' . count($turnos));
    }
}
