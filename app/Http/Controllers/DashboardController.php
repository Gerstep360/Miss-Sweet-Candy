<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Support\BusinessHours;
use App\Models\CobroCaja;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\EspecialDelDia;
use App\Models\User;
use App\Models\Mesa;

class DashboardController extends Controller
{
    public function index(Request $request, BusinessHours $hours)
    {
        $user = auth()->user();
        
        // Detectar el rol y redirigir al dashboard correspondiente
        if ($user->hasRole('administrador')) {
            return $this->adminDashboard($hours);
        } elseif ($user->hasRole('cajero')) {
            return $this->cajeroDashboard($hours);
        } elseif ($user->hasRole('cliente')) {
            return $this->clienteDashboard($hours);
        }
        
        // Dashboard por defecto
        return $this->defaultDashboard($hours);
    }

    /**
     * Dashboard para Administrador
     */
    private function adminDashboard(BusinessHours $hours)
    {
        $tz  = 'America/La_Paz';
        $now = Carbon::now($tz);

        $start = $now->copy()->startOfDay();
        $end   = $now->copy()->endOfDay();

        // ========== MÉTRICAS GENERALES ==========
        $ventasHoy = CobroCaja::cobrados()
            ->whereBetween('created_at', [$start, $end])
            ->sum('importe');

        $ordenesHoy = Pedido::whereBetween('created_at', [$start, $end])->count();

        $clientesUnicos = Pedido::whereBetween('created_at', [$start, $end])
            ->whereNotNull('cliente_id')
            ->distinct()
            ->count('cliente_id');

        $lowStock = 0;
        if (Schema::hasColumn((new Producto)->getTable(), 'stock')) {
            $lowStock = Producto::where('stock', '<=', DB::raw('umbral_minimo'))->count();
        }

        // ========== VENTAS POR MES (últimos 12 meses) ==========
        $ventasPorMes = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes = $now->copy()->subMonths($i);
            $totalMes = CobroCaja::cobrados()
                ->whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)
                ->sum('importe');
            
            $ventasPorMes[] = [
                'mes' => $mes->format('M Y'),
                'total' => (float) $totalMes
            ];
        }

        // ========== VENTAS POR SEMANA ==========
        $ventasSemana = [];
        $dias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        
        for ($i = 0; $i < 7; $i++) {
            $fecha = $now->copy()->startOfWeek()->addDays($i);
            $totalDia = CobroCaja::cobrados()
                ->whereDate('created_at', $fecha->toDateString())
                ->sum('importe');
            
            $ordenesDia = Pedido::whereDate('created_at', $fecha->toDateString())->count();
            
            $ventasSemana[] = [
                'dia' => $dias[$i],
                'fecha' => $fecha->format('Y-m-d'),
                'ventas' => (float) $totalDia,
                'ordenes' => $ordenesDia,
                'esHoy' => $fecha->isToday()
            ];
        }

        // ========== PRODUCTOS MÁS VENDIDOS (Top 10) ==========
        $topProductos = DB::table('pedido_items')
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->whereBetween('pedidos.created_at', [$start, $end])
            ->select(
                'productos.nombre',
                DB::raw('SUM(pedido_items.cantidad) as total_vendido'),
                DB::raw('SUM(pedido_items.subtotal_item) as ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        // ========== VENTAS POR MÉTODO DE PAGO ==========
        $ventasPorMetodo = CobroCaja::cobrados()
            ->whereBetween('created_at', [$start, $end])
            ->select('metodo', DB::raw('SUM(importe) as total'), DB::raw('COUNT(*) as cantidad'))
            ->groupBy('metodo')
            ->get()
            ->map(function($item) {
                return [
                    'metodo' => match($item->metodo) {
                        'efectivo' => 'Efectivo',
                        'pos' => 'Tarjeta',
                        'qr' => 'QR/Transfer',
                        default => 'Otro'
                    },
                    'total' => (float) $item->total,
                    'cantidad' => $item->cantidad
                ];
            });

        // ========== ÓRDENES RECIENTES ==========
        $ordenesRecientes = Pedido::with(['cliente', 'mesa', 'items'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($pedido) {
                return [
                    'id' => $pedido->id,
                    'tipo' => $pedido->tipo_nombre,
                    'cliente' => $pedido->cliente->name ?? 'Sin cliente',
                    'mesa' => $pedido->mesa->nombre ?? '-',
                    'estado' => $pedido->estado_nombre,
                    'total' => $pedido->total,
                    'fecha' => $pedido->created_at->format('d/m/Y H:i'),
                    'items_count' => $pedido->items->count()
                ];
            });

        // ========== EMPLEADOS MÁS ACTIVOS ==========
        $empleadosActivos = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['cajero', 'mesero']);
            })
            ->withCount(['cobrosRealizados' => function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }])
            ->orderByDesc('cobros_realizados_count')
            ->limit(5)
            ->get();

        // ========== ESPECIAL DEL DÍA ==========
        $especialHoy = EspecialDelDia::getEspecialHoy();

        // ========== ESTADO DE MESAS ==========
        $estadoMesas = [
            'libres' => Mesa::where('estado', 'libre')->count(),
            'ocupadas' => Mesa::where('estado', 'ocupada')->count(),
            'reservadas' => Mesa::where('estado', 'reservada')->count(),
            'total' => Mesa::count()
        ];

        return view('dashboard.admin', compact(
            'now',
            'hours',
            'ventasHoy',
            'ordenesHoy',
            'clientesUnicos',
            'lowStock',
            'ventasPorMes',
            'ventasSemana',
            'topProductos',
            'ventasPorMetodo',
            'ordenesRecientes',
            'empleadosActivos',
            'especialHoy',
            'estadoMesas'
        ));
    }

    /**
     * Dashboard para Cajero
     */
    private function cajeroDashboard(BusinessHours $hours)
    {
        $tz  = 'America/La_Paz';
        $now = Carbon::now($tz);
        $start = $now->copy()->startOfDay();
        $end   = $now->copy()->endOfDay();

        $cajeroId = auth()->id();

        // ========== MÉTRICAS DEL CAJERO ==========
        $misCobrosHoy = CobroCaja::cobrados()
            ->deCajero($cajeroId)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $misVentasHoy = CobroCaja::cobrados()
            ->deCajero($cajeroId)
            ->whereBetween('created_at', [$start, $end])
            ->sum('importe');

        $efectivoRecaudado = CobroCaja::cobrados()
            ->deCajero($cajeroId)
            ->porMetodo('efectivo')
            ->whereBetween('created_at', [$start, $end])
            ->sum('importe');

        $tarjetasRecaudadas = CobroCaja::cobrados()
            ->deCajero($cajeroId)
            ->porMetodo('pos')
            ->whereBetween('created_at', [$start, $end])
            ->sum('importe');

        // ========== COBROS RECIENTES DEL CAJERO ==========
        $misCobros = CobroCaja::with(['pedido.cliente', 'pedido.mesa'])
            ->deCajero($cajeroId)
            ->latest()
            ->limit(15)
            ->get()
            ->map(function($cobro) {
                return [
                    'id' => $cobro->id,
                    'pedido_id' => $cobro->pedido_id,
                    'cliente' => $cobro->pedido->cliente->name ?? 'Sin cliente',
                    'mesa' => $cobro->pedido->mesa->nombre ?? 'Mostrador',
                    'importe' => $cobro->importe,
                    'metodo' => $cobro->nombre_metodo,
                    'estado' => $cobro->estado,
                    'fecha' => $cobro->created_at->format('H:i:s'),
                    'comprobante' => $cobro->comprobante
                ];
            });

        // ========== PEDIDOS PENDIENTES DE COBRO ==========
        $pedidosPendientes = Pedido::whereIn('estado', ['preparado', 'entregado'])
            ->with(['cliente', 'mesa', 'items'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($pedido) {
                return [
                    'id' => $pedido->id,
                    'tipo' => $pedido->tipo_nombre,
                    'cliente' => $pedido->cliente->name ?? 'Sin cliente',
                    'mesa' => $pedido->mesa->nombre ?? '-',
                    'total' => $pedido->total,
                    'items_count' => $pedido->items->count()
                ];
            });

        // ========== VENTAS POR MÉTODO (del cajero) ==========
        $misVentasPorMetodo = CobroCaja::cobrados()
            ->deCajero($cajeroId)
            ->whereBetween('created_at', [$start, $end])
            ->select('metodo', DB::raw('SUM(importe) as total'), DB::raw('COUNT(*) as cantidad'))
            ->groupBy('metodo')
            ->get();

        // ========== VENTAS POR HORA (del cajero) ==========
        $ventasPorHora = CobroCaja::cobrados()
            ->deCajero($cajeroId)
            ->whereBetween('created_at', [$start, $end])
            ->select(
                DB::raw('HOUR(created_at) as hora'),
                DB::raw('SUM(importe) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->groupBy('hora')
            ->orderBy('hora')
            ->get();

        // ========== ESPECIAL DEL DÍA ==========
        $especialHoy = EspecialDelDia::getEspecialHoy();

        return view('dashboard.cajero', compact(
            'now',
            'hours',
            'misCobrosHoy',
            'misVentasHoy',
            'efectivoRecaudado',
            'tarjetasRecaudadas',
            'misCobros',
            'pedidosPendientes',
            'misVentasPorMetodo',
            'ventasPorHora',
            'especialHoy'
        ));
    }

    /**
     * Dashboard para Cliente
     */
    private function clienteDashboard(BusinessHours $hours)
    {
        $tz  = 'America/La_Paz';
        $now = Carbon::now($tz);
        $clienteId = auth()->id();

        // ========== MIS PEDIDOS ==========
        $misPedidos = Pedido::where('cliente_id', $clienteId)
            ->with(['items.producto', 'mesa'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($pedido) {
                return [
                    'id' => $pedido->id,
                    'tipo' => $pedido->tipo_nombre,
                    'mesa' => $pedido->mesa->nombre ?? '-',
                    'estado' => $pedido->estado_nombre,
                    'total' => $pedido->total,
                    'items' => $pedido->items->map(function($item) {
                        return [
                            'producto' => $item->producto->nombre,
                            'cantidad' => $item->cantidad,
                            'precio' => $item->precio_unitario,
                            'subtotal' => $item->subtotal_item
                        ];
                    }),
                    'fecha' => $pedido->created_at->format('d/m/Y H:i'),
                    'puede_cancelar' => in_array($pedido->estado, ['pendiente', 'en_preparacion'])
                ];
            });

        // ========== ESTADÍSTICAS DEL CLIENTE ==========
        $totalGastado = Pedido::where('cliente_id', $clienteId)
            ->where('estado', 'pagado')
            ->with('items')
            ->get()
            ->sum(function($pedido) {
                return $pedido->total;
            });

        $totalPedidos = Pedido::where('cliente_id', $clienteId)->count();

        $productoFavorito = DB::table('pedido_items')
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
            ->where('pedidos.cliente_id', $clienteId)
            ->select('productos.nombre', DB::raw('COUNT(*) as veces_pedido'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('veces_pedido')
            ->first();

        $ultimaVisita = Pedido::where('cliente_id', $clienteId)
            ->latest()
            ->first()
            ->created_at ?? null;

        // ========== PEDIDOS ACTIVOS ==========
        $pedidosActivos = Pedido::where('cliente_id', $clienteId)
            ->whereIn('estado', ['pendiente', 'en_preparacion', 'preparado'])
            ->with(['items.producto', 'mesa'])
            ->get();

        // ========== ESPECIAL DEL DÍA ==========
        $especialHoy = EspecialDelDia::getEspecialHoy();

        // ========== PRODUCTOS POPULARES ==========
        $productosPopulares = Producto::withCount('pedidoItems')
            ->orderByDesc('pedido_items_count')
            ->limit(6)
            ->get();

        return view('dashboard.cliente', compact(
            'now',
            'hours',
            'misPedidos',
            'totalGastado',
            'totalPedidos',
            'productoFavorito',
            'ultimaVisita',
            'pedidosActivos',
            'especialHoy',
            'productosPopulares'
        ));
    }

    /**
     * Dashboard por defecto (fallback)
     */
    private function defaultDashboard(BusinessHours $hours)
    {
        $now = Carbon::now('America/La_Paz');
        $especialHoy = EspecialDelDia::getEspecialHoy();

        return view('dashboard.default', compact('now', 'hours', 'especialHoy'));
    }

    /**
     * Cambiar especial del día
     */
    public function setSpecialProduct(Request $request, $productId)
    {
        $producto = Producto::findOrFail($productId);
        
        $hoy = Carbon::now('America/La_Paz');
        $dias = [
            0 => 'domingo', 1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado'
        ];
        $diaSemana = $dias[$hoy->dayOfWeek];

        EspecialDelDia::where('dia_semana', $diaSemana)
            ->where('activo', true)
            ->update(['activo' => false]);

        EspecialDelDia::create([
            'producto_id' => $productId,
            'dia_semana' => $diaSemana,
            'descripcion_especial' => 'Especial seleccionado desde el dashboard',
            'activo' => true,
            'prioridad' => 10
        ]);
        
        return response()->json([
            'success' => true, 
            'message' => "Especial actualizado a: {$producto->nombre}"
        ]);
    }
}