<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Support\BusinessHours;
use App\Models\CobroCaja;
use App\Models\PedidoMesa;
use App\Models\PedidoMostrador;
use App\Models\Producto;
use App\Models\EspecialDelDia;

class DashboardController extends Controller
{
    public function index(Request $request, BusinessHours $hours)
    {
        $tz  = 'America/La_Paz';
        $now = Carbon::now($tz);

        $start = $now->copy()->startOfDay()->timezone('UTC');
        $end   = $now->copy()->endOfDay()->timezone('UTC');

        // Métricas KPI 
        $sales = (float) CobroCaja::where('estado', 'cancelado')
            ->whereBetween('created_at', [$start, $end])
            ->sum('importe');

        $ordersToday = PedidoMesa::whereBetween('created_at', [$start, $end])->count() +
                      PedidoMostrador::whereBetween('created_at', [$start, $end])->count();

        $clients = PedidoMesa::whereBetween('created_at', [$start, $end])
                       ->whereNotNull('cliente_id')->distinct()->count('cliente_id') +
                   PedidoMostrador::whereBetween('created_at', [$start, $end])
                       ->whereNotNull('cliente_id')->distinct()->count('cliente_id');

        $lowStock = 0;
        if (Schema::hasColumn((new Producto)->getTable(), 'stock')) {
            $lowStock = Producto::where('stock', '<=', DB::raw('umbral_minimo'))->count();
        }

        // Órdenes recientes
        $orders = CobroCaja::with(['pedidoMostrador', 'pedidoMesa.mesa'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($c) {
                $isMesa = !is_null($c->pedido_mesa_id);
                $title  = $isMesa
                    ? 'Mesa '.optional(optional($c->pedidoMesa)->mesa)->nombre
                    : 'Mostrador';
                return (object)[
                    'id'         => $c->id,
                    'type'       => $isMesa ? 'mesa' : 'takeaway',
                    'title'      => trim($title) ?: 'Orden',
                    'total'      => (float)$c->importe,
                    'created_at' => $c->created_at,
                ];
            });

        // Especial del día
        $especialHoy = EspecialDelDia::getEspecialHoy();

        // Ventas de la semana
        $ventasPorDia = [];
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        
        for ($i = 0; $i < 7; $i++) {
            $fecha = $now->copy()->startOfWeek()->addDays($i);
            $nombreDia = $dias[$i];
            
            $ventasDelDia = CobroCaja::where('estado', 'cancelado')
                ->whereDate('created_at', $fecha->toDateString())
                ->sum('importe');
            
            $ordenesDelDia = CobroCaja::where('estado', 'cancelado')
                ->whereDate('created_at', $fecha->toDateString())
                ->count();
            
            $ventasPorDia[] = [
                'day' => ucfirst($nombreDia),
                'sales' => (float) $ventasDelDia,
                'orders' => $ordenesDelDia,
                'fecha' => $fecha->format('Y-m-d')
            ];
        }

        return view('dashboard', [
            'today'   => $now->format('d/m/Y'),
            'time'    => $now->format('H:i'),
            'isOpen'  => $hours->isOpenAt($now),
            'metrics' => [
                'sales'    => $sales,
                'orders'   => $ordersToday,
                'clients'  => $clients,
                'lowStock' => $lowStock,
            ],
            'orders'       => $orders,
            'especialHoy'  => $especialHoy,
            'ventasSemana' => $ventasPorDia,
        ]);
    }

    public function setSpecialProduct(Request $request, $productId)
    {
        // Validar que el producto existe
        $producto = Producto::findOrFail($productId);
        
        $hoy = Carbon::now('America/La_Paz');
        $dias = [
            0 => 'domingo', 1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado'
        ];
        $diaSemana = $dias[$hoy->dayOfWeek];

        // Desactivar especial actual del día
        EspecialDelDia::where('dia_semana', $diaSemana)
            ->where('activo', true)
            ->update(['activo' => false]);

        // Crear nuevo especial para hoy
        EspecialDelDia::create([
            'producto_id' => $productId,
            'dia_semana' => $diaSemana,
            'descripcion_especial' => 'Especial seleccionado desde el dashboard para ' . ucfirst($diaSemana),
            'activo' => true,
            'prioridad' => 10 // Máxima prioridad para especiales manuales
        ]);
        
        return response()->json([
            'success' => true, 
            'message' => "Especial del día actualizado a: {$producto->nombre}"
        ]);
    }

    public function ordersFragment(Request $request, BusinessHours $hours)
    {
        $response = $this->index($request, $hours);
        return $response->getOriginalContent()->fragment('orders');
    }
}