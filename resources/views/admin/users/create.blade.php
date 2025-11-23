{{-- resources/views/admin/users/create.blade.php --}}
<x-layouts.app :title="__('Crear Usuario - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center gap-3">
                    <a href="{{ route('users.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white p-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Crear Nuevo Usuario</h1>
                        <p class="text-zinc-300">El usuario recibirá un email para configurar su contraseña</p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="dashboard-card">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <!-- Nombre -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-white mb-2">Nombre Completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors"
                               placeholder="Ej: Juan Pérez">
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-white mb-2">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                               class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors"
                               placeholder="correo@ejemplo.com">
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
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Información adicional -->
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h3 class="text-blue-400 font-medium mb-1">¿Qué sucede después?</h3>
                                <ul class="text-blue-300 text-sm space-y-1">
                                    <li>• Se enviará un email al usuario con un enlace de activación</li>
                                    <li>• El usuario deberá configurar su contraseña desde el enlace</li>
                                    <li>• Una vez configurada, podrá iniciar sesión normalmente</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-3">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Crear Usuario
                        </button>
                        <a href="{{ route('users.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>