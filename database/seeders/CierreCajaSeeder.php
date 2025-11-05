<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CierreCaja;
use App\Models\CierreCajaDetalle;
use App\Models\TurnoCaja;
use App\Models\CobroCaja;
use Carbon\Carbon;

class CierreCajaSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener turnos cerrados
        $turnosCerrados = TurnoCaja::where('estado', 'cerrado')->get();

        if ($turnosCerrados->isEmpty()) {
            $this->command->warn('No hay turnos cerrados. Ejecuta TurnoCajaSeeder primero.');
            return;
        }

        $cierresCreados = 0;

        foreach ($turnosCerrados as $turno) {
            // Obtener cobros del turno
            $cobros = CobroCaja::where('cajero_id', $turno->cajero_id)
                ->whereBetween('created_at', [$turno->inicio, $turno->fin])
                ->get();

            if ($cobros->isEmpty()) {
                continue; // Si no hay cobros, no crear cierre
            }

            // Calcular totales del sistema
            $totalEfectivo = $cobros->where('metodo', 'efectivo')->sum('importe');
            $totalPos = $cobros->where('metodo', 'pos')->sum('importe');
            $totalQr = $cobros->where('metodo', 'qr')->sum('importe');
            $totalSistema = $totalEfectivo + $totalPos + $totalQr;

            // Simular arqueo (con pequeñas diferencias aleatorias)
            $factorDiferencia = rand(-300, 300) / 100; // Diferencia entre -3 y +3 Bs
            $efectivoDeclarado = $totalEfectivo + ($factorDiferencia * 0.7);
            $posDeclarado = $totalPos + ($factorDiferencia * 0.2);
            $qrDeclarado = $totalQr + ($factorDiferencia * 0.1);
            $totalDeclarado = $efectivoDeclarado + $posDeclarado + $qrDeclarado;

            // 70% de cierres cuadrados
            if (rand(0, 10) > 3) {
                $efectivoDeclarado = $totalEfectivo;
                $posDeclarado = $totalPos;
                $qrDeclarado = $totalQr;
                $totalDeclarado = $totalSistema;
            }

            // Crear cierre de caja
            $cierre = CierreCaja::create([
                'turno_caja_id' => $turno->id,
                'cajero_id' => $turno->cajero_id,
                'inicio' => $turno->inicio,
                'fin' => $turno->fin,
                'monto_inicial' => $turno->monto_inicial,
                'total_efectivo_sistema' => $totalEfectivo,
                'total_pos_sistema' => $totalPos,
                'total_qr_sistema' => $totalQr,
                'total_sistema' => $totalSistema,
                'total_efectivo_declarado' => round($efectivoDeclarado, 2),
                'total_pos_declarado' => round($posDeclarado, 2),
                'total_qr_declarado' => round($qrDeclarado, 2),
                'total_declarado' => round($totalDeclarado, 2),
                'diferencia' => round($totalDeclarado - $totalSistema, 2),
                'observaciones' => $this->generarObservacion($totalDeclarado - $totalSistema),
            ]);

            // Crear detalles del cierre (denominaciones de billetes y monedas)
            $this->crearDetalles($cierre, $efectivoDeclarado);

            $cierresCreados++;
        }

        $this->command->info('✅ Cierres de caja creados: ' . $cierresCreados);
    }

    private function crearDetalles(CierreCaja $cierre, float $efectivoTotal): void
    {
        $denominaciones = [
            200 => 'Billete 200 Bs',
            100 => 'Billete 100 Bs',
            50 => 'Billete 50 Bs',
            20 => 'Billete 20 Bs',
            10 => 'Billete 10 Bs',
            5 => 'Moneda 5 Bs',
            2 => 'Moneda 2 Bs',
            1 => 'Moneda 1 Bs',
            0.50 => 'Moneda 0.50 Bs',
        ];

        $montoRestante = $efectivoTotal;
        $detalles = [];

        foreach ($denominaciones as $valor => $descripcion) {
            if ($montoRestante <= 0) break;

            $cantidad = (int)($montoRestante / $valor);
            
            if ($cantidad > 0) {
                // Agregar algo de variación realista
                $cantidad = max(0, $cantidad + rand(-2, 2));
                
                if ($cantidad > 0) {
                    $subtotal = $cantidad * $valor;
                    
                    $detalles[] = [
                        'cierre_caja_id' => $cierre->id,
                        'denominacion' => $valor,
                        'cantidad' => $cantidad,
                        'subtotal' => round($subtotal, 2),
                        'tipo' => $valor >= 10 ? 'billete' : 'moneda',
                    ];
                    
                    $montoRestante -= $subtotal;
                }
            }
        }

        if (!empty($detalles)) {
            CierreCajaDetalle::insert($detalles);
        }
    }

    private function generarObservacion(float $diferencia): ?string
    {
        $absDiferencia = abs($diferencia);

        if ($absDiferencia < 0.01) {
            return rand(0, 1) ? 'Caja cuadrada perfectamente.' : null;
        }

        if ($diferencia < 0) {
            $observaciones = [
                'Faltante en efectivo, posible error en conteo.',
                'Diferencia negativa, revisar transacciones del día.',
                'Faltante menor, probablemente vuelto mal dado.',
                'Se verificó dos veces, faltante confirmado.',
            ];
        } else {
            $observaciones = [
                'Sobrante en efectivo, revisar registro de ventas.',
                'Diferencia positiva menor.',
                'Posible error de ingreso en sistema.',
                'Cliente dejó vuelto, sobrante registrado.',
            ];
        }

        return $observaciones[array_rand($observaciones)];
    }
}
