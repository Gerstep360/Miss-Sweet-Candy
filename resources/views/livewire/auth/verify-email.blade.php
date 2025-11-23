<?php

use App\Livewire\Actions\Logout;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        // Enviar email de verificación personalizado
        try {
            $user = Auth::user();
            Mail::to($user->email)->send(new VerifyEmail($user));
            Session::flash('status', 'verification-link-sent');
            Log::info("Email de verificación reenviado a: {$user->email}");
        } catch (\Exception $e) {
            Log::error("Error al reenviar verificación: " . $e->getMessage());
            Session::flash('error', 'Hubo un error al enviar el correo. Inténtalo de nuevo.');
        }
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header 
        title="Verifica tu Correo Electrónico" 
        description="Hemos enviado un enlace de verificación a tu correo" 
    />

    <div class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-6 text-center">
        <div class="w-16 h-16 bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        
        <h3 class="text-white font-semibold text-lg mb-2">
            📧 Revisa tu bandeja de entrada
        </h3>
        
        <p class="text-zinc-300 text-sm mb-4">
            Hemos enviado un enlace de verificación a:<br>
            <strong class="text-amber-400">{{ Auth::user()->email }}</strong>
        </p>
        
        <p class="text-zinc-400 text-sm">
            Por favor, haz clic en el enlace del correo para verificar tu cuenta.<br>
            El enlace expirará en <strong>60 minutos</strong>.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="bg-green-900/20 border border-green-500/50 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-300 text-sm">
                    ✅ ¡Se ha enviado un nuevo correo de verificación!
                </p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-4">
            <p class="text-red-300 text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-zinc-800/30 border border-zinc-700/50 rounded-lg p-4">
        <h4 class="text-white font-medium mb-2 text-sm">💡 ¿No recibes el correo?</h4>
        <ul class="text-zinc-400 text-xs space-y-1 ml-4 list-disc">
            <li>Revisa tu carpeta de <strong>spam</strong> o <strong>correo no deseado</strong></li>
            <li>Verifica que el correo electrónico sea correcto</li>
            <li>Espera unos minutos y vuelve a intentar</li>
        </ul>
    </div>

    <div class="flex flex-col gap-3">
        <button 
            wire:click="sendVerification" 
            class="w-full bg-amber-500 hover:bg-amber-400 text-black font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Reenviar Correo de Verificación
        </button>

        <button 
            wire:click="logout" 
            class="w-full bg-zinc-800 hover:bg-zinc-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
            Cerrar Sesión
        </button>
    </div>

    <div class="text-center text-xs text-zinc-500">
        <p>¿Problemas para verificar? Contacta a soporte</p>
    </div>
</div>
