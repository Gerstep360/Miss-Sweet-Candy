<?php

namespace App\Http\Controllers\Usuarios;

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
        // Verificar permiso
        if (!auth()->check() || !auth()->user()->can('ver-alergenos')) {
            abort(403, 'No tienes permiso para ver los alérgenos.');
        }

        $alergenos = Alergeno::withCount('productos')
                             ->orderBy('nombre')
                             ->paginate(15);

        // Registrar en bitácora
        BitacoraController::registrar('alergenos_listados', 'alergeno', null, auth()->id());

        return view('alergenos.index', compact('alergenos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        // Verificar permiso
        if (!auth()->check() || !auth()->user()->can('crear-alergenos')) {
            abort(403, 'No tienes permiso para crear alérgenos.');
        }

        // Registrar en bitácora
        BitacoraController::registrar('alergeno_create_view', 'alergeno', null, auth()->id());

        return view('alergenos.create');
    }

    /**
     * Guardar nuevo alérgeno
     */
    public function store(Request $request)
    {
        // Verificar permiso
        if (!auth()->check() || !auth()->user()->can('crear-alergenos')) {
            abort(403, 'No tienes permiso para crear alérgenos.');
        }

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
            $alergeno = Alergeno::create($validated);

            // Registrar en bitácora
            BitacoraController::registrar('alergeno_creado', 'alergeno', $alergeno->id, auth()->id());

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
        // Verificar permiso
        if (!auth()->check() || !auth()->user()->can('editar-alergenos')) {
            abort(403, 'No tienes permiso para editar alérgenos.');
        }

        // Registrar en bitácora
        BitacoraController::registrar('alergeno_edit_view', 'alergeno', $alergeno->id, auth()->id());

        return view('alergenos.edit', compact('alergeno'));
    }

    /**
     * Actualizar alérgeno
     */
    public function update(Request $request, Alergeno $alergeno)
    {
        // Verificar permiso
        if (!auth()->check() || !auth()->user()->can('editar-alergenos')) {
            abort(403, 'No tienes permiso para editar alérgenos.');
        }

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

            // Registrar en bitácora
            BitacoraController::registrar('alergeno_actualizado', 'alergeno', $alergeno->id, auth()->id());

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
        // Verificar permiso
        if (!auth()->check() || !auth()->user()->can('eliminar-alergenos')) {
            abort(403, 'No tienes permiso para eliminar alérgenos.');
        }

        try {
            $nombre = $alergeno->nombre;
            $alergenoId = $alergeno->id;
            $productosAfectados = $alergeno->productos()->count();

            if ($productosAfectados > 0) {
                return back()->with('warning', 
                    "⚠️ No se puede eliminar '{$nombre}' porque está asignado a {$productosAfectados} producto(s). Desactívalo en su lugar.");
            }

            $alergeno->delete();

            // Registrar en bitácora
            BitacoraController::registrar('alergeno_eliminado', 'alergeno', $alergenoId, auth()->id());

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
        // Verificar permiso
        if (!auth()->check() || !auth()->user()->can('ver-alergenos')) {
            abort(403, 'No tienes permiso para ver los alérgenos.');
        }

        $productos = $alergeno->productos()
                              ->with('categoria')
                              ->paginate(20);

        // Registrar en bitácora
        BitacoraController::registrar('alergeno_productos_listados', 'alergeno', $alergeno->id, auth()->id());

        return view('alergenos.productos', compact('alergeno', 'productos'));
    }

    /**
     * Gestionar alérgenos de un producto específico
     */
    public function gestionarProducto(Producto $producto)
    {
        // Verificar permiso
        if (!auth()->check() || !auth()->user()->can('gestionar-alergenos-productos')) {
            abort(403, 'No tienes permiso para gestionar alérgenos de productos.');
        }

        $alergenos = Alergeno::activos()->orderBy('nombre')->get();
        $alergenosAsignados = $producto->alergenos->pluck('id')->toArray();

        // Registrar en bitácora
        BitacoraController::registrar('alergenos_producto_view', 'producto', $producto->id, auth()->id());

        return view('alergenos.gestionar-producto', compact('producto', 'alergenos', 'alergenosAsignados'));
    }

    /**
     * Actualizar alérgenos de un producto
     */
    public function actualizarProducto(Request $request, Producto $producto)
    {
        // Verificar permiso
        if (!auth()->check() || !auth()->user()->can('gestionar-alergenos-productos')) {
            abort(403, 'No tienes permiso para gestionar alérgenos de productos.');
        }

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

            // Registrar en bitácora
            BitacoraController::registrar('alergenos_producto_actualizados', 'producto', $producto->id, auth()->id());

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
        // Verificar permiso
        if (!auth()->check() || !auth()->user()->can('editar-alergenos')) {
            abort(403, 'No tienes permiso para cambiar el estado de alérgenos.');
        }

        try {
            $alergeno->activo = !$alergeno->activo;
            $alergeno->save();

            $estado = $alergeno->activo ? 'activado' : 'desactivado';

            // Registrar en bitácora
            BitacoraController::registrar('alergeno_estado_cambiado', 'alergeno', $alergeno->id, auth()->id());

            return back()->with('success', "✅ Alérgeno '{$alergeno->nombre}' {$estado} exitosamente");
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del alérgeno: ' . $e->getMessage());
            return back()->with('error', '❌ Error al cambiar el estado del alérgeno');
        }
    }
}
