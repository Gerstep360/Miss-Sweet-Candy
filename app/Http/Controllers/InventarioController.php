<?php

namespace App\Http\Controllers;

use App\Models\InventarioProducto;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InventarioController extends Controller
{
    use AuthorizesRequests;

    /**
     * Muestra el listado de inventario con filtros
     */
    public function index(Request $request)
    {
        $this->authorize('ver-inventario');

        $query = InventarioProducto::with(['producto.categoria']);

        // Filtro por producto
        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->whereHas('producto', function($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            });
        }

        // Filtro por estado (OK, BAJO, CRÍTICO)
        if ($request->filled('estado')) {
            $query->porEstado($request->estado);
        }

        // Filtro por búsqueda de nombre
        if ($request->filled('buscar')) {
            $query->whereHas('producto', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%');
            });
        }

        $inventarios = $query->orderBy('stock_actual', 'asc')->paginate(20);

        $productos = Producto::orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta',
            'inventario',
            null,
            null,
            $request
        );

        return view('inventario.index', compact('inventarios', 'productos', 'categorias'));
    }

    /**
     * Muestra el detalle de inventario de un producto
     */
    public function show($id)
    {
        $this->authorize('ver-inventario');

        $inventario = InventarioProducto::with('producto.categoria')->findOrFail($id);

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_detalle',
            'inventario',
            $inventario->id,
            null,
            request()
        );

        return view('inventario.show', compact('inventario'));
    }

    /**
     * Actualiza el stock de un producto
     */
    public function updateStock(Request $request, $productoId)
    {
        $this->authorize('editar-inventario');

        $validated = $request->validate([
            'tipo_movimiento' => 'required|string|in:ENTRADA,SALIDA,AJUSTE',
            'cantidad' => 'required|integer|min:1',
        ]);

        $inventario = InventarioProducto::where('producto_id', $productoId)->firstOrFail();
        $producto = $inventario->producto;
        $stockAnterior = $inventario->stock_actual;

        // Actualizar stock según el tipo de movimiento
        switch ($validated['tipo_movimiento']) {
            case 'ENTRADA':
                $inventario->incrementarStock($validated['cantidad']);
                break;
            case 'SALIDA':
                $inventario->decrementarStock($validated['cantidad']);
                break;
            case 'AJUSTE':
                // Ajuste manual: establecer el stock exacto
                $inventario->stock_actual = $validated['cantidad'];
                $inventario->save();
                break;
        }

        $stockNuevo = $inventario->stock_actual;

        // Registrar en bitácora
        BitacoraController::registrar(
            'actualizacion_stock',
            'inventario',
            $inventario->id,
            null,
            $request
        );

        // Verificar si requiere alerta y generarla
        if ($inventario->requiereAlerta()) {
            $this->generarAlertaStock($inventario, $producto);
        }

        return redirect()->route('inventario.show', $inventario->producto_id)
            ->with('success', 'Stock actualizado correctamente.');
    }

    /**
     * Muestra el formulario de ajuste de stock
     */
    public function editStock($productoId)
    {
        $this->authorize('editar-inventario');

        $inventario = InventarioProducto::where('producto_id', $productoId)
            ->with('producto')
            ->firstOrFail();

        return view('inventario.edit-stock', compact('inventario'));
    }

    /**
     * Muestra productos con alertas (stock bajo o crítico)
     */
    public function alertas()
    {
        $this->authorize('ver-inventario');

        $criticos = InventarioProducto::criticos()
            ->with(['producto.categoria'])
            ->orderBy('stock_actual', 'asc')
            ->get();

        $bajos = InventarioProducto::bajos()
            ->with(['producto.categoria'])
            ->orderBy('stock_actual', 'asc')
            ->get();

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_alertas',
            'inventario',
            null,
            null,
            request()
        );

        return view('inventario.alertas', compact('criticos', 'bajos'));
    }

    /**
     * Genera una alerta/notificación de stock bajo o crítico
     */
    protected function generarAlertaStock(InventarioProducto $inventario, Producto $producto)
    {
        $estado = $inventario->estado_stock;
        $mensaje = "El producto '{$producto->nombre}' tiene stock {$estado}. Stock actual: {$inventario->stock_actual}, Stock mínimo: {$inventario->stock_minimo}.";

        // Obtener usuarios con permiso para recibir alertas de inventario
        $usuariosDestino = User::permission('ver-inventario')->get();

        foreach ($usuariosDestino as $usuario) {
            // Verificar si ya existe una notificación reciente (últimas 24 horas)
            $notificacionReciente = Notificacion::where('usuario_destino_id', $usuario->id)
                ->where('rel_model', 'producto')
                ->where('rel_id', $producto->id)
                ->where('tipo', 'stock')
                ->where('leido', false)
                ->where('id', '>', now()->subDay()->timestamp) // Aproximación simple
                ->first();

            // Solo crear notificación si no hay una reciente
            if (!$notificacionReciente) {
                Notificacion::create([
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

        // Registrar en bitácora
        BitacoraController::registrar(
            'alerta_generada',
            'inventario',
            $inventario->id,
            null,
            request()
        );
    }

    /**
     * Exporta el reporte de inventario a PDF/Excel
     */
    public function exportar(Request $request)
    {
        $this->authorize('ver-inventario');

        $formato = $request->input('formato', 'pdf'); // pdf o excel

        $query = InventarioProducto::with(['producto.categoria']);

        // Aplicar mismos filtros que en index
        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('categoria_id')) {
            $query->whereHas('producto', function($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            });
        }

        if ($request->filled('estado')) {
            $query->porEstado($request->estado);
        }

        if ($request->filled('buscar')) {
            $query->whereHas('producto', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%');
            });
        }

        $inventarios = $query->orderBy('stock_actual', 'asc')->get();

        // Registrar en bitácora
        BitacoraController::registrar(
            'exportacion',
            'inventario',
            null,
            null,
            $request
        );

        // TODO: Implementar lógica de exportación PDF/Excel
        // Por ahora, retornar vista simple
        return view('inventario.reporte', compact('inventarios', 'formato'));
    }

    /**
     * Muestra el formulario de configuración de umbrales
     */
    public function editUmbrales($productoId)
    {
        $this->authorize('editar-inventario');

        $inventario = InventarioProducto::where('producto_id', $productoId)
            ->with('producto')
            ->firstOrFail();

        return view('inventario.edit-umbrales', compact('inventario'));
    }

    /**
     * Actualiza los umbrales de stock (mínimo y punto de reposición)
     */
    public function updateUmbrales(Request $request, $productoId)
    {
        $this->authorize('editar-inventario');

        $validated = $request->validate([
            'stock_minimo' => 'required|integer|min:0',
            'punto_reposicion' => 'required|integer|min:0',
            'ubicacion' => 'nullable|string|max:120',
        ]);

        $inventario = InventarioProducto::where('producto_id', $productoId)->firstOrFail();
        $producto = $inventario->producto;

        $inventario->update($validated);

        // Registrar en bitácora
        BitacoraController::registrar(
            'actualizacion_umbrales',
            'inventario',
            $inventario->id,
            null,
            $request
        );

        return redirect()->route('inventario.index')
            ->with('success', 'Umbrales actualizados correctamente.');
    }
}
