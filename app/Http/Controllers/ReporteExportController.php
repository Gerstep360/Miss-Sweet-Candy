<?php

namespace App\Http\Controllers;


use App\Models\InventarioProducto;
use App\Models\CobroCaja;
use App\Models\CierreCaja;
use App\Models\Pedido;
use App\Models\Promocion;
use App\Models\User;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteExportController extends Controller
{
    /**
     * Vista principal de exportación de reportes
     */
    public function index()
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        return view('reportes.exportar.index');
    }

    // =====================================================
    // REPORTES DE INVENTARIO
    // =====================================================

    /**
     * Formulario de exportación de inventario
     */
    public function inventarioForm()
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $categorias = Categoria::orderBy('nombre')->get();

        return view('reportes.exportar.inventario-form', compact('categorias'));
    }

    /**
     * Exportar reporte de inventario a PDF
     */
    public function inventarioPDF(Request $request)
    {
        // Aumentar límite de memoria y tiempo de ejecución
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // VALIDACIÓN
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:todo,personalizado',
            'estado_stock' => 'nullable|in:OK,BAJO,CRÍTICO',
            'categoria_id' => 'nullable|exists:categorias,id',
        ]);

        try {
            // VERIFICACIÓN - Obtener datos
            $query = InventarioProducto::with(['producto.categoria']);

            // Aplicar filtros personalizados
            if ($validated['tipo'] === 'personalizado') {
                if (!empty($validated['estado_stock'])) {
                    $query->porEstado($validated['estado_stock']);
                }

                if (!empty($validated['categoria_id'])) {
                    $query->whereHas('producto', function ($q) use ($validated) {
                        $q->where('categoria_id', $validated['categoria_id']);
                    });
                }
            }

            $inventarios = $query->orderBy('stock_actual', 'asc')->get();

            // Calcular estadísticas
            $estadisticas = [
                'total_productos' => $inventarios->count(),
                'criticos' => $inventarios->where('estado_stock', 'CRÍTICO')->count(),
                'bajos' => $inventarios->where('estado_stock', 'BAJO')->count(),
                'ok' => $inventarios->where('estado_stock', 'OK')->count(),
            ];

            // Generar PDF
            $pdf = Pdf::loadView('reportes.exportar.inventario-pdf', compact(
                'inventarios',
                'estadisticas',
                'validated'
            ));

            $pdf->setPaper('letter', 'portrait');

            $nombreArchivo = 'reporte-inventario-' . now()->format('Y-m-d-His') . '.pdf';



            // Registrar en bitácora
            BitacoraController::registrar('Exportar PDF', 'ReporteInventario');

            return $pdf->download($nombreArchivo);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Exportar reporte de inventario a Excel (CSV)
     */
    public function inventarioExcel(Request $request)
    {
        // VALIDACIÓN
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:todo,personalizado',
            'estado_stock' => 'nullable|in:OK,BAJO,CRÍTICO',
            'categoria_id' => 'nullable|exists:categorias,id',
        ]);

        try {
            // VERIFICACIÓN - Obtener datos
            $query = InventarioProducto::with(['producto.categoria']);

            // Aplicar filtros personalizados
            if ($validated['tipo'] === 'personalizado') {
                if (!empty($validated['estado_stock'])) {
                    $query->porEstado($validated['estado_stock']);
                }

                if (!empty($validated['categoria_id'])) {
                    $query->whereHas('producto', function ($q) use ($validated) {
                        $q->where('categoria_id', $validated['categoria_id']);
                    });
                }
            }

            $inventarios = $query->orderBy('stock_actual', 'asc')->get();

            $nombreArchivo = 'reporte-inventario-' . now()->format('Y-m-d-His') . '.csv';



            // Registrar en bitácora
            BitacoraController::registrar('Exportar Excel', 'ReporteInventario');

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
            ];

            $callback = function () use ($inventarios) {
                $file = fopen('php://output', 'w');

                // BOM para UTF-8
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Encabezados
                fputcsv($file, [
                    'ID',
                    'Producto',
                    'Categoría',
                    'Stock Actual',
                    'Stock Mínimo',
                    'Punto Reposición',
                    'Estado',
                    'Ubicación'
                ]);

                // Datos
                foreach ($inventarios as $inv) {
                    fputcsv($file, [
                        $inv->id,
                        $inv->producto->nombre ?? '',
                        $inv->producto->categoria->nombre ?? '',
                        $inv->stock_actual,
                        $inv->stock_minimo,
                        $inv->punto_reposicion,
                        $inv->estado_stock,
                        $inv->ubicacion ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    // =====================================================
    // REPORTES DE COBRO CAJA
    // =====================================================

    /**
     * Formulario de exportación de cobros de caja
     */
    public function cobroCajaForm()
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $cajeros = User::role(['cajero', 'administrador'])->orderBy('name')->get();

        return view('reportes.exportar.cobro-caja-form', compact('cajeros'));
    }

    /**
     * Exportar reporte de cobros de caja a PDF
     */
    public function cobroCajaPDF(Request $request)
    {
        // Aumentar límite de memoria y tiempo de ejecución para reportes grandes
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // VALIDACIÓN
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:todo,personalizado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'metodo_pago' => 'nullable|in:efectivo,pos,qr',
            'cajero_id' => 'nullable|exists:users,id',
            'estado' => 'nullable|in:cobrado,cancelado',
        ]);

        try {
            // VERIFICACIÓN - Obtener datos
            $query = CobroCaja::with(['pedido', 'cajero']);

            // Aplicar filtros
            if ($validated['tipo'] === 'personalizado') {
                if (!empty($validated['fecha_inicio']) && !empty($validated['fecha_fin'])) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($validated['fecha_inicio'])->startOfDay(),
                        Carbon::parse($validated['fecha_fin'])->endOfDay()
                    ]);
                }

                if (!empty($validated['metodo_pago'])) {
                    $query->where('metodo', $validated['metodo_pago']);
                }

                if (!empty($validated['cajero_id'])) {
                    $query->where('cajero_id', $validated['cajero_id']);
                }

                if (!empty($validated['estado'])) {
                    $query->where('estado', $validated['estado']);
                }
            } else {
                // Por defecto, del último mes
                $query->whereBetween('created_at', [
                    now()->subMonth()->startOfDay(),
                    now()->endOfDay()
                ]);
            }

            $cobros = $query->orderBy('created_at', 'desc')->get();

            // Estadísticas
            $estadisticas = [
                'total_cobros' => $cobros->count(),
                'total_importe' => $cobros->sum('importe'),
                'cobrados' => $cobros->where('estado', 'cobrado')->count(),
                'cancelados' => $cobros->where('estado', 'cancelado')->count(),
            ];

            // Generar PDF
            $pdf = Pdf::loadView('reportes.exportar.cobro-caja-pdf', compact(
                'cobros',
                'estadisticas',
                'validated'
            ));

            $pdf->setPaper('letter', 'landscape');

            $nombreArchivo = 'reporte-cobros-' . now()->format('Y-m-d-His') . '.pdf';



            // Registrar en bitácora
            BitacoraController::registrar('Exportar PDF', 'ReporteCobros');

            return $pdf->download($nombreArchivo);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Exportar reporte de cobros de caja a Excel (CSV)
     */
    public function cobroCajaExcel(Request $request)
    {
        // VALIDACIÓN
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:todo,personalizado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'metodo_pago' => 'nullable|in:efectivo,pos,qr',
            'cajero_id' => 'nullable|exists:users,id',
            'estado' => 'nullable|in:cobrado,cancelado',
        ]);

        try {
            // VERIFICACIÓN - Obtener datos
            $query = CobroCaja::with(['pedido', 'cajero']);

            // Aplicar filtros
            if ($validated['tipo'] === 'personalizado') {
                if (!empty($validated['fecha_inicio']) && !empty($validated['fecha_fin'])) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($validated['fecha_inicio'])->startOfDay(),
                        Carbon::parse($validated['fecha_fin'])->endOfDay()
                    ]);
                }

                if (!empty($validated['metodo_pago'])) {
                    $query->where('metodo', $validated['metodo_pago']);
                }

                if (!empty($validated['cajero_id'])) {
                    $query->where('cajero_id', $validated['cajero_id']);
                }

                if (!empty($validated['estado'])) {
                    $query->where('estado', $validated['estado']);
                }
            } else {
                // Por defecto, del último mes
                $query->whereBetween('created_at', [
                    now()->subMonth()->startOfDay(),
                    now()->endOfDay()
                ]);
            }

            $cobros = $query->orderBy('created_at', 'desc')->get();

            $nombreArchivo = 'reporte-cobros-' . now()->format('Y-m-d-His') . '.csv';



            // Registrar en bitácora
            BitacoraController::registrar('Exportar Excel', 'ReporteCobros');

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
            ];

            $callback = function () use ($cobros) {
                $file = fopen('php://output', 'w');

                // BOM para UTF-8
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Encabezados
                fputcsv($file, [
                    'ID',
                    'Fecha',
                    'Hora',
                    'Pedido #',
                    'Método Pago',
                    'Cajero',
                    'Importe (Bs)',
                    'Estado',
                    'Comprobante'
                ]);

                // Datos
                foreach ($cobros as $cobro) {
                    fputcsv($file, [
                        $cobro->id,
                        $cobro->created_at->format('d/m/Y'),
                        $cobro->created_at->format('H:i:s'),
                        $cobro->pedido_id,
                        ucfirst($cobro->metodo),
                        $cobro->cajero->name ?? '',
                        number_format((float) $cobro->importe, 2, '.', ''),
                        ucfirst($cobro->estado),
                        $cobro->comprobante ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    // =====================================================
    // REPORTES DE ARQUEOS (CIERRES DE CAJA)
    // =====================================================

    /**
     * Formulario de exportación de arqueos
     */
    public function arqueosForm()
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $cajeros = User::role(['cajero', 'administrador'])->orderBy('name')->get();

        return view('reportes.exportar.arqueos-form', compact('cajeros'));
    }

    /**
     * Exportar reporte de arqueos a PDF
     */
    public function arqueosPDF(Request $request)
    {
        // Aumentar límite de memoria y tiempo de ejecución
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // VALIDACIÓN
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:todo,personalizado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'cajero_id' => 'nullable|exists:users,id',
            'con_diferencias' => 'nullable|boolean',
        ]);

        try {
            // VERIFICACIÓN - Obtener datos
            $query = CierreCaja::with(['cajero', 'turno', 'detalles']);

            // Aplicar filtros
            if ($validated['tipo'] === 'personalizado') {
                if (!empty($validated['fecha_inicio']) && !empty($validated['fecha_fin'])) {
                    $query->whereBetween('fin', [
                        Carbon::parse($validated['fecha_inicio'])->startOfDay(),
                        Carbon::parse($validated['fecha_fin'])->endOfDay()
                    ]);
                }

                if (!empty($validated['cajero_id'])) {
                    $query->where('cajero_id', $validated['cajero_id']);
                }

                if (!empty($validated['con_diferencias'])) {
                    $query->conDiferencias();
                }
            } else {
                // Por defecto, del último mes
                $query->whereBetween('fin', [
                    now()->subMonth()->startOfDay(),
                    now()->endOfDay()
                ]);
            }

            $arqueos = $query->orderBy('fin', 'desc')->get();

            // Estadísticas
            $estadisticas = [
                'total_arqueos' => $arqueos->count(),
                'total_sistema' => $arqueos->sum('total_sistema'),
                'total_declarado' => $arqueos->sum('total_declarado'),
                'total_diferencia' => $arqueos->sum('diferencia'),
                'cuadrados' => $arqueos->filter->estaCuadrado()->count(),
                'con_diferencias' => $arqueos->filter->tieneDiferencia()->count(),
            ];

            // Generar PDF
            $pdf = Pdf::loadView('reportes.exportar.arqueos-pdf', compact(
                'arqueos',
                'estadisticas',
                'validated'
            ));

            $pdf->setPaper('letter', 'landscape');

            $nombreArchivo = 'reporte-arqueos-' . now()->format('Y-m-d-His') . '.pdf';



            // Registrar en bitácora
            BitacoraController::registrar('Exportar PDF', 'ReporteArqueos');

            return $pdf->download($nombreArchivo);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Exportar reporte de arqueos a Excel (CSV)
     */
    public function arqueosExcel(Request $request)
    {
        // VALIDACIÓN
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:todo,personalizado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'cajero_id' => 'nullable|exists:users,id',
            'con_diferencias' => 'nullable|boolean',
        ]);

        try {
            // VERIFICACIÓN - Obtener datos
            $query = CierreCaja::with(['cajero', 'turno']);

            // Aplicar filtros
            if ($validated['tipo'] === 'personalizado') {
                if (!empty($validated['fecha_inicio']) && !empty($validated['fecha_fin'])) {
                    $query->whereBetween('fin', [
                        Carbon::parse($validated['fecha_inicio'])->startOfDay(),
                        Carbon::parse($validated['fecha_fin'])->endOfDay()
                    ]);
                }

                if (!empty($validated['cajero_id'])) {
                    $query->where('cajero_id', $validated['cajero_id']);
                }

                if (!empty($validated['con_diferencias'])) {
                    $query->conDiferencias();
                }
            } else {
                // Por defecto, del último mes
                $query->whereBetween('fin', [
                    now()->subMonth()->startOfDay(),
                    now()->endOfDay()
                ]);
            }

            $arqueos = $query->orderBy('fin', 'desc')->get();

            $nombreArchivo = 'reporte-arqueos-' . now()->format('Y-m-d-His') . '.csv';



            // Registrar en bitácora
            BitacoraController::registrar('Exportar Excel', 'ReporteArqueos');

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
            ];

            $callback = function () use ($arqueos) {
                $file = fopen('php://output', 'w');

                // BOM para UTF-8
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Encabezados
                fputcsv($file, [
                    'ID',
                    'Cajero',
                    'Fecha Inicio',
                    'Fecha Fin',
                    'Duración (horas)',
                    'Total Sistema (Bs)',
                    'Total Declarado (Bs)',
                    'Diferencia (Bs)',
                    'Tipo Diferencia',
                    'Observaciones'
                ]);

                // Datos
                foreach ($arqueos as $arqueo) {
                    fputcsv($file, [
                        $arqueo->id,
                        $arqueo->cajero->name ?? '',
                        $arqueo->inicio->format('d/m/Y H:i'),
                        $arqueo->fin->format('d/m/Y H:i'),
                        number_format($arqueo->duracion_turno, 2),
                        number_format((float) $arqueo->total_sistema, 2, '.', ''),
                        number_format((float) $arqueo->total_declarado, 2, '.', ''),
                        number_format((float) $arqueo->diferencia, 2, '.', ''),
                        $arqueo->tipo_diferencia,
                        $arqueo->observaciones ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    // =====================================================
    // REPORTES DE PEDIDOS
    // =====================================================

    /**
     * Formulario de exportación de pedidos
     */
    public function pedidosForm()
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        return view('reportes.exportar.pedidos-form');
    }

    /**
     * Exportar reporte de pedidos a PDF
     */
    public function pedidosPDF(Request $request)
    {
        // Aumentar límite de memoria y tiempo de ejecución
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // VALIDACIÓN
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:todo,personalizado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'tipo_pedido' => 'nullable|in:mesa,mostrador,web',
            'estado' => 'nullable|in:pendiente,en_preparacion,preparado,entregado,pagado,anulado',
            'cliente_id' => 'nullable|exists:users,id',
        ]);

        try {
            // VERIFICACIÓN - Obtener datos
            $query = Pedido::with(['cliente', 'atendidoPor', 'items.producto', 'mesa']);

            // Aplicar filtros
            if ($validated['tipo'] === 'personalizado') {
                if (!empty($validated['fecha_inicio']) && !empty($validated['fecha_fin'])) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($validated['fecha_inicio'])->startOfDay(),
                        Carbon::parse($validated['fecha_fin'])->endOfDay()
                    ]);
                }

                if (!empty($validated['tipo_pedido'])) {
                    $query->where('tipo', $validated['tipo_pedido']);
                }

                if (!empty($validated['estado'])) {
                    $query->where('estado', $validated['estado']);
                }

                if (!empty($validated['cliente_id'])) {
                    $query->where('cliente_id', $validated['cliente_id']);
                }
            } else {
                // Por defecto, del último mes
                $query->whereBetween('created_at', [
                    now()->subMonth()->startOfDay(),
                    now()->endOfDay()
                ]);
            }

            $pedidos = $query->orderBy('created_at', 'desc')->get();

            // Estadísticas
            $estadisticas = [
                'total_pedidos' => $pedidos->count(),
                'total_items' => $pedidos->sum(fn($p) => $p->items->sum('cantidad')),
                'total_importe' => $pedidos->sum('total'),
                'por_estado' => $pedidos->groupBy('estado')->map->count(),
            ];

            // Generar PDF
            $pdf = Pdf::loadView('reportes.exportar.pedidos-pdf', compact(
                'pedidos',
                'estadisticas',
                'validated'
            ));

            $pdf->setPaper('letter', 'landscape');

            $nombreArchivo = 'reporte-pedidos-' . now()->format('Y-m-d-His') . '.pdf';



            // Registrar en bitácora
            BitacoraController::registrar('Exportar PDF', 'ReportePedidos');

            return $pdf->download($nombreArchivo);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Exportar reporte de pedidos a Excel (CSV)
     */
    public function pedidosExcel(Request $request)
    {
        // VALIDACIÓN
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:todo,personalizado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'tipo_pedido' => 'nullable|in:mesa,mostrador,web',
            'estado' => 'nullable|in:pendiente,en_preparacion,preparado,entregado,pagado,anulado',
            'cliente_id' => 'nullable|exists:users,id',
        ]);

        try {
            // VERIFICACIÓN - Obtener datos
            $query = Pedido::with(['cliente', 'atendidoPor', 'items', 'mesa']);

            // Aplicar filtros
            if ($validated['tipo'] === 'personalizado') {
                if (!empty($validated['fecha_inicio']) && !empty($validated['fecha_fin'])) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($validated['fecha_inicio'])->startOfDay(),
                        Carbon::parse($validated['fecha_fin'])->endOfDay()
                    ]);
                }

                if (!empty($validated['tipo_pedido'])) {
                    $query->where('tipo', $validated['tipo_pedido']);
                }

                if (!empty($validated['estado'])) {
                    $query->where('estado', $validated['estado']);
                }

                if (!empty($validated['cliente_id'])) {
                    $query->where('cliente_id', $validated['cliente_id']);
                }
            } else {
                // Por defecto, del último mes
                $query->whereBetween('created_at', [
                    now()->subMonth()->startOfDay(),
                    now()->endOfDay()
                ]);
            }

            $pedidos = $query->orderBy('created_at', 'desc')->get();

            $nombreArchivo = 'reporte-pedidos-' . now()->format('Y-m-d-His') . '.csv';



            // Registrar en bitácora
            BitacoraController::registrar('Exportar Excel', 'ReportePedidos');

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
            ];

            $callback = function () use ($pedidos) {
                $file = fopen('php://output', 'w');

                // BOM para UTF-8
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Encabezados
                fputcsv($file, [
                    'ID',
                    'Fecha',
                    'Hora',
                    'Tipo',
                    'Cliente',
                    'Atendido Por',
                    'Mesa',
                    'Estado',
                    'Cant. Items',
                    'Total (Bs)',
                    'Modalidad',
                    'Notas'
                ]);

                // Datos
                foreach ($pedidos as $pedido) {
                    fputcsv($file, [
                        $pedido->id,
                        $pedido->created_at->format('d/m/Y'),
                        $pedido->created_at->format('H:i:s'),
                        ucfirst($pedido->tipo),
                        $pedido->cliente->name ?? '',
                        $pedido->atendidoPor->name ?? '',
                        $pedido->mesa->numero ?? '',
                        ucfirst($pedido->estado),
                        $pedido->items->sum('cantidad'),
                        number_format($pedido->total, 2, '.', ''),
                        ucfirst($pedido->modalidad ?? ''),
                        $pedido->notas ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    // =====================================================
    // REPORTES DE PROMOCIONES
    // =====================================================

    /**
     * Formulario de exportación de promociones
     */
    public function promocionesForm()
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        return view('reportes.exportar.promociones-form');
    }

    /**
     * Exportar reporte de promociones a PDF
     */
    public function promocionesPDF(Request $request)
    {
        // Aumentar límite de memoria y tiempo de ejecución
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // VALIDACIÓN
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:todo,personalizado',
            'estado' => 'nullable|in:activo,inactivo',
            'tipo_promocion' => 'nullable|in:porcentaje,monto_fijo,2x1,3x2',
            'vigencia' => 'nullable|in:vigente,vencida,futura',
        ]);

        try {
            // VERIFICACIÓN - Obtener datos
            $query = Promocion::with(['productos', 'categorias']);

            // Aplicar filtros
            if ($validated['tipo'] === 'personalizado') {
                if (!empty($validated['estado'])) {
                    $activo = $validated['estado'] === 'activo';
                    $query->where('activo', $activo);
                }

                if (!empty($validated['tipo_promocion'])) {
                    $query->where('tipo', $validated['tipo_promocion']);
                }

                if (!empty($validated['vigencia'])) {
                    $hoy = now();
                    switch ($validated['vigencia']) {
                        case 'vigente':
                            $query->where('activo', true)
                                ->where(function ($q) use ($hoy) {
                                    $q->whereNull('fecha_inicio')
                                        ->orWhere('fecha_inicio', '<=', $hoy);
                                })
                                ->where(function ($q) use ($hoy) {
                                    $q->whereNull('fecha_fin')
                                        ->orWhere('fecha_fin', '>=', $hoy);
                                });
                            break;
                        case 'vencida':
                            $query->where('fecha_fin', '<', $hoy);
                            break;
                        case 'futura':
                            $query->where('fecha_inicio', '>', $hoy);
                            break;
                    }
                }
            }

            $promociones = $query->orderBy('prioridad', 'desc')->get();

            // Estadísticas
            $hoy = now();
            $estadisticas = [
                'total_promociones' => $promociones->count(),
                'activas' => $promociones->where('activo', true)->count(),
                'inactivas' => $promociones->where('activo', false)->count(),
                'vigentes' => $promociones->filter(function ($promo) use ($hoy) {
                    if (!$promo->fecha_inicio || !$promo->fecha_fin) return false;
                    return $promo->fecha_inicio <= $hoy && $promo->fecha_fin >= $hoy && $promo->activo;
                })->count(),
                'vencidas' => $promociones->filter(function ($promo) use ($hoy) {
                    if (!$promo->fecha_fin) return false;
                    return $promo->fecha_fin < $hoy;
                })->count(),
            ];

            // Generar PDF
            $pdf = Pdf::loadView('reportes.exportar.promociones-pdf', compact(
                'promociones',
                'estadisticas',
                'validated'
            ));

            $pdf->setPaper('letter', 'landscape');

            $nombreArchivo = 'reporte-promociones-' . now()->format('Y-m-d-His') . '.pdf';



            // Registrar en bitácora
            BitacoraController::registrar('Exportar PDF', 'ReportePromociones');

            return $pdf->download($nombreArchivo);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Exportar reporte de promociones a Excel (CSV)
     */
    public function promocionesExcel(Request $request)
    {
        // VALIDACIÓN
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:todo,personalizado',
            'estado' => 'nullable|in:activo,inactivo',
            'tipo_promocion' => 'nullable|in:porcentaje,monto_fijo,2x1,3x2',
            'vigencia' => 'nullable|in:vigente,vencida,futura',
        ]);

        try {
            // VERIFICACIÓN - Obtener datos
            $query = Promocion::query();

            // Aplicar filtros
            if ($validated['tipo'] === 'personalizado') {
                if (!empty($validated['estado'])) {
                    $activo = $validated['estado'] === 'activo';
                    $query->where('activo', $activo);
                }

                if (!empty($validated['tipo_promocion'])) {
                    $query->where('tipo', $validated['tipo_promocion']);
                }

                if (!empty($validated['vigencia'])) {
                    $hoy = now();
                    switch ($validated['vigencia']) {
                        case 'vigente':
                            $query->where('activo', true)
                                ->where(function ($q) use ($hoy) {
                                    $q->whereNull('fecha_inicio')
                                        ->orWhere('fecha_inicio', '<=', $hoy);
                                })
                                ->where(function ($q) use ($hoy) {
                                    $q->whereNull('fecha_fin')
                                        ->orWhere('fecha_fin', '>=', $hoy);
                                });
                            break;
                        case 'vencida':
                            $query->where('fecha_fin', '<', $hoy);
                            break;
                        case 'futura':
                            $query->where('fecha_inicio', '>', $hoy);
                            break;
                    }
                }
            }

            $promociones = $query->orderBy('prioridad', 'desc')->get();

            $nombreArchivo = 'reporte-promociones-' . now()->format('Y-m-d-His') . '.csv';



            // Registrar en bitácora
            BitacoraController::registrar('Exportar Excel', 'ReportePromociones');

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
            ];

            $callback = function () use ($promociones) {
                $file = fopen('php://output', 'w');

                // BOM para UTF-8
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Encabezados
                fputcsv($file, [
                    'ID',
                    'Nombre',
                    'Tipo',
                    'Aplica Sobre',
                    'Valor',
                    'Tope Descuento',
                    'Fecha Inicio',
                    'Fecha Fin',
                    'Hora Inicio',
                    'Hora Fin',
                    'Días Semana',
                    'Prioridad',
                    'Activo',
                    'Vigente'
                ]);

                // Datos
                foreach ($promociones as $promo) {
                    fputcsv($file, [
                        $promo->id,
                        $promo->nombre,
                        ucfirst($promo->tipo),
                        ucfirst($promo->aplica_sobre),
                        number_format((float) $promo->valor, 2, '.', ''),
                        number_format((float) ($promo->tope_descuento ?? 0), 2, '.', ''),
                        $promo->fecha_inicio ? $promo->fecha_inicio->format('d/m/Y') : '',
                        $promo->fecha_fin ? $promo->fecha_fin->format('d/m/Y') : '',
                        $promo->hora_inicio ?? '',
                        $promo->hora_fin ?? '',
                        implode(', ', $promo->dias_semana ?? []),
                        $promo->prioridad,
                        $promo->activo ? 'Sí' : 'No',
                        $promo->esta_vigente ? 'Sí' : 'No'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }
}
