<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoMesa;
use App\Models\PedidoMesaItem;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class PedidoMesaController extends Controller
{
    use AuthorizesRequests;
    // Mostrar todos los pedidos en mesa
    public function index()
    {
        $this->authorize('ver-ordenes');
        $pedidos = PedidoMesa::with('mesa', 'cliente', 'atendidoPor', 'items.producto')->get();
        return view('cajero.pedido_mesas.index', compact('pedidos'));
    }

    // Mostrar formulario para crear un pedido en mesa
    public function create()
    {
        $mesas = Mesa::all();
        $clientes = User::role('cliente')->get();   // Usando Spatie
        $empleados = User::role('cajero')->get();   // Usando Spatie
        $productos = Producto::with('categoria')->get();
        $categorias = Categoria::all();
        
        return view('cajero.pedido_mesas.create', compact('mesas', 'clientes', 'empleados', 'productos', 'categorias'));
    }
    // editar un pedido en mesa
    public function edit($id)
    {
        $this->authorize('editar-ordenes');
        $pedido = PedidoMesa::with('mesa', 'cliente', 'items.producto')->findOrFail($id);
        $mesas = Mesa::all();
        $clientes = User::role('cliente')->get();   // Solo clientes
        $empleados = User::role('cajero')->get();   // Solo cajeros
        $productos = Producto::with('categoria')->get();
        $categorias = Categoria::all();
        $itemsJson = $pedido->items->map(function($item) {
            return [
                'producto_id' => $item->producto_id,
                'nombre' => $item->producto->nombre ?? '-',
                'precio' => floatval($item->precio), 
                'imagen' => $item->producto->imagen ?? '',
                'cantidad' => intval($item->cantidad) 
            ];
        })->toArray();
        return view('cajero.pedido_mesas.edit', compact(
            'pedido', 'mesas', 'clientes', 'empleados', 'productos', 'categorias', 'itemsJson'
        ));
    }

    // Actualizar un pedido en mesa
public function update(Request $request, $id)
{
    $this->authorize('editar-ordenes');
    $request->validate([
        'mesa_id' => 'required|exists:mesas,id',
        'cliente_id' => 'nullable|exists:users,id',
        'estado' => 'required|in:pendiente,enviado,anulado,servido',
        'notas' => 'nullable|string',
        'items' => 'required|array',
        'items.*.producto_id' => 'required|exists:productos,id',
        'items.*.cantidad' => 'required|integer|min:1',
        'items.*.precio' => 'required|numeric|min:0',
    ]);

    $pedido = PedidoMesa::findOrFail($id);
    $pedido->update($request->only(['mesa_id', 'cliente_id', 'estado', 'notas']));

    // Eliminar los items anteriores
    $pedido->items()->delete();

    // Crear los nuevos items
    foreach ($request->items as $item) {
        $pedido->items()->create([
            'producto_id' => $item['producto_id'],
            'cantidad' => $item['cantidad'],
            'precio' => $item['precio'],
        ]);
    }

    return redirect()->route('pedido-mesas.index')->with('success', 'Pedido actualizado correctamente.');
}
    // Guardar un nuevo pedido en mesa
    public function store(Request $request)
    {
        $this->authorize('crear-ordenes');
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'cliente_id' => 'nullable|exists:users,id',
            'atendido_por' => 'required|exists:users,id',
            'estado' => 'required|in:pendiente,enviado,anulado,servido',
            'notas' => 'nullable|string',
            'items' => 'required|array',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
        ]);

        $pedido = PedidoMesa::create($request->only([
            'mesa_id', 'cliente_id', 'atendido_por', 'estado', 'notas'
        ]));

        foreach ($request->items as $item) {
            PedidoMesaItem::create([
                'pedido_mesa_id' => $pedido->id,
                'producto_id' => $item['producto_id'],
                'cantidad' => $item['cantidad'],
                'precio' => $item['precio'],
            ]);
        }

        return redirect()->route('pedido-mesas.index')->with('success', 'Pedido en mesa creado correctamente.');
    }

    // Mostrar un pedido en mesa
    public function show($id)
    {
        $this->authorize('ver-ordenes');
        $pedido = PedidoMesa::with('mesa', 'cliente', 'atendidoPor', 'items.producto')->findOrFail($id);
        return view('cajero.pedido_mesas.show', compact('pedido'));
    }

    // Eliminar un pedido en mesa
    public function destroy($id)
    {
        $this->authorize('anular-ordenes');
        $pedido = PedidoMesa::findOrFail($id);
        $pedido->delete();
        return redirect()->route('pedido-mesas.index')->with('success', 'Pedido eliminado correctamente.');
    }

        public function completar($id)
    {
        // Lógica para marcar como completado
        $this->authorize('completar-ordenes');
        $pedido = PedidoMesa::findOrFail($id);
        $pedido->estado = 'servido';
        $pedido->save();
        return redirect()->route('pedido-mesas.index')->with('success', 'Pedido marcado como completado.');
    }

    public function procesar($id)
    {
        // Lógica para procesar el pedido
        $this->authorize('procesar-ordenes');
        $pedido = PedidoMesa::findOrFail($id);
        $pedido->estado = 'enviado';
        $pedido->save();
        return redirect()->route('pedido-mesas.index')->with('success', 'Pedido enviado a barra/cocina.');
    }
}
