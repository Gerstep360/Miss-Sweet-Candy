<?php

namespace App\Http\Controllers\Reportes;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\CobroCaja;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteVentasController extends Controller
{

    /**
     * Vista principal de reportes de ventas
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para ver los reportes de ventas.');
        }

        // Parámetros de filtro
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $tipoVenta = $request->input('tipo_venta', 'todos'); // mesa, mostrador, web, todos
        $metodoPago = $request->input('metodo_pago', 'todos'); // efectivo, pos, qr, todos
        $cajero = $request->input('cajero_id', 'todos');

        // Query base de ventas cobradas
        $ventasQuery = CobroCaja::with(['pedido.items.producto', 'pedido.mesa', 'cajero'])
            ->where('estado', 'cobrado')
            ->whereBetween('created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ]);

        // Filtros adicionales
        if ($tipoVenta !== 'todos') {
            $ventasQuery->whereHas('pedido', function ($q) use ($tipoVenta) {
                $q->where('tipo', $tipoVenta);
            });
        }

        if ($metodoPago !== 'todos') {
            $ventasQuery->where('metodo', $metodoPago);
        }

        if ($cajero !== 'todos') {
            $ventasQuery->where('cajero_id', $cajero);
        }

        // Obtener ventas
        $ventas = $ventasQuery->orderBy('created_at', 'desc')->paginate(20);

        // Resumen general
        $resumen = $this->calcularResumen($fechaInicio, $fechaFin, $tipoVenta, $metodoPago, $cajero);

        // Ventas por tipo
        $ventasPorTipo = $this->ventasPorTipo($fechaInicio, $fechaFin);

        // Ventas por método de pago
        $ventasPorMetodo = $this->ventasPorMetodo($fechaInicio, $fechaFin);

        // Top productos vendidos
        $topProductos = $this->topProductosVendidos($fechaInicio, $fechaFin, 10);

        // Cajeros
        $cajeros = User::role(['cajero', 'administrador'])->orderBy('name')->get();

        // Registrar en bitácora
        BitacoraController::registrar('Ver', 'ReporteVentas');

        return view('reportes.ventas.index', compact(
            'ventas',
            'resumen',
            'ventasPorTipo',
            'ventasPorMetodo',
            'topProductos',
            'cajeros',
            'fechaInicio',
            'fechaFin',
            'tipoVenta',
            'metodoPago',
            'cajero'
        ));
    }

    /**
     * Calcular resumen de ventas
     */
    private function calcularResumen($fechaInicio, $fechaFin, $tipoVenta, $metodoPago, $cajero)
    {
        $query = CobroCaja::where('estado', 'cobrado')
            ->whereBetween('created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ]);

        // Aplicar filtros
        if ($tipoVenta !== 'todos') {
            $query->whereHas('pedido', function ($q) use ($tipoVenta) {
                $q->where('tipo', $tipoVenta);
            });
        }

        if ($metodoPago !== 'todos') {
            $query->where('metodo', $metodoPago);
        }

        if ($cajero !== 'todos') {
            $query->where('cajero_id', $cajero);
        }

        $totalVentas = $query->sum('importe');
        $cantidadVentas = $query->count();
        $promedioVenta = $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0;

        // Total de items vendidos
        $pedidoIds = $query->pluck('pedido_id');
        $totalItems = PedidoItem::whereIn('pedido_id', $pedidoIds)
            ->whereNotIn('estado_item', ['cancelado', 'anulado'])
            ->sum('cantidad');

        return [
            'total_ventas' => $totalVentas,
            'cantidad_ventas' => $cantidadVentas,
            'promedio_venta' => $promedioVenta,
            'total_items' => $totalItems,
        ];
    }

    /**
     * Ventas agrupadas por tipo (mesa, mostrador, web)
     */
    private function ventasPorTipo($fechaInicio, $fechaFin)
    {
        return CobroCaja::join('pedidos', 'cobro_cajas.pedido_id', '=', 'pedidos.id')
            ->where('cobro_cajas.estado', 'cobrado')
            ->whereBetween('cobro_cajas.created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ])
            ->select(
                'pedidos.tipo',
                DB::raw('COUNT(cobro_cajas.id) as cantidad'),
                DB::raw('SUM(cobro_cajas.importe) as total')
            )
            ->groupBy('pedidos.tipo')
            ->get();
    }

    /**
     * Ventas agrupadas por método de pago
     */
    private function ventasPorMetodo($fechaInicio, $fechaFin)
    {
        return CobroCaja::where('estado', 'cobrado')
            ->whereBetween('created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ])
            ->select(
                'metodo',
                DB::raw('COUNT(id) as cantidad'),
                DB::raw('SUM(importe) as total')
            )
            ->groupBy('metodo')
            ->get();
    }

    /**
     * Top productos más vendidos
     */
    private function topProductosVendidos($fechaInicio, $fechaFin, $limit = 10)
    {
        $pedidoIds = CobroCaja::where('estado', 'cobrado')
            ->whereBetween('created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ])
            ->pluck('pedido_id');

        return PedidoItem::whereIn('pedido_id', $pedidoIds)
            ->whereNotIn('estado_item', ['cancelado', 'anulado'])
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
            ->select(
                'productos.id',
                'productos.nombre',
                DB::raw('SUM(pedido_items.cantidad) as total_vendido'),
                DB::raw('SUM(pedido_items.subtotal_item) as total_ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Reporte de ventas por cajero
     */
    public function porCajero(Request $request)
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para ver los reportes de ventas.');
        }

        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        $ventasPorCajero = CobroCaja::with('cajero')
            ->where('estado', 'cobrado')
            ->whereBetween('created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ])
            ->select(
                'cajero_id',
                DB::raw('COUNT(id) as cantidad_ventas'),
                DB::raw('SUM(importe) as total_ventas'),
                DB::raw('AVG(importe) as promedio_venta')
            )
            ->groupBy('cajero_id')
            ->orderBy('total_ventas', 'desc')
            ->get();

        return view('reportes.ventas.por-cajero', compact(
            'ventasPorCajero',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Reporte de ventas por producto
     */
    public function porProducto(Request $request)
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para ver los reportes de ventas.');
        }

        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $categoriaId = $request->input('categoria_id', 'todos');

        $pedidoIds = CobroCaja::where('estado', 'cobrado')
            ->whereBetween('created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ])
            ->pluck('pedido_id');

        $query = PedidoItem::whereIn('pedido_id', $pedidoIds)
            ->whereNotIn('estado_item', ['cancelado', 'anulado'])
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id');

        if ($categoriaId !== 'todos') {
            $query->where('productos.categoria_id', $categoriaId);
        }

        $ventasPorProducto = $query->select(
                'productos.id',
                'productos.nombre',
                'productos.precio as precio_actual',
                DB::raw('SUM(pedido_items.cantidad) as cantidad_vendida'),
                DB::raw('AVG(pedido_items.precio_unitario) as precio_promedio'),
                DB::raw('SUM(pedido_items.subtotal_item) as total_ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.precio')
            ->orderBy('total_ingresos', 'desc')
            ->paginate(20);

        $categorias = \App\Models\Categoria::orderBy('nombre')->get();

        return view('reportes.ventas.por-producto', compact(
            'ventasPorProducto',
            'categorias',
            'fechaInicio',
            'fechaFin',
            'categoriaId'
        ));
    }

    /**
     * Reporte de ventas diarias (gráfico)
     */
    public function ventasDiarias(Request $request)
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para ver los reportes de ventas.');
        }

        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        $ventasDiarias = CobroCaja::where('estado', 'cobrado')
            ->whereBetween('created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ])
            ->select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('COUNT(id) as cantidad'),
                DB::raw('SUM(importe) as total')
            )
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        return view('reportes.ventas.diarias', compact(
            'ventasDiarias',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Exportar reporte a PDF
     */
    public function exportarPDF(Request $request)
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes de ventas.');
        }

        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $tipoVenta = $request->input('tipo_venta', 'todos');
        $metodoPago = $request->input('metodo_pago', 'todos');
        $cajero = $request->input('cajero_id', 'todos');

        // Query de ventas
        $ventasQuery = CobroCaja::with(['pedido.items.producto', 'pedido.mesa', 'cajero'])
            ->where('estado', 'cobrado')
            ->whereBetween('created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ]);

        if ($tipoVenta !== 'todos') {
            $ventasQuery->whereHas('pedido', function ($q) use ($tipoVenta) {
                $q->where('tipo', $tipoVenta);
            });
        }

        if ($metodoPago !== 'todos') {
            $ventasQuery->where('metodo', $metodoPago);
        }

        if ($cajero !== 'todos') {
            $ventasQuery->where('cajero_id', $cajero);
        }

        $ventas = $ventasQuery->orderBy('created_at', 'desc')->get();
        $resumen = $this->calcularResumen($fechaInicio, $fechaFin, $tipoVenta, $metodoPago, $cajero);
        $ventasPorTipo = $this->ventasPorTipo($fechaInicio, $fechaFin);
        $ventasPorMetodo = $this->ventasPorMetodo($fechaInicio, $fechaFin);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportes.ventas.pdf', compact(
            'ventas',
            'resumen',
            'ventasPorTipo',
            'ventasPorMetodo',
            'fechaInicio',
            'fechaFin',
            'tipoVenta',
            'metodoPago'
        ));

        $pdf->setPaper('letter', 'portrait');

        $nombreArchivo = 'reporte-ventas-' . $fechaInicio . '-al-' . $fechaFin . '.pdf';

        // Registrar en bitácora
        BitacoraController::registrar('Exportar PDF', 'ReporteVentas');

        return $pdf->download($nombreArchivo);
    }

    /**
     * Exportar reporte a Excel (CSV)
     */
    public function exportarExcel(Request $request)
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes de ventas.');
        }

        // Registrar en bitácora
        BitacoraController::registrar('Exportar Excel', 'ReporteVentas');

        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $tipoVenta = $request->input('tipo_venta', 'todos');
        $metodoPago = $request->input('metodo_pago', 'todos');
        $cajero = $request->input('cajero_id', 'todos');

        $ventasQuery = CobroCaja::with(['pedido', 'cajero'])
            ->where('estado', 'cobrado')
            ->whereBetween('created_at', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ]);

        if ($tipoVenta !== 'todos') {
            $ventasQuery->whereHas('pedido', function ($q) use ($tipoVenta) {
                $q->where('tipo', $tipoVenta);
            });
        }

        if ($metodoPago !== 'todos') {
            $ventasQuery->where('metodo', $metodoPago);
        }

        if ($cajero !== 'todos') {
            $ventasQuery->where('cajero_id', $cajero);
        }

        $ventas = $ventasQuery->orderBy('created_at', 'desc')->get();

        $nombreArchivo = 'reporte-ventas-' . $fechaInicio . '-al-' . $fechaFin . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ];

        $callback = function() use ($ventas) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($file, [
                'ID',
                'Fecha',
                'Hora',
                'Pedido #',
                'Tipo Venta',
                'Método Pago',
                'Cajero',
                'Importe (Bs)',
                'Estado'
            ]);

            // Datos
            foreach ($ventas as $venta) {
                fputcsv($file, [
                    $venta->id,
                    $venta->created_at->format('d/m/Y'),
                    $venta->created_at->format('H:i:s'),
                    $venta->pedido_id,
                    ucfirst($venta->pedido->tipo ?? ''),
                    ucfirst($venta->metodo),
                    $venta->cajero->name ?? '',
                    number_format($venta->importe, 2, '.', ''),
                    ucfirst($venta->estado)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
