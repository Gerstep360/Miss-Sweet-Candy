<?php


namespace App\Http\Controllers\VentasYCaja;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\InventarioProducto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Usuarios\BitacoraController;
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
        
        $productos = Producto::with(['categoria', 'inventario', 'especialVigente', 'alergenos'])
            ->orderBy('nombre')
            ->get()
            ->each->append(['imagen_url','precio_vigente','tiene_oferta','porcentaje_oferta','ahorro_oferta']);

            
        $categorias = Categoria::orderBy('nombre')->get();
        
        // 🎁 CARGAR PROMOCIONES VIGENTES
        $promociones = \App\Models\Promocion::with(['productos', 'categorias'])
            ->where('activo', true)
            ->get()
            ->filter(fn($p) => $p->esta_vigente)
            ->values();
        
        BitacoraController::registrar('crear', 'Pedido', null);

        return view('admin.pedidos.create-mostrador', compact('clientes', 'productos', 'categorias', 'promociones'));
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
        
        $productos = Producto::with(['categoria', 'inventario', 'especialVigente'])
            ->orderBy('nombre')
            ->get()
            ->each->append(['imagen_url','precio_vigente','tiene_oferta','porcentaje_oferta','ahorro_oferta']);

            
        $categorias = Categoria::orderBy('nombre')->get();
        
        // 🎁 CARGAR PROMOCIONES VIGENTES
        $promociones = \App\Models\Promocion::with(['productos', 'categorias'])
            ->where('activo', true)
            ->get()
            ->filter(fn($p) => $p->esta_vigente)
            ->values();
        
        BitacoraController::registrar('crear', 'Pedido', null);
        return view('admin.pedidos.create-mostrador', compact('clientes', 'productos', 'categorias', 'promociones'));
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
                'cliente_id' => $validated['cliente_id'],
                'atendido_por_id' => Auth::id(),
                'mesa_id' => $validated['mesa_id'],
                'tipo' => 'mesa',
                'estado' => 'pendiente',
                'total' => 0, // Se calculará después
                'notas' => $validated['notas'] ?? null,
            ]);

            // Crear los items del pedido
            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::with('especialVigente')->findOrFail($productoData['producto_id']);

                // Calcular precios considerando especiales del día
                $precioBase = (float) $producto->precio;
                $precioUnitario = (float) $producto->precio_vigente; // Usa el accessor que calcula especiales
                $descuentoItem = max(0, $precioBase - $precioUnitario);
                $cantidad = (int) $productoData['cantidad'];
                $subtotalItem = $precioUnitario * $cantidad;

                // Crear item
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento_item' => $descuentoItem,
                    'subtotal_item' => $subtotalItem,
                    'estado_item' => 'pendiente',
                    'destino' => $producto->categoria->destino ?? 'cocina',
                    'notas' => $productoData['notas'] ?? null,
                ]);

                // 📦 DESCONTAR DEL INVENTARIO
                $inventario = InventarioProducto::where('producto_id', $producto->id)->first();
                if ($inventario) {
                    $inventario->decrementarStock($cantidad);
                    
                    // Generar alerta si el stock está bajo
                    if ($inventario->requiereAlerta()) {
                        $this->generarAlertaStock($inventario, $producto);
                    }
                }
            }

            // Calcular el total del pedido
            $totalPedido = $pedido->items()->sum('subtotal_item');
            $pedido->update(['total' => $totalPedido]);

            // ✅ AQUÍ va token + ETA (ya existen items, entonces se puede calcular)
            $pedido->token = $this->generarToken($pedido->tipo);
            $pedido->eta_minutes = $this->calcularEtaPorProductos($pedido);
            $pedido->save();

            // Cambiar estado de la mesa a ocupada
            $mesa->update(['estado' => 'ocupada']);

            // Registrar en bitácora
            BitacoraController::registrar('crear', 'Pedido', $pedido->id);

            // ✅ Broadcast PERO después de commit (seguro)
            // Laravel sugiere afterCommit / ShouldDispatchAfterCommit
            DB::afterCommit(function () use ($pedido) {
                event(new \App\Events\PedidoActualizado($pedido));
            });

            DB::commit();

            // 🔔 NOTIFICAR A BARISTAS sobre el nuevo pedido
            NotificacionController::notificarNuevoPedidoABarista($pedido);

            return redirect()
                ->route('pedidos.show', $pedido)
                ->with('success', 'Pedido de mesa creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
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
                'cliente_id' => $validated['cliente_id'],
                'atendido_por_id' => Auth::id(),
                'tipo' => 'mostrador',
                'estado' => 'pendiente',
                'total' => 0, // Se calculará después
                'telefono_contacto' => $validated['telefono_contacto'] ?? null,
                'notas' => $validated['notas'] ?? null,
            ]);

            // Crear los items del pedido
            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::with('especialVigente')->findOrFail($productoData['producto_id']);

                // Calcular precios considerando especiales del día
                $precioBase = (float) $producto->precio;
                $precioUnitario = (float) $producto->precio_vigente; // Usa el accessor que calcula especiales
                $descuentoItem = max(0, $precioBase - $precioUnitario);
                $cantidad = (int) $productoData['cantidad'];
                $subtotalItem = $precioUnitario * $cantidad;

                // Crear item
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento_item' => $descuentoItem,
                    'subtotal_item' => $subtotalItem,
                    'estado_item' => 'pendiente',
                    'destino' => $producto->categoria->destino ?? 'cocina',
                    'notas' => $productoData['notas'] ?? null,
                ]);

                // 📦 DESCONTAR DEL INVENTARIO
                $inventario = InventarioProducto::where('producto_id', $producto->id)->first();
                if ($inventario) {
                    $inventario->decrementarStock($cantidad);
                    
                    // Generar alerta si el stock está bajo
                    if ($inventario->requiereAlerta()) {
                        $this->generarAlertaStock($inventario, $producto);
                    }
                }
            }

            // Calcular el total del pedido
            $totalPedido = $pedido->items()->sum('subtotal_item');
            $pedido->update(['total' => $totalPedido]);

            // ✅ AQUÍ va token + ETA
            $pedido->token = $this->generarToken($pedido->tipo);
            $pedido->eta_minutes = $this->calcularEtaPorProductos($pedido);
            $pedido->save();

            // Registrar en bitácora
            BitacoraController::registrar('crear', 'Pedido', $pedido->id);

            // ✅ Broadcast seguro post-commit
            DB::afterCommit(function () use ($pedido) {
                event(new \App\Events\PedidoActualizado($pedido));
            });

            DB::commit();

            // 🔔 NOTIFICAR A BARISTAS sobre el nuevo pedido
            NotificacionController::notificarNuevoPedidoABarista($pedido);

            return redirect()
                ->route('pedidos.show', $pedido)
                ->with('success', 'Pedido de mostrador creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
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
        $productos = Producto::with(['categoria', 'inventario', 'especialVigente'])
            ->orderBy('nombre')
            ->get()
            ->each->append(['imagen_url','precio_vigente','tiene_oferta','porcentaje_oferta','ahorro_oferta']);

        $categorias = Categoria::orderBy('nombre')->get();
        $clientes = User::role('cliente')->get();
        
        // 🎁 CARGAR PROMOCIONES VIGENTES
        $promociones = \App\Models\Promocion::with(['productos', 'categorias'])
            ->where('activo', true)
            ->get()
            ->filter(fn($p) => $p->esta_vigente)
            ->values();

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
            
            return view('admin.pedidos.edit-mesa', compact('pedido', 'productos', 'mesas', 'categorias', 'clientes', 'itemsJson', 'promociones'));
        } else {
            return view('admin.pedidos.edit-mostrador', compact('pedido', 'productos', 'categorias', 'clientes', 'itemsJson', 'promociones'));
        }
        BitacoraController::registrar('editar', 'Pedido', $pedido->id);
    }
/*
    |--------------------------------------------------------------------------
    | 🛒 MÓDULO PEDIDOS WEB (CLICK & COLLECT) - CU29
    |--------------------------------------------------------------------------
    */

    /**
     * Listado específico para pedidos Web (Para Cajeros/Cocina)
     * Muestra los pedidos que entran por la web para preparar.
     */
    public function indexWeb(Request $request)
    {
        // 🔒 Solo personal autorizado puede ver la cola de pedidos web
        try {
            $this->authorize('ver-pedidos-web');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        $estado = $request->get('estado');

        $query = Pedido::with(['cliente', 'items.producto'])
            ->where('tipo', 'web');

        // Filtro por estado opcional
        if ($estado) {
            $query->where('estado', $estado);
        }

        $pedidosWeb = $query->latest()->paginate(15);
        
        BitacoraController::registrar('ver lista web', 'Pedido', null);

        // Retornamos una vista específica o reutilizamos la index pasando una flag
        return view('admin.pedidos.index-web', compact('pedidosWeb', 'estado'));
    }

    /**
     * Muestra el "Menú Digital" para el Cliente.
     * Aquí el cliente selecciona sus productos desde su casa/oficina.
     */
    public function createWeb()
    {
        // 🔒 Solo clientes registrados pueden acceder
        try {
            $this->authorize('crear-pedidos-en-linea');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        // Cargar productos disponibles para venta web (stock > 0)
        // Opcional: Podrías tener un campo 'visible_web' en Producto si quisieras ocultar algunos
        
        $productos = Producto::with(['categoria', 'inventario', 'especialVigente'])
            ->orderBy('nombre')
            ->get()
            ->each->append(['imagen_url','precio_vigente','tiene_oferta','porcentaje_oferta','ahorro_oferta']);

            
        $categorias = Categoria::orderBy('nombre')->get();
        // 🎁 CARGAR PROMOCIONES VIGENTES WEB
        $promociones = \App\Models\Promocion::with(['productos'])
            ->where('activo', true)
            ->get()
            ->filter(fn($p) => $p->esta_vigente)
            ->values();

        BitacoraController::registrar('iniciar pedido web', 'Pedido', null);

        // Vista pública/cliente del menú
        return view('client.pedidos.create', compact('productos', 'categorias', 'promociones'));
    }

    /**
     * Procesa el pedido "Click & Collect" del cliente.
     */
    public function storeWeb(Request $request)
    {
        // 🔒 Validar que sea cliente
        try {
            $this->authorize('crear-pedidos-en-linea');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        $validated = $request->validate([
            // El cliente es el usuario autenticado, no se envía por request para evitar suplantación
            'hora_recogida' => 'nullable|date_format:H:i|after:now', // Opcional: si quieres agendar
            'productos'     => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad'    => 'required|integer|min:1',
            'productos.*.notas'       => 'nullable|string|max:100', // "Sin cebolla", etc.
            'notas_generales' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // 1. Crear el Encabezado del Pedido
            $pedido = Pedido::create([
                'cliente_id'      => Auth::id(),
                'atendido_por_id' => null, // Aún no lo atiende un cajero, es automático
                'mesa_id'         => null,
                'tipo'            => 'web', // 👈 Importante para el prefijo 'W'
                'estado'          => 'pendiente', // Entra a cola de confirmación
                'total'           => 0,
                'notas'           => $validated['notas_generales'] ?? null,
                // Podrías guardar la hora de recogida en 'notas' o una columna específica si tienes
            ]);

            // 2. Procesar Items
            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::with('especialVigente')->findOrFail($productoData['producto_id']);
                $inventario = InventarioProducto::where('producto_id', $producto->id)->lockForUpdate()->first();

                // Validar Stock en tiempo real (Critical Check)
                if (!$inventario || $inventario->stock_actual < $productoData['cantidad']) {
                    throw new \Exception("Lo sentimos, el producto '{$producto->nombre}' ya no tiene stock suficiente.");
                }

                // Calcular precios (Misma lógica que mostrador)
                $precioBase = (float) $producto->precio;
                $precioUnitario = (float) $producto->precio_vigente; 
                $descuentoItem = max(0, $precioBase - $precioUnitario);
                $cantidad = (int) $productoData['cantidad'];
                $subtotalItem = $precioUnitario * $cantidad;

                // Crear Item
                PedidoItem::create([
                    'pedido_id'       => $pedido->id,
                    'producto_id'     => $producto->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento_item'  => $descuentoItem,
                    'subtotal_item'   => $subtotalItem,
                    'estado_item'     => 'pendiente',
                    'destino'         => $producto->categoria->destino ?? 'cocina',
                    'notas'           => $productoData['notas'] ?? null,
                ]);

                // 📦 DESCONTAR DEL INVENTARIO
                $inventario->decrementarStock($cantidad);
                
                // Alertas de stock
                if ($inventario->requiereAlerta()) {
                    $this->generarAlertaStock($inventario, $producto);
                }
            }

            // 3. Finalizar Cálculos
            $totalPedido = $pedido->items()->sum('subtotal_item');
            $pedido->update(['total' => $totalPedido]);

            // Generar Token (W001, W002...)
            $pedido->token = $this->generarToken('web');
            
            // Calcular ETA (Tiempo estimado para recoger)
            $pedido->eta_minutes = $this->calcularEtaPorProductos($pedido);
            $pedido->save();

            // 4. Eventos y Logs
            BitacoraController::registrar('crear web', 'Pedido', $pedido->id);

            DB::afterCommit(function () use ($pedido) {
                // Broadcast para actualizar pantalla de cocina/caja en tiempo real
                event(new \App\Events\PedidoActualizado($pedido));
            });

            DB::commit();

            // 🔔 NOTIFICAR A CAJEROS (Nuevo pedido web entrante)
            // Es importante notificar al cajero para que "Acepte/Confirme" el pedido
            \App\Http\Controllers\NotificacionController::notificarNuevoPedidoWeb($pedido);

            // Retornar a la vista de seguimiento del cliente
            return redirect()
                ->route('client.pedidos.track', $pedido->token) // Asumiendo que tienes una ruta de tracking
                ->with('success', '¡Tu pedido ha sido enviado! Espera la confirmación.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Error al procesar tu pedido: ' . $e->getMessage()])
                ->withInput();
        }
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
        try {
            $this->authorize($pedido->tipo === 'mesa' ? 'editar-pedidos-mesa' : 'editar-pedidos-mostrador');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        $validated = $request->validate([
            'estado' => 'required|in:pendiente,confirmado,en_preparacion,preparado,en_reparto,entregado,servido,retirado,anulado,cancelado',
            'motivo_anulacion' => 'required_if:estado,anulado|nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $estadoAnterior = $pedido->estado;
            $pedido->estado = $validated['estado'];
            
            if (in_array($validated['estado'], ['anulado']) && isset($validated['motivo_anulacion'])) {
                $pedido->motivo_anulacion = $validated['motivo_anulacion'];
            }

            // Si se completa el pedido (entregado/servido/retirado), liberar mesa
            if (in_array($validated['estado'], ['retirado','servido','entregado'])) {
                $pedido->delivered_at = now();
            }

            $pedido->save();
            event(new \App\Events\PedidoActualizado($pedido));

            // ✅ Acumular puntos si corresponde
            $fidelidadController1 = new \App\Http\Controllers\FidelidadController();

            // Definir estados que acumulan puntos
            $estadosQueAcumulan = ['cancelado'];
            
            if (in_array($validated['estado'], $estadosQueAcumulan) && 
                $fidelidadController->pedidoPuedeAcumularPuntos($pedido)) {
                if($fidelidadController == true){
                    $puntosAcumulados = $fidelidadController1->acumularPuntosPorPedido($pedido);
                }
            }

            DB::commit();
            // 🔔 NOTIFICACIONES según el cambio de estado
            if ($validated['estado'] === 'preparado' || $validated['estado'] === 'listo') {
                // Barista terminó de preparar → Notificar a cajeros
                NotificacionController::notificarPedidoListo($pedido);
            } else {
                // Para otros cambios de estado
                NotificacionController::notificarCambioEstadoPedido($pedido, $estadoAnterior, $validated['estado']);
            }

            // Registrar en bitácora

            BitacoraController::registrar('cambiar estado', 'Pedido', $pedido->id);
            return back()->with('success', "Estado del pedido cambiado de '{$estadoAnterior}' a '{$validated['estado']}'.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
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

    /**
     * Genera alertas automáticas de stock bajo o crítico
     */
    protected function generarAlertaStock(InventarioProducto $inventario, Producto $producto)
    {
        $estado = $inventario->estado_stock;
        $mensaje = "⚠️ El producto '{$producto->nombre}' tiene stock {$estado}. Stock actual: {$inventario->stock_actual}, Stock mínimo: {$inventario->stock_minimo}.";

        // Obtener usuarios con permiso para recibir alertas de inventario
        $usuariosDestino = \App\Models\User::permission('ver-inventario')->get();

        foreach ($usuariosDestino as $usuario) {
            // Verificar si ya existe una notificación reciente sin leer (últimas 24 horas)
            $notificacionReciente = \App\Models\Notificacion::where('usuario_destino_id', $usuario->id)
                ->where('rel_model', 'producto')
                ->where('rel_id', $producto->id)
                ->where('tipo', 'stock')
                ->where('leido', false)
                ->where('id', '>', now()->subDay()->timestamp)
                ->first();

            // Solo crear notificación si no hay una reciente
            if (!$notificacionReciente) {
                \App\Models\Notificacion::create([
                    'tipo' => 'stock',
                    'canal' => 'panel',
                    'mensaje' => $mensaje,
                    'usuario_destino_id' => $usuario->id,
                    'rel_model' => 'producto',
                    'rel_id' => $producto->id,
                    'leido' => false,
                ]);
            }
        }
    }

    /**
 * Reparar puntos para pedidos existentes que no acumularon puntos
 */
    public function repararPuntosPedidos()
    {
        $this->authorize('gestionar-pedidos');

        $pedidosSinPuntos = Pedido::whereIn('estado', ['entregado', 'servido', 'retirado'])
            ->whereNotNull('cliente_id')
            ->whereDoesntHave('movimientosFidelidad')
            ->get();

        $procesados = 0;
        $errores = 0;

        foreach ($pedidosSinPuntos as $pedido) {
            try {
                $fidelidadController = new \App\Http\Controllers\FidelidadController();
                if ($fidelidadController->isProgramaActivo()) {
                    $fidelidadController->acumularPuntosPorPedido($pedido);
                    $procesados++;
                }
            } catch (\Exception $e) {
                \Log::error("Error reparando puntos para pedido #{$pedido->id}: " . $e->getMessage());
                $errores++;
            }
        }

            BitacoraController::registrar('reparar puntos pedidos', 'Pedido', null);
            return back()->with('success', "Se acumularon puntos para {$procesados} pedidos. Errores: {$errores}");
    }
    
    private function generarToken(string $tipo): string
    {
        $prefijo = match($tipo){
            'mesa' => 'M',
            'mostrador' => 'A',
            'web' => 'W',
            default => 'A'
        };

        $ultimo = Pedido::where('token','like', $prefijo.'%')
            ->orderByDesc('id')
            ->value('token');

        $num = $ultimo ? intval(substr($ultimo,1)) + 1 : 1;

        return $prefijo . str_pad((string)$num, 3, '0', STR_PAD_LEFT); // A001
    }

    private function calcularEtaPorProductos(Pedido $pedido): int
    {
        // 1) max tiempo base de productos del pedido
        $maxItemTime = $pedido->items()
            ->join('productos','productos.id','=','pedido_items.producto_id')
            ->max('productos.prep_time_minutes') ?? 1;

        // 2) penalidad por carga actual
        $penalty = Pedido::where('estado','en_preparacion')->count() * 2;

        return $maxItemTime + $penalty;
    }

}

