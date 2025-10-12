<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\EspecialDelDia;
use App\Models\Producto;
use Carbon\Carbon;

class EspecialDelDiaController extends Controller
{
    public function index(Request $request)
    {
        $especiales = EspecialDelDia::with('producto.categoria')
            ->when($request->filled('search'), fn($q) =>
                $q->whereHas('producto', fn($p) =>
                    $p->where('nombre', 'like', '%'.$request->search.'%')))
            ->when($request->filled('estado'), fn($q) =>
                $q->where('activo', $request->estado === 'activo'))
            ->when($request->filled('dia'), fn($q) =>
                $q->where('dia_semana', $request->dia))
            ->orderBy('dia_semana')
            ->orderByDesc('prioridad')
            ->paginate(10)
            ->withQueryString();

        return view('admin.especial_dia.index', compact('especiales'));
    }

    public function create()
    {
        $productos   = Producto::with('categoria')->orderBy('nombre')->get();
        $diasSemana  = $this->diasSemana();
        return view('admin.especial_dia.create', compact('productos', 'diasSemana'));
    }

    public function store(Request $request)
    {
            // 1. Validar datos
            $data = $this->validated($request, false);

            // 2. Verificar conflictos
            if ($this->hayConflictoDiaSemana($request)) {
                return back()->withInput()->withErrors([
                    'dia_semana' => 'Ya existe un especial activo para este día.'
                ]);
            }

            // 3. Crear registro
            EspecialDelDia::create($this->normalizar($data, $request));
            $this->clearCache();

            // 4. Redirigir con éxito
            return redirect()->route('especial_dia.index')
                ->with('success', 'Especial del día creado correctamente.');
    }

    public function show(EspecialDelDia $especiale)
{
    // Cargar relación producto + categoría
    $especiale->load(['producto.categoria']);

    // Pasamos el modelo a la vista con la variable 'especial'
    return view('admin.especial_dia.show', ['especial' => $especiale]);
}

    public function edit(EspecialDelDia $especial)
    {
        $productos  = Producto::with('categoria')->orderBy('nombre')->get();
        $diasSemana = $this->diasSemana();
        return view('admin.especial_dia.show', compact('especial', 'productos', 'diasSemana'));
    }

    public function update(Request $request, EspecialDelDia $especial)
    {
        $data = $this->validated($request, true);

        if ($this->hayConflictoDiaSemana($request, $especial->id)) {
            return back()->withInput()->withErrors(['dia_semana' => 'Ya existe un especial activo para este día.']);
        }

        $especial->update($this->normalizar($data, $request));
        $this->clearCache();

        return redirect()->route('especial_dia.index')->with('success', 'Especial del día actualizado.');
    }

  public function destroy(EspecialDelDia $especiale)
    {
        try {
            \Log::info('Destroy method called for especial: ' . $especiale->id);

            $especiale->delete();

            return redirect()->route('especial_dia.index')
                ->with('success', 'Especial eliminado correctamente');
        } catch (\Exception $e) {
            \Log::error('Error deleting especial: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el especial: ' . $e->getMessage());
        }
    }



    public function toggle(EspecialDelDia $especiale)
    {
        try {
            $especiale->activo = !$especiale->activo;
            $especiale->save();

            return redirect()->route('especial_dia.index')
                ->with('success', 'Estado del especial actualizado correctamente');
        } catch (\Exception $e) {
            \Log::error('Error al cambiar el estado: ' . $e->getMessage());
            return back()->with('error', 'No se pudo cambiar el estado');
        }
    }

    // ------- API sencillas -------
    public function getEspecialHoy()
    {
        $especial = EspecialDelDia::getEspecialHoy();
        if (!$especial) {
            return response()->json(['success' => false, 'message' => 'No hay especial del día configurado']);
        }
        return response()->json(['success' => true, 'especial' => $this->formatEspecialForApi($especial)]);
    }

    public function getEspecialesSemana()
    {
        $especiales = EspecialDelDia::getEspecialesSemana();
        $payload = collect($especiales)->mapWithKeys(fn($esp, $dia) => [$dia => $this->formatEspecialForApi($esp)]);
        return response()->json(['success' => true, 'especiales' => $payload]);
    }

    // ------- Helpers privados compactos ------
    private function validated(Request $request, bool $isUpdate): array
{
    $rules = [
        'producto_id'          => 'required|exists:productos,id',
        'tipo_especial'        => 'required|in:dia_semana,fecha_especifica,rango_fechas',
        'dia_semana'           => 'required_if:tipo_especial,dia_semana|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
        'fecha_especifica'     => 'nullable|date',
        'fecha_inicio'         => 'nullable|date',
        'fecha_fin'            => 'nullable|date',
        'descuento_porcentaje' => 'nullable|numeric|min:1|max:99',
        'precio_especial'      => 'nullable|numeric|min:0',
        'descripcion_especial' => 'nullable|string|max:500',
        'prioridad'            => 'nullable|integer|min:1|max:10',
        'activo'               => 'nullable|boolean',
        'tipo_descuento'       => 'required|in:porcentaje,precio_fijo',
    ];

    $data = $request->validate($rules);

    // Validación manual para descuento
    if ($request->tipo_descuento === 'porcentaje' && !$request->filled('descuento_porcentaje')) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'descuento_porcentaje' => 'El descuento porcentual es requerido.'
        ]);
    }

    if ($request->tipo_descuento === 'precio_fijo' && !$request->filled('precio_especial')) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'precio_especial' => 'El precio especial es requerido.'
        ]);
    }

    return $data;
}

    private function normalizar(array $data, Request $request): array
    {
        // Base
        $out = [
            'producto_id'         => $data['producto_id'],
            'descripcion_especial'=> $data['descripcion_especial'] ?? null,
            'prioridad'           => $data['prioridad'] ?? 1,
            'activo'              => $request->boolean('activo'),
            'dia_semana'          => null,
            'fecha_especifica'    => null,
            'fecha_inicio'        => null,
            'fecha_fin'           => null,
            'descuento_porcentaje'=> null,
            'precio_especial'     => null,
        ];

        // Fechas/periodicidad
        switch ($data['tipo_especial']) {
            case 'dia_semana':
                $out['dia_semana'] = $data['dia_semana'];
                break;
            case 'fecha_especifica':
                $out['fecha_especifica'] = isset($data['fecha_especifica']) ? Carbon::parse($data['fecha_especifica']) : null;
                break;
            case 'rango_fechas':
                $out['fecha_inicio'] = isset($data['fecha_inicio']) ? Carbon::parse($data['fecha_inicio']) : null;
                $out['fecha_fin']    = isset($data['fecha_fin']) ? Carbon::parse($data['fecha_fin']) : null;
                break;
        }

        // Descuento
        $tipo = $data['tipo_descuento'] ?? null;
        if ($tipo === 'porcentaje' && $request->filled('descuento_porcentaje')) {
            $out['descuento_porcentaje'] = (float) $data['descuento_porcentaje'];
        } elseif ($tipo === 'precio_fijo' && $request->filled('precio_especial')) {
            $out['precio_especial'] = (float) $data['precio_especial'];
        }

        return $out;
    }

    private function hayConflictoDiaSemana(Request $request, ?int $exceptId = null): bool
    {
        return $request->tipo_especial === 'dia_semana'
            && $request->boolean('activo')
            && EspecialDelDia::where('dia_semana', $request->dia_semana)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->where('activo', true)
                ->exists();
    }

    private function formatEspecialForApi(EspecialDelDia $e): array
    {
        return [
            'id'            => $e->id,
            'producto'      => [
                'id' => $e->producto->id,
                'nombre' => $e->producto->nombre,
                'precio_original' => $e->producto->precio,
                'imagen_url' => $e->producto->imagen_url,
                'categoria' => $e->producto->categoria->nombre ?? null,
            ],
            'precio_final'  => $e->getPrecioFinal(),
            'descuento_monto' => $e->getDescuentoMonto(),
            'descripcion'   => $e->getDescripcionCompleta(),
            'tiene_descuento' => $e->tieneDescuento(),
            'dia_semana'    => $e->dia_semana,
            'fecha_especifica' => $e->fecha_especifica?->format('Y-m-d'),
            'activo'        => $e->activo,
        ];
    }

    private function diasSemana(): array
    {
        return [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sábado',
            'domingo' => 'Domingo',
        ];
    }

    private function clearCache(): void
    {
        try {
            Cache::forget('especiales_activos');
            Cache::forget('especiales_hoy');
            Cache::forget('especiales_del_dia');
        } catch (\Throwable $e) {
            // Silencioso
        }
    }
}
