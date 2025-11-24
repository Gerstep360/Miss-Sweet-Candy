<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\LoginIntento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\BackupService;
use App\Services\AnomaliaService;

class AuditoriaController extends Controller
{
    protected $backupService;
    protected $anomaliaService;

    public function __construct(BackupService $backupService, AnomaliaService $anomaliaService)
    {
        $this->backupService = $backupService;
        $this->anomaliaService = $anomaliaService;
    }

    /**
     * Muestra la lista de auditorías con filtros
     */
    public function index(Request $request)
    {
        // 🔒 Solo administradores pueden acceder
        if (!auth()->check() || !auth()->user()->hasRole('administrador')) {
            return redirect()->route('403');
        }

        $query = Auditoria::with('usuario');

        // Filtros
        if ($request->filled('usuario_id')) {
            $query->porUsuario($request->usuario_id);
        }

        if ($request->filled('accion')) {
            $query->porAccion($request->accion);
        }

        if ($request->filled('entidad')) {
            $query->porEntidad($request->entidad);
        }

        if ($request->filled('ip')) {
            $query->porIp($request->ip);
        }

        if ($request->filled('desde') && $request->filled('hasta')) {
            $desde = Carbon::parse($request->desde)->startOfDay();
            $hasta = Carbon::parse($request->hasta)->endOfDay();
            $query->porRangoFechas($desde, $hasta);
        } elseif ($request->filled('desde')) {
            $desde = Carbon::parse($request->desde)->startOfDay();
            $query->where('created_at', '>=', $desde);
        } elseif ($request->filled('hasta')) {
            $hasta = Carbon::parse($request->hasta)->endOfDay();
            $query->where('created_at', '<=', $hasta);
        }

        // Ordenar por fecha más reciente
        $query->orderByDesc('created_at');

        // Paginación con límite de seguridad
        $auditorias = $query->paginate(50);

        // Advertencia si hay demasiados resultados
        if ($auditorias->total() > 10000) {
            session()->flash('warning', 'Demasiados resultados (' . number_format($auditorias->total()) . '). Por favor, refine su búsqueda o exporte para análisis completo.');
        }

        // Datos para filtros
        $usuarios = User::orderBy('name')->get();
        $acciones = Auditoria::distinct()->pluck('accion')->sort()->values();
        $entidades = Auditoria::distinct()->pluck('entidad')->sort()->values();

        // Registrar acceso
        BitacoraController::registrar('ver', 'Auditoria', null);

        return view('admin.auditoria.index', compact('auditorias', 'usuarios', 'acciones', 'entidades'));
    }

    /**
     * Muestra el detalle de una auditoría
     */
    public function show($id)
    {
        // 🔒 Solo administradores pueden acceder
        if (!auth()->check() || !auth()->user()->hasRole('administrador')) {
            return redirect()->route('403');
        }

        $auditoria = Auditoria::with('usuario')->findOrFail($id);

        // Obtener registros relacionados (mismo usuario, misma entidad, etc.)
        $relacionados = Auditoria::where('id', '!=', $id)
            ->where(function($q) use ($auditoria) {
                $q->where('usuario_id', $auditoria->usuario_id)
                  ->orWhere('entidad', $auditoria->entidad)
                  ->orWhere('ip', $auditoria->ip);
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        BitacoraController::registrar('ver detalle', 'Auditoria', $id);

        return view('admin.auditoria.show', compact('auditoria', 'relacionados'));
    }

    /**
     * Exporta los registros de auditoría a PDF
     */
    public function exportarPdf(Request $request)
    {
        // 🔒 Solo administradores pueden exportar
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect()->route('403');
        }

        // Aplicar mismos filtros que index
        $query = Auditoria::with('usuario');
        
        // ... aplicar filtros igual que en index ...
        
        $auditorias = $query->limit(10000)->get();

        // TODO: Implementar generación de PDF usando DomPDF o similar
        // Por ahora retornamos JSON como fallback
        return response()->json([
            'message' => 'Exportación PDF pendiente de implementar',
            'registros' => $auditorias->count()
        ]);
    }

    /**
     * Exporta los registros de auditoría a Excel
     */
    public function exportarExcel(Request $request)
    {
        // 🔒 Solo administradores pueden exportar
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect()->route('403');
        }

        // Aplicar mismos filtros que index
        $query = Auditoria::with('usuario');
        
        // ... aplicar filtros igual que en index ...
        
        $auditorias = $query->limit(10000)->get();

        // TODO: Implementar generación de Excel usando Maatwebsite/Excel
        // Por ahora retornamos JSON como fallback
        return response()->json([
            'message' => 'Exportación Excel pendiente de implementar',
            'registros' => $auditorias->count()
        ]);
    }

    /**
     * Muestra la gestión de backups
     */
    public function backups(Request $request)
    {
        // 🔒 Solo administradores pueden acceder
        if (!auth()->check() || !auth()->user()->hasRole('administrador')) {
            return redirect()->route('403');
        }

        $backups = $this->backupService->listarBackups();
        $configuracion = $this->backupService->obtenerConfiguracion();

        BitacoraController::registrar('ver backups', 'Backup', null);

        return view('admin.auditoria.backups', compact('backups', 'configuracion'));
    }

    /**
     * Crea un backup manual
     */
    public function crearBackup(Request $request)
    {
        // 🔒 Solo administradores pueden crear backups
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect()->route('403');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:completo,base_datos,archivos',
        ]);

        try {
            $resultado = $this->backupService->crearBackup($validated['tipo']);

            BitacoraController::registrar('crear backup', 'Backup', null);

            return back()->with('success', 'Backup creado exitosamente: ' . $resultado['archivo']);
        } catch (\Exception $e) {
            Log::error('Error al crear backup: ' . $e->getMessage());
            return back()->with('error', 'Error al crear backup: ' . $e->getMessage());
        }
    }

    /**
     * Descarga un backup
     */
    public function descargarBackup($archivo)
    {
        // 🔒 Solo administradores pueden descargar backups
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect()->route('403');
        }

        try {
            $ruta = $this->backupService->obtenerRutaBackup($archivo);
            
            if (!Storage::exists($ruta)) {
                abort(404, 'Backup no encontrado');
            }

            BitacoraController::registrar('descargar backup', 'Backup', null);

            return Storage::download($ruta);
        } catch (\Exception $e) {
            Log::error('Error al descargar backup: ' . $e->getMessage());
            return back()->with('error', 'Error al descargar backup: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un backup
     */
    public function eliminarBackup(Request $request, $archivo)
    {
        // 🔒 Solo administradores pueden eliminar backups
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect()->route('403');
        }

        try {
            $this->backupService->eliminarBackup($archivo);

            BitacoraController::registrar('eliminar backup', 'Backup', null);

            return back()->with('success', 'Backup eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar backup: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar backup: ' . $e->getMessage());
        }
    }

    /**
     * Restaura desde un backup
     */
    public function restaurarBackup(Request $request, $archivo)
    {
        // 🔒 Solo administradores pueden restaurar backups
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect()->route('403');
        }

        $validated = $request->validate([
            'confirmar' => 'required|accepted',
            'crear_backup_actual' => 'nullable|boolean',
        ]);

        try {
            // Crear backup del estado actual si se solicita
            if ($validated['crear_backup_actual'] ?? false) {
                $this->backupService->crearBackup('completo');
            }

            $resultado = $this->backupService->restaurarBackup($archivo);

            BitacoraController::registrar('restaurar backup', 'Backup', null);

            return back()->with('success', 'Backup restaurado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al restaurar backup: ' . $e->getMessage());
            return back()->with('error', 'Error al restaurar backup: ' . $e->getMessage());
        }
    }

    /**
     * Configura backups automáticos
     */
    public function configurarBackups(Request $request)
    {
        // 🔒 Solo administradores pueden configurar backups
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect()->route('403');
        }

        $validated = $request->validate([
            'frecuencia' => 'required|in:diaria,semanal,mensual',
            'hora' => 'required|date_format:H:i',
            'tipo' => 'required|in:completo,base_datos,archivos',
            'retencion' => 'required|integer|min:1|max:365',
            'notificar_email' => 'nullable|boolean',
        ]);

        try {
            $this->backupService->configurarAutomatico($validated);

            BitacoraController::registrar('configurar backups', 'Backup', null);

            return back()->with('success', 'Configuración de backups automáticos guardada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al configurar backups: ' . $e->getMessage());
            return back()->with('error', 'Error al configurar backups: ' . $e->getMessage());
        }
    }

    /**
     * Muestra las anomalías detectadas
     */
    public function anomalias(Request $request)
    {
        // 🔒 Solo administradores pueden ver anomalías
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect()->route('403');
        }

        $anomalias = $this->anomaliaService->obtenerAnomalias($request->all());

        BitacoraController::registrar('ver anomalias', 'Anomalia', null);

        return view('admin.auditoria.anomalias', compact('anomalias'));
    }

    /**
     * Ejecuta detección de anomalías manualmente
     */
    public function detectarAnomalias()
    {
        // 🔒 Solo administradores pueden ejecutar detección
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect()->route('403');
        }

        try {
            $anomalias = $this->anomaliaService->detectarAnomalias();

            BitacoraController::registrar('detectar anomalias', 'Anomalia', null);

            return back()->with('success', 'Detección completada. Se encontraron ' . count($anomalias) . ' anomalías.');
        } catch (\Exception $e) {
            Log::error('Error al detectar anomalías: ' . $e->getMessage());
            return back()->with('error', 'Error al detectar anomalías: ' . $e->getMessage());
        }
    }

    /**
     * Muestra intentos de login
     */
    public function intentosLogin(Request $request)
    {
        // 🔒 Solo administradores pueden ver intentos de login
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect()->route('403');
        }

        $query = LoginIntento::query();

        if ($request->filled('email')) {
            $query->porEmail($request->email);
        }

        if ($request->filled('ip')) {
            $query->porIp($request->ip);
        }

        if ($request->filled('exitoso')) {
            $query->where('exitoso', $request->exitoso === '1');
        }

        if ($request->filled('desde') && $request->filled('hasta')) {
            $desde = Carbon::parse($request->desde)->startOfDay();
            $hasta = Carbon::parse($request->hasta)->endOfDay();
            $query->whereBetween('created_at', [$desde, $hasta]);
        }

        $intentos = $query->orderByDesc('created_at')->paginate(50);

        BitacoraController::registrar('ver intentos login', 'LoginIntento', null);

        return view('admin.auditoria.intentos-login', compact('intentos'));
    }
}

