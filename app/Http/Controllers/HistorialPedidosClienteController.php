<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

/**
 * CU23: Historial de Pedidos del Cliente
 * 
 * Permite a los clientes consultar su historial completo de pedidos,
 * ver detalles, estadísticas, reordenar pedidos anteriores y exportar su historial.
 */
class HistorialPedidosClienteController extends Controller
{
    use AuthorizesRequests;

    /**
     * Muestra el historial completo de pedidos del cliente autenticado
     */
    public function index(Request $request)
    {
        $this->authorize('ver-mis-pedidos');

        $clienteId = Auth::id();

        $query = Pedido::with(['items.producto', 'mesa', 'atendidoPor'])
            ->where('cliente_id', $clienteId);

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Filtro por tipo de pedido
        if ($request->filled('tipo') && in_array($request->tipo, ['mesa', 'mostrador', 'web'])) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por búsqueda (número de pedido o productos)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('id', 'like', "%{$busqueda}%")
                  ->orWhereHas('items.producto', function($q2) use ($busqueda) {
                      $q2->where('nombre', 'like', "%{$busqueda}%");
                  });
            });
        }

        // Ordenar por más reciente
        $pedidos = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calcular estadísticas del cliente
        $estadisticas = $this->calcularEstadisticas($clienteId);

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_historial',
            'historial_pedidos_cliente',
            null,
            null,
            $request
        );

        return view('cliente.historial.index', compact('pedidos', 'estadisticas'));
    }

    /**
     * Muestra el detalle completo de un pedido específico
     */
    public function show($id)
    {
        $this->authorize('ver-mis-pedidos');

        $pedido = Pedido::with([
            'items.producto.categoria',
            'mesa',
            'atendidoPor',
            'cobros',
            'feedback'
        ])->findOrFail($id);

        // Verificar que el pedido pertenece al cliente autenticado
        if ($pedido->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }

        // Obtener promociones aplicadas
        $promocionesAplicadas = $pedido->getPromocionesAplicadas();

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_detalle_pedido',
            'historial_pedidos_cliente',
            $pedido->id,
            null,
            request()
        );

        return view('cliente.historial.show', compact('pedido', 'promocionesAplicadas'));
    }

    /**
     * Muestra el formulario para reordenar un pedido anterior
     */
    public function reordenar($id)
    {
        $this->authorize('crear-pedidos-web');

        $pedidoOriginal = Pedido::with('items.producto')->findOrFail($id);

        // Verificar que el pedido pertenece al cliente autenticado
        if ($pedidoOriginal->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para reordenar este pedido.');
        }

        // Verificar disponibilidad de productos
        $productosDisponibles = [];
        $productosNoDisponibles = [];

        foreach ($pedidoOriginal->items as $item) {
            $producto = Producto::with(['inventario', 'especialVigente'])
                ->find($item->producto_id);

            if ($producto && $producto->activo) {
                // Verificar stock disponible
                $stockDisponible = $producto->inventario ? $producto->inventario->stock_actual : 0;
                
                $productosDisponibles[] = [
                    'producto' => $producto,
                    'cantidad_original' => $item->cantidad,
                    'cantidad_disponible' => $stockDisponible,
                    'notas' => $item->notas,
                ];
            } else {
                $productosNoDisponibles[] = [
                    'nombre' => $item->producto->nombre ?? 'Producto no disponible',
                    'cantidad' => $item->cantidad,
                ];
            }
        }

        // Registrar en bitácora
        BitacoraController::registrar(
            'reordenar_pedido',
            'historial_pedidos_cliente',
            $pedidoOriginal->id,
            null,
            request()
        );

        return view('cliente.historial.reordenar', compact(
            'pedidoOriginal',
            'productosDisponibles',
            'productosNoDisponibles'
        ));
    }

    /**
     * Procesa el reorden de un pedido anterior
     */
    public function storeReorden(Request $request, $id)
    {
        $this->authorize('crear-pedidos-web');

        $pedidoOriginal = Pedido::findOrFail($id);

        // Verificar que el pedido pertenece al cliente autenticado
        if ($pedidoOriginal->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para reordenar este pedido.');
        }

        $validated = $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.notas' => 'nullable|string|max:255',
            'tipo' => 'required|in:mostrador,web',
            'modalidad' => 'required_if:tipo,web|in:retiro,entrega',
            'direccion_entrega' => 'required_if:modalidad,entrega|nullable|string|max:255',
            'telefono_contacto' => 'required|string|max:30',
            'notas' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Crear nuevo pedido
            $nuevoPedido = Pedido::create([
                'cliente_id' => Auth::id(),
                'tipo' => $validated['tipo'],
                'modalidad' => $validated['modalidad'] ?? null,
                'estado' => 'pendiente',
                'direccion_entrega' => $validated['direccion_entrega'] ?? null,
                'telefono_contacto' => $validated['telefono_contacto'],
                'notas' => $validated['notas'] ?? null,
                'canal' => 'web',
            ]);

            $totalPedido = 0;

            // Crear items del nuevo pedido
            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::with(['inventario', 'especialVigente'])->findOrFail($productoData['producto_id']);

                // Verificar stock disponible
                if ($producto->inventario && $producto->inventario->stock_actual < $productoData['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}");
                }

                $precioBase = (float) $producto->precio;
                $precioUnitario = (float) $producto->precio_vigente;
                $descuentoItem = max(0, $precioBase - $precioUnitario);
                $cantidad = (int) $productoData['cantidad'];
                $subtotalItem = $precioUnitario * $cantidad;

                PedidoItem::create([
                    'pedido_id' => $nuevoPedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento_item' => $descuentoItem,
                    'subtotal_item' => $subtotalItem,
                    'estado_item' => 'pendiente',
                    'destino' => $producto->categoria->destino ?? 'cocina',
                    'notas' => $productoData['notas'] ?? null,
                ]);

                $totalPedido += $subtotalItem;

                // Descontar del inventario
                if ($producto->inventario) {
                    $producto->inventario->decrementarStock($cantidad);
                }
            }

            // Registrar en bitácora
            BitacoraController::registrar(
                'pedido_reordenado',
                'historial_pedidos_cliente',
                $nuevoPedido->id,
                [
                    'pedido_original_id' => $pedidoOriginal->id,
                    'total' => $totalPedido,
                ],
                $request
            );

            DB::commit();

            return redirect()
                ->route('cliente.historial.show', $nuevoPedido->id)
                ->with('success', 'Pedido reordenado exitosamente. Número de pedido: #' . $nuevoPedido->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al reordenar pedido: ' . $e->getMessage());
        }
    }

    /**
     * Muestra estadísticas detalladas del historial del cliente
     */
    public function estadisticas()
    {
        $this->authorize('ver-mis-pedidos');

        $clienteId = Auth::id();

        // Estadísticas generales
        $estadisticas = $this->calcularEstadisticas($clienteId);

        // Productos más pedidos
        $productosMasPedidos = DB::table('pedido_items')
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
            ->where('pedidos.cliente_id', $clienteId)
            ->whereNotIn('pedidos.estado', ['anulado', 'cancelado'])
            ->select(
                'productos.id',
                'productos.nombre',
                DB::raw('SUM(pedido_items.cantidad) as total_pedido'),
                DB::raw('COUNT(DISTINCT pedidos.id) as veces_pedido')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_pedido', 'desc')
            ->limit(10)
            ->get();

        // Pedidos por mes (últimos 12 meses)
        $pedidosPorMes = DB::table('pedidos')
            ->where('cliente_id', $clienteId)
            ->whereNotIn('estado', ['anulado', 'cancelado'])
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(CAST(JSON_EXTRACT(pedidos.total, "$") AS DECIMAL(10,2))) as total_gastado')
            )
            ->groupBy('mes')
            ->orderBy('mes', 'desc')
            ->get();

        // Gasto por categoría
        $gastoPorCategoria = DB::table('pedido_items')
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('pedidos.cliente_id', $clienteId)
            ->whereNotIn('pedidos.estado', ['anulado', 'cancelado'])
            ->select(
                'categorias.nombre',
                DB::raw('SUM(pedido_items.subtotal_item) as total_gastado'),
                DB::raw('COUNT(DISTINCT pedidos.id) as total_pedidos')
            )
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderBy('total_gastado', 'desc')
            ->get();

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_estadisticas',
            'historial_pedidos_cliente',
            null,
            null,
            request()
        );

        return view('cliente.historial.estadisticas', compact(
            'estadisticas',
            'productosMasPedidos',
            'pedidosPorMes',
            'gastoPorCategoria'
        ));
    }

    /**
     * Exporta el historial de pedidos del cliente
     */
    public function exportar(Request $request)
    {
        $this->authorize('ver-mis-pedidos');

        $formato = $request->input('formato', 'pdf'); // pdf, excel, csv
        $clienteId = Auth::id();

        $query = Pedido::with(['items.producto', 'mesa'])
            ->where('cliente_id', $clienteId);

        // Aplicar filtros de fecha si existen
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->get();

        // Registrar en bitácora
        BitacoraController::registrar(
            'exportacion_historial',
            'historial_pedidos_cliente',
            null,
            ['formato' => $formato],
            $request
        );

        // TODO: Implementar lógica de exportación según formato
        return view('cliente.historial.exportar', compact('pedidos', 'formato'));
    }

    /**
     * Busca pedidos por diferentes criterios
     */
    public function buscar(Request $request)
    {
        $this->authorize('ver-mis-pedidos');

        $validated = $request->validate([
            'termino' => 'required|string|min:1',
        ]);

        $clienteId = Auth::id();
        $termino = $validated['termino'];

        $pedidos = Pedido::with(['items.producto', 'mesa'])
            ->where('cliente_id', $clienteId)
            ->where(function($query) use ($termino) {
                $query->where('id', 'like', "%{$termino}%")
                      ->orWhereHas('items.producto', function($q) use ($termino) {
                          $q->where('nombre', 'like', "%{$termino}%");
                      })
                      ->orWhere('notas', 'like', "%{$termino}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Registrar en bitácora
        BitacoraController::registrar(
            'busqueda_historial',
            'historial_pedidos_cliente',
            null,
            ['termino' => $termino],
            $request
        );

        return view('cliente.historial.buscar', compact('pedidos', 'termino'));
    }

    /**
     * Calcula estadísticas del cliente
     */
    private function calcularEstadisticas($clienteId): array
    {
        $pedidos = Pedido::where('cliente_id', $clienteId)
            ->whereNotIn('estado', ['anulado', 'cancelado'])
            ->get();

        $totalPedidos = $pedidos->count();
        $totalGastado = $pedidos->sum('total');
        $promedioGasto = $totalPedidos > 0 ? $totalGastado / $totalPedidos : 0;

        // Pedidos por tipo
        $pedidosPorTipo = [
            'mesa' => $pedidos->where('tipo', 'mesa')->count(),
            'mostrador' => $pedidos->where('tipo', 'mostrador')->count(),
            'web' => $pedidos->where('tipo', 'web')->count(),
        ];

        // Pedidos por estado
        $pedidosPorEstado = Pedido::where('cliente_id', $clienteId)
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // Último pedido
        $ultimoPedido = Pedido::where('cliente_id', $clienteId)
            ->latest()
            ->first();

        // Producto favorito
        $productoFavorito = DB::table('pedido_items')
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
            ->where('pedidos.cliente_id', $clienteId)
            ->whereNotIn('pedidos.estado', ['anulado', 'cancelado'])
            ->select('productos.nombre', DB::raw('SUM(pedido_items.cantidad) as total'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total', 'desc')
            ->first();

        return [
            'total_pedidos' => $totalPedidos,
            'total_gastado' => $totalGastado,
            'promedio_gasto' => $promedioGasto,
            'pedidos_por_tipo' => $pedidosPorTipo,
            'pedidos_por_estado' => $pedidosPorEstado,
            'ultimo_pedido' => $ultimoPedido,
            'producto_favorito' => $productoFavorito,
        ];
    }
}
