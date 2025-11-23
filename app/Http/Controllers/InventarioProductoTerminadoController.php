<?php

namespace App\Http\Controllers;

use App\Models\InventarioProducto;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * CU15: Inventario de Producto Terminado
 * 
 * Gestiona el inventario de productos terminados (bebidas preparadas, postres, etc.)
 * incluyendo registro de producción, mermas, ajustes y reportes de rotación.
 */
class InventarioProductoTerminadoController extends Controller
{
    use AuthorizesRequests;

    /**
     * Muestra el listado de productos terminados en inventario
     */
    public function index(Request $request)
    {
        $this->authorize('ver-inventario');

        $query = InventarioProducto::with(['producto.categoria'])
            ->whereHas('producto', function($q) {
                // Solo productos terminados (bebidas, postres, etc.)
                $q->where('tipo', 'terminado');
            });

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->whereHas('producto', function($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            });
        }

        // Filtro por búsqueda
        if ($request->filled('buscar')) {
            $query->whereHas('producto', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%');
            });
        }

        // Filtro por estado de stock
        if ($request->filled('estado')) {
            $query->porEstado($request->estado);
        }

        $inventarios = $query->orderBy('updated_at', 'desc')->paginate(20);
        $categorias = Categoria::orderBy('nombre')->get();

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta',
            'inventario_producto_terminado',
            null,
            null,
            $request
        );

        return view('inventario.producto-terminado.index', compact('inventarios', 'categorias'));
    }

    /**
     * Muestra el formulario para registrar producción de productos terminados
     */
    public function create()
    {
        $this->authorize('editar-inventario');

        $productos = Producto::where('tipo', 'terminado')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('inventario.producto-terminado.create', compact('productos'));
    }

    /**
     * Registra la producción de productos terminados
     */
    public function store(Request $request)
    {
        $this->authorize('editar-inventario');

        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'lote' => 'nullable|string|max:50',
            'fecha_produccion' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after:fecha_produccion',
            'notas' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $inventario = InventarioProducto::where('producto_id', $validated['producto_id'])
                ->firstOrFail();

            $stockAnterior = $inventario->stock_actual;

            // Incrementar stock por producción
            $inventario->incrementarStock($validated['cantidad']);

            // Registrar en bitácora
            BitacoraController::registrar(
                'produccion_registrada',
                'inventario_producto_terminado',
                $inventario->id,
                [
                    'producto_id' => $validated['producto_id'],
                    'cantidad' => $validated['cantidad'],
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $inventario->stock_actual,
                    'lote' => $validated['lote'] ?? null,
                    'fecha_produccion' => $validated['fecha_produccion'],
                    'fecha_vencimiento' => $validated['fecha_vencimiento'] ?? null,
                ],
                $request
            );

            DB::commit();

            return redirect()->route('inventario.producto-terminado.index')
                ->with('success', 'Producción registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al registrar producción: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el detalle de un producto terminado en inventario
     */
    public function show($id)
    {
        $this->authorize('ver-inventario');

        $inventario = InventarioProducto::with('producto.categoria')->findOrFail($id);

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_detalle',
            'inventario_producto_terminado',
            $inventario->id,
            null,
            request()
        );

        return view('inventario.producto-terminado.show', compact('inventario'));
    }

    /**
     * Muestra el formulario para registrar merma o desperdicio
     */
    public function createMerma($productoId)
    {
        $this->authorize('editar-inventario');

        $inventario = InventarioProducto::where('producto_id', $productoId)
            ->with('producto')
            ->firstOrFail();

        return view('inventario.producto-terminado.merma', compact('inventario'));
    }

    /**
     * Registra merma o desperdicio de productos terminados
     */
    public function storeMerma(Request $request, $productoId)
    {
        $this->authorize('editar-inventario');

        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|in:vencido,dañado,derramado,calidad,otro',
            'descripcion' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $inventario = InventarioProducto::where('producto_id', $productoId)
                ->firstOrFail();

            $stockAnterior = $inventario->stock_actual;

            // Verificar que hay suficiente stock
            if ($inventario->stock_actual < $validated['cantidad']) {
                return back()->withInput()
                    ->with('error', 'No hay suficiente stock para registrar esta merma.');
            }

            // Decrementar stock por merma
            $inventario->decrementarStock($validated['cantidad']);

            $stockNuevo = $inventario->stock_actual;

            // Registrar en bitácora
            BitacoraController::registrar(
                'merma_registrada',
                'inventario_producto_terminado',
                $inventario->id,
                [
                    'producto_id' => $productoId,
                    'cantidad' => $validated['cantidad'],
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'motivo' => $validated['motivo'],
                    'descripcion' => $validated['descripcion'] ?? null,
                ],
                $request
            );

            DB::commit();

            return redirect()->route('inventario.producto-terminado.show', $productoId)
                ->with('success', 'Merma registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al registrar merma: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para ajuste manual de inventario
     */
    public function createAjuste($productoId)
    {
        $this->authorize('editar-inventario');

        $inventario = InventarioProducto::where('producto_id', $productoId)
            ->with('producto')
            ->firstOrFail();

        return view('inventario.producto-terminado.ajuste', compact('inventario'));
    }

    /**
     * Realiza ajuste manual de inventario de producto terminado
     */
    public function storeAjuste(Request $request, $productoId)
    {
        $this->authorize('editar-inventario');

        $validated = $request->validate([
            'stock_nuevo' => 'required|integer|min:0',
            'motivo' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $inventario = InventarioProducto::where('producto_id', $productoId)
                ->firstOrFail();

            $stockAnterior = $inventario->stock_actual;
            $diferencia = $validated['stock_nuevo'] - $stockAnterior;

            // Ajustar stock
            $inventario->stock_actual = $validated['stock_nuevo'];
            $inventario->save();

            // Registrar en bitácora
            BitacoraController::registrar(
                'ajuste_manual',
                'inventario_producto_terminado',
                $inventario->id,
                [
                    'producto_id' => $productoId,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $validated['stock_nuevo'],
                    'diferencia' => $diferencia,
                    'motivo' => $validated['motivo'],
                ],
                $request
            );

            DB::commit();

            return redirect()->route('inventario.producto-terminado.show', $productoId)
                ->with('success', 'Ajuste de inventario realizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al realizar ajuste: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el reporte de rotación de productos terminados
     */
    public function reporteRotacion(Request $request)
    {
        $this->authorize('ver-inventario');

        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        // Obtener productos terminados con sus movimientos
        $productos = Producto::where('tipo', 'terminado')
            ->with(['inventario'])
            ->get();

        // Registrar en bitácora
        BitacoraController::registrar(
            'reporte_rotacion',
            'inventario_producto_terminado',
            null,
            [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
            ],
            $request
        );

        return view('inventario.producto-terminado.reporte-rotacion', compact(
            'productos',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Exporta el reporte de productos terminados
     */
    public function exportar(Request $request)
    {
        $this->authorize('ver-inventario');

        $formato = $request->input('formato', 'pdf');

        $query = InventarioProducto::with(['producto.categoria'])
            ->whereHas('producto', function($q) {
                $q->where('tipo', 'terminado');
            });

        // Aplicar filtros
        if ($request->filled('categoria_id')) {
            $query->whereHas('producto', function($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            });
        }

        if ($request->filled('estado')) {
            $query->porEstado($request->estado);
        }

        $inventarios = $query->orderBy('stock_actual', 'asc')->get();

        // Registrar en bitácora
        BitacoraController::registrar(
            'exportacion',
            'inventario_producto_terminado',
            null,
            ['formato' => $formato],
            $request
        );

        // TODO: Implementar exportación PDF/Excel
        return view('inventario.producto-terminado.reporte', compact('inventarios', 'formato'));
    }
}
