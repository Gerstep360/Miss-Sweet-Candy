{{-- filepath: resources/views/livewire/auth/register.blade.php --}}
<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Crear usuario con registro normal (sin temporal_token)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'password_set' => true, // Contraseña ya establecida
            'temporal_token' => null, // Sin token temporal
            'email_verified_at' => null, // Se verificará después si es necesario
        ]);
        
        // Asignar rol cliente por defecto
        $user->assignRole('cliente');

        event(new Registered($user));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header title="Únete a Miss Sweet Candy" description="Crea tu cuenta para gestionar tu experiencia en nuestra cafetería" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-white">
                {{ __('Nombre Completo') }}
            </label>
            <input
                wire:model="name"
                id="name"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Tu nombre completo"
                class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors"
            />
            @error('name')
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-white">
                {{ __('Correo Electrónico') }}
            </label>
            <input
                wire:model="email"
                id="email"
                type="email"
                required
                autocomplete="email"
                placeholder="correo@ejemplo.com"
                class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors"
            />
            @error('email')
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-white">
                {{ __('Contraseña') }}
            </label>
            <input
                wire:model="password"
                id="password"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Mínimo 8 caracteres"
                class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors"
            />
            @error('password')
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-medium text-white">
                {{ __('Confirmar Contraseña') }}
            </label>
            <input
                wire:model="password_confirmation"
                id="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Repite tu contraseña"
                class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors"
            />
            @error('password_confirmation')
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-black font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                {{ __('Crear Cuenta') }}
            </button>
        </div>
    </form>

    <div class="text-center">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-zinc-700"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-zinc-900 text-zinc-400">O</span>
            </div>
        </div>
        <div class="mt-6">
            <p class="text-zinc-400">
                ¿Ya tienes una cuenta?
                <a href="{{ route('login') }}" class="text-amber-400 hover:text-amber-300 font-medium transition-colors">
                    Inicia sesión aquí
                </a>
            </p>
        </div>
    </div>
</div>