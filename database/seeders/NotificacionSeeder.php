<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notificacion;
use App\Models\User;

class NotificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = User::pluck('id');
        if ($usuarios->isEmpty()) {
            // Si no hay usuarios, no se puede crear notificaciones
            return;
        }

        $tipos = ['stock', 'pedido', 'reserva', 'sistema'];
        $canales = ['panel', 'email', 'push'];

        foreach (range(1, 20) as $i) {
            Notificacion::create([
                'tipo' => $tipos[array_rand($tipos)],
                'canal' => $canales[array_rand($canales)],
                'mensaje' => fake()->sentence(8),
                'usuario_destino_id' => $usuarios->random(),
                'rel_model' => null,
                'rel_id' => null,
                'leido' => (bool)random_int(0, 1),
            ]);
        }
    }
}
