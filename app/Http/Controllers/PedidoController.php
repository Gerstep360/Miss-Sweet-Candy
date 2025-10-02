<?php


namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\BitacoraController;
class PedidoController extends BaseController
{
     use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tipo = $request->get('tipo'); // mesa, mostrador, web
        $estado = $request->get('estado');

        $query = Pedido::with(['cliente', 'atendidoPor', 'mesa', 'items.producto']);

        // 🔒 SEGURIDAD: Si es cliente, solo ver sus propios pedidos
        if (auth()->user()->hasRole('cliente')) {
            $query->where('cliente_id', auth()->id());
        }

        // Filtrar por tipo si se especifica
        if ($tipo && in_array($tipo, ['mesa', 'mostrador', 'web'])) {
            $query->where('tipo', $tipo);
        }

        // Filtrar por estado si se especifica
        if ($estado) {
            $query->where('estado', $estado);
        }

        $pedidos = $query->latest()->paginate(15);
        BitacoraController::registrar('ver lista', 'Pedido', null);
        return view('admin.pedidos.index', compact('pedidos', 'tipo', 'estado'));
    }

    /**
     * Show the form for creating a new resource - MESA
     */
    public function createMesa()
    {
        // 🔒 Solo cajero y admin pueden crear pedidos de mesa

        try {
        $this->authorize('crear-pedidos-mesa');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403'); // Redirige a tu página personalizada
        }
        // Buscar mesas disponibles (libre o disponible)
        $mesas = Mesa::whereIn('estado', ['libre', 'disponible'])
            ->whereNull('fusion_id')
            ->orderBy('nombre')
            ->get();
            
        $clientes = User::role('cliente')->get();
        
        $productos = Producto::with('categoria')
            ->orderBy('nombre')
            ->get();
            
        $categorias = Categoria::orderBy('nombre')->get();
        BitacoraController::registrar('crear', 'Pedido', null);

        return view('admin.pedidos.create-mesa', compact('mesas', 'clientes', 'productos', 'categorias'));
    }

    /**
     * Show the form for creating a new resource - MOSTRADOR
     */
    public function createMostrador()
    {
        // 🔒 Solo cajero y admin pueden crear pedidos de mostrador
        try {
        $this->authorize('crear-pedidos-mostrador');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403'); // Redirige a tu página personalizada
        }

        $clientes = User::role('cliente')->get();
        
        $productos = Producto::with('categoria')
            ->orderBy('nombre')
            ->get();
            
        $categorias = Categoria::orderBy('nombre')->get();
        BitacoraController::registrar('crear', 'Pedido', null);
        return view('admin.pedidos.create-mostrador', compact('clientes', 'productos', 'categorias'));
    }

    /**
     * Store a newly created resource in storage - MESA
     */
    public function storeMesa(Request $request)
    {
        // 🔒 Solo cajero y admin pueden crear pedidos de mesa
 

        $validated = $request->validate([
            'cliente_id' => 'nullable|exists:users,id',
            'mesa_id' => 'required|exists:mesas,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.notas' => 'nullable|string|max:255',
            'notas' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Verificar que la mesa esté disponible
            $mesa = Mesa::findOrFail($validated['mesa_id']);
            if (!in_array($mesa->estado, ['libre', 'disponible'])) {
                return back()->withErrors(['mesa_id' => 'La mesa seleccionada no está disponible.'])->withInput();
            }

            // Crear el pedido
            $pedido = Pedido::create([
                'tipo' => 'mesa',
                'cliente_id' => $validated['cliente_id'] ?? null,
                'atendido_por' => Auth::id(),
                'mesa_id' => $validated['mesa_id'],
                'estado' => 'pendiente',
                'canal' => 'local',
                'notas' => $validated['notas'] ?? null,
            ]);

            // Crear los items del pedido
            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::findOrFail($productoData['producto_id']);

                // Calcular subtotal
                $precioUnitario = $producto->precio;
                $cantidad = $productoData['cantidad'];
                $subtotalItem = $precioUnitario * $cantidad;

                // Crear item
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento_item' => 0.00,
                    'subtotal_item' => $subtotalItem,
                    'estado_item' => 'pendiente',
                    'destino' => $producto->categoria->destino ?? 'cocina', // barra o cocina
                    'notas' => $productoData['notas'] ?? null,
                ]);
            }

            // Cambiar estado de la mesa a ocupada
            $mesa->update(['estado' => 'ocupada']);

            DB::commit();

            return redirect()
                ->route('pedidos.show', $pedido)
                ->with('success', 'Pedido de mesa creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
        BitacoraController::registrar('crear', 'Pedido', $pedido->id);
    }

    /**
     * Store a newly created resource in storage - MOSTRADOR
     */
    public function storeMostrador(Request $request)
    {
        // 🔒 Solo cajero y admin pueden crear pedidos de mostrador
 

        $validated = $request->validate([
            'cliente_id' => 'nullable|exists:users,id',
            'telefono_contacto' => 'nullable|string|max:30',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.notas' => 'nullable|string|max:255',
            'notas' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Crear el pedido
            $pedido = Pedido::create([
                'tipo' => 'mostrador',
                'cliente_id' => $validated['cliente_id'] ?? null,
                'atendido_por' => Auth::id(),
                'estado' => 'pendiente',
                'canal' => 'local',
                'telefono_contacto' => $validated['telefono_contacto'] ?? null,
                'notas' => $validated['notas'] ?? null,
            ]);

            // Crear los items del pedido
            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::findOrFail($productoData['producto_id']);

                // Calcular subtotal
                $precioUnitario = $producto->precio;
                $cantidad = $productoData['cantidad'];
                $subtotalItem = $precioUnitario * $cantidad;

                // Crear item
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento_item' => 0.00,
                    'subtotal_item' => $subtotalItem,
                    'estado_item' => 'pendiente',
                    'destino' => $producto->categoria->destino ?? 'cocina',
                    'notas' => $productoData['notas'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('pedidos.show', $pedido)
                ->with('success', 'Pedido de mostrador creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
        BitacoraController::registrar('crear', 'Pedido', $pedido->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        // 🔒 SEGURIDAD: Cliente solo puede ver sus propios pedidos
        if (auth()->user()->hasRole('cliente') && $pedido->cliente_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }

        $pedido->load(['cliente', 'atendidoPor', 'mesa', 'items.producto']);
            BitacoraController::registrar('ver', 'Pedido', $pedido->id);
        return view('admin.pedidos.show', compact('pedido'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        // 🔒 SEGURIDAD: Solo cajero y admin pueden editar pedidos
        try {
            if ($pedido->tipo === 'mesa') {
                $this->authorize('editar-pedidos-mesa');
            } else {
                $this->authorize('editar-pedidos-mostrador');
            }
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403'); // Redirige a tu página personalizada
        }

        if (!in_array($pedido->estado, ['pendiente', 'confirmado'])) {
            return back()->with('error', 'No se puede editar un pedido en este estado.');
        }

        $pedido->load(['items.producto.categoria']);
        $productos = Producto::with('categoria')->orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        $clientes = User::role('cliente')->get();

        // ✅ PREPARAR LOS ITEMS DEL PEDIDO PARA JAVASCRIPT
        $itemsJson = $pedido->items->map(function($item) {
            return [
                'producto_id' => $item->producto_id,
                'nombre' => $item->producto->nombre,
                'precio' => (float)$item->precio_unitario,
                'imagen' => $item->producto->imagen,
                'cantidad' => $item->cantidad,
                'notas' => $item->notas ?? ''
            ];
        });

        if ($pedido->tipo === 'mesa') {
            $mesas = Mesa::where(function($query) use ($pedido) {
                $query->whereIn('estado', ['libre', 'disponible'])
                    ->orWhere('id', $pedido->mesa_id);
            })
            ->whereNull('fusion_id')
            ->orderBy('nombre')
            ->get();
            
            return view('admin.pedidos.edit-mesa', compact('pedido', 'productos', 'mesas', 'categorias', 'clientes', 'itemsJson'));
        } else {
            return view('admin.pedidos.edit-mostrador', compact('pedido', 'productos', 'categorias', 'clientes', 'itemsJson'));
        }
        BitacoraController::registrar('editar', 'Pedido', $pedido->id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        // 🔒 SEGURIDAD: Solo cajero y admin pueden editar pedidos
        try {
            if ($pedido->tipo === 'mesa') {
                $this->authorize('editar-pedidos-mesa');
            } else {
                $this->authorize('editar-pedidos-mostrador');
            }
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403'); // Redirige a tu página personalizada
        }

        // Solo se puede editar si está en estado pendiente o confirmado
        if (!in_array($pedido->estado, ['pendiente', 'confirmado'])) {
            return back()->with('error', 'No se puede editar un pedido en este estado.');
        }

        $rules = [
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.notas' => 'nullable|string|max:255',
            'notas' => 'nullable|string|max:255',
        ];

        if ($pedido->tipo === 'mesa') {
            $rules['mesa_id'] = 'required|exists:mesas,id';
        } else {
            $rules['telefono_contacto'] = 'nullable|string|max:30';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            // Eliminar items antiguos
            $pedido->items()->delete();

            // Actualizar mesa si aplica
            if ($pedido->tipo === 'mesa' && $validated['mesa_id'] != $pedido->mesa_id) {
                // Verificar que la nueva mesa esté disponible
                $nuevaMesa = Mesa::findOrFail($validated['mesa_id']);
                if (!in_array($nuevaMesa->estado, ['libre', 'disponible']) && $nuevaMesa->id !== $pedido->mesa_id) {
                    throw new \Exception("La mesa seleccionada no está disponible.");
                }
                
                // Liberar mesa anterior
                if ($pedido->mesa) {
                    $pedido->mesa->update(['estado' => 'libre']);
                }
                
                // Ocupar nueva mesa
                $nuevaMesa->update(['estado' => 'ocupada']);
                $pedido->mesa_id = $validated['mesa_id'];
            }

            // Actualizar notas del pedido
            $pedido->notas = $validated['notas'] ?? null;
            
            if ($pedido->tipo === 'mostrador') {
                $pedido->telefono_contacto = $validated['telefono_contacto'] ?? null;
            }
            
            $pedido->save();

            // Crear nuevos items
            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::findOrFail($productoData['producto_id']);

                // Calcular subtotal
                $precioUnitario = $producto->precio;
                $cantidad = $productoData['cantidad'];
                $subtotalItem = $precioUnitario * $cantidad;

                // Crear item
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento_item' => 0.00,
                    'subtotal_item' => $subtotalItem,
                    'estado_item' => 'pendiente',
                    'destino' => $producto->categoria->destino ?? 'cocina',
                    'notas' => $productoData['notas'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('pedidos.show', $pedido)
                ->with('success', 'Pedido actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
        BitacoraController::registrar('editado', 'Pedido', $pedido->id);
    }

    /**
     * Cambiar el estado del pedido
     */
    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        // 🔒 SEGURIDAD: Solo cajero y admin pueden cambiar estado
        try {
            if ($pedido->tipo === 'mesa') {
                $this->authorize('editar-pedidos-mesa');
            } else {
                $this->authorize('editar-pedidos-mostrador');
            }
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403'); // Redirige a tu página personalizada
        }

        $validated = $request->validate([
            'estado' => 'required|in:pendiente,confirmado,en_preparacion,preparado,en_reparto,entregado,servido,retirado,anulado,cancelado',
            'motivo_anulacion' => 'required_if:estado,anulado,cancelado|nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $estadoAnterior = $pedido->estado;
            $pedido->estado = $validated['estado'];

            // Si se anula o cancela
            if (in_array($validated['estado'], ['anulado', 'cancelado'])) {
                // Marcar items como anulados
                foreach ($pedido->items as $item) {
                    $item->update(['estado_item' => 'anulado']);
                }

                // Liberar mesa si aplica
                if ($pedido->tipo === 'mesa' && $pedido->mesa) {
                    $pedido->mesa->update(['estado' => 'libre']);
                }
            }

            // Si se completa el pedido (entregado/servido/retirado), liberar mesa
            if (in_array($validated['estado'], ['entregado', 'servido', 'retirado']) && $pedido->tipo === 'mesa' && $pedido->mesa) {
                $pedido->mesa->update(['estado' => 'libre']);
            }

            $pedido->save();

            DB::commit();

            return back()->with('success', "Estado del pedido cambiado de '{$estadoAnterior}' a '{$validated['estado']}'.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
        BitacoraController::registrar('cambiar estado', 'Pedido', $pedido->id);
    }

    /**
     * Anular un item del pedido
     */
    public function anularItem(Request $request, PedidoItem $item)
    {
        // 🔒 SEGURIDAD: Solo cajero y admin pueden anular items
        try {
            if ($pedido->tipo === 'mesa') {
                $this->authorize('editar-pedidos-mesa');
            } else {
                $this->authorize('editar-pedidos-mostrador');
            }
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403'); // Redirige a tu página personalizada
        }

        // Solo se puede anular si el pedido está en ciertos estados
        if (!in_array($item->pedido->estado, ['pendiente', 'confirmado', 'en_preparacion'])) {
            return back()->with('error', 'No se puede anular items de un pedido en este estado.');
        }

        DB::beginTransaction();

        try {
            // Marcar item como anulado
            $item->update(['estado_item' => 'anulado']);

            // Si todos los items están anulados, anular el pedido
            $itemsActivos = $item->pedido->items()->where('estado_item', '!=', 'anulado')->count();
            if ($itemsActivos === 0) {
                $item->pedido->update(['estado' => 'anulado']);
                
                // Liberar mesa si aplica
                if ($item->pedido->tipo === 'mesa' && $item->pedido->mesa) {
                    $item->pedido->mesa->update(['estado' => 'libre']);
                }
            }

            DB::commit();

            return back()->with('success', 'Item anulado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular el item: ' . $e->getMessage());
        }
        BitacoraController::registrar('anular item', 'PedidoItem', $item->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        // 🔒 SEGURIDAD: Solo admin puede eliminar pedidos
        try {
            $this->authorize('eliminar-pedidos');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403'); // Redirige a tu página personalizada
        }

        // Solo se puede eliminar si está anulado o cancelado
        if (!in_array($pedido->estado, ['anulado', 'cancelado'])) {
            return back()->with('error', 'Solo se pueden eliminar pedidos anulados o cancelados.');
        }

        DB::beginTransaction();

        try {
            // Liberar mesa si aplica
            if ($pedido->tipo === 'mesa' && $pedido->mesa) {
                $pedido->mesa->update(['estado' => 'libre']);
            }

            $pedido->delete();

            DB::commit();

            return redirect()
                ->route('pedidos.index')
                ->with('success', 'Pedido eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el pedido: ' . $e->getMessage());
        }
        BitacoraController::registrar('eliminado', 'Pedido', $pedido->id);
    }
}