{{-- resources/views/admin/controlAcceso/two-factor.blade.php --}}
<x-layouts.app :title="__('Gestión de 2FA - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Gestión de Autenticación de Dos Factores</h1>
                        <p class="text-zinc-300">Configura y gestiona el 2FA para todos los usuarios del sistema</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('control-acceso.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-1">Total Usuarios</h3>
                    <p class="text-2xl font-bold text-blue-400">{{ $totalUsers }}</p>
                    <p class="text-zinc-400 text-sm mt-1">En el sistema</p>
                </div>

                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-1">2FA Activado</h3>
                    <p class="text-2xl font-bold text-green-400">{{ $usersWith2FA }}</p>
                    <p class="text-zinc-400 text-sm mt-1">{{ number_format(($usersWith2FA / $totalUsers) * 100, 1) }}%</p>
                </div>

                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-1">2FA Pendiente</h3>
                    <p class="text-2xl font-bold text-amber-400">{{ $usersWithout2FA }}</p>
                    <p class="text-zinc-400 text-sm mt-1">Por configurar</p>
                </div>

                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-1">2FA Obligatorio</h3>
                    <p class="text-2xl font-bold text-red-400">{{ $rolesWithMandatory2FA }}</p>
                    <p class="text-zinc-400 text-sm mt-1">Roles protegidos</p>
                </div>
            </div>

            <!-- Configuración de Políticas -->
            <div class="dashboard-card mb-8">
                <h3 class="text-lg font-semibold text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configuración por Rol
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($roles as $role)
                    <div class="bg-zinc-800/50 rounded-lg p-4 border {{ $role->requires_2fa ? 'border-red-500/30' : 'border-zinc-700' }}">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-white font-semibold">{{ $role->name }}</h4>
                            <span class="text-xs px-2 py-1 rounded-full {{ $role->requires_2fa ? 'bg-red-500/20 text-red-400' : 'bg-zinc-500/20 text-zinc-400' }}">
                                {{ $role->requires_2fa ? 'Obligatorio' : 'Opcional' }}
                            </span>
                        </div>
                        
                        <div class="text-zinc-400 text-sm mb-3">
                            {{ $role->users_count }} usuarios
                            @if($role->users_with_2fa_count > 0)
                            <span class="text-green-400">• {{ $role->users_with_2fa_count }} con 2FA</span>
                            @endif
                        </div>

                        <form action="{{ route('control-acceso.toggle-role-2fa') }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                            
                            @if($role->requires_2fa)
                            <button type="submit" name="action" value="disable" class="flex-1 bg-zinc-600 hover:bg-zinc-500 text-white text-xs font-medium py-2 rounded transition-colors">
                                Hacer Opcional
                            </button>
                            @else
                            <button type="submit" name="action" value="enable" class="flex-1 bg-red-500 hover:bg-red-400 text-white text-xs font-medium py-2 rounded transition-colors">
                                Hacer Obligatorio
                            </button>
                            @endif
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Lista de Usuarios -->
            <div class="dashboard-card">
                <h3 class="text-lg font-semibold text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    Gestión de Usuarios
                </h3>

                <!-- Filtros -->
                <div class="flex flex-wrap gap-3 mb-6">
                    <button class="px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                        Todos
                    </button>
                    <button class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-500/20 text-zinc-400 border border-zinc-500/30 hover:bg-zinc-500/30">
                        Con 2FA
                    </button>
                    <button class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-500/20 text-zinc-400 border border-zinc-500/30 hover:bg-zinc-500/30">
                        Sin 2FA
                    </button>
                    <button class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-500/20 text-zinc-400 border border-zinc-500/30 hover:bg-zinc-500/30">
                        Con Rol Obligatorio
                    </button>
                </div>

                <!-- Tabla de Usuarios -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-zinc-700">
                                <th class="text-left text-zinc-400 font-medium py-3 px-4">Usuario</th>
                                <th class="text-left text-zinc-400 font-medium py-3 px-4">Rol</th>
                                <th class="text-left text-zinc-400 font-medium py-3 px-4">Estado 2FA</th>
                                <th class="text-left text-zinc-400 font-medium py-3 px-4">Requerido</th>
                                <th class="text-left text-zinc-400 font-medium py-3 px-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="border-b border-zinc-800/50 hover:bg-zinc-800/30">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-zinc-700 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">{{ $user->initials() }}</span>
                                        </div>
                                        <div>
                                            <div class="text-white font-medium">{{ $user->name }}</div>
                                            <div class="text-zinc-400 text-xs">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($user->roles->isNotEmpty())
                                        @foreach($user->roles as $role)
                                            <span class="text-xs px-2 py-1 rounded-full bg-zinc-500/20 text-zinc-400">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-zinc-400 text-sm">Sin rol</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($user->hasTwoFactorEnabled())
                                        <span class="text-xs px-2 py-1 rounded-full bg-green-500/20 text-green-400">
                                            Activado
                                        </span>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full bg-amber-500/20 text-amber-400">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($user->roles->contains('requires_2fa', true))
                                        <span class="text-xs px-2 py-1 rounded-full bg-red-500/20 text-red-400">
                                            Obligatorio
                                        </span>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full bg-zinc-500/20 text-zinc-400">
                                            Opcional
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        @if($user->hasTwoFactorEnabled())
                                        <form action="{{ route('control-acceso.disable-user-2fa') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <button type="submit" 
                                                    class="text-red-400 hover:text-red-300 text-xs font-medium py-1 px-2 rounded transition-colors"
                                                    onclick="return confirm('¿Desactivar 2FA para {{ $user->name }}?')">
                                                Desactivar
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('control-acceso.force-enable-2fa') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <button type="submit" 
                                                    class="text-green-400 hover:text-green-300 text-xs font-medium py-1 px-2 rounded transition-colors"
                                                    onclick="return confirm('¿Forzar activación de 2FA para {{ $user->name }}?')">
                                                Forzar 2FA
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($users->hasPages())
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>