<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CobroCaja;
use App\Models\PedidoMostrador;
use App\Models\PedidoMesa;

class CobroCajaController extends Controller
{
    // Mostrar todos los pedidos pendientes de cobro (mostrador y mesa)
    public function index()
    {
        // Pendientes de cobro
        $pendientesMostrador = PedidoMostrador::where('estado', 'enviado')->get();
        $pendientesMesa = PedidoMesa::where('estado', 'servido')->get();

        // Pagados
        $pagadosMostrador = PedidoMostrador::where('estado', 'cancelado')->get();
        $pagadosMesa = PedidoMesa::where('estado', 'cancelado')->get();

        return view('cajero.cobro_caja.index', compact(
            'pendientesMostrador',
            'pendientesMesa',
            'pagadosMostrador',
            'pagadosMesa'
        ));
    }
    //Mostrar detalles de un cobro
    public function show($tipo, $id)
    {
        if ($tipo === 'mostrador') {
            $pedido = PedidoMostrador::findOrFail($id);
            $cobro = CobroCaja::where('pedido_mostrador_id', $pedido->id)->latest()->first();
        } elseif ($tipo === 'mesa') {
            $pedido = PedidoMesa::findOrFail($id);
            $cobro = CobroCaja::where('pedido_mesa_id', $pedido->id)->latest()->first();
        } else {
            abort(404);
        }

        return view('cajero.cobro_caja.show', compact('pedido', 'cobro', 'tipo'));
    }
    // Mostrar formulario de cobro para un pedido
    public function create($tipo, $id)
    {
        if ($tipo === 'mostrador') {
            $pedido = PedidoMostrador::findOrFail($id);
        } elseif ($tipo === 'mesa') {
            $pedido = PedidoMesa::findOrFail($id);
        }
        // Agrega otros tipos aquí...

        return view('cajero.cobro_caja.create', compact('pedido', 'tipo'));
    }

    // Registrar el cobro y cambiar el estado del pedido
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|integer',
            'tipo' => 'required|string',
            'importe' => 'required|numeric|min:0',
            'metodo' => 'required|in:efectivo,pos',
        ]);

        // Buscar el pedido según el tipo
        if ($request->tipo === 'mostrador') {
            $pedido = PedidoMostrador::findOrFail($request->pedido_id);
            $cobro = CobroCaja::create([
                'pedido_mostrador_id' => $pedido->id,
                'importe' => $request->importe,
                'metodo' => $request->metodo,
                'estado' => 'cancelado',
                'comprobante' => 'CAJA-' . now()->format('YmdHis'),
                'cajero_id' => auth()->id(),
            ]);
            $pedido->estado = 'cancelado';
        } elseif ($request->tipo === 'mesa') {
            $pedido = PedidoMesa::findOrFail($request->pedido_id);
            $cobro = CobroCaja::create([
                'pedido_mesa_id' => $pedido->id,
                'importe' => $request->importe,
                'metodo' => $request->metodo,
                'estado' => 'cancelado',
                'comprobante' => 'CAJA-' . now()->format('YmdHis'),
                'cajero_id' => auth()->id(),
            ]);
            $pedido->estado = 'cancelado';
        }
$pedido->save();

        return redirect()->route('cobro_caja.index')->with('success', 'Cobro registrado correctamente.');
    }
}