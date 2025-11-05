<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Mesa;
use App\Support\ReservaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    protected $reservaService;

    public function __construct(ReservaService $reservaService)
    {
        $this->reservaService = $reservaService;
    }

    /**
     * Mostrar listado de reservas del cliente
     */
    public function index()
    {
          $reservas = Reserva::delCliente(Auth::id())
        ->with('mesa')
        ->orderBy('fecha', 'desc')
        ->orderBy('hora', 'desc')
        ->get();

    return view('reservas.index', [
        'reservas' => $reservas,
        'reservaService' => $this->reservaService
    ]);
    }

    /**
     * Mostrar formulario para nueva reserva
     */
    public function create()
    {
        return view('reservas.create');
    }

    /**
     * Verificar disponibilidad de mesas
     */
    public function verificarDisponibilidad(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
            'numero_personas' => 'required|integer|min:1|max:20'
        ]);

        $mesasDisponibles = $this->reservaService->verificarDisponibilidad(
            $request->fecha,
            $request->hora,
            $request->numero_personas
        );

        return response()->json([
            'disponible' => $mesasDisponibles->isNotEmpty(),
            'mesas' => $mesasDisponibles
        ]);
    }

    /**
     * Guardar nueva reserva
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
            'numero_personas' => 'required|integer|min:1|max:20',
            'mesa_id' => 'required|exists:mesas,id',
            'observaciones' => 'nullable|string|max:255'
        ]);

        try {
            $reserva = $this->reservaService->crearReserva(
                Auth::id(),
                $request->mesa_id,
                $request->fecha,
                $request->hora,
                $request->numero_personas,
                $request->observaciones
            );

            return redirect()->route('reservas.show', $reserva->id)
                ->with('success', '¡Reserva confirmada! Te hemos enviado un email de confirmación.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear la reserva: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de una reserva
     */
    public function show(Reserva $reserva)
    {
       // Verificar que el cliente solo vea sus propias reservas
    if ($reserva->cliente_id !== Auth::id()) {
        abort(403, 'No tienes permiso para ver esta reserva.');
    }

    $reserva->load('mesa');

    return view('reservas.show', [
        'reserva' => $reserva,
        'reservaService' => $this->reservaService
    ]);
    }

    /**
     * Mostrar formulario para editar reserva
     */
    public function edit(Reserva $reserva)
    {
        if ($reserva->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta reserva.');
        }

        if (!$reserva->estaActiva()) {
            return redirect()->route('reservas.show', $reserva->id)
                ->with('error', 'No puedes modificar una reserva cancelada o cumplida.');
        }

        return view('reservas.edit', compact('reserva'));
    }

    /**
     * Actualizar reserva
     */
    public function update(Request $request, Reserva $reserva)
    {
        if ($reserva->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta reserva.');
        }

        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
            'numero_personas' => 'required|integer|min:1|max:20',
            'observaciones' => 'nullable|string|max:255'
        ]);

        try {
            $this->reservaService->actualizarReserva(
                $reserva,
                $request->fecha,
                $request->hora,
                $request->numero_personas,
                $request->observaciones
            );

            return redirect()->route('reservas.show', $reserva->id)
                ->with('success', 'Reserva actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la reserva: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancelar reserva
     */
    public function destroy(Reserva $reserva)
    {
        if ($reserva->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para cancelar esta reserva.');
        }

        try {
            $this->reservaService->cancelarReserva($reserva);

            return redirect()->route('reservas.index')
                ->with('success', 'Reserva cancelada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al cancelar la reserva: ' . $e->getMessage());
        }
    }
}