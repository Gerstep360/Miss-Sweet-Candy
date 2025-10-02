<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\UserCreated;
use App\Http\Controllers\BitacoraController;
class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');
        
        // Búsqueda
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por rol
        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        $users = $query->paginate(10);
        $roles = Role::all();
            BitacoraController::registrar('ver lista', 'User', null);
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
            BitacoraController::registrar('crear', 'User', null);
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|exists:roles,name',
        ]);

        // Generar token temporal para el usuario creado por admin
        $temporalToken = Str::random(32);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(12)), // Password temporal
            'temporal_token' => $temporalToken,
            'password_set' => false, // Usuario creado por admin necesita establecer contraseña
        ]);

        // Asignar rol
        $user->assignRole($request->role);

        // Enviar email de bienvenida
        Mail::to($user->email)->send(new UserCreated($user, $temporalToken));
            BitacoraController::registrar('creado', 'User', $user->id);
        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente. Se ha enviado un email de bienvenida.');
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
            BitacoraController::registrar('editar', 'User', $user->id);
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Actualizar rol
        $user->syncRoles([$request->role]);
            BitacoraController::registrar('actualizado', 'User', $user->id);
        return redirect()->route('users.show', $user)->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(User $user)
    {
        $user->delete();
            BitacoraController::registrar('eliminado', 'User', $user->id);
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente');
    }

    // Método para activar cuenta desde el email
    public function activateAccount($token)
    {
        $user = User::where('temporal_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token inválido o expirado');
        }
        BitacoraController::registrar('activar cuenta', 'User', $user->id);
        return view('auth.set-password', compact('user', 'token'));
    }

    // Método para establecer contraseña
    public function setPassword(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('temporal_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token inválido o expirado');
        }

        $user->update([
            'password' => Hash::make($request->password),
            'temporal_token' => null,
            'password_set' => true,
            'email_verified_at' => now(),
        ]);
        BitacoraController::registrar('establecer contraseña', 'User', $user->id);
        return redirect()->route('login')->with('success', 'Contraseña establecida exitosamente. Ya puedes iniciar sesión.');
    }
}