<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

/**
 * CU25: Cumplimiento Sanitario (SENASAG)
 * 
 * Gestiona el registro y seguimiento de actividades de cumplimiento sanitario:
 * - Registro de actividades de limpieza y desinfección
 * - Control de temperaturas de equipos y alimentos
 * - Carga de evidencias fotográficas
 * - Generación de reportes para auditorías SENASAG
 * - Alertas de incumplimientos
 */
class CumplimientoSanitarioController extends Controller
{
    use AuthorizesRequests;

    /**
     * Muestra el dashboard de cumplimiento sanitario
     */
    public function index(Request $request)
    {
        $this->authorize('ver-cumplimiento-sanitario');

        $fecha = $request->input('fecha', now()->format('Y-m-d'));

        // Obtener registros del día
        $registrosLimpieza = DB::table('cumplimiento_limpieza')
            ->whereDate('fecha_registro', $fecha)
            ->orderBy('hora_registro', 'desc')
            ->get();

        $registrosTemperatura = DB::table('cumplimiento_temperatura')
            ->whereDate('fecha_registro', $fecha)
            ->orderBy('hora_registro', 'desc')
            ->get();

        // Calcular estadísticas del día
        $estadisticas = $this->calcularEstadisticasDia($fecha);

        // Verificar alertas activas
        $alertas = $this->verificarAlertas($fecha);

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_dashboard',
            'cumplimiento_sanitario',
            null,
            ['fecha' => $fecha],
            $request
        );

        return view('cumplimiento-sanitario.index', compact(
            'registrosLimpieza',
            'registrosTemperatura',
            'estadisticas',
            'alertas',
            'fecha'
        ));
    }

    /**
     * Muestra el formulario para registrar actividad de limpieza
     */
    public function createLimpieza()
    {
        $this->authorize('registrar-cumplimiento-sanitario');

        // Áreas predefinidas de limpieza
        $areas = [
            'cocina' => 'Cocina',
            'barra' => 'Barra de café',
            'salon' => 'Salón comedor',
            'banos' => 'Baños',
            'almacen' => 'Almacén',
            'refrigeradores' => 'Refrigeradores',
            'equipos' => 'Equipos de cocina',
            'mesas' => 'Mesas y sillas',
            'pisos' => 'Pisos',
            'paredes' => 'Paredes',
        ];

        // Tipos de limpieza
        $tiposLimpieza = [
            'rutinaria' => 'Limpieza rutinaria',
            'profunda' => 'Limpieza profunda',
            'desinfeccion' => 'Desinfección',
            'sanitizacion' => 'Sanitización',
        ];

        return view('cumplimiento-sanitario.limpieza.create', compact('areas', 'tiposLimpieza'));
    }

    /**
     * Registra una actividad de limpieza
     */
    public function storeLimpieza(Request $request)
    {
        $this->authorize('registrar-cumplimiento-sanitario');

        $validated = $request->validate([
            'area' => 'required|string|max:100',
            'tipo_limpieza' => 'required|string|max:50',
            'productos_usados' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
            'evidencias.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB
        ]);

        DB::beginTransaction();

        try {
            // Crear registro de limpieza
            $registroId = DB::table('cumplimiento_limpieza')->insertGetId([
                'area' => $validated['area'],
                'tipo_limpieza' => $validated['tipo_limpieza'],
                'productos_usados' => $validated['productos_usados'],
                'observaciones' => $validated['observaciones'] ?? null,
                'responsable_id' => Auth::id(),
                'fecha_registro' => now()->format('Y-m-d'),
                'hora_registro' => now()->format('H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Procesar evidencias fotográficas
            if ($request->hasFile('evidencias')) {
                foreach ($request->file('evidencias') as $evidencia) {
                    $path = $evidencia->store('cumplimiento-sanitario/limpieza', 'public');
                    
                    DB::table('cumplimiento_evidencias')->insert([
                        'tipo_registro' => 'limpieza',
                        'registro_id' => $registroId,
                        'ruta_archivo' => $path,
                        'nombre_original' => $evidencia->getClientOriginalName(),
                        'created_at' => now(),
                    ]);
                }
            }

            // Registrar en bitácora
            BitacoraController::registrar(
                'registro_limpieza',
                'cumplimiento_sanitario',
                $registroId,
                [
                    'area' => $validated['area'],
                    'tipo' => $validated['tipo_limpieza'],
                ],
                $request
            );

            DB::commit();

            return redirect()
                ->route('cumplimiento-sanitario.index')
                ->with('success', 'Actividad de limpieza registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al registrar limpieza: ' . $e->getMessage());
        }
    }


    /**
     * Muestra el formulario para registrar control de temperatura
     */
    public function createTemperatura()
    {
        $this->authorize('registrar-cumplimiento-sanitario');

        // Equipos/áreas de control de temperatura
        $equipos = [
            'refrigerador_1' => 'Refrigerador 1 (Lácteos)',
            'refrigerador_2' => 'Refrigerador 2 (Carnes)',
            'congelador_1' => 'Congelador 1',
            'vitrina_fria' => 'Vitrina refrigerada',
            'camara_fria' => 'Cámara fría',
            'barra_caliente' => 'Barra caliente',
            'horno' => 'Horno',
            'plancha' => 'Plancha',
        ];

        // Rangos de temperatura recomendados
        $rangosRecomendados = [
            'refrigerador_1' => ['min' => 0, 'max' => 4],
            'refrigerador_2' => ['min' => 0, 'max' => 4],
            'congelador_1' => ['min' => -18, 'max' => -15],
            'vitrina_fria' => ['min' => 2, 'max' => 8],
            'camara_fria' => ['min' => 0, 'max' => 4],
            'barra_caliente' => ['min' => 60, 'max' => 70],
            'horno' => ['min' => 180, 'max' => 250],
            'plancha' => ['min' => 150, 'max' => 200],
        ];

        return view('cumplimiento-sanitario.temperatura.create', compact('equipos', 'rangosRecomendados'));
    }

    /**
     * Registra un control de temperatura
     */
    public function storeTemperatura(Request $request)
    {
        $this->authorize('registrar-cumplimiento-sanitario');

        $validated = $request->validate([
            'equipo' => 'required|string|max:100',
            'temperatura' => 'required|numeric|between:-50,300',
            'temperatura_minima' => 'nullable|numeric|between:-50,300',
            'temperatura_maxima' => 'nullable|numeric|between:-50,300',
            'estado_equipo' => 'required|in:normal,alerta,critico',
            'observaciones' => 'nullable|string|max:1000',
            'evidencias.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        DB::beginTransaction();

        try {
            // Determinar si está fuera de rango
            $fueraRango = false;
            if ($validated['temperatura_minima'] && $validated['temperatura_maxima']) {
                $fueraRango = $validated['temperatura'] < $validated['temperatura_minima'] 
                           || $validated['temperatura'] > $validated['temperatura_maxima'];
            }

            // Crear registro de temperatura
            $registroId = DB::table('cumplimiento_temperatura')->insertGetId([
                'equipo' => $validated['equipo'],
                'temperatura' => $validated['temperatura'],
                'temperatura_minima' => $validated['temperatura_minima'] ?? null,
                'temperatura_maxima' => $validated['temperatura_maxima'] ?? null,
                'fuera_rango' => $fueraRango,
                'estado_equipo' => $validated['estado_equipo'],
                'observaciones' => $validated['observaciones'] ?? null,
                'responsable_id' => Auth::id(),
                'fecha_registro' => now()->format('Y-m-d'),
                'hora_registro' => now()->format('H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Procesar evidencias
            if ($request->hasFile('evidencias')) {
                foreach ($request->file('evidencias') as $evidencia) {
                    $path = $evidencia->store('cumplimiento-sanitario/temperatura', 'public');
                    
                    DB::table('cumplimiento_evidencias')->insert([
                        'tipo_registro' => 'temperatura',
                        'registro_id' => $registroId,
                        'ruta_archivo' => $path,
                        'nombre_original' => $evidencia->getClientOriginalName(),
                        'created_at' => now(),
                    ]);
                }
            }

            // Generar alerta si está fuera de rango o en estado crítico
            if ($fueraRango || $validated['estado_equipo'] === 'critico') {
                $this->generarAlertaTemperatura($registroId, $validated);
            }

            // Registrar en bitácora
            BitacoraController::registrar(
                'registro_temperatura',
                'cumplimiento_sanitario',
                $registroId,
                [
                    'equipo' => $validated['equipo'],
                    'temperatura' => $validated['temperatura'],
                    'fuera_rango' => $fueraRango,
                ],
                $request
            );

            DB::commit();

            $mensaje = 'Control de temperatura registrado correctamente.';
            if ($fueraRango) {
                $mensaje .= ' ALERTA: Temperatura fuera del rango permitido.';
            }

            return redirect()
                ->route('cumplimiento-sanitario.index')
                ->with($fueraRango ? 'warning' : 'success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al registrar temperatura: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el detalle de un registro de limpieza
     */
    public function showLimpieza($id)
    {
        $this->authorize('ver-cumplimiento-sanitario');

        $registro = DB::table('cumplimiento_limpieza')
            ->where('id', $id)
            ->first();

        if (!$registro) {
            abort(404, 'Registro no encontrado');
        }

        // Obtener responsable
        $responsable = DB::table('users')
            ->where('id', $registro->responsable_id)
            ->first();

        // Obtener evidencias
        $evidencias = DB::table('cumplimiento_evidencias')
            ->where('tipo_registro', 'limpieza')
            ->where('registro_id', $id)
            ->get();

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_detalle_limpieza',
            'cumplimiento_sanitario',
            $id,
            null,
            request()
        );

        return view('cumplimiento-sanitario.limpieza.show', compact('registro', 'responsable', 'evidencias'));
    }

    /**
     * Muestra el detalle de un registro de temperatura
     */
    public function showTemperatura($id)
    {
        $this->authorize('ver-cumplimiento-sanitario');

        $registro = DB::table('cumplimiento_temperatura')
            ->where('id', $id)
            ->first();

        if (!$registro) {
            abort(404, 'Registro no encontrado');
        }

        // Obtener responsable
        $responsable = DB::table('users')
            ->where('id', $registro->responsable_id)
            ->first();

        // Obtener evidencias
        $evidencias = DB::table('cumplimiento_evidencias')
            ->where('tipo_registro', 'temperatura')
            ->where('registro_id', $id)
            ->get();

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_detalle_temperatura',
            'cumplimiento_sanitario',
            $id,
            null,
            request()
        );

        return view('cumplimiento-sanitario.temperatura.show', compact('registro', 'responsable', 'evidencias'));
    }


    /**
     * Muestra el historial de registros con filtros
     */
    public function historial(Request $request)
    {
        $this->authorize('ver-cumplimiento-sanitario');

        $tipo = $request->input('tipo', 'limpieza'); // limpieza o temperatura
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $area = $request->input('area');
        $equipo = $request->input('equipo');

        if ($tipo === 'limpieza') {
            $query = DB::table('cumplimiento_limpieza')
                ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin]);

            if ($area) {
                $query->where('area', $area);
            }

            $registros = $query->orderBy('fecha_registro', 'desc')
                ->orderBy('hora_registro', 'desc')
                ->paginate(20);

        } else {
            $query = DB::table('cumplimiento_temperatura')
                ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin]);

            if ($equipo) {
                $query->where('equipo', $equipo);
            }

            $registros = $query->orderBy('fecha_registro', 'desc')
                ->orderBy('hora_registro', 'desc')
                ->paginate(20);
        }

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_historial',
            'cumplimiento_sanitario',
            null,
            [
                'tipo' => $tipo,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
            ],
            $request
        );

        return view('cumplimiento-sanitario.historial', compact(
            'registros',
            'tipo',
            'fechaInicio',
            'fechaFin',
            'area',
            'equipo'
        ));
    }

    /**
     * Genera reporte para auditoría SENASAG
     */
    public function reporteSenasag(Request $request)
    {
        $this->authorize('generar-reportes-sanitarios');

        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'tipo_reporte' => 'required|in:completo,limpieza,temperatura',
            'formato' => 'required|in:pdf,excel',
        ]);

        $fechaInicio = $validated['fecha_inicio'];
        $fechaFin = $validated['fecha_fin'];
        $tipoReporte = $validated['tipo_reporte'];

        // Obtener datos según tipo de reporte
        $datos = [];

        if (in_array($tipoReporte, ['completo', 'limpieza'])) {
            $datos['limpieza'] = DB::table('cumplimiento_limpieza')
                ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                ->orderBy('fecha_registro', 'desc')
                ->get();
        }

        if (in_array($tipoReporte, ['completo', 'temperatura'])) {
            $datos['temperatura'] = DB::table('cumplimiento_temperatura')
                ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                ->orderBy('fecha_registro', 'desc')
                ->get();
        }

        // Calcular estadísticas del período
        $estadisticas = $this->calcularEstadisticasPeriodo($fechaInicio, $fechaFin);

        // Obtener incumplimientos
        $incumplimientos = $this->obtenerIncumplimientos($fechaInicio, $fechaFin);

        // Registrar en bitácora
        BitacoraController::registrar(
            'generacion_reporte_senasag',
            'cumplimiento_sanitario',
            null,
            [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'tipo_reporte' => $tipoReporte,
                'formato' => $validated['formato'],
            ],
            $request
        );

        // TODO: Implementar generación de PDF/Excel
        return view('cumplimiento-sanitario.reporte-senasag', compact(
            'datos',
            'estadisticas',
            'incumplimientos',
            'fechaInicio',
            'fechaFin',
            'tipoReporte'
        ));
    }

    /**
     * Muestra alertas activas de incumplimiento
     */
    public function alertas()
    {
        $this->authorize('ver-cumplimiento-sanitario');

        // Alertas de temperatura fuera de rango (últimas 24 horas)
        $alertasTemperatura = DB::table('cumplimiento_temperatura')
            ->where('fuera_rango', true)
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->get();

        // Alertas de equipos en estado crítico (últimas 24 horas)
        $alertasCriticas = DB::table('cumplimiento_temperatura')
            ->where('estado_equipo', 'critico')
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->get();

        // Áreas sin limpieza en las últimas 24 horas
        $areasSinLimpieza = $this->verificarAreasSinLimpieza();

        // Registrar en bitácora
        BitacoraController::registrar(
            'consulta_alertas',
            'cumplimiento_sanitario',
            null,
            null,
            request()
        );

        return view('cumplimiento-sanitario.alertas', compact(
            'alertasTemperatura',
            'alertasCriticas',
            'areasSinLimpieza'
        ));
    }

    /**
     * Exporta registros en formato específico
     */
    public function exportar(Request $request)
    {
        $this->authorize('generar-reportes-sanitarios');

        $validated = $request->validate([
            'tipo' => 'required|in:limpieza,temperatura',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'formato' => 'required|in:pdf,excel,csv',
        ]);

        $tipo = $validated['tipo'];
        $fechaInicio = $validated['fecha_inicio'];
        $fechaFin = $validated['fecha_fin'];
        $formato = $validated['formato'];

        // Obtener registros
        if ($tipo === 'limpieza') {
            $registros = DB::table('cumplimiento_limpieza')
                ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                ->orderBy('fecha_registro', 'desc')
                ->get();
        } else {
            $registros = DB::table('cumplimiento_temperatura')
                ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                ->orderBy('fecha_registro', 'desc')
                ->get();
        }

        // Registrar en bitácora
        BitacoraController::registrar(
            'exportacion',
            'cumplimiento_sanitario',
            null,
            [
                'tipo' => $tipo,
                'formato' => $formato,
                'registros' => $registros->count(),
            ],
            $request
        );

        // TODO: Implementar exportación según formato
        return view('cumplimiento-sanitario.exportar', compact(
            'registros',
            'tipo',
            'formato',
            'fechaInicio',
            'fechaFin'
        ));
    }


    /**
     * Elimina una evidencia fotográfica
     */
    public function eliminarEvidencia($id)
    {
        $this->authorize('registrar-cumplimiento-sanitario');

        $evidencia = DB::table('cumplimiento_evidencias')
            ->where('id', $id)
            ->first();

        if (!$evidencia) {
            return back()->with('error', 'Evidencia no encontrada.');
        }

        // Eliminar archivo físico
        if (Storage::disk('public')->exists($evidencia->ruta_archivo)) {
            Storage::disk('public')->delete($evidencia->ruta_archivo);
        }

        // Eliminar registro
        DB::table('cumplimiento_evidencias')->where('id', $id)->delete();

        // Registrar en bitácora
        BitacoraController::registrar(
            'eliminacion_evidencia',
            'cumplimiento_sanitario',
            $id,
            [
                'tipo_registro' => $evidencia->tipo_registro,
                'registro_id' => $evidencia->registro_id,
            ],
            request()
        );

        return back()->with('success', 'Evidencia eliminada correctamente.');
    }

    /**
     * Calcula estadísticas del día
     */
    private function calcularEstadisticasDia($fecha): array
    {
        $totalLimpiezas = DB::table('cumplimiento_limpieza')
            ->whereDate('fecha_registro', $fecha)
            ->count();

        $totalTemperaturas = DB::table('cumplimiento_temperatura')
            ->whereDate('fecha_registro', $fecha)
            ->count();

        $temperaturasAlerta = DB::table('cumplimiento_temperatura')
            ->whereDate('fecha_registro', $fecha)
            ->where('fuera_rango', true)
            ->count();

        $equiposCriticos = DB::table('cumplimiento_temperatura')
            ->whereDate('fecha_registro', $fecha)
            ->where('estado_equipo', 'critico')
            ->count();

        $areasLimpiadas = DB::table('cumplimiento_limpieza')
            ->whereDate('fecha_registro', $fecha)
            ->distinct('area')
            ->count('area');

        return [
            'total_limpiezas' => $totalLimpiezas,
            'total_temperaturas' => $totalTemperaturas,
            'temperaturas_alerta' => $temperaturasAlerta,
            'equipos_criticos' => $equiposCriticos,
            'areas_limpiadas' => $areasLimpiadas,
            'cumplimiento_limpieza' => $areasLimpiadas >= 5 ? 'completo' : 'parcial',
            'cumplimiento_temperatura' => $totalTemperaturas >= 3 ? 'completo' : 'parcial',
        ];
    }

    /**
     * Calcula estadísticas de un período
     */
    private function calcularEstadisticasPeriodo($fechaInicio, $fechaFin): array
    {
        $totalLimpiezas = DB::table('cumplimiento_limpieza')
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
            ->count();

        $totalTemperaturas = DB::table('cumplimiento_temperatura')
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
            ->count();

        $incumplimientosTemperatura = DB::table('cumplimiento_temperatura')
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
            ->where('fuera_rango', true)
            ->count();

        $diasPeriodo = Carbon::parse($fechaInicio)->diffInDays(Carbon::parse($fechaFin)) + 1;
        $promedioLimpiezasDia = $diasPeriodo > 0 ? round($totalLimpiezas / $diasPeriodo, 2) : 0;
        $promedioTemperaturasDia = $diasPeriodo > 0 ? round($totalTemperaturas / $diasPeriodo, 2) : 0;

        $tasaCumplimiento = $totalTemperaturas > 0 
            ? round((($totalTemperaturas - $incumplimientosTemperatura) / $totalTemperaturas) * 100, 2)
            : 100;

        return [
            'total_limpiezas' => $totalLimpiezas,
            'total_temperaturas' => $totalTemperaturas,
            'incumplimientos_temperatura' => $incumplimientosTemperatura,
            'dias_periodo' => $diasPeriodo,
            'promedio_limpiezas_dia' => $promedioLimpiezasDia,
            'promedio_temperaturas_dia' => $promedioTemperaturasDia,
            'tasa_cumplimiento' => $tasaCumplimiento,
        ];
    }

    /**
     * Verifica alertas activas
     */
    private function verificarAlertas($fecha): array
    {
        $alertas = [];

        // Verificar temperaturas fuera de rango
        $temperaturasAlerta = DB::table('cumplimiento_temperatura')
            ->whereDate('fecha_registro', $fecha)
            ->where('fuera_rango', true)
            ->count();

        if ($temperaturasAlerta > 0) {
            $alertas[] = [
                'tipo' => 'temperatura',
                'nivel' => 'warning',
                'mensaje' => "Hay {$temperaturasAlerta} registro(s) de temperatura fuera de rango.",
            ];
        }

        // Verificar equipos críticos
        $equiposCriticos = DB::table('cumplimiento_temperatura')
            ->whereDate('fecha_registro', $fecha)
            ->where('estado_equipo', 'critico')
            ->count();

        if ($equiposCriticos > 0) {
            $alertas[] = [
                'tipo' => 'equipo_critico',
                'nivel' => 'danger',
                'mensaje' => "Hay {$equiposCriticos} equipo(s) en estado crítico.",
            ];
        }

        // Verificar cumplimiento de limpieza
        $areasLimpiadas = DB::table('cumplimiento_limpieza')
            ->whereDate('fecha_registro', $fecha)
            ->distinct('area')
            ->count('area');

        if ($areasLimpiadas < 5) {
            $alertas[] = [
                'tipo' => 'limpieza_incompleta',
                'nivel' => 'info',
                'mensaje' => "Solo se han limpiado {$areasLimpiadas} áreas hoy. Se recomienda completar todas las áreas.",
            ];
        }

        return $alertas;
    }

    /**
     * Obtiene incumplimientos del período
     */
    private function obtenerIncumplimientos($fechaInicio, $fechaFin): array
    {
        $incumplimientos = [];

        // Temperaturas fuera de rango
        $temperaturasIncumplidas = DB::table('cumplimiento_temperatura')
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
            ->where('fuera_rango', true)
            ->get();

        foreach ($temperaturasIncumplidas as $registro) {
            $incumplimientos[] = [
                'tipo' => 'temperatura',
                'fecha' => $registro->fecha_registro,
                'hora' => $registro->hora_registro,
                'detalle' => "Equipo: {$registro->equipo} - Temperatura: {$registro->temperatura}°C",
                'gravedad' => 'alta',
            ];
        }

        // Equipos en estado crítico
        $equiposCriticos = DB::table('cumplimiento_temperatura')
            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
            ->where('estado_equipo', 'critico')
            ->get();

        foreach ($equiposCriticos as $registro) {
            $incumplimientos[] = [
                'tipo' => 'equipo_critico',
                'fecha' => $registro->fecha_registro,
                'hora' => $registro->hora_registro,
                'detalle' => "Equipo: {$registro->equipo} en estado crítico",
                'gravedad' => 'crítica',
            ];
        }

        return $incumplimientos;
    }

    /**
     * Verifica áreas sin limpieza en las últimas 24 horas
     */
    private function verificarAreasSinLimpieza(): array
    {
        $areasRequeridas = [
            'cocina', 'barra', 'salon', 'banos', 'almacen',
            'refrigeradores', 'equipos', 'mesas', 'pisos', 'paredes'
        ];

        $areasLimpiadas = DB::table('cumplimiento_limpieza')
            ->where('created_at', '>=', now()->subDay())
            ->pluck('area')
            ->unique()
            ->toArray();

        return array_diff($areasRequeridas, $areasLimpiadas);
    }

    /**
     * Genera alerta de temperatura fuera de rango
     */
    private function generarAlertaTemperatura($registroId, $datos): void
    {
        $mensaje = "⚠️ ALERTA SANITARIA: El equipo '{$datos['equipo']}' registró una temperatura de {$datos['temperatura']}°C";
        
        if ($datos['estado_equipo'] === 'critico') {
            $mensaje .= " y está en ESTADO CRÍTICO";
        }

        // Obtener usuarios con permiso para recibir alertas sanitarias
        $usuariosDestino = DB::table('users')
            ->join('model_has_permissions', 'users.id', '=', 'model_has_permissions.model_id')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('permissions.name', 'ver-cumplimiento-sanitario')
            ->where('model_has_permissions.model_type', 'App\\Models\\User')
            ->select('users.*')
            ->distinct()
            ->get();

        foreach ($usuariosDestino as $usuario) {
            DB::table('notificaciones')->insert([
                'tipo' => 'alerta_sanitaria',
                'canal' => 'panel',
                'mensaje' => $mensaje,
                'usuario_destino_id' => $usuario->id,
                'rel_model' => 'cumplimiento_temperatura',
                'rel_id' => $registroId,
                'leido' => false,
            ]);
        }
    }
}
