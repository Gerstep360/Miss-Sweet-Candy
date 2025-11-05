<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Mesa;
use App\Models\User;
use App\Support\ReservaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservaCajeroController extends Controller
{
    protected $reservaService;

    public function __construct(ReservaService $reservaService)
    {
        $this->reservaService = $reservaService;
    }

    /**
     * Mostrar listado de reservas para cajero
     */
    public function index(Request $request)
    {
        $query = Reserva::with(['cliente', 'mesa'])
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc');

        // Filtros
        if ($request->has('fecha') && $request->fecha) {
            $query->where('fecha', $request->fecha);
        } else {
            $query->where('fecha', now()->toDateString());
        }

        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('cliente', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('mesa', function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
            });
        }

        $reservas = $query->get();

        // Estadísticas
        $stats = [
            'total_hoy' => Reserva::where('fecha', now()->toDateString())->count(),
            'confirmadas' => Reserva::where('fecha', now()->toDateString())->where('estado', 'confirmada')->count(),
            'pendientes' => Reserva::where('fecha', now()->toDateString())->where('estado', 'pendiente')->count(),
            'cumplidas' => Reserva::where('fecha', now()->toDateString())->where('estado', 'cumplida')->count(),
        ];

        return view('cajero.reservas.index', compact('reservas', 'stats'));
    }

    /**
     * Mostrar formulario para crear reserva manual
     */
    public function create()
    {
        $mesas = Mesa::where('estado', 'libre')->get();
        $clientes = User::role('cliente')->get();
        
        return view('cajero.reservas.create', compact('mesas', 'clientes'));
    }

    /**
     * Guardar reserva manual (desde cajero)
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'mesa_id' => 'required|exists:mesas,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
            'numero_personas' => 'required|integer|min:1|max:20',
            'observaciones' => 'nullable|string|max:255'
        ]);

        try {
            $reserva = $this->reservaService->crearReserva(
                $request->cliente_id,
                $request->mesa_id,
                $request->fecha,
                $request->hora,
                $request->numero_personas,
                $request->observaciones
            );

            return redirect()->route('cajero.reservas.index')
                ->with('success', 'Reserva creada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear la reserva: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de reserva
     */
    public function show(Reserva $reserva)
    {
        $reserva->load(['cliente', 'mesa']);
        
        return view('cajero.reservas.show', [
            'reserva' => $reserva,
            'reservaService' => $this->reservaService
        ]);
    }

    /**
     * Confirmar llegada del cliente
     */
    public function confirmarLlegada(Reserva $reserva)
    {
        try {
            $reserva->update(['estado' => 'cumplida']);
            
            // Aquí podrías también cambiar el estado de la mesa a "ocupada"
            $reserva->mesa->update(['estado' => 'ocupada']);

            return redirect()->back()
                ->with('success', 'Llegada del cliente confirmada. Mesa marcada como ocupada.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al confirmar llegada: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar reserva (desde cajero)
     */
    public function cancelar(Reserva $reserva)
    {
        try {
            $this->reservaService->cancelarReserva($reserva);
            
            return redirect()->back()
                ->with('success', 'Reserva cancelada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al cancelar reserva: ' . $e->getMessage());
        }
    }

    /**
     * Marcar como no-show
     */
    public function marcarNoShow(Reserva $reserva)
    {
        try {
            $reserva->update([
                'estado' => 'cancelada',
                'observaciones' => $reserva->observaciones . " [NO-SHOW - " . now()->format('d/m/Y H:i') . "]"
            ]);

            return redirect()->back()
                ->with('success', 'Reserva marcada como No-Show.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al marcar como No-Show: ' . $e->getMessage());
        }
    }
}