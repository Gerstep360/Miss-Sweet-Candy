<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header title="Iniciar Sesión en Miss Sweet Candy" description="Accede a tu cuenta para gestionar tu cafetería" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
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
                autofocus
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
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-white">
                    {{ __('Contraseña') }}
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-amber-400 hover:text-amber-300 transition-colors">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif
            </div>
            <input
                wire:model="password"
                id="password"
                type="password"
                required
                autocomplete="current-password"
                placeholder="Tu contraseña"
                class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors"
            />
            @error('password')
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input
                wire:model="remember"
                id="remember"
                type="checkbox"
                class="w-4 h-4 bg-zinc-800 border-zinc-700 rounded text-amber-500 focus:ring-amber-500/20 focus:ring-2"
            />
            <label for="remember" class="ml-2 text-sm text-zinc-300">
                {{ __('Recordarme') }}
            </label>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-black font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ __('Iniciar Sesión') }}
            </button>
        </div>
    </form>

    @if (Route::has('register'))
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
                    ¿No tienes una cuenta?
                    <a href="{{ route('register') }}" class="text-amber-400 hover:text-amber-300 font-medium transition-colors">
                        Regístrate aquí
                    </a>
                </p>
            </div>
        </div>
    @endif
</div>