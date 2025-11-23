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
    /**
     * Run the database seeds.
     * Este seeder crea datos realistas para los últimos 60 días
     * para poder generar reportes completos y útiles.
     */
    public function run(): void
    {
        try {
            $this->command->info('🚀 Iniciando generación de datos para reportes...');

            // Obtener datos base
            $cajeros = User::role(['cajero', 'administrador'])->get();
            $clientes = User::role('cliente')->get();
            $mesas = Mesa::all();
            $productos = Producto::all();

            if ($cajeros->isEmpty() || $clientes->isEmpty() || $productos->isEmpty()) {
                $this->command->warn('⚠️  Faltan datos base (usuarios, productos). Ejecuta los seeders base primero.');
                return;
            }

            // Asegurarse de que tengamos al menos un cliente
            if ($clientes->isEmpty()) {
                $this->command->warn('⚠️  No hay clientes en la base de datos.');
                return;
            }

            $pedidosCreados = 0;
            $cobrosCreados = 0;
            $turnosCreados = 0;
            $cierresCreados = 0;

            // Generar datos para los últimos 60 días
            for ($dia = 60; $dia >= 0; $dia--) {
                $fecha = Carbon::now()->subDays($dia);

                // Solo generar datos para días pasados
                if ($fecha->isFuture()) {
                    continue;
                }

                // Generar entre 5 y 15 pedidos por día
                $numPedidos = rand(5, 15);

                for ($i = 0; $i < $numPedidos; $i++) {
                    $pedido = $this->crearPedidoAleatorio(
                        $fecha,
                        $cajeros,
                        $clientes,
                        $mesas,
                        $productos
                    );

                    if ($pedido) {
                        $pedidosCreados++;

                        // 80% de los pedidos tienen cobro
                        if (rand(1, 100) <= 80) {
                            $cobro = $this->crearCobroPedido($pedido, $cajeros, $fecha);
                            if ($cobro) {
                                $cobrosCreados++;
                            }
                        }
                    }
                }

                // Crear turnos y cierres de caja (2 turnos por día: mañana y tarde)
                if ($dia > 0 && $fecha->isPast()) {
                    // Turno de mañana
                    $turnoManana = $this->crearTurno($fecha, 'mañana', $cajeros);
                    if ($turnoManana) {
                        $turnosCreados++;
                        if (rand(1, 100) <= 90) { // 90% de los turnos tienen cierre
                            $cierre = $this->crearCierre($turnoManana, $fecha);
                            if ($cierre) {
                                $cierresCreados++;
                            }
                        }
                    }

                    // Turno de tarde
                    $turnoTarde = $this->crearTurno($fecha, 'tarde', $cajeros);
                    if ($turnoTarde) {
                        $turnosCreados++;
                        if (rand(1, 100) <= 90) {
                            $cierre = $this->crearCierre($turnoTarde, $fecha);
                            if ($cierre) {
                                $cierresCreados++;
                            }
                        }
                    }
                }
            }

            $this->command->info('✅ Datos de reportes generados exitosamente:');
            $this->command->info("   - Pedidos: $pedidosCreados");
            $this->command->info("   - Cobros: $cobrosCreados");
            $this->command->info("   - Turnos: $turnosCreados");
            $this->command->info("   - Cierres de caja: $cierresCreados");
        } catch (\Exception $e) {
            $this->command->error('Error al generar datos de reportes: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }

    /**
     * Crear un pedido aleatorio
     */
    private function crearPedidoAleatorio($fecha, $cajeros, $clientes, $mesas, $productos)
    {
        $tipos = ['mesa', 'mostrador', 'web'];
        $tipo = $tipos[array_rand($tipos)];
        
        // Estados válidos según la migración: pendiente, en_preparacion, preparado, cancelado, anulado
        $estados = ['pendiente', 'en_preparacion', 'preparado', 'cancelado', 'anulado'];
        
        // Peso para estados (más pedidos preparados que son los completados)
        $estadosPesados = ['preparado', 'preparado', 'preparado', 'preparado', 'preparado', 'preparado', 'pendiente'];
        $estado = $estadosPesados[array_rand($estadosPesados)];

        $datoPedido = [
            'tipo' => $tipo,
            'cliente_id' => $clientes->random()->id,
            'atendido_por' => $cajeros->random()->id,
            'estado' => $estado,
            'canal' => $tipo === 'web' ? 'web' : 'local',
            'created_at' => $fecha->copy()->setTime(rand(8, 22), rand(0, 59)),
            'updated_at' => $fecha->copy()->setTime(rand(8, 22), rand(0, 59)),
        ];

        if ($tipo === 'mesa' && $mesas->isNotEmpty()) {
            $datoPedido['mesa_id'] = $mesas->random()->id;
        }

        if ($tipo === 'web') {
            $modalidades = ['click_collect', 'delivery'];
            $datoPedido['modalidad'] = $modalidades[array_rand($modalidades)];

            if ($datoPedido['modalidad'] === 'delivery') {
                $direcciones = [
                    'Av. Libertador 1234, La Paz',
                    'Calle Potosí 567, Zona Sur',
                    'Av. Arce 890, Sopocachi',
                    'Calle Sucre 321, Centro',
                    'Av. 6 de Agosto 456, San Miguel',
                ];
                $datoPedido['direccion_entrega'] = $direcciones[array_rand($direcciones)];
            }
        }

        $pedido = Pedido::create($datoPedido);

        // Crear items del pedido (2-5 items)
        $numItems = rand(2, 5);
        $totalPedido = 0;

        for ($j = 0; $j < $numItems; $j++) {
            $producto = $productos->random();
            $cantidad = rand(1, 3);
            $precioUnitario = $producto->precio ?? rand(20, 80);
            $descuentoItem = rand(0, 1) === 1 ? rand(0, 10) : 0;
            $subtotal = ($precioUnitario * $cantidad) - $descuentoItem;
            $totalPedido += $subtotal;

            PedidoItem::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'descuento_item' => $descuentoItem,
                'subtotal_item' => $subtotal,
                'estado_item' => $estado === 'anulado' ? 'cancelado' : $estado,
                'destino' => rand(0, 1) === 1 ? 'cocina' : 'barra',
            ]);
        }

        // El total es un atributo calculado, no se guarda en la tabla pedidos
        // $pedido->update(['total' => $totalPedido]);

        return $pedido;
    }

    /**
     * Crear cobro para un pedido
     */
    private function crearCobroPedido($pedido, $cajeros, $fecha)
    {
        $metodos = ['efectivo', 'pos', 'qr'];
        $metodo = $metodos[array_rand($metodos)];
        
        // Peso hacia efectivo y POS (más comunes)
        $metodosPesados = ['efectivo', 'efectivo', 'pos', 'pos', 'pos', 'qr'];
        $metodo = $metodosPesados[array_rand($metodosPesados)];

        $estados = ['cobrado', 'cancelado'];
        // 95% cobrados, 5% cancelados
        $estado = rand(1, 100) <= 95 ? 'cobrado' : 'cancelado';

        return CobroCaja::create([
            'pedido_id' => $pedido->id,
            'importe' => $pedido->total,
            'metodo' => $metodo,
            'estado' => $estado,
            'comprobante' => 'CAJA-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'cajero_id' => $cajeros->random()->id,
            'created_at' => $fecha->copy()->addMinutes(rand(5, 30)),
        ]);
    }

    /**
     * Crear un turno de caja
     */
    private function crearTurno($fecha, $tipoTurno, $cajeros)
    {
        if ($tipoTurno === 'mañana') {
            $inicio = $fecha->copy()->setTime(8, 0);
            $fin = $fecha->copy()->setTime(14, 0);
        } else { // tarde
            $inicio = $fecha->copy()->setTime(14, 0);
            $fin = $fecha->copy()->setTime(20, 0);
        }

        try {
            return TurnoCaja::create([
                'cajero_id' => $cajeros->random()->id,
                'inicio' => $inicio,
                'fin' => $fin,
                'monto_inicial' => rand(50, 200),
                'estado' => 'cerrado',
                // 'created_at' y 'updated_at' no existen en la tabla turnos_caja
            ]);
        } catch (\Exception $e) {
            // Si ya existe un turno para este cajero en este horario, retornar null
            return null;
        }
    }

    /**
     * Crear cierre de caja
     */
    private function crearCierre($turno, $fecha)
    {
        // Calcular montos
        $totalSistema = rand(500, 2000);
        $totalDeclarado = $totalSistema + rand(-50, 50); // Puede haber diferencias
        $diferencia = $totalDeclarado - $totalSistema;

        // 'tipo_diferencia' y 'duracion_turno' no existen en la tabla cierres_caja
        // 'created_at' no existe en la tabla cierres_caja

        $cierre = CierreCaja::create([
            'turno_id' => $turno->id,
            'cajero_id' => $turno->cajero_id,
            'inicio' => $turno->inicio,
            'fin' => $turno->fin,
            'total_sistema' => $totalSistema,
            'total_declarado' => $totalDeclarado,
            'diferencia' => $diferencia,
            'observaciones' => $diferencia != 0 
                ? "Diferencia de Bs. " . abs($diferencia) 
                : "Cierre cuadrado sin diferencias",
        ]);

        // Crear detalles del cierre
        $this->crearDetallesCierre($cierre, $totalDeclarado);

        return $cierre;
    }

    /**
     * Crear detalles de cierre (efectivo, POS, QR)
     */
    private function crearDetallesCierre($cierre, $totalDeclarado)
    {
        // Distribuir el total entre los diferentes métodos de pago
        $efectivo = rand(100, $totalDeclarado * 0.4);
        $pos = rand(100, $totalDeclarado * 0.4);
        $qr = $totalDeclarado - $efectivo - $pos;

        if ($qr < 0) {
            $qr = 0;
            $efectivo = $totalDeclarado * 0.6;
            $pos = $totalDeclarado * 0.4;
        }

        CierreCajaDetalle::create([
            'cierre_caja_id' => $cierre->id,
            'metodo' => 'efectivo', // Corregido de metodo_pago a metodo
            'monto_sistema' => $efectivo + rand(-20, 20),
            'monto_declarado' => $efectivo,
        ]);

        CierreCajaDetalle::create([
            'cierre_caja_id' => $cierre->id,
            'metodo' => 'pos', // Corregido de metodo_pago a metodo
            'monto_sistema' => $pos + rand(-10, 10),
            'monto_declarado' => $pos,
        ]);

        if ($qr > 0) {
            CierreCajaDetalle::create([
                'cierre_caja_id' => $cierre->id,
                'metodo' => 'qr', // Corregido de metodo_pago a metodo
                'monto_sistema' => $qr + rand(-5, 5),
                'monto_declarado' => $qr,
            ]);
        }
    }
}
