<?php

namespace App\Services;

use App\Models\Promocion;
use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PromocionService
{
    /**
     * Obtener promociones activas y vigentes
     */
    public function getPromocionesActivas(): Collection
    {
        return Promocion::where('activo', true)
            ->orderBy('prioridad', 'desc')
            ->get()
            ->filter(function($promocion) {
                return $this->esVigente($promocion);
            });
    }

    /**
     * Verificar si una promoción está vigente en este momento
     */
    public function esVigente(Promocion $promocion): bool
    {
        $ahora = Carbon::now('America/La_Paz');
        
        // Verificar fechas
        if ($promocion->fecha_inicio && $ahora->lt($promocion->fecha_inicio)) {
            return false;
        }
        
        if ($promocion->fecha_fin && $ahora->gt($promocion->fecha_fin->endOfDay())) {
            return false;
        }

        // Verificar horarios
        if ($promocion->hora_inicio && $promocion->hora_fin) {
            $horaActual = $ahora->format('H:i:s');
            if ($horaActual < $promocion->hora_inicio || $horaActual > $promocion->hora_fin) {
                return false;
            }
        }

        // Verificar día de la semana
        if (!empty($promocion->dias_semana) && count($promocion->dias_semana) > 0) {
            $diasMap = [
                'Mon' => 'lun', 'Tue' => 'mar', 'Wed' => 'mie', 
                'Thu' => 'jue', 'Fri' => 'vie', 'Sat' => 'sab', 'Sun' => 'dom'
            ];
            $diaActual = $diasMap[$ahora->format('D')] ?? '';
            
            if (!in_array($diaActual, $promocion->dias_semana)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Aplicar promociones a un pedido
     */
    public function aplicarPromociones(Pedido $pedido): array
    {
        $promocionesAplicadas = [];
        $descuentoTotal = 0;

        $promocionesActivas = $this->getPromocionesActivas();

        foreach ($promocionesActivas as $promocion) {
            $descuento = $this->calcularDescuento($promocion, $pedido);
            
            if ($descuento > 0) {
                $promocionesAplicadas[] = [
                    'promocion_id' => $promocion->id,
                    'nombre' => $promocion->nombre,
                    'tipo' => $promocion->tipo,
                    'descuento' => $descuento
                ];
                
                $descuentoTotal += $descuento;
            }
        }

        return [
            'promociones' => $promocionesAplicadas,
            'descuento_total' => $descuentoTotal
        ];
    }

    /**
     * Calcular descuento de una promoción específica
     */
    private function calcularDescuento(Promocion $promocion, Pedido $pedido): float
    {
        $descuento = 0;

        if ($promocion->aplica_sobre === 'pedido') {
            // Descuento sobre el total del pedido
            $subtotal = $pedido->items->sum('subtotal_item');
            
            if ($promocion->tipo === 'porcentaje') {
                $descuento = ($subtotal * $promocion->valor) / 100;
            } elseif ($promocion->tipo === 'monto_fijo') {
                $descuento = min($promocion->valor, $subtotal);
            }

        } elseif ($promocion->aplica_sobre === 'item') {
            // Descuento sobre items específicos
            $productosPromo = $promocion->productos->pluck('id')->toArray();
            $categoriasPromo = $promocion->categorias->pluck('id')->toArray();

            foreach ($pedido->items as $item) {
                $aplicaItem = false;

                // Verificar si el producto está en la promoción
                if (in_array($item->producto_id, $productosPromo)) {
                    $aplicaItem = true;
                }

                // Verificar si la categoría está en la promoción
                if (!empty($categoriasPromo) && $item->producto && in_array($item->producto->categoria_id, $categoriasPromo)) {
                    $aplicaItem = true;
                }

                if ($aplicaItem) {
                    if ($promocion->tipo === 'porcentaje') {
                        $descuento += ($item->subtotal_item * $promocion->valor) / 100;
                    } elseif ($promocion->tipo === 'monto_fijo') {
                        $descuento += min($promocion->valor * $item->cantidad, $item->subtotal_item);
                    } elseif ($promocion->tipo === '2x1') {
                        // 2x1: descuento del 50% sobre items pares
                        if ($item->cantidad >= 2) {
                            $itemsGratis = intdiv($item->cantidad, 2);
                            $descuento += $itemsGratis * $item->precio_unitario;
                        }
                    }
                }
            }
        }

        // Aplicar tope de descuento si existe
        if ($promocion->tope_descuento && $descuento > $promocion->tope_descuento) {
            $descuento = $promocion->tope_descuento;
        }

        return round($descuento, 2);
    }

    /**
     * Obtener promociones próximas a expirar (3 días o menos)
     */
    public function getPromocionesProximasExpirar(): Collection
    {
        $hoy = Carbon::now('America/La_Paz');
        $tresDias = $hoy->copy()->addDays(3);

        return Promocion::where('activo', true)
            ->whereNotNull('fecha_fin')
            ->whereBetween('fecha_fin', [$hoy, $tresDias])
            ->orderBy('fecha_fin')
            ->get();
    }

    /**
     * Obtener días restantes para una promoción
     */
    public function getDiasRestantes(Promocion $promocion): int
    {
        if (!$promocion->fecha_fin) {
            return -1; // Sin fecha de expiración
        }

        $hoy = Carbon::now('America/La_Paz');
        return max(0, $hoy->diffInDays($promocion->fecha_fin, false));
    }
}
