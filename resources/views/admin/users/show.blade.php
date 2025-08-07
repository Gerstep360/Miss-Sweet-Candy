{{-- filepath: resources/views/admin/users/show.blade.php --}}
<x-layouts.app :title="__('Ver Usuario - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('users.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white p-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                <span class="text-amber-400 font-medium text-lg">{{ $user->initials() }}</span>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
                                <p class="text-zinc-300">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Usuario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información del usuario -->
            <div class="grid gap-6">
                <!-- Información básica -->
                <div class="dashboard-card">
                    <h2 class="text-xl font-bold text-white mb-6">Información del Usuario</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-2">Nombre</label>
                            <p class="text-white font-medium">{{ $user->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-2">Email</label>
                            <p class="text-white font-medium">{{ $user->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-2">Estado de la cuenta</label>
                            <div class="flex items-center gap-2">
                                @if($user->needsPasswordSetup())
                                <span class="bg-orange-500/20 text-orange-400 text-sm px-3 py-1 rounded-full">Pendiente activación</span>
                                @elseif($user->isFullyActive())
                                <span class="bg-green-500/20 text-green-400 text-sm px-3 py-1 rounded-full">Activo</span>
                                @else
                                <span class="bg-blue-500/20 text-blue-400 text-sm px-3 py-1 rounded-full">Registrado</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-2">Fecha de registro</label>
                            <p class="text-white font-medium">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Roles y permisos -->
                <div class="dashboard-card">
                    <h2 class="text-xl font-bold text-white mb-6">Roles y Permisos</h2>
                    
                    <div class="space-y-6">
                        <!-- Roles -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-3">Roles asignados</label>
                            @if($user->roles->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($user->roles as $role)
                                    <span class="bg-amber-500/20 text-amber-400 text-sm px-3 py-1 rounded-full capitalize">{{ $role->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-zinc-400">Sin roles asignados</p>
                            @endif
                        </div>

                        <!-- Permisos -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-3">Permisos disponibles</label>
                            @php
                                $permissions = $user->getAllPermissions();
                            @endphp
                            
                            @if($permissions->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($permissions as $permission)
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
                                    <p class="text-zinc-400">Este usuario no tiene permisos asignados</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                @if($user->needsPasswordSetup())
                <div class="dashboard-card">
                    <div class="bg-orange-500/10 border border-orange-500/20 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L5.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <div>
                                <h3 class="text-orange-400 font-medium mb-1">Usuario pendiente de activación</h3>
                                <p class="text-orange-300 text-sm">Este usuario aún no ha configurado su contraseña. Se le ha enviado un email con las instrucciones para activar su cuenta.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>