<?php

namespace App\Support;

use App\Models\Reserva;
use App\Models\Mesa;
use App\Support\BusinessHours;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservaService
{
    protected $businessHours;

    public function __construct(BusinessHours $businessHours)
    {
        $this->businessHours = $businessHours;
    }

    /**
 * Verificar disponibilidad de mesas para una fecha/hora
 */
public function verificarDisponibilidad(string $fecha, string $hora, int $numeroPersonas)
{
    try {
        // Verificar que esté dentro del horario de atención (comentar temporalmente para probar)
        // if (!$this->businessHours->isWithinBusinessHours($fecha, $hora)) {
        //     throw new \Exception('La fecha y hora seleccionada está fuera del horario de atención.');
        // }

        // Obtener mesas con capacidad suficiente
        $mesasCapacidad = Mesa::where('capacidad', '>=', $numeroPersonas)
            ->where('estado', 'libre')
            ->get();

        $mesasDisponibles = collect();

        foreach ($mesasCapacidad as $mesa) {
            if ($this->mesaDisponible($mesa->id, $fecha, $hora)) {
                $mesasDisponibles->push([
                    'id' => $mesa->id,
                    'nombre' => $mesa->nombre,
                    'capacidad' => $mesa->capacidad
                ]);
            }
        }

        return $mesasDisponibles;

    } catch (\Exception $e) {
        \Log::error('Error en verificarDisponibilidad: ' . $e->getMessage());
        throw $e;
    }
}

    /**
     * Verificar si una mesa específica está disponible
     * Considera tanto reservas confirmadas como pendientes
     */
    private function mesaDisponible(int $mesaId, string $fecha, string $hora): bool
    {
        $reservaExistente = Reserva::where('mesa_id', $mesaId)
            ->where('fecha', $fecha)
            ->whereIn('estado', ['pendiente', 'confirmada']) // Considerar ambas
            ->whereTime('hora', '=', $hora)
            ->exists();

        return !$reservaExistente;
    }

    /**
     * Crear una nueva reserva
     * @param bool $confirmada Si es true, se crea como confirmada (cajero), si es false como pendiente (cliente online)
     */
    public function crearReserva(
        int $clienteId,
        int $mesaId,
        string $fecha,
        string $hora,
        int $numeroPersonas,
        ?string $observaciones = null,
        bool $confirmada = false
    ): Reserva {
        return DB::transaction(function () use (
            $clienteId,
            $mesaId,
            $fecha,
            $hora,
            $numeroPersonas,
            $observaciones,
            $confirmada
        ) {
            // Verificar disponibilidad final antes de crear
            if (!$this->mesaDisponible($mesaId, $fecha, $hora)) {
                throw new \Exception('La mesa seleccionada ya no está disponible. Por favor elige otra.');
            }

            // Verificar capacidad de la mesa
            $mesa = Mesa::findOrFail($mesaId);
            if ($mesa->capacidad < $numeroPersonas) {
                throw new \Exception("La mesa {$mesa->nombre} tiene capacidad para {$mesa->capacidad} personas.");
            }

            // Verificar horarios de negocio si la reserva es para confirmar inmediatamente
            if ($confirmada && !$this->businessHours->isWithinBusinessHours($fecha, $hora)) {
                throw new \Exception('La fecha y hora seleccionada está fuera del horario de atención.');
            }

            // Crear la reserva con estado según quién la crea
            $reserva = Reserva::create([
                'cliente_id' => $clienteId,
                'mesa_id' => $mesaId,
                'fecha' => $fecha,
                'hora' => $hora,
                'numero_personas' => $numeroPersonas,
                'estado' => $confirmada ? 'confirmada' : 'pendiente',
                'observaciones' => $observaciones,
            ]);

            Log::info("Nueva reserva creada", [
                'reserva_id' => $reserva->id,
                'cliente_id' => $clienteId,
                'mesa_id' => $mesaId,
                'fecha' => $fecha,
                'hora' => $hora,
                'estado' => $reserva->estado,
                'confirmada' => $confirmada
            ]);

            return $reserva;
        });
    }

    /**
     * Confirmar una reserva pendiente (usado por cajero)
     */
    public function confirmarReserva(Reserva $reserva): void
    {
        if ($reserva->estado !== 'pendiente') {
            throw new \Exception('Solo se pueden confirmar reservas pendientes.');
        }

        // Verificar que la mesa siga disponible
        if (!$this->mesaDisponible($reserva->mesa_id, $reserva->fecha->toDateString(), $reserva->hora->format('H:i'))) {
            throw new \Exception('La mesa ya no está disponible para esta fecha y hora.');
        }

        $reserva->update(['estado' => 'confirmada']);

        Log::info("Reserva confirmada", [
            'reserva_id' => $reserva->id,
            'cliente_id' => $reserva->cliente_id
        ]);
    }

    /**
     * Actualizar una reserva existente
     */
    public function actualizarReserva(
        Reserva $reserva,
        string $fecha,
        string $hora,
        int $numeroPersonas,
        ?string $observaciones = null
    ): void {
        DB::transaction(function () use (
            $reserva,
            $fecha,
            $hora,
            $numeroPersonas,
            $observaciones
        ) {
            // Si cambió la mesa, fecha u hora, verificar disponibilidad
            if ($reserva->fecha != $fecha || $reserva->hora != $hora) {
                if (!$this->mesaDisponible($reserva->mesa_id, $fecha, $hora)) {
                    throw new \Exception('La mesa ya no está disponible en la nueva fecha/hora seleccionada.');
                }
            }

            // Verificar capacidad si cambió el número de personas
            if ($reserva->numero_personas != $numeroPersonas) {
                $mesa = $reserva->mesa;
                if ($mesa->capacidad < $numeroPersonas) {
                    throw new \Exception("La mesa {$mesa->nombre} tiene capacidad para {$mesa->capacidad} personas.");
                }
            }

            // Actualizar reserva
            $reserva->update([
                'fecha' => $fecha,
                'hora' => $hora,
                'numero_personas' => $numeroPersonas,
                'observaciones' => $observaciones,
            ]);

            Log::info("Reserva actualizada", [
                'reserva_id' => $reserva->id,
                'fecha' => $fecha,
                'hora' => $hora
            ]);
        });
    }

    /**
     * Cancelar una reserva
     */
    public function cancelarReserva(Reserva $reserva): void
    {
        if (!in_array($reserva->estado, ['pendiente', 'confirmada'])) {
            throw new \Exception('No se puede cancelar una reserva que ya fue cumplida o cancelada.');
        }

        $reserva->update([
            'estado' => 'cancelada'
        ]);

        Log::info("Reserva cancelada", [
            'reserva_id' => $reserva->id,
            'cliente_id' => $reserva->cliente_id
        ]);

        // Aquí podrías agregar:
        // - Envío de email de cancelación
        // - Notificación al personal
    }

    /**
     * Generar código de reserva único
     */
    public function generarCodigoReserva(Reserva $reserva): string
    {
        $fecha = $reserva->fecha->format('Ymd');
        $id = str_pad($reserva->id, 3, '0', STR_PAD_LEFT);
        return "RES-{$fecha}-{$id}";
    }
}