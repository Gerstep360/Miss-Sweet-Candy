<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClientePerfil;

class ClientePerfilSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener usuarios con rol cliente
        $clientes = User::role('cliente')->get();

        foreach ($clientes as $cliente) {
            // Verificar si ya tiene perfil
            if ($cliente->perfil) {
                continue;
            }

            // Crear perfiles de ejemplo
            $perfilData = $this->generarPerfilEjemplo($cliente->id);

            ClientePerfil::create(array_merge(
                ['user_id' => $cliente->id],
                $perfilData
            ));
        }
    }

    private function generarPerfilEjemplo($userId): array
    {
        $ejemplos = [
            // Cliente 1: Con alergias graves
            [
                'telefono' => '77123456',
                'direccion' => 'Av. 6 de Agosto #1234, La Paz',
                'alergias' => [
                    ['nombre' => 'Nueces', 'severidad' => 'grave'],
                    ['nombre' => 'Mariscos', 'severidad' => 'grave'],
                ],
                'preferencias' => ['vegetariano', 'sin_gluten'],
                'acepta_marketing' => true,
            ],
            // Cliente 2: Vegano sin alergias
            [
                'telefono' => '71234567',
                'direccion' => 'Calle Comercio #567, La Paz',
                'alergias' => [],
                'preferencias' => ['vegano', 'sin_azucar'],
                'acepta_marketing' => false,
            ],
            // Cliente 3: Con alergias moderadas
            [
                'telefono' => '72345678',
                'direccion' => 'Zona Sur, Calle 21 #890',
                'alergias' => [
                    ['nombre' => 'Lactosa', 'severidad' => 'moderado'],
                ],
                'preferencias' => ['sin_lactosa'],
                'acepta_marketing' => true,
            ],
            // Cliente 4: Sin restricciones
            [
                'telefono' => '73456789',
                'direccion' => 'Sopocachi, Calle Guachalla #123',
                'alergias' => [],
                'preferencias' => [],
                'acepta_marketing' => false,
            ],
        ];

        // Retornar un perfil aleatorio o el índice correspondiente
        $index = ($userId - 1) % count($ejemplos);
        return $ejemplos[$index];
    }
}
