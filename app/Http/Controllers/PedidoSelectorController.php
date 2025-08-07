<?php
namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class PedidoSelectorController extends Controller
{
    public function index(Request $request)
    {
        // Obtener todos los productos con su categoría, ordenados por nombre
        $productos = Producto::with('categoria')
            ->orderBy('nombre')
            ->get();

        // Obtener todas las categorías que tengan al menos un producto y cargar la relación productos
        $categorias = Categoria::with('productos')
            ->whereHas('productos')
            ->orderBy('nombre')
            ->get();

        // Productos existentes desde parámetros URL o JSON
        $productosSeleccionados = [];
        if ($request->has('productos_existentes')) {
            $productosExistentes = $request->get('productos_existentes');
            if (is_string($productosExistentes)) {
                $productosSeleccionados = json_decode($productosExistentes, true) ?: [];
            } else {
                $productosSeleccionados = $productosExistentes ?: [];
            }
        }

        $origen = $request->get('origen', 'default');
        
        // Para modal, renderizar parcial
        if ($request->ajax() || $request->has('modal')) {
            return view('cajero.pedido.selector-modal', compact('productos', 'categorias', 'productosSeleccionados', 'origen'));
        }

        return view('cajero.pedido.selector', compact('productos', 'categorias', 'productosSeleccionados', 'origen'));
    }

    public function confirmar(Request $request)
    {
        $productos = $request->input('productos', []);
        $origen = $request->input('origen', 'default');
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'productos' => $productos,
                'total' => collect($productos)->sum(function($item) {
                    return $item['cantidad'] * $item['precio'];
                }),
                'origen' => $origen
            ]);
        }

        return redirect()->back()->with('success', 'Productos seleccionados correctamente');
    }
}