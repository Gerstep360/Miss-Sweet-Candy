<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesa;
class MesaController extends Controller
{
    // Listar todas las mesas
    public function index()
    {
        $mesas = Mesa::with('fusionadas')->get();
        return view('cajero.mesas.index', compact('mesas'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $mesas = Mesa::all();
        return view('cajero.mesas.create', compact('mesas'));
    }

    // Guardar nueva mesa
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:mesas,nombre|max:30',
            'estado' => 'required|in:libre,ocupada,reservada,fusionada',
            'capacidad' => 'required|integer|min:1',
            'fusion_id' => 'nullable|exists:mesas,id',
        ]);

        $data = $request->all();

        // Si NO tiene permiso para fusionar, fuerza fusion_id a null y estado a no fusionada
        if (!auth()->user()->can('fusionar-mesas')) {
            $data['fusion_id'] = null;
            if ($data['estado'] === 'fusionada') {
                $data['estado'] = 'libre'; // o el estado que prefieras
            }
        }

        Mesa::create($data);
        return redirect()->route('mesas.index')->with('success', 'Mesa creada correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(Mesa $mesa)
    {
        if (!auth()->user()->can('editar-mesas')) {
            abort(403);
        }
        $mesas = Mesa::where('id', '!=', $mesa->id)->get();
        return view('cajero.mesas.edit', compact('mesa', 'mesas'));
    }

    // Actualizar mesa
    public function update(Request $request, Mesa $mesa)
    {
        $request->validate([
            'nombre' => 'required|max:30|unique:mesas,nombre,' . $mesa->id,
            'estado' => 'required|in:libre,ocupada,reservada,fusionada',
            'capacidad' => 'required|integer|min:1',
            'fusion_id' => 'nullable|exists:mesas,id',
        ]);

        $data = $request->all();

        // Si NO tiene permiso para fusionar, no permitas cambiar fusion_id ni estado a fusionada
        if (!auth()->user()->can('fusionar-mesas')) {
            // Si la mesa ya estaba fusionada, mantenemos su estado y fusion_id
            if ($mesa->estado === 'fusionada') {
                $data['fusion_id'] = $mesa->fusion_id;
                $data['estado'] = $mesa->estado;
            } else {
                $data['fusion_id'] = null;
                if ($data['estado'] === 'fusionada') {
                    $data['estado'] = 'libre'; // o el estado que prefieras
                }
            }
        }

        $mesa->update($data);
        return redirect()->route('mesas.index')->with('success', 'Mesa actualizada correctamente.');
    }

    // Eliminar mesa
    public function destroy(Mesa $mesa)
    {
        $mesa->delete();
        return redirect()->route('mesas.index')->with('success', 'Mesa eliminada correctamente.');
    }

    // Cambiar estado de la mesa
    public function cambiarEstado(Request $request, Mesa $mesa)
    {
        $request->validate([
            'estado' => 'required|in:libre,ocupada,reservada,fusionada',
        ]);
        $mesa->estado = $request->estado;
        $mesa->save();
        return redirect()->route('mesas.index')->with('success', 'Estado de la mesa actualizado.');
    }

    // Fusión de mesas
    public function fusionar(Request $request)
    {
        if (!auth()->user()->can('fusionar-mesas')) {
            abort(403);
        }

        $request->validate([
            'mesa_principal_id' => 'required|exists:mesas,id',
            'mesa_fusionada_id' => 'required|exists:mesas,id|different:mesa_principal_id',
        ]);

        $mesaPrincipal = Mesa::findOrFail($request->mesa_principal_id);
        $mesaFusionada = Mesa::findOrFail($request->mesa_fusionada_id);

        // Cambia estado y referencia de la fusionada
        $mesaFusionada->estado = 'fusionada';
        $mesaFusionada->fusion_id = $mesaPrincipal->id;
        $mesaFusionada->save();

        // Suma la capacidad a la principal
        $mesaPrincipal->capacidad += $mesaFusionada->capacidad;
        $mesaPrincipal->estado = 'ocupada'; // o 'fusionada' si prefieres
        $mesaPrincipal->save();

        return redirect()->route('mesas.index')->with('success', 'Mesas fusionadas correctamente.');
    }
}
