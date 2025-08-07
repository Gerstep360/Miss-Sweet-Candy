<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoMostrador;
use App\Models\PedidoMostradorItem;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class PedidoMostradorController extends Controller
{
   use AuthorizesRequests;

    // Mostrar todos los pedidos de mostrador
    public function index()
    {
        $this->authorize('ver-ordenes');
        $pedidos = PedidoMostrador::with('cliente', 'atendidoPor', 'items.producto')->get();
        return view('cajero.pedido_mostrador.index', compact('pedidos'));
    }

    // Mostrar formulario para crear un pedido de mostrador
    public function create()
    {
        $clientes = User::role('cliente')->get();
        $empleados = User::role('cajero')->get();
        $productos = Producto::with('categoria')->get();
        $categorias = Categoria::all();

        return view('cajero.pedido_mostrador.create', compact('clientes', 'empleados', 'productos', 'categorias'));
    }

    // Guardar un nuevo pedido de mostrador
    public function store(Request $request)
    {
        $this->authorize('crear-ordenes');
        $request->validate([
            'cliente_id' => 'nullable|exists:users,id',
            'atendido_por' => 'required|exists:users,id',
            'estado' => 'required|in:pendiente,enviado,anulado,retirado',
            'notas' => 'nullable|string',
            'items' => 'required|array',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
        ]);

        $pedido = PedidoMostrador::create($request->only([
            'cliente_id', 'atendido_por', 'estado', 'notas'
        ]));

        foreach ($request->items as $item) {
            PedidoMostradorItem::create([
                'pedido_mostrador_id' => $pedido->id,
                'producto_id' => $item['producto_id'],
                'cantidad' => $item['cantidad'],
                'precio' => $item['precio'],
            ]);
        }

        // Aquí podrías imprimir el ticket de cocina si lo necesitas

        return redirect()->route('pedido_mostrador.index')->with('success', 'Pedido para llevar creado correctamente.');
    }

    // Mostrar un pedido de mostrador
    public function show($id)
    {
        $this->authorize('ver-ordenes');
        $pedido = PedidoMostrador::with('cliente', 'atendidoPor', 'items.producto')->findOrFail($id);
        return view('cajero.pedido_mostrador.show', compact('pedido'));
    }

    // Mostrar formulario para editar un pedido de mostrador
    public function edit($id)
    {
        $this->authorize('editar-ordenes');
        $pedido = PedidoMostrador::with('cliente', 'items.producto')->findOrFail($id);
        $clientes = User::role('cliente')->get();
        $empleados = User::role('cajero')->get();
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

        return view('cajero.pedido_mostrador.edit', compact(
            'pedido', 'clientes', 'empleados', 'productos', 'categorias', 'itemsJson'
        ));
    }

    // Actualizar un pedido de mostrador
    public function update(Request $request, $id)
    {
        $this->authorize('editar-ordenes');
        $request->validate([
            'cliente_id' => 'nullable|exists:users,id',
            'atendido_por' => 'required|exists:users,id',
            'estado' => 'required|in:pendiente,enviado,anulado,retirado',
            'notas' => 'nullable|string',
            'items' => 'required|array',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
        ]);

        $pedido = PedidoMostrador::findOrFail($id);
        $pedido->update($request->only(['cliente_id', 'atendido_por', 'estado', 'notas']));

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

        return redirect()->route('pedido_mostrador.index')->with('success', 'Pedido actualizado correctamente.');
    }

    // Eliminar un pedido de mostrador
    public function destroy($id)
    {
        $this->authorize('anular-ordenes');
        $pedido = PedidoMostrador::findOrFail($id);
        $pedido->delete();
        return redirect()->route('pedido_mostrador.index')->with('success', 'Pedido eliminado correctamente.');
    }

    // Confirmar retiro del pedido
    public function confirmarRetiro($id)
    {
        $this->authorize('completar-ordenes');
        $pedido = PedidoMostrador::findOrFail($id);
        $pedido->estado = 'retirado';
        $pedido->save();
        return redirect()->route('pedido_mostrador.index')->with('success', 'Pedido marcado como retirado.');
    }

    // Enviar pedido a cocina
    public function procesar($id)
    {
        $this->authorize('procesar-ordenes');
        $pedido = PedidoMostrador::findOrFail($id);
        $pedido->estado = 'enviado';
        $pedido->save();
        // Aquí podrías imprimir el ticket de cocina si lo necesitas
        return redirect()->route('pedido_mostrador.index')->with('success', 'Pedido enviado a cocina.');
    }
}
