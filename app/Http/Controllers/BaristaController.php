<?php


namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaristaController extends Controller
{
    use AuthorizesRequests;

    /**
     * Muestra la lista de pedidos que el barista puede gestionar
     */
    public function index()
    {
        try {
            $this->authorize('gestionar-pedidos-barista');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        // Mostrar solo pedidos en estados que el barista puede gestionar
        $pedidos = Pedido::with(['cliente', 'atendidoPor', 'mesa', 'items.producto'])
            ->whereIn('estado', ['pendiente', 'en_preparacion'])
            ->latest()
            ->paginate(15);

        BitacoraController::registrar('ver lista', 'Pedido Barista', null, auth()->id());

        return view('barista.pedidos.index', compact('pedidos'));
    }

    /**
     * Muestra el detalle de un pedido específico
     */
    public function show(Pedido $pedido)
    {
        try {
            $this->authorize('gestionar-pedidos-barista');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('error');
        }

        // Solo mostrar pedidos que el barista puede gestionar
        if (!in_array($pedido->estado, ['pendiente', 'en_preparacion', 'preparado'])) {
            return back()->with('error', 'No tienes acceso a este pedido.');
        }

        $pedido->load(['cliente', 'atendidoPor', 'mesa', 'items.producto']);

        BitacoraController::registrar('ver detalle', 'Pedido Barista', $pedido->id, auth()->id());

        return view('barista.pedidos.show', compact('pedido'));
    }

    /**
     * Cambiar estado a "en_preparacion"
     */
    public function preparar(Pedido $pedido)
    {
        try {
            $this->authorize('gestionar-pedidos-barista');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        // Solo permitir transición de "pendiente" a "en_preparacion"
        if ($pedido->estado !== 'pendiente') {
            return back()->with('error', 'El pedido no está en estado pendiente.');
        }

        DB::beginTransaction();

        try {
            $pedido->update(['estado' => 'en_preparacion']);
            $pedido->items()->update(['estado_item' => 'en_preparacion']);

            DB::commit();

            BitacoraController::registrar('cambiar estado a en_preparacion', 'Pedido Barista', $pedido->id, auth()->id());

            return back()->with('success', 'Pedido marcado como en preparación.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado a "preparado"
     */
    public function servir(Pedido $pedido)
    {
        try {
            $this->authorize('gestionar-pedidos-barista');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        // Solo permitir transición de "en_preparacion" a "preparado"
        if ($pedido->estado !== 'en_preparacion') {
            return back()->with('error', 'El pedido no está en preparación.');
        }

        DB::beginTransaction();

        try {
            $pedido->update(['estado' => 'preparado']);
            $pedido->items()->update(['estado_item' => 'preparado']);

            DB::commit();

            BitacoraController::registrar('cambiar estado a preparado', 'Pedido Barista', $pedido->id, auth()->id());

            // Opcional: redirige al index para que "desaparezca" del tablero
            return redirect()->route('barista.pedidos.index')->with('success', 'Pedido marcado como preparado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }
}