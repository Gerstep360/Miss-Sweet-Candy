<?php

namespace App\Http\Controllers;

use App\Models\Alergeno;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlergenoController extends Controller
{
    /**
     * Mostrar lista de alérgenos
     */
    public function index()
    {
        $alergenos = Alergeno::withCount('productos')
                             ->orderBy('nombre')
                             ->paginate(15);

        return view('alergenos.index', compact('alergenos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('alergenos.create');
    }

    /**
     * Guardar nuevo alérgeno
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:alergenos,nombre',
            'icono' => 'nullable|string|max:50',
            'color' => 'required|in:red,orange,yellow',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre del alérgeno es obligatorio',
            'nombre.unique' => 'Este alérgeno ya existe en el sistema',
            'color.required' => 'Debes seleccionar un color de alerta',
            'color.in' => 'El color debe ser rojo, naranja o amarillo',
        ]);

        try {
            Alergeno::create($validated);

            return redirect()
                ->route('alergenos.index')
                ->with('success', '✅ Alérgeno creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear alérgeno: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', '❌ Error al crear el alérgeno: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Alergeno $alergeno)
    {
        return view('alergenos.edit', compact('alergeno'));
    }

    /**
     * Actualizar alérgeno
     */
    public function update(Request $request, Alergeno $alergeno)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:alergenos,nombre,' . $alergeno->id,
            'icono' => 'nullable|string|max:50',
            'color' => 'required|in:red,orange,yellow',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre del alérgeno es obligatorio',
            'nombre.unique' => 'Este alérgeno ya existe en el sistema',
            'color.required' => 'Debes seleccionar un color de alerta',
        ]);

        try {
            $alergeno->update($validated);

            return redirect()
                ->route('alergenos.index')
                ->with('success', '✅ Alérgeno actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar alérgeno: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', '❌ Error al actualizar el alérgeno: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar alérgeno
     */
    public function destroy(Alergeno $alergeno)
    {
        try {
            $nombre = $alergeno->nombre;
            $productosAfectados = $alergeno->productos()->count();

            if ($productosAfectados > 0) {
                return back()->with('warning', 
                    "⚠️ No se puede eliminar '{$nombre}' porque está asignado a {$productosAfectados} producto(s). Desactívalo en su lugar.");
            }

            $alergeno->delete();

            return redirect()
                ->route('alergenos.index')
                ->with('success', "✅ Alérgeno '{$nombre}' eliminado exitosamente");
        } catch (\Exception $e) {
            Log::error('Error al eliminar alérgeno: ' . $e->getMessage());
            return back()->with('error', '❌ Error al eliminar el alérgeno: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar productos con este alérgeno
     */
    public function productos(Alergeno $alergeno)
    {
        $productos = $alergeno->productos()
                              ->with('categoria')
                              ->paginate(20);

        return view('alergenos.productos', compact('alergeno', 'productos'));
    }

    /**
     * Gestionar alérgenos de un producto específico
     */
    public function gestionarProducto(Producto $producto)
    {
        $alergenos = Alergeno::activos()->orderBy('nombre')->get();
        $alergenosAsignados = $producto->alergenos->pluck('id')->toArray();

        return view('alergenos.gestionar-producto', compact('producto', 'alergenos', 'alergenosAsignados'));
    }

    /**
     * Actualizar alérgenos de un producto
     */
    public function actualizarProducto(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'alergenos' => 'nullable|array',
            'alergenos.*' => 'exists:alergenos,id',
            'niveles_presencia' => 'nullable|array',
            'niveles_presencia.*' => 'in:contiene,puede_contener,trazas',
        ]);

        try {
            DB::beginTransaction();

            $alergenos = $validated['alergenos'] ?? [];
            $niveles = $validated['niveles_presencia'] ?? [];

            // Preparar datos para sync con pivot
            $syncData = [];
            foreach ($alergenos as $index => $alergenoId) {
                $syncData[$alergenoId] = [
                    'nivel_presencia' => $niveles[$index] ?? 'contiene'
                ];
            }

            $producto->alergenos()->sync($syncData);

            DB::commit();

            return redirect()
                ->route('productos.index')
                ->with('success', "✅ Alérgenos del producto '{$producto->nombre}' actualizados exitosamente");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar alérgenos del producto: ' . $e->getMessage());
            return back()->with('error', '❌ Error al actualizar los alérgenos: ' . $e->getMessage());
        }
    }

    /**
     * Toggle estado activo/inactivo
     */
    public function toggleActivo(Alergeno $alergeno)
    {
        try {
            $alergeno->activo = !$alergeno->activo;
            $alergeno->save();

            $estado = $alergeno->activo ? 'activado' : 'desactivado';

            return back()->with('success', "✅ Alérgeno '{$alergeno->nombre}' {$estado} exitosamente");
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del alérgeno: ' . $e->getMessage());
            return back()->with('error', '❌ Error al cambiar el estado del alérgeno');
        }
    }
}
