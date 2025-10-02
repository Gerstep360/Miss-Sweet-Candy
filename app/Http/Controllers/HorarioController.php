<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Horario;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Controllers\BitacoraController;
class HorarioController extends BaseController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->authorize('ver-horarios');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $horarios = Horario::orderByRaw("FIELD(dia, 'lunes','martes','miércoles','jueves','viernes','sábado','domingo')")->get();
        BitacoraController::registrar('ver lista', 'Horario', null);
        return view('admin.horarios.index', compact('horarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $this->authorize('configurar-horarios');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }
        BitacoraController::registrar('crear', 'Horario', null);
        return view('admin.horarios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('configurar-horarios');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $request->validate([
            'dia'    => 'required|string|max:15|unique:horarios,dia',
            'abre'   => 'required|date_format:H:i',
            'cierra' => 'required|date_format:H:i|after:abre',
        ]);

        Horario::create($request->only('dia', 'abre', 'cierra'));
        BitacoraController::registrar('creado', 'Horario', null);
        return redirect()->route('horarios.index')->with('success', 'Horario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $this->authorize('ver-horarios');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $horario = Horario::findOrFail($id);
        BitacoraController::registrar('ver', 'Horario', $horario->id);
        return view('horarios.show', compact('horario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $this->authorize('configurar-horarios');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $horario = Horario::findOrFail($id);
        BitacoraController::registrar('editar', 'Horario', $horario->id);
        return view('admin.horarios.edit', compact('horario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->authorize('configurar-horarios');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $horario = Horario::findOrFail($id);

        $request->validate([
            'dia'    => 'required|string|max:15|unique:horarios,dia,' . $horario->id,
            'abre'   => 'required|date_format:H:i',
            'cierra' => 'required|date_format:H:i|after:abre',
        ]);

        $horario->update($request->only('dia', 'abre', 'cierra'));
        BitacoraController::registrar('actualizado', 'Horario', $horario->id);
        return redirect()->route('horarios.index')->with('success', 'Horario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->authorize('configurar-horarios');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $horario = Horario::findOrFail($id);
        $horario->delete();
        BitacoraController::registrar('eliminado', 'Horario', $horario->id);
        return redirect()->route('horarios.index')->with('success', 'Horario eliminado correctamente.');
    }
}
