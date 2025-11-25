<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BitacoraController;
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
        // 🔒 Solo cajero y admin pueden ver reservas
        if (!auth()->check() || (!auth()->user()->hasRole('cajero') && !auth()->user()->hasRole('administrador'))) {
            abort(403, 'Solo el cajero puede acceder a esta sección.');
        }

        $query = Reserva::with(['cliente', 'mesa'])
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc');

        // Filtros
        if ($request->has('fecha') && $request->fecha) {
            $query->where('fecha', $request->fecha);
        } else {
            $query->where('fecha', '>=', now()->toDateString());
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

        $reservas = $query->paginate(20);

        // Estadísticas para hoy
        $fechaHoy = now()->toDateString();
        $stats = [
            'total_hoy' => Reserva::where('fecha', $fechaHoy)->count(),
            'confirmadas' => Reserva::where('fecha', $fechaHoy)->where('estado', 'confirmada')->count(),
            'pendientes' => Reserva::where('fecha', $fechaHoy)->where('estado', 'pendiente')->count(),
            'cumplidas' => Reserva::where('fecha', $fechaHoy)->where('estado', 'cumplida')->count(),
        ];

        BitacoraController::registrar('ver lista', 'Reserva', null);

        return view('cajero.reservas.index', compact('reservas', 'stats'));
    }

    /**
     * Mostrar formulario para crear reserva manual
     */
    public function create()
    {
        // 🔒 Solo cajero y admin pueden crear reservas manuales
        if (!auth()->check() || (!auth()->user()->hasRole('cajero') && !auth()->user()->hasRole('administrador'))) {
            abort(403, 'Solo el cajero puede crear reservas manuales.');
        }

        $mesas = Mesa::whereIn('estado', ['libre', 'disponible'])->get();
        $clientes = User::role('cliente')->get();
        
        BitacoraController::registrar('crear', 'Reserva', null);

        return view('cajero.reservas.create', compact('mesas', 'clientes'));
    }

    /**
     * Guardar reserva manual (desde cajero - se crea como confirmada)
     */
    public function store(Request $request)
    {
        // 🔒 Solo cajero y admin pueden crear reservas manuales
        if (!auth()->check() || (!auth()->user()->hasRole('cajero') && !auth()->user()->hasRole('administrador'))) {
            abort(403, 'Solo el cajero puede crear reservas manuales.');
        }

        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'mesa_id' => 'required|exists:mesas,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
            'numero_personas' => 'required|integer|min:1|max:20',
            'observaciones' => 'nullable|string|max:255'
        ]);

        try {
            // Crear reserva como CONFIRMADA (cajero crea directamente confirmada)
            $reserva = $this->reservaService->crearReserva(
                $request->cliente_id,
                $request->mesa_id,
                $request->fecha,
                $request->hora,
                $request->numero_personas,
                $request->observaciones,
                true // true = confirmada (cajero)
            );

            BitacoraController::registrar('crear reserva manual', 'Reserva', $reserva->id);

            return redirect()->route('cajero.reservas.index')
                ->with('success', 'Reserva creada y confirmada exitosamente.');

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
        // 🔒 Solo cajero y admin pueden ver detalles de reservas
        if (!auth()->check() || (!auth()->user()->hasRole('cajero') && !auth()->user()->hasRole('administrador'))) {
            abort(403, 'Solo el cajero puede ver detalles de reservas.');
        }

        $reserva->load(['cliente', 'mesa']);
        
        BitacoraController::registrar('ver', 'Reserva', $reserva->id);

        return view('cajero.reservas.show', [
            'reserva' => $reserva,
            'reservaService' => $this->reservaService
        ]);
    }

    /**
     * Confirmar una reserva pendiente (cambiar de pendiente a confirmada)
     */
    public function confirmar(Reserva $reserva)
    {
        // 🔒 Solo cajero y admin pueden confirmar reservas
        if (!auth()->check() || (!auth()->user()->hasRole('cajero') && !auth()->user()->hasRole('administrador'))) {
            abort(403, 'Solo el cajero puede confirmar reservas.');
        }

        try {
            $this->reservaService->confirmarReserva($reserva);

            BitacoraController::registrar('confirmar reserva', 'Reserva', $reserva->id);

            return redirect()->back()
                ->with('success', 'Reserva confirmada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al confirmar reserva: ' . $e->getMessage());
        }
    }

    /**
     * Confirmar llegada del cliente (marcar como cumplida)
     */
    public function confirmarLlegada(Reserva $reserva)
    {
        // 🔒 Solo cajero y admin pueden confirmar llegada
        if (!auth()->check() || (!auth()->user()->hasRole('cajero') && !auth()->user()->hasRole('administrador'))) {
            abort(403, 'Solo el cajero puede confirmar llegada.');
        }

        if ($reserva->estado !== 'confirmada') {
            return redirect()->back()
                ->with('error', 'Solo se puede confirmar llegada de reservas confirmadas.');
        }

        try {
            $reserva->update(['estado' => 'cumplida']);
            
            // Cambiar el estado de la mesa a "ocupada"
            $reserva->mesa->update(['estado' => 'ocupada']);

            BitacoraController::registrar('confirmar llegada', 'Reserva', $reserva->id);

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
        // 🔒 Solo cajero y admin pueden cancelar reservas
        if (!auth()->check() || (!auth()->user()->hasRole('cajero') && !auth()->user()->hasRole('administrador'))) {
            abort(403, 'Solo el cajero puede cancelar reservas.');
        }

        try {
            $this->reservaService->cancelarReserva($reserva);

            BitacoraController::registrar('cancelar reserva (cajero)', 'Reserva', $reserva->id);
            
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
        // 🔒 Solo cajero y admin pueden marcar no-show
        if (!auth()->check() || (!auth()->user()->hasRole('cajero') && !auth()->user()->hasRole('administrador'))) {
            abort(403, 'Solo el cajero puede marcar no-show.');
        }

        try {
            $observaciones = $reserva->observaciones ?? '';
            $observaciones .= " [NO-SHOW - " . now()->format('d/m/Y H:i') . "]";

            $reserva->update([
                'estado' => 'cancelada',
                'observaciones' => trim($observaciones)
            ]);

            BitacoraController::registrar('marcar no-show', 'Reserva', $reserva->id);

            return redirect()->back()
                ->with('success', 'Reserva marcada como No-Show.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al marcar como No-Show: ' . $e->getMessage());
        }
    }
}