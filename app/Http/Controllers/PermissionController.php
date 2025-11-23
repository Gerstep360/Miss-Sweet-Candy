<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Controllers\BitacoraController;
class PermissionController extends BaseController
{
    use AuthorizesRequests;

    public function index()
    {
        try {
            $this->authorize('gestionar-permisos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $permissions = Permission::orderBy('name')->get();
            BitacoraController::registrar('ver lista', 'Permission', null);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        try {
            $this->authorize('gestionar-permisos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }
            BitacoraController::registrar('crear', 'Permission', null);
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('gestionar-permisos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);
        BitacoraController::registrar('creado', 'Permission', null);
        return redirect()->route('permissions.index')->with('success', 'Permiso creado');
    }

    public function edit(Permission $permission)
    {
        try {
            $this->authorize('gestionar-permisos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }
            BitacoraController::registrar('editar', 'Permission', $permission->id);
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        try {
            $this->authorize('gestionar-permisos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);
        BitacoraController::registrar('actualizado', 'Permission', $permission->id);
        return redirect()->route('permissions.index')->with('success', 'Permiso actualizado');
    }

    public function destroy(Permission $permission)
    {
        try {
            $this->authorize('gestionar-permisos');
        } catch (AuthorizationException $e) {
            return redirect()->route('403');
        }

        $permission->delete();
        BitacoraController::registrar('eliminado', 'Permission', $permission->id);
        return redirect()->route('permissions.index')->with('success', 'Permiso eliminado');
    }
}
