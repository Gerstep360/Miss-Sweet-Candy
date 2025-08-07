<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::with('permissions');
        
        // Búsqueda
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $roles = $query->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles']);
        
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);
        
        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente');
    }

    public function show(Role $role)
    {
        $users = User::role($role->name)->get();
        return view('admin.roles.show', compact('role', 'users'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => 'required|unique:roles,name,' . $role->id]);
        
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);
        
        return redirect()->route('roles.index')->with('success', 'Rol actualizado exitosamente');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado exitosamente');
    }
}