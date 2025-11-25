<?php

namespace App\Http\Controllers\Personal;

use App\Models\TurnoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TurnoCajaController extends Controller
{
    /**
     * API endpoint para obtener estado del turno actual
     */
    public function estado()
    {
        $turnoActivo = TurnoCaja::turnoActivo();
        
        if (!$turnoActivo) {
            return response()->json([
                'hay_turno_activo' => false,
                'es_mi_turno' => false,
                'turno' => null
            ]);
        }

        return response()->json([
            'hay_turno_activo' => true,
            'es_mi_turno' => $turnoActivo->cajero_id === Auth::id(),
            'turno' => [
                'id' => $turnoActivo->id,
                'cajero' => [
                    'id' => $turnoActivo->cajero->id,
                    'name' => $turnoActivo->cajero->name,
                ],
                'inicio' => $turnoActivo->inicio->format('Y-m-d H:i:s'),
                'tiempo_transcurrido' => $turnoActivo->tiempo_transcurrido,
                'duracion_formateada' => $turnoActivo->duracion_formateada,
                'monto_inicial' => $turnoActivo->monto_inicial,
            ]
        ]);
    }

    /**
     * Mostrar formulario para iniciar turno
     */
    public function formIniciar()
    {
        // Verificar si ya hay un turno activo
        if (TurnoCaja::hayTurnoActivo()) {
            $turnoActivo = TurnoCaja::turnoActivo();
            
            return redirect()->route('dashboard')->with('error', 
                "{$turnoActivo->cajero->name} está de turno actualmente. Debe cerrar su turno primero."
            );
        }

        // Verificar si el cajero actual ya tiene un turno activo
        if (TurnoCaja::tieneTurnoActivo()) {
            return redirect()->route('dashboard')->with('error', 
                'Ya tienes un turno activo. Debes cerrarlo antes de iniciar uno nuevo.'
            );
        }

        return view('turnos_caja.iniciar');
    }

    /**
     * Iniciar un nuevo turno
     */
    public function iniciar(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
            'observaciones_apertura' => 'nullable|string|max:500'
        ]);

        $resultado = TurnoCaja::iniciarTurno(
            Auth::id(),
            $request->monto_inicial,
            $request->observaciones_apertura
        );

        if (!$resultado['success']) {
            return redirect()->back()
                           ->with('error', $resultado['message'])
                           ->withInput();
        }

        // Registrar en bitácora
        app(BitacoraController::class)->registrar(
            'Turno de Caja',
            'Crear',
            "Turno iniciado con monto inicial: Bs. " . number_format($resultado['turno']->monto_inicial, 2)
        );

        return redirect()->route('dashboard')
                       ->with('success', 'Turno iniciado correctamente. Puedes empezar a trabajar.');
    }

    /**
     * Cerrar el turno actual (redirecciona a crear cierre de caja)
     */
    public function cerrar()
    {
        // Verificar que existe un turno activo
        $turnoActivo = TurnoCaja::turnoActivo();
        
        if (!$turnoActivo) {
            return redirect()->route('dashboard')->with('error', 'No hay ningún turno activo para cerrar.');
        }

        // Verificar que el turno es del cajero actual
        if ($turnoActivo->cajero_id !== Auth::id()) {
            return redirect()->route('dashboard')->with('error', 
                'No puedes cerrar el turno de otro cajero.'
            );
        }

        // Redirigir a crear cierre de caja (que automáticamente cerrará el turno)
        return redirect()->route('cierres_caja.create')
                       ->with('info', 'Completa el cierre de caja para finalizar tu turno.');
    }

    /**
     * Historial de turnos
     */
    public function index(Request $request)
    {
        $query = TurnoCaja::with('cajero', 'cierre');

        // Filtros
        if ($request->filled('cajero_id')) {
            $query->where('cajero_id', $request->cajero_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->entreFechas($request->fecha_inicio, $request->fecha_fin);
        }

        $turnos = $query->orderBy('inicio', 'desc')->paginate(15);

        // Cajeros para el filtro
        $cajeros = \App\Models\User::role(['cajero', 'administrador'])->get();

        return view('turnos_caja.index', compact('turnos', 'cajeros'));
    }

    /**
     * Ver detalle de un turno
     */
    public function show(TurnoCaja $turno)
    {
        $turno->load('cajero', 'cierre.detalles');

        return view('turnos_caja.show', compact('turno'));
    }
}
