{{-- resources/views/admin/users/edit.blade.php --}}
<x-layouts.app :title="__('Editar Usuario - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center gap-3">
                    <a href="{{ route('users.show', $user) }}" class="bg-zinc-700 hover:bg-zinc-600 text-white p-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                            <span class="text-amber-400 font-medium">{{ $user->initials() }}</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Editar: {{ $user->name }}</h1>
                            <p class="text-zinc-300">Modifica la información del usuario</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="dashboard-card">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Nombre -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-white mb-2">Nombre Completo</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                               class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors">
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-white mb-2">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                               class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors">
                        @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rol -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-white mb-2">Rol del Usuario</label>
                        <select name="role" required class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors">
                            <option value="">Selecciona un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" 
                                        {{ (old('role') ?? $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado actual -->
                    <div class="bg-zinc-800/30 rounded-lg p-4 mb-6">
                        <h3 class="text-white font-medium mb-3">Estado Actual</h3>
                        <div class="flex items-center gap-2">
                            @if($user->needsPasswordSetup())
                            <span class="bg-orange-500/20 text-orange-400 text-sm px-3 py-1 rounded-full">Pendiente activación</span>
                            <p class="text-zinc-400 text-sm">El usuario aún no ha configurado su contraseña</p>
                            @elseif($user->isFullyActive())
                            <span class="bg-green-500/20 text-green-400 text-sm px-3 py-1 rounded-full">Activo</span>
                            <p class="text-zinc-400 text-sm">El usuario puede iniciar sesión normalmente</p>
                            @else
                            <span class="bg-blue-500/20 text-blue-400 text-sm px-3 py-1 rounded-full">Registrado</span>
                            <p class="text-zinc-400 text-sm">Usuario registrado por el sistema</p>
                            @endif
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-3">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Actualizar Usuario
                        </button>
                        <a href="{{ route('users.show', $user) }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>