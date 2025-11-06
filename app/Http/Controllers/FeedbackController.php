<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FeedbackController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource - ADMIN
     */
    public function index(Request $request)
    {
        try {
            $this->authorize('ver-estadisticas-feedback');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        $periodo = $request->get('periodo', '30'); // días
        $tipo = $request->get('tipo', 'todos'); // Filtro por tipo
        $estado = $request->get('estado', 'todos'); // Filtro por estado
        
        // Feedbacks con relaciones
        $query = Feedback::with(['cliente', 'pedido'])->latest('created_at');

        // Filtro por período
        if ($periodo !== 'todo') {
            $query->where('created_at', '>=', now()->subDays($periodo));
        }

        // Filtro por tipo
        if ($tipo !== 'todos') {
            $query->where('tipo', $tipo);
        }

        // Filtro por estado
        if ($estado !== 'todos') {
            $query->where('estado', $estado);
        }

        $feedbacks = $query->paginate(20);

        // Estadísticas generales
        $estadisticas = $this->calcularEstadisticas($periodo);

        return view('admin.feedback.index', compact('feedbacks', 'estadisticas', 'periodo', 'tipo', 'estado'));
    }

    /**
     * Display client's own feedbacks - CLIENTE
     */
    public function misFeedbacks(Request $request)
    {
        try {
            $this->authorize('ver-mis-feedbacks');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        // Solo feedbacks del cliente autenticado
        $feedbacks = Feedback::where('cliente_id', auth()->id())
            ->with(['pedido'])
            ->latest('created_at')
            ->paginate(10);

        return view('admin.feedback.mis-feedbacks', compact('feedbacks'));
    }

    /**
     * Show the form for creating a new resource - CLIENTE
     */
    public function create(Request $request)
    {
        try {
            $this->authorize('crear-feedback');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        // Obtener pedidos completados sin feedback del usuario
        $pedidosSinFeedback = Pedido::where('cliente_id', auth()->id())
            ->where('estado', 'completado')
            ->whereDoesntHave('feedback')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.feedback.create', compact('pedidosSinFeedback'));
    }

    /**
     * Store a newly created resource in storage - CLIENTE
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('crear-feedback');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        // DEBUG: Ver qué datos llegan
        \Log::info('Feedback Store - Request Data:', $request->all());

        // Validar que al menos una calificación esté presente
        $calificaciones = [
            $request->calificacion_comida,
            $request->calificacion_servicio,
            $request->calificacion_ambiente,
            $request->calificacion_precio,
            $request->calificacion_limpieza,
            $request->calificacion_web,
        ];

        $tieneCalificacion = collect($calificaciones)->filter(function($val) {
            return $val !== null && $val > 0;
        })->isNotEmpty();

        if (!$tieneCalificacion) {
            return back()
                ->withErrors(['calificacion' => 'Debes calificar al menos un aspecto con estrellas.'])
                ->withInput();
        }

        $validated = $request->validate([
            'tipo' => 'required|in:general,pedido,servicio,local,web',
            'pedido_id' => 'nullable|exists:pedidos,id',
            'calificacion_comida' => 'nullable|integer|min:0|max:5',
            'calificacion_servicio' => 'nullable|integer|min:0|max:5',
            'calificacion_ambiente' => 'nullable|integer|min:0|max:5',
            'calificacion_precio' => 'nullable|integer|min:0|max:5',
            'calificacion_limpieza' => 'nullable|integer|min:0|max:5',
            'calificacion_web' => 'nullable|integer|min:0|max:5',
            'comentario' => 'nullable|string|max:1000',
            'sugerencias' => 'nullable|string|max:1000',
            'quejas' => 'nullable|string|max:1000',
            'elogios' => 'nullable|string|max:1000',
            'recomendaria' => 'required|in:0,1',
            'frecuencia_visita' => 'required|in:primera_vez,ocasional,frecuente,regular',
        ], [
            'tipo.required' => 'Debes seleccionar el tipo de feedback.',
            'recomendaria.required' => 'Debes indicar si recomendarías nuestro negocio.',
            'recomendaria.in' => 'Debes seleccionar Sí o No.',
            'frecuencia_visita.required' => 'Debes indicar tu frecuencia de visita.',
        ]);

        // DEBUG: Ver datos validados
        \Log::info('Feedback Store - Validated Data:', $validated);

        // Si es de tipo pedido, verificar que el pedido sea del usuario
        if ($request->tipo === 'pedido' && $request->pedido_id) {
            $pedido = Pedido::where('id', $request->pedido_id)
                ->where('cliente_id', auth()->id())
                ->where('estado', 'completado')
                ->first();

            if (!$pedido) {
                return back()
                    ->withErrors(['pedido_id' => 'El pedido no existe, no te pertenece o no está completado.'])
                    ->withInput();
            }

            // Verificar si ya existe feedback para este pedido
            if ($pedido->feedback) {
                return redirect()->route('feedback.show', $pedido->feedback)
                    ->with('info', 'Ya has enviado feedback para este pedido.');
            }
        }

        // Calcular calificación general (promedio de las calificaciones dadas)
        $calificaciones = array_filter([
            $validated['calificacion_comida'] ?? null,
            $validated['calificacion_servicio'] ?? null,
            $validated['calificacion_ambiente'] ?? null,
            $validated['calificacion_precio'] ?? null,
            $validated['calificacion_limpieza'] ?? null,
            $validated['calificacion_web'] ?? null,
        ]);

        $calificacionGeneral = !empty($calificaciones) 
            ? round(array_sum($calificaciones) / count($calificaciones)) 
            : 3;

        // DEBUG: Ver calificación general calculada
        \Log::info('Feedback Store - Calificación General:', ['calificacion' => $calificacionGeneral, 'calificaciones' => $calificaciones]);

        // Crear el feedback
        $feedback = Feedback::create([
            'cliente_id' => auth()->id(),
            'pedido_id' => $request->tipo === 'pedido' ? $validated['pedido_id'] : null,
            'tipo' => $validated['tipo'],
            'calificacion' => $calificacionGeneral,
            'calificacion_comida' => $validated['calificacion_comida'],
            'calificacion_servicio' => $validated['calificacion_servicio'],
            'calificacion_ambiente' => $validated['calificacion_ambiente'],
            'calificacion_precio' => $validated['calificacion_precio'],
            'calificacion_limpieza' => $validated['calificacion_limpieza'],
            'calificacion_web' => $validated['calificacion_web'],
            'comentario' => $validated['comentario'],
            'sugerencias' => $validated['sugerencias'],
            'quejas' => $validated['quejas'],
            'elogios' => $validated['elogios'],
            'recomendaria' => (bool) $validated['recomendaria'], // Convertir a boolean
            'frecuencia_visita' => $validated['frecuencia_visita'],
            'estado' => 'pendiente',
        ]);

        // DEBUG: Feedback creado exitosamente
        \Log::info('Feedback Store - Feedback Creado:', ['id' => $feedback->id, 'cliente_id' => $feedback->cliente_id]);

        return redirect()->route('feedback.show', $feedback)
            ->with('success', '¡Gracias por tu feedback! Nos ayuda a mejorar nuestro servicio.');
    }

    /**
     * Display the specified resource
     */
    public function show(Feedback $feedback)
    {
        // Cliente puede ver su propio feedback
        if (auth()->user()->hasRole('cliente') && $feedback->cliente_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este feedback.');
        }

        // Admin puede ver cualquier feedback
        if (!auth()->user()->hasRole('cliente')) {
            try {
                $this->authorize('ver-estadisticas-feedback');
            } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
                return redirect()->route('403');
            }
        }

        $feedback->load(['cliente', 'pedido.items.producto']);

        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Dashboard de estadísticas - ADMIN
     */
    public function estadisticas(Request $request)
    {
        try {
            $this->authorize('ver-estadisticas-feedback');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('403');
        }

        $periodo = $request->get('periodo', '30');
        
        // Calcular todas las estadísticas
        $estadisticas = $this->calcularEstadisticas($periodo);
        
        // Tendencias por mes (últimos 6 meses)
        $tendencias = $this->calcularTendencias();
        
        // Productos con peor calificación
        $productosPeorCalificados = $this->obtenerProductosPeorCalificados(10);
        
        // Productos con mejor calificación
        $productosMejorCalificados = $this->obtenerProductosMejorCalificados(10);

        return view('admin.feedback.estadisticas', compact(
            'estadisticas',
            'tendencias',
            'productosPeorCalificados',
            'productosMejorCalificados',
            'periodo'
        ));
    }

    /**
     * Calcular estadísticas generales
     */
    private function calcularEstadisticas($periodo)
    {
        $query = Feedback::query();

        if ($periodo !== 'todo') {
            $query->where('created_at', '>=', now()->subDays($periodo));
        }

        $feedbacks = $query->get();
        $total = $feedbacks->count();

        if ($total === 0) {
            return [
                'total' => 0,
                'promedio' => 0,
                'distribucion' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'porcentajes' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'satisfaccion' => 0,
                'insatisfaccion' => 0,
                'neutral' => 0,
                'positivos' => 0,
                'negativos' => 0,
                'por_tipo' => [],
                'recomendarian' => 0,
                'pendientes' => 0,
            ];
        }

        // Calcular promedio de calificación general
        $promedio = round($feedbacks->avg('calificacion'), 2);
        
        $distribucion = [
            1 => $feedbacks->where('calificacion', 1)->count(),
            2 => $feedbacks->where('calificacion', 2)->count(),
            3 => $feedbacks->where('calificacion', 3)->count(),
            4 => $feedbacks->where('calificacion', 4)->count(),
            5 => $feedbacks->where('calificacion', 5)->count(),
        ];

        $porcentajes = [
            1 => round(($distribucion[1] / $total) * 100, 1),
            2 => round(($distribucion[2] / $total) * 100, 1),
            3 => round(($distribucion[3] / $total) * 100, 1),
            4 => round(($distribucion[4] / $total) * 100, 1),
            5 => round(($distribucion[5] / $total) * 100, 1),
        ];

        $satisfaccion = $distribucion[4] + $distribucion[5];
        $insatisfaccion = $distribucion[1] + $distribucion[2];
        $neutral = $distribucion[3];

        // Para las cards del index
        $positivos = $feedbacks->filter(function($f) {
            return $f->calificacion_general >= 4;
        })->count();
        
        $negativos = $feedbacks->filter(function($f) {
            return $f->calificacion_general < 3;
        })->count();

        // Promedios por categoría
        $promedioComida = round($feedbacks->whereNotNull('calificacion_comida')->avg('calificacion_comida'), 2);
        $promedioServicio = round($feedbacks->whereNotNull('calificacion_servicio')->avg('calificacion_servicio'), 2);
        $promedioAmbiente = round($feedbacks->whereNotNull('calificacion_ambiente')->avg('calificacion_ambiente'), 2);
        $promedioPrecio = round($feedbacks->whereNotNull('calificacion_precio')->avg('calificacion_precio'), 2);
        $promedioLimpieza = round($feedbacks->whereNotNull('calificacion_limpieza')->avg('calificacion_limpieza'), 2);
        $promedioWeb = round($feedbacks->whereNotNull('calificacion_web')->avg('calificacion_web'), 2);

        // Distribución por tipo
        $porTipo = [
            'general' => $feedbacks->where('tipo', 'general')->count(),
            'pedido' => $feedbacks->where('tipo', 'pedido')->count(),
            'servicio' => $feedbacks->where('tipo', 'servicio')->count(),
            'local' => $feedbacks->where('tipo', 'local')->count(),
            'web' => $feedbacks->where('tipo', 'web')->count(),
        ];

        // Porcentaje de recomendación
        $recomendarian = round(($feedbacks->where('recomendaria', true)->count() / $total) * 100, 1);

        // Feedbacks pendientes
        $pendientes = $feedbacks->where('estado', 'pendiente')->count();

        return [
            'total' => $total,
            'promedio' => $promedio,
            'distribucion' => $distribucion,
            'porcentajes' => $porcentajes,
            'satisfaccion' => $satisfaccion,
            'insatisfaccion' => $insatisfaccion,
            'neutral' => $neutral,
            'positivos' => $positivos,
            'negativos' => $negativos,
            'porcentaje_satisfaccion' => round(($satisfaccion / $total) * 100, 1),
            'porcentaje_insatisfaccion' => round(($insatisfaccion / $total) * 100, 1),
            'porcentaje_neutral' => round(($neutral / $total) * 100, 1),
            'promedio_comida' => $promedioComida ?: 0,
            'promedio_servicio' => $promedioServicio ?: 0,
            'promedio_ambiente' => $promedioAmbiente ?: 0,
            'promedio_precio' => $promedioPrecio ?: 0,
            'promedio_limpieza' => $promedioLimpieza ?: 0,
            'promedio_web' => $promedioWeb ?: 0,
            'por_tipo' => $porTipo,
            'recomendarian' => $recomendarian,
            'pendientes' => $pendientes,
        ];
    }

    /**
     * Calcular tendencias mensuales
     */
    private function calcularTendencias()
    {
        $meses = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $inicio = now()->subMonths($i)->startOfMonth();
            $fin = now()->subMonths($i)->endOfMonth();
            
            $feedbacks = Feedback::join('pedidos', 'feedbacks.pedido_id', '=', 'pedidos.id')
                ->whereBetween('pedidos.created_at', [$inicio, $fin])
                ->select('feedbacks.*')
                ->get();
            
            $total = $feedbacks->count();
            $promedio = $total > 0 ? round($feedbacks->avg('calificacion'), 2) : 0;
            
            $meses[] = [
                'mes' => $inicio->format('M Y'),
                'mes_numero' => $inicio->format('Y-m'),
                'total' => $total,
                'promedio' => $promedio,
            ];
        }
        
        return $meses;
    }

    /**
     * Obtener productos con peor calificación
     */
    private function obtenerProductosPeorCalificados($limit = 10)
    {
        return DB::table('feedbacks')
            ->join('pedidos', 'feedbacks.pedido_id', '=', 'pedidos.id')
            ->join('pedido_items', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
            ->select(
                'productos.id',
                'productos.nombre',
                DB::raw('AVG(feedbacks.calificacion) as promedio'),
                DB::raw('COUNT(DISTINCT feedbacks.id) as total_feedbacks')
            )
            ->where('feedbacks.calificacion', '<=', 3)
            ->groupBy('productos.id', 'productos.nombre')
            ->having('total_feedbacks', '>=', 3)
            ->orderBy('promedio', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener productos con mejor calificación
     */
    private function obtenerProductosMejorCalificados($limit = 10)
    {
        return DB::table('feedbacks')
            ->join('pedidos', 'feedbacks.pedido_id', '=', 'pedidos.id')
            ->join('pedido_items', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
            ->select(
                'productos.id',
                'productos.nombre',
                DB::raw('AVG(feedbacks.calificacion) as promedio'),
                DB::raw('COUNT(DISTINCT feedbacks.id) as total_feedbacks')
            )
            ->where('feedbacks.calificacion', '>=', 4)
            ->groupBy('productos.id', 'productos.nombre')
            ->having('total_feedbacks', '>=', 3)
            ->orderBy('promedio', 'desc')
            ->limit($limit)
            ->get();
    }
}
