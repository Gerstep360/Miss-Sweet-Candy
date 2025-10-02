{{-- resources/views/auth/set-password.blade.php --}}
<x-layouts.guest :title="__('Configurar Contraseña')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-amber-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Configura tu contraseña</h1>
                <p class="text-zinc-400">Hola {{ $user->name }}, establece tu contraseña para acceder al sistema</p>
            </div>

            <!-- Formulario -->
            <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-xl p-6">
                <form method="POST" action="{{ route('users.set-password', $token) }}">
                    @csrf
                    
                    <!-- Info del usuario -->
                    <div class="bg-zinc-800/50 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                <span class="text-amber-400 font-medium">{{ $user->initials() }}</span>
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $user->name }}</p>
                                <p class="text-zinc-400 text-sm">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-zinc-300 mb-2">
                            Nueva Contraseña
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required
                               class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors"
                               placeholder="Mínimo 8 caracteres">
                        @error('password')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar contraseña -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-zinc-300 mb-2">
                            Confirmar Contraseña
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required
                               class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors"
                               placeholder="Repite tu contraseña">
                    </div>

                    <!-- Botón -->
                    <button type="submit" 
                            class="w-full bg-amber-500 hover:bg-amber-400 text-black font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Establecer Contraseña
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-zinc-500 text-sm">
                    ¿Problemas para acceder? 
                    <a href="mailto:admin@cafearoma.com" class="text-amber-400 hover:text-amber-300 transition-colors">
                        Contacta al administrador
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.guest>