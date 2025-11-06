<?php

namespace App\Http\Controllers;

use App\Models\ClientePerfil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ClientePerfilController extends Controller
{
    /**
     * Mostrar el perfil del cliente autenticado
     */
    public function show()
    {
        $user = Auth::user();
        $perfil = $user->perfil;

        // Si no existe perfil, crear uno vacío
        if (!$perfil) {
            $perfil = $user->obtenerOCrearPerfil();
        }

        return view('perfil.show', [
            'user' => $user,
            'perfil' => $perfil,
            'preferenciasDisponibles' => ClientePerfil::preferenciasDisponibles(),
            'nivelesSeверidad' => ClientePerfil::nivelesSeверidad(),
        ]);
    }

    /**
     * Mostrar formulario de edición del perfil
     */
    public function edit()
    {
        $user = Auth::user();
        $perfil = $user->perfil;

        // Si no existe perfil, crear uno vacío
        if (!$perfil) {
            $perfil = $user->obtenerOCrearPerfil();
        }

        return view('perfil.edit', [
            'user' => $user,
            'perfil' => $perfil,
            'preferenciasDisponibles' => ClientePerfil::preferenciasDisponibles(),
            'nivelesSeверidad' => ClientePerfil::nivelesSeверidad(),
        ]);
    }

    /**
     * Actualizar el perfil del cliente
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            // Validación
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'telefono' => 'nullable|string|max:30',
                'direccion' => 'nullable|string|max:200',
                'preferencias' => 'nullable|array',
                'preferencias.*' => 'string|in:' . implode(',', array_keys(ClientePerfil::preferenciasDisponibles())),
                'alergias' => 'nullable|array',
                'alergias.*.nombre' => 'required|string|max:100',
                'alergias.*.severidad' => 'required|string|in:leve,moderado,grave',
                'acepta_marketing' => 'nullable|boolean',
            ], [
                'name.required' => 'El nombre es obligatorio',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El formato del email no es válido',
                'email.unique' => 'Este email ya está registrado',
                'preferencias.*.in' => 'Preferencia alimentaria no válida',
                'alergias.*.nombre.required' => 'El nombre de la alergia es obligatorio',
                'alergias.*.severidad.required' => 'El nivel de severidad es obligatorio',
                'alergias.*.severidad.in' => 'El nivel de severidad debe ser: leve, moderado o grave',
            ]);

            DB::beginTransaction();

            try {
                // Actualizar datos del usuario
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);

                // Obtener o crear perfil
                $perfil = $user->perfil ?? $user->obtenerOCrearPerfil();

                // Preparar alergias (asegurar estructura correcta)
                $alergias = [];
                if (!empty($validated['alergias'])) {
                    foreach ($validated['alergias'] as $alergia) {
                        if (!empty($alergia['nombre'])) {
                            $alergias[] = [
                                'nombre' => $alergia['nombre'],
                                'severidad' => $alergia['severidad'] ?? 'leve'
                            ];
                        }
                    }
                }

                // Actualizar perfil
                $perfil->update([
                    'telefono' => $validated['telefono'] ?? null,
                    'direccion' => $validated['direccion'] ?? null,
                    'preferencias' => $validated['preferencias'] ?? [],
                    'alergias' => $alergias,
                    'acepta_marketing' => $validated['acepta_marketing'] ?? false,
                ]);

                DB::commit();

                Log::info('Perfil de cliente actualizado', [
                    'user_id' => $user->id,
                    'tiene_alergias' => count($alergias) > 0,
                    'tiene_preferencias' => count($validated['preferencias'] ?? []) > 0,
                ]);

                return redirect()->route('perfil.show')
                    ->with('success', '✅ Perfil actualizado correctamente');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', '⚠️ Por favor corrige los errores en el formulario');

        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil de cliente', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', '❌ Error al actualizar el perfil. Por favor intenta nuevamente.');
        }
    }

    /**
     * Consultar perfil de un cliente (para cajeros)
     * CU22 - Permitir al cajero consultar perfil durante el pedido
     */
    public function consultar($userId)
    {
        // Verificar permiso
        if (!Auth::user()->can('consultar-perfil-cliente')) {
            abort(403, 'No tienes permisos para consultar perfiles de clientes');
        }

        $user = User::findOrFail($userId);
        $perfil = $user->perfil;

        // Si no existe perfil, retornar vacío
        if (!$perfil) {
            return response()->json([
                'success' => false,
                'message' => 'El cliente no tiene perfil registrado',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nombre' => $user->name,
                'email' => $user->email,
                'telefono' => $perfil->telefono,
                'direccion' => $perfil->direccion,
                'preferencias' => $perfil->preferencias ?? [],
                'alergias' => $perfil->alergias ?? [],
                'tiene_alergias' => $perfil->tieneAlergias(),
                'tiene_alergias_graves' => $perfil->tieneAlergiasGraves(),
                'alergias_graves' => $perfil->alergias_graves,
                'alergias_moderadas' => $perfil->alergias_moderadas,
                'alergias_leves' => $perfil->alergias_leves,
            ]
        ]);
    }

    /**
     * Vista de consulta de perfil para cajeros
     */
    public function consultarVista($userId)
    {
        // Verificar permiso
        if (!Auth::user()->can('consultar-perfil-cliente')) {
            abort(403, 'No tienes permisos para consultar perfiles de clientes');
        }

        $user = User::findOrFail($userId);
        $perfil = $user->perfil;

        return view('perfil.consultar', [
            'user' => $user,
            'perfil' => $perfil,
        ]);
    }

    /**
     * Buscar clientes por nombre o email (para cajeros)
     */
    public function buscarClientes(Request $request)
    {
        // Verificar permiso
        if (!Auth::user()->can('consultar-perfil-cliente')) {
            abort(403, 'No tienes permisos para consultar perfiles de clientes');
        }

        $search = $request->get('q', '');

        $clientes = User::role('cliente')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('perfil')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'telefono' => $user->perfil->telefono ?? 'N/A',
                    'tiene_alergias' => $user->perfil ? $user->perfil->tieneAlergias() : false,
                    'tiene_alergias_graves' => $user->perfil ? $user->perfil->tieneAlergiasGraves() : false,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $clientes
        ]);
    }

    /**
     * Mostrar tabla de todos los clientes con alergias y preferencias
     */
    public function consultarTodos(Request $request)
    {
        // Verificar permiso
        if (!Auth::user()->can('consultar-perfil-cliente')) {
            abort(403, 'No tienes permisos para consultar perfiles de clientes');
        }

        $buscar = $request->get('buscar', '');

        $clientes = User::role('cliente')
            ->when($buscar, function($query) use ($buscar) {
                $query->where(function($q) use ($buscar) {
                    $q->where('name', 'like', "%{$buscar}%")
                      ->orWhere('email', 'like', "%{$buscar}%")
                      ->orWhereHas('perfil', function($perfilQ) use ($buscar) {
                          $perfilQ->where('telefono', 'like', "%{$buscar}%");
                      });
                });
            })
            ->with('perfil')
            ->orderBy('name')
            ->paginate(20);

        // Estadísticas
        $stats = [
            'con_alergias' => User::role('cliente')
                ->whereHas('perfil', function($q) {
                    $q->whereNotNull('alergias')
                      ->where('alergias', '!=', '[]');
                })->count(),
            'con_alergias_graves' => User::role('cliente')
                ->whereHas('perfil', function($q) {
                    $q->whereNotNull('alergias')
                      ->where('alergias', 'like', '%"severidad":"grave"%');
                })->count(),
            'con_preferencias' => User::role('cliente')
                ->whereHas('perfil', function($q) {
                    $q->whereNotNull('preferencias')
                      ->where('preferencias', '!=', '[]');
                })->count(),
        ];

        return view('perfil.consultar-todos', [
            'clientes' => $clientes,
            'stats' => $stats,
        ]);
    }

}
