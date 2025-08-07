{{-- filepath: resources/views/admin/roles/show.blade.php --}}
<x-layouts.app :title="__('Ver Rol - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('roles.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white p-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-white capitalize">Rol: {{ $role->name }}</h1>
                            <p class="text-zinc-300">{{ $role->permissions->count() }} permisos asignados</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('roles.edit', $role) }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Rol
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información del rol -->
            <div class="grid gap-6">
                <!-- Permisos asignados -->
                <div class="dashboard-card">
                    <h2 class="text-xl font-bold text-white mb-6">Permisos Asignados</h2>
                    
                    @if($role->permissions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($role->permissions as $permission)
                            <div class="bg-zinc-800/30 p-3 rounded-lg border border-green-500/20">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                    <span class="text-zinc-200 text-sm">{{ $permission->name }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L5.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <p class="text-zinc-400">Este rol no tiene permisos asignados</p>
                        </div>
                    @endif
                </div>

                <!-- Usuarios con este rol -->
                <div class="dashboard-card">
                    <h2 class="text-xl font-bold text-white mb-6">Usuarios con este Rol</h2>
                    
                    @if($users->count() > 0)
                        <div class="space-y-3">
                            @foreach($users as $user)
                            <div class="bg-zinc-800/30 p-4 rounded-lg flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                        <span class="text-amber-400 font-medium">{{ $user->initials() }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-white font-medium">{{ $user->name }}</h3>
                                        <p class="text-zinc-400 text-sm">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <span class="bg-zinc-700 text-zinc-300 text-xs px-2 py-1 rounded">Usuario activo</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <p class="text-zinc-400">Ningún usuario tiene este rol asignado</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>