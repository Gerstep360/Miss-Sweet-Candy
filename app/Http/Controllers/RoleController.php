<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\BitacoraController;
class RoleController extends BaseController
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        // ver-roles OR gestionar-permisos
        try {
            $this->authorize('ver-roles');
        } catch (AuthorizationException $e) {
            try {
                $this->authorize('gestionar-permisos');
            } catch (AuthorizationException $e2) {
                return $this->denyResponse();
            }
        }

        $query = Role::with('permissions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $roles = $query->get();
            BitacoraController::registrar('ver lista', 'Role', null);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        // crear-roles OR gestionar-permisos
        try {
            $this->authorize('crear-roles');
        } catch (AuthorizationException $e) {
            try {
                $this->authorize('gestionar-permisos');
            } catch (AuthorizationException $e2) {
                return $this->denyResponse();
            }
        }

        $permissions = Permission::orderBy('name')->get();
        BitacoraController::registrar('crear', 'Role', null);
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        // crear-roles OR gestionar-permisos
        try {
            $this->authorize('crear-roles');
        } catch (AuthorizationException $e) {
            try {
                $this->authorize('gestionar-permisos');
            } catch (AuthorizationException $e2) {
                return $this->denyResponse();
            }
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name'       => trim($validated['name']),
            'guard_name' => config('auth.defaults.guard', 'web'),
        ]);

        // Si intenta asignar permisos, requiere asignar-roles OR gestionar-permisos
        $toSync = $request->input('permissions', []);
        if (!empty($toSync)) {
            try {
                $this->authorize('asignar-roles');
            } catch (AuthorizationException $e) {
                try {
                    $this->authorize('gestionar-permisos');
                } catch (AuthorizationException $e2) {
                    return $this->denyResponse();
                }
            }
            $role->syncPermissions($toSync);
        }

        BitacoraController::registrar('creado', 'Role', $role->id);
        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente');
    }

    public function show(Role $role)
    {
        // ver-roles OR gestionar-permisos
        try {
            $this->authorize('ver-roles');
        } catch (AuthorizationException $e) {
            try {
                $this->authorize('gestionar-permisos');
            } catch (AuthorizationException $e2) {
                return $this->denyResponse();
            }
        }

        $users = User::role($role->name)->get();
            BitacoraController::registrar('ver', 'Role', $role->id);
        return view('admin.roles.show', compact('role', 'users'));
    }

    public function edit(Role $role)
    {
        // Puede entrar si tiene al menos una de: editar-roles, asignar-roles, gestionar-permisos
        if (!auth()->user()->can('editar-roles')
            && !auth()->user()->can('asignar-roles')
            && !auth()->user()->can('gestionar-permisos')) {
            return $this->denyResponse();
        }

        $permissions = Permission::orderBy('name')->get();
            BitacoraController::registrar('editar', 'Role', $role->id);
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        // Si cambia el nombre => requiere editar-roles OR gestionar-permisos
        if (trim($validated['name']) !== $role->name) {
            try {
                $this->authorize('editar-roles');
            } catch (AuthorizationException $e) {
                try {
                    $this->authorize('gestionar-permisos');
                } catch (AuthorizationException $e2) {
                    return $this->denyResponse();
                }
            }
        }

        // Si intenta actualizar permisos => requiere asignar-roles OR gestionar-permisos
        $wantsSync = $request->has('permissions');
        if ($wantsSync) {
            try {
                $this->authorize('asignar-roles');
            } catch (AuthorizationException $e) {
                try {
                    $this->authorize('gestionar-permisos');
                } catch (AuthorizationException $e2) {
                    return $this->denyResponse();
                }
            }
        }

        $role->update(['name' => trim($validated['name'])]);

        if ($wantsSync) {
            $role->syncPermissions($request->input('permissions', []));
        }
        BitacoraController::registrar('actualizado', 'Role', $role->id);
        return redirect()->route('roles.index')->with('success', 'Rol actualizado exitosamente');
    }

    public function destroy(Role $role)
    {
        // eliminar-roles OR gestionar-permisos
        try {
            $this->authorize('eliminar-roles');
        } catch (AuthorizationException $e) {
            try {
                $this->authorize('gestionar-permisos');
            } catch (AuthorizationException $e2) {
                return $this->denyResponse();
            }
        }

        $role->delete();
        BitacoraController::registrar('eliminado', 'Role', $role->id);
        return redirect()->route('roles.index')->with('success', 'Rol eliminado exitosamente');
    }

    /**
     * Si hay ruta '403' personalizada, redirige; si no, aborta 403.
     */
    private function denyResponse()
    {
        return app('router')->has('403')
            ? redirect()->route('403')
            : abort(403);
    }
}
