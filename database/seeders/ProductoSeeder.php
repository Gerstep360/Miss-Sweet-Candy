<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtén los IDs de las categorías por nombre
        $categorias = Categoria::pluck('id', 'nombre');

        $productos = [
            // Cafés
            [
                'nombre' => 'Café Americano',
                'categoria' => 'Café',
                'unidad' => 'Taza',
                'precio' => 20,
                'imagen' => 'img/productos/JZMXfBxxo7mgGBGtoy3mbPU0TClHXbzW3zGzHPy2.png',
            ],
            [
                'nombre' => 'Café Espresso',
                'categoria' => 'Café',
                'unidad' => 'Taza',
                'precio' => 25,
                'imagen' => 'img/productos/vodMGgn8ZqeuCFSqu0yBPlzevsJWXKrGvfH2pb5V.jpg',
            ],

            // Latte
            [
                'nombre' => 'Latte Vainilla',
                'categoria' => 'Latte',
                'unidad' => 'Taza',
                'precio' => 30,
                'imagen' => 'img/productos/latte_vainilla.jpg',
            ],
            [
                'nombre' => 'Latte Caramelo',
                'categoria' => 'Latte',
                'unidad' => 'Taza',
                'precio' => 32,
                'imagen' => 'img/productos/latte_caramelo.jpg',
            ],

            // Postres y dulces
            [
                'nombre' => 'Masitas de Chocolate',
                'categoria' => 'Masitas',
                'unidad' => 'Porción',
                'precio' => 15,
                'imagen' => 'img/productos/masitas_chocolate.jpg',
            ],
            [
                'nombre' => 'Masitas de Limón',
                'categoria' => 'Masitas',
                'unidad' => 'Porción',
                'precio' => 15,
                'imagen' => 'img/productos/masitas_limon.jpg',
            ],

            [
                'nombre' => 'Minitorta de Frutilla',
                'categoria' => 'Minitorta',
                'unidad' => 'Unidad',
                'precio' => 18,
                'imagen' => 'img/productos/minitorta_frutilla.jpg',
            ],
            [
                'nombre' => 'Minitorta de Chocolate',
                'categoria' => 'Minitorta',
                'unidad' => 'Unidad',
                'precio' => 18,
                'imagen' => 'img/productos/minitorta_chocolate.jpg',
            ],

            [
                'nombre' => 'Torta Selva Negra',
                'categoria' => 'Torta',
                'unidad' => 'Porción',
                'precio' => 25,
                'imagen' => 'img/productos/torta_selva_negra.jpg',
            ],
            [
                'nombre' => 'Torta de Zanahoria',
                'categoria' => 'Torta',
                'unidad' => 'Porción',
                'precio' => 25,
                'imagen' => 'img/productos/torta_zanahoria.jpg',
            ],

            [
                'nombre' => 'Postre Tres Leches',
                'categoria' => 'Postre',
                'unidad' => 'Porción',
                'precio' => 22,
                'imagen' => 'img/productos/postre_tres_leches.jpg',
            ],
            [
                'nombre' => 'Postre Flan',
                'categoria' => 'Postre',
                'unidad' => 'Porción',
                'precio' => 20,
                'imagen' => 'img/productos/postre_flan.jpg',
            ],

            // Snacks y salados
            [
                'nombre' => 'Salteña de Pollo',
                'categoria' => 'Salteña',
                'unidad' => 'Unidad',
                'precio' => 10,
                'imagen' => 'img/productos/saltena_pollo.jpg',
            ],
            [
                'nombre' => 'Salteña de Carne',
                'categoria' => 'Salteña',
                'unidad' => 'Unidad',
                'precio' => 10,
                'imagen' => 'img/productos/saltena_carne.jpg',
            ],

            // Helados
            [
                'nombre' => 'Helado de Vainilla',
                'categoria' => 'Helado',
                'unidad' => 'Porción',
                'precio' => 12,
                'imagen' => 'img/productos/helado_vainilla.jpg',
            ],
            [
                'nombre' => 'Helado de Chocolate',
                'categoria' => 'Helado',
                'unidad' => 'Porción',
                'precio' => 12,
                'imagen' => 'img/productos/helado_chocolate.jpg',
            ],

            //prueba (parece que si soporta Gif, ideal para presentaciones llamativas)
            [
                'nombre' => 'Pruebas',
                'categoria' => 'Café',
                'unidad' => 'Taza',
                'precio' => 22,
                'imagen' => 'img/productos/tLqcNV7GKin48PYbZr9JZXAdASeYLXLhxr74NVVE.gif',
            ],
        ];

        
        foreach ($productos as $p) {
            Producto::create([
                'categoria_id' => $categorias[$p['categoria']],
                'nombre' => $p['nombre'],
                'unidad' => $p['unidad'],
                'precio' => $p['precio'],
                'imagen' => $p['imagen'],
            ]);
        }
    }
}
