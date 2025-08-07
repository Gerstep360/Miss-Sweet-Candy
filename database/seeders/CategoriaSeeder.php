<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            // Cafés
            'Café',
            'Latte',

            // Postres y dulces
            'Masitas',
            'Minitorta',
            'Torta',
            'Postre',

            // Snacks y salados
            'Salteña',

            // Helados
            'Helado',
        ];

        foreach ($categorias as $nombre) {
            Categoria::create([
                'nombre' => $nombre,
            ]);
        }
    }
}
