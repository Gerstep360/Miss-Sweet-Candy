<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\CobroCaja;
use App\Models\CierreCaja;
use App\Models\CierreCajaDetalle;
use App\Models\TurnoCaja;
use App\Models\User;
use App\Models\Mesa;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportesDataSeeder extends Seeder
{
    // CONFIGURACIÓN "LITE" PARA AHORRAR ESPACIO
    protected $diasParaGenerar = 15; // Antes 60. 15 es suficiente para pruebas.
    protected $minPedidosDia = 1;
    protected $maxPedidosDia = 3;    // Mantenemos bajo volumen diario.

    public function run(): void
    {
        // Prevenir ejecución en producción para no ensuciar la DB
        if (app()->environment('production')) {
            $this->command->warn('⚠️  Este seeder no debe correrse en producción.');
            return;
        }

        DB::transaction(function () {
            try {
                $this->command->info("🚀 Generando datos 'LITE' para los últimos {$this->diasParaGenerar} días...");

                // Cargar datos en memoria una sola vez
                $cajeros = User::role(['cajero', 'administrador'])->get();
                $clientes = User::role('cliente')->get();
                $mesas = Mesa::all();
                $productos = Producto::all();

                if ($cajeros->isEmpty() || $clientes->isEmpty() || $productos->isEmpty()) {
                    $this->command->warn('⚠️  Faltan datos base (usuarios/productos).');
                    return;
                }

                $stats = [
                    'pedidos' => 0,
                    'cobros' => 0,
                    'turnos' => 0,
                    'cierres' => 0
                ];

                // Recorrer días
                for ($dia = $this->diasParaGenerar; $dia >= 0; $dia--) {
                    $fecha = Carbon::now()->subDays($dia);

                    // 1. GENERAR PEDIDOS (Pocos por día)
                    $numPedidos = rand($this->minPedidosDia, $this->maxPedidosDia);

                    for ($i = 0; $i < $numPedidos; $i++) {
                        $pedido = $this->crearPedidoAleatorio($fecha, $cajeros, $clientes, $mesas, $productos);
                        
                        if ($pedido) {
                            $stats['pedidos']++;
                            // Solo el 60% tiene cobro para ahorrar espacio en tabla cobros
                            if (rand(1, 100) <= 60) {
                                $cobro = $this->crearCobroPedido($pedido, $cajeros, $fecha);
                                if ($cobro) $stats['cobros']++;
                            }
                        }
                    }

                    // 2. GENERAR TURNOS (Solo si hubo actividad ese día)
                    // Reducimos a 1 turno por día aleatorio (mañana O tarde) para ahorrar espacio
                    if ($dia > 0) {
                        $tipoTurno = rand(0, 1) ? 'mañana' : 'tarde';
                        $turno = $this->crearTurno($fecha, $tipoTurno, $cajeros);
                        
                        if ($turno) {
                            $stats['turnos']++;
                            // Solo generamos cierre si el turno se creó
                            $cierre = $this->crearCierre($turno, $fecha);
                            if ($cierre) $stats['cierres']++;
                        }
                    }
                }

                $this->command->info('✅ Datos generados (Modo Ahorro):');
                $this->command->table(['Entidad', 'Cantidad'], [
                    ['Pedidos', $stats['pedidos']],
                    ['Cobros', $stats['cobros']],
                    ['Turnos', $stats['turnos']],
                    ['Cierres', $stats['cierres']],
                ]);

            } catch (\Exception $e) {
                $this->command->error('Error: ' . $e->getMessage());
                throw $e; // Revertir transacción
            }
        });
    }

    private function crearPedidoAleatorio($fecha, $cajeros, $clientes, $mesas, $productos)
    {
        $tipo = ['mesa', 'mostrador', 'web'][rand(0, 2)];
        $estado = ['preparado', 'preparado', 'pendiente'][rand(0, 2)]; // Mayor probabilidad de completado

        $datoPedido = [
            'tipo' => $tipo,
            'cliente_id' => $clientes->random()->id,
            'atendido_por' => $cajeros->random()->id,
            'estado' => $estado,
            'canal' => $tipo === 'web' ? 'web' : 'local',
            'created_at' => $fecha->copy()->setTime(rand(9, 21), rand(0, 59)),
            'updated_at' => $fecha->copy()->setTime(rand(9, 21), rand(0, 59)),
        ];

        if ($tipo === 'mesa' && $mesas->isNotEmpty()) {
            $datoPedido['mesa_id'] = $mesas->random()->id;
        }

        // Simplificamos la lógica web para ahorrar espacio en DB (strings más cortos)
        if ($tipo === 'web') {
            $datoPedido['modalidad'] = rand(0, 1) ? 'click_collect' : 'delivery';
            if ($datoPedido['modalidad'] === 'delivery') {
                $datoPedido['direccion_entrega'] = 'Dirección Genérica #' . rand(1, 100);
            }
        }

        $pedido = Pedido::create($datoPedido);

        // MENOS ITEMS por pedido (1 a 3) para ahorrar filas en pedido_items
        $numItems = rand(1, 3);
        $totalPedido = 0;

        $itemsData = [];
        for ($j = 0; $j < $numItems; $j++) {
            $producto = $productos->random();
            $cantidad = rand(1, 2);
            $precio = $producto->precio ?? rand(20, 50);
            $subtotal = $precio * $cantidad;
            
            $itemsData[] = [
                'pedido_id' => $pedido->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'descuento_item' => 0,
                'subtotal_item' => $subtotal,
                'estado_item' => $estado === 'anulado' ? 'cancelado' : $estado,
                'destino' => rand(0, 1) ? 'cocina' : 'barra',
                'created_at' => $datoPedido['created_at'],
                'updated_at' => $datoPedido['updated_at'],
            ];
        }
        
        // Insertar items en lote (Bulk Insert) es mucho más rápido y ligero
        PedidoItem::insert($itemsData);

        return $pedido;
    }

    private function crearCobroPedido($pedido, $cajeros, $fecha)
    {
        // Cálculo simplificado del total (ya que no actualizamos el pedido padre en el paso anterior)
        $totalEstimado = PedidoItem::where('pedido_id', $pedido->id)->sum('subtotal_item');

        return CobroCaja::create([
            'pedido_id' => $pedido->id,
            'importe' => $totalEstimado,
            'metodo' => ['efectivo', 'pos', 'qr'][rand(0, 2)],
            'estado' => 'cobrado',
            'comprobante' => 'T-' . rand(1000, 9999),
            'cajero_id' => $cajeros->random()->id,
            'created_at' => $fecha->copy()->addMinutes(rand(5, 30)),
        ]);
    }

    private function crearTurno($fecha, $tipoTurno, $cajeros)
    {
        $inicio = $tipoTurno === 'mañana' 
            ? $fecha->copy()->setTime(8, 0) 
            : $fecha->copy()->setTime(14, 0);
            
        $fin = $inicio->copy()->addHours(6);

        // Usar firstOrCreate para evitar errores de duplicados y ahorrar lógica
        return TurnoCaja::firstOrCreate([
            'cajero_id' => $cajeros->random()->id,
            'inicio' => $inicio,
        ], [
            'fin' => $fin,
            'monto_inicial' => 100, // Monto fijo para ahorrar random()
            'estado' => 'cerrado',
        ]);
    }

    private function crearCierre($turno, $fecha)
    {
        // Verificar si ya existe cierre para no duplicar
        if (CierreCaja::where('turno_id', $turno->id)->exists()) return null;

        $total = rand(500, 1500);
        
        $cierre = CierreCaja::create([
            'turno_id' => $turno->id,
            'cajero_id' => $turno->cajero_id,
            'inicio' => $turno->inicio,
            'fin' => $turno->fin,
            'total_sistema' => $total,
            'total_declarado' => $total, // Cuadrado perfecto para ahorrar texto en observaciones
            'diferencia' => 0,
            'observaciones' => null,
        ]);

        // Solo creamos detalle de Efectivo para ahorrar espacio en DB
        CierreCajaDetalle::create([
            'cierre_caja_id' => $cierre->id,
            'metodo' => 'efectivo',
            'monto_sistema' => $total,
            'monto_declarado' => $total,
        ]);

        return $cierre;
    }
}