<x-layouts.app>
    <div class="min-h-screen bg-zinc-950 text-white flex items-center justify-center py-8">
        <main class="w-full max-w-xl px-4 sm:px-8">
            <div class="mb-8 flex items-center gap-3">
                <a href="{{ route('notificaciones.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-amber-500 hover:bg-amber-400 text-black rounded-lg shadow transition-colors" wire:navigate>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Volver') }}
                </a>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-100">Detalle de Notificación</h1>
            </div>
            <div class="bg-zinc-900/95 border border-zinc-800 rounded-2xl shadow-xl p-8 flex flex-col items-center">
                <div class="mb-6">
                    @if($notificacion->tipo === 'stock')
                        <div class="w-16 h-16 bg-gradient-to-br from-amber-400/30 to-orange-500/30 rounded-full grid place-items-center shadow">
                            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    @elseif($notificacion->tipo === 'pedido')
                        <div class="w-16 h-16 bg-blue-500/20 rounded-full grid place-items-center shadow">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                    @elseif($notificacion->tipo === 'reserva')
                        <div class="w-16 h-16 bg-green-500/20 rounded-full grid place-items-center shadow">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @else
                        <div class="w-16 h-16 bg-zinc-700/40 rounded-full grid place-items-center shadow">
                            <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <span class="inline-flex items-center gap-2 px-4 py-1 rounded-full font-bold uppercase tracking-wide text-xs mb-4
                    @if($notificacion->tipo === 'stock') bg-gradient-to-r from-amber-400/20 to-orange-500/20 text-orange-400 @elseif($notificacion->tipo === 'pedido') bg-blue-500/20 text-blue-400 @elseif($notificacion->tipo === 'reserva') bg-green-500/20 text-green-400 @else bg-zinc-700/40 text-zinc-300 @endif">
                    {{ ucfirst($notificacion->tipo) }}
                </span>
                @if(!$notificacion->leido)
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold bg-amber-400/20 text-amber-400 border border-amber-400/30 animate-pulse mb-4">
                        {{ __('No leída') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold bg-zinc-700/40 text-zinc-300 border border-zinc-600/30 mb-4">
                        {{ __('Leída') }}
                    </span>
                @endif
                <p class="text-lg font-semibold text-zinc-100 mb-2 text-center">{{ $notificacion->mensaje }}</p>
                <div class="flex flex-wrap items-center justify-center gap-4 text-sm text-zinc-400 mb-4">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                        <span class="font-mono">ID: {{ $notificacion->id }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="font-mono">{{ ucfirst($notificacion->canal) }}</span>
                    </span>
                </div>
            </div>
        </main>
    </div>
</x-layouts.app>


