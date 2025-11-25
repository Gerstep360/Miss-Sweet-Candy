<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
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
        // 🔒 Solo usuarios con permiso pueden ver sus reservas
        if (! auth()->check() || ! auth()->user()->can('hacer-reserva')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $reservas = Reserva::delCliente(Auth::id())
            ->with('mesa')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        BitacoraController::registrar('ver lista', 'Reserva', null);

        return view('cajero.reservas.index', [
            'reservas' => $reservas,
            'reservaService' => $this->reservaService,
        ]);
    }

    /**
     * Mostrar formulario para nueva reserva
     */
    public function create()
    {
        // 🔒 Solo usuarios con permiso pueden crear reservas online
        if (! auth()->check() || ! auth()->user()->can('hacer-reserva')) {
            abort(403, 'No tienes permiso para crear reservas online.');
        }

        BitacoraController::registrar('crear', 'Reserva', null);

        return view('cajero.reservas.create');
    }

    /**
     * Verificar disponibilidad de mesas
     */
    public function verificarDisponibilidad(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
            'numero_personas' => 'required|integer|min:1|max:20',
        ]);

        $mesasDisponibles = $this->reservaService->verificarDisponibilidad(
            $request->fecha,
            $request->hora,
            $request->numero_personas
        );

        return response()->json([
            'disponible' => $mesasDisponibles->isNotEmpty(),
            'mesas' => $mesasDisponibles,
        ]);
    }

    /**
     * Guardar nueva reserva (cliente online - se crea como pendiente)
     */
    public function store(Request $request)
    {
        // 🔒 Solo usuarios con permiso pueden crear reservas online
        if (! auth()->check() || ! auth()->user()->can('hacer-reserva')) {
            abort(403, 'No tienes permiso para crear reservas online.');
        }

        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
            'numero_personas' => 'required|integer|min:1|max:20',
            'mesa_id' => 'required|exists:mesas,id',
            'observaciones' => 'nullable|string|max:255',
        ]);

        try {
            // Crear reserva como PENDIENTE (el cajero la confirmará)
            $reserva = $this->reservaService->crearReserva(
                Auth::id(),
                $request->mesa_id,
                $request->fecha,
                $request->hora,
                $request->numero_personas,
                $request->observaciones,
                false // false = pendiente (cliente online)
            );

            BitacoraController::registrar('crear', 'Reserva', $reserva->id);

            return redirect()->route('reservas.show', $reserva->id)
                ->with('success', '¡Reserva creada exitosamente! Está pendiente de confirmación. Te notificaremos cuando sea confirmada.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear la reserva: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de una reserva
     */
    public function show(Reserva $reserva)
    {
        // 🔒 Solo usuarios con permiso pueden ver sus reservas
        if (! auth()->check() || ! auth()->user()->can('hacer-reserva')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // Verificar que el cliente solo vea sus propias reservas
        if ($reserva->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta reserva.');
        }

        $reserva->load('mesa');

        BitacoraController::registrar('ver', 'Reserva', $reserva->id);

        return view('cajero.reservas.show', [
            'reserva' => $reserva,
            'reservaService' => $this->reservaService,
        ]);
    }

    /**
     * Mostrar formulario para editar reserva
     */
    public function edit(Reserva $reserva)
    {
        // 🔒 Solo usuarios con permiso pueden editar sus reservas
        if (! auth()->check() || ! auth()->user()->can('hacer-reserva')) {
            abort(403, 'No tienes permiso para editar reservas.');
        }

        if ($reserva->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta reserva.');
        }

        if (! $reserva->estaActiva()) {
            return redirect()->route('reservas.show', $reserva->id)
                ->with('error', 'No puedes modificar una reserva cancelada o cumplida.');
        }

        BitacoraController::registrar('editar', 'Reserva', $reserva->id);

        return view('cajero.reservas.edit', compact('reserva'));
    }

    /**
     * Actualizar reserva
     */
    public function update(Request $request, Reserva $reserva)
    {
        // 🔒 Solo usuarios con permiso pueden actualizar sus reservas
        if (! auth()->check() || ! auth()->user()->can('hacer-reserva')) {
            abort(403, 'No tienes permiso para actualizar reservas.');
        }

        if ($reserva->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta reserva.');
        }

        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
            'numero_personas' => 'required|integer|min:1|max:20',
            'observaciones' => 'nullable|string|max:255',
        ]);

        try {
            $this->reservaService->actualizarReserva(
                $reserva,
                $request->fecha,
                $request->hora,
                $request->numero_personas,
                $request->observaciones
            );

            BitacoraController::registrar('actualizar', 'Reserva', $reserva->id);

            return redirect()->route('reservas.show', $reserva->id)
                ->with('success', 'Reserva actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la reserva: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancelar reserva
     */
    public function destroy(Reserva $reserva)
    {
        // 🔒 Solo usuarios con permiso pueden cancelar sus reservas
        if (! auth()->check() || ! auth()->user()->can('hacer-reserva')) {
            abort(403, 'No tienes permiso para cancelar reservas.');
        }

        if ($reserva->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para cancelar esta reserva.');
        }

        try {
            $this->reservaService->cancelarReserva($reserva);

            BitacoraController::registrar('cancelar', 'Reserva', $reserva->id);

            return redirect()->route('reservas.index')
                ->with('success', 'Reserva cancelada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al cancelar la reserva: '.$e->getMessage());
        }
    }
}
