<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Usuarios\BitacoraController;

class MenuPublicoController extends Controller
{
    public function index()
    {
        // Cargar categorías con productos + relaciones necesarias para el menú
        $categorias = Categoria::with([
            'productos' => function ($q) {
                $q->with(['inventario', 'especialVigente'])
                  ->orderBy('nombre');
            }
        ])->orderBy('nombre')->get();

        // Asegurar que el JSON de productos trae campos listos para el UI
        $categorias->each(function ($cat) {
            $cat->productos->each->append([
                'imagen_url',
                'precio_vigente',
                'tiene_oferta',
                'porcentaje_oferta',
                'ahorro_oferta',
                'stock_actual',
                'estado_stock',
            ]);
        });

        BitacoraController::registrar('ver menu publico', 'MenuPublico', null);

        return view('menu', compact('categorias'));
    }
}
