<?php


namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class MenuPublicoController extends Controller
{
    public function index()
    {
        // Obtiene todas las categorías con sus productos (sin filtro de activo)
        $categorias = Categoria::with('productos')->get();

        // Cambia la vista a 'menu'
        return view('menu', compact('categorias'));
    }
}