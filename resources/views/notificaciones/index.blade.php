{{-- filepath: resources/views/admin/notificaciones/index.blade.php --}}
<x-layouts.app :title="__('Mis Notificaciones')">
    <div class="min-h-screen bg-zinc-950 text-white">
        <main class="py-8">
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Encabezado de página (NO sticky) --}}
                @php $unread = $notificaciones->where('leido', false)->count(); @endphp
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl shadow-lg grid place-items-center">
                            <svg class="w-6 h-6 text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-100 drop-shadow">Mis Notificaciones</h1>
                    </div>

                    @if($unread > 0)
                        <form method="POST" action="{{ route('notificaciones.marcar-todas-leidas') }}" class="hidden sm:block">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-400 to-orange-500 text-black px-4 py-2 rounded-xl font-semibold shadow hover:from-orange-400 hover:to-amber-500 transition-colors">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Marcar todas como leídas
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Resumen / flash --}}
                <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5 sm:p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm bg-gradient-to-r from-amber-400/20 to-orange-500/20 text-amber-400 border border-amber-500/30 font-semibold shadow">
                            <span class="w-2 h-2 bg-amber-400 rounded-full mr-2 animate-pulse"></span>
                            Centro de alertas y avisos
                        </div>

                        <div class="text-zinc-400 text-sm">
                            Mostrando <span class="text-amber-400 font-bold">{{ $notificaciones->count() }}</span>
                            de <span class="text-amber-400 font-bold">{{ $notificaciones->total() }}</span> notificaciones
                        </div>
                    </div>

                    {{-- Acciones móvil --}}
                    <div class="mt-4 sm:hidden flex items-center gap-2">
                        <a href="{{ route('dashboard') }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-400 to-orange-500 text-black px-3 py-2 rounded-xl font-semibold shadow hover:from-orange-400 hover:to-amber-500 transition-colors">
                            Volver
                        </a>
                        @if($unread > 0)
                            <form class="flex-1" method="POST" action="{{ route('notificaciones.marcar-todas-leidas') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-400 to-orange-500 text-black px-3 py-2 rounded-xl font-semibold shadow hover:from-orange-400 hover:to-amber-500 transition-colors">
                                    Marcar leídas
                                </button>
                            </form>
                        @endif
                    </div>

                    @if(session('success'))
                        <div class="mt-4 p-3 rounded-xl bg-green-500/10 text-green-200 border border-green-600/30 shadow">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>

                {{-- Lista / tarjetas --}}
                @forelse($notificaciones as $notificacion)
                    @php
                        $tipo = strtolower($notificacion->tipo ?? '');
                        $pill = 'bg-zinc-800/80 text-zinc-300 ring-1 ring-zinc-700';
                        $iconWrap = 'bg-zinc-800';
                        $iconColor = 'text-zinc-300';

                        if ($tipo === 'stock') {
                            $pill = 'bg-orange-500/20 text-orange-400 ring-1 ring-orange-500/30';
                            $iconWrap = 'bg-orange-500/20';
                            $iconColor = 'text-orange-400';
                        } elseif ($tipo === 'pedido') {
                            $pill = 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30';
                            $iconWrap = 'bg-blue-500/20';
                            $iconColor = 'text-blue-400';
                        } elseif ($tipo === 'reserva') {
                            $pill = 'bg-green-500/20 text-green-400 ring-1 ring-green-500/30';
                            $iconWrap = 'bg-green-500/20';
                            $iconColor = 'text-green-400';
                        }
                    @endphp

                    <a href="{{ route('notificaciones.show', $notificacion->id) }}" class="block group">
                        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-4 sm:p-5 mb-3 transition hover:border-amber-500/30">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                                {{-- Icono + contenido --}}
                                <div class="flex-1 flex items-start gap-3 sm:gap-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg grid place-items-center {{ $iconWrap }}">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 {{ $iconColor }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <p class="text-sm sm:text-base {{ !$notificacion->leido ? 'font-semibold text-white' : 'text-zinc-300' }}">
                                                {{ $notificacion->mensaje }}
                                            </p>
                                            @if(!$notificacion->leido)
                                                <span class="flex-shrink-0 inline-block w-2 h-2 sm:w-2.5 sm:h-2.5 bg-amber-400 rounded-full"></span>
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs">

                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold uppercase tracking-wide {{ $pill }} shadow">
                                                {{ ucfirst($notificacion->tipo) }}
                                            </span>

                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-semibold bg-zinc-800/80 text-zinc-300 ring-1 ring-zinc-700">
                                                Canal: {{ ucfirst($notificacion->canal) }}
                                            </span>

                                            <span class="text-zinc-500 font-mono">ID: {{ $notificacion->id }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- CTA derecha (desktop) --}}
                                <div class="lg:min-w-[160px] lg:justify-end hidden lg:flex">
                                    <span class="inline-flex items-center gap-2 text-amber-400 group-hover:text-amber-300">
                                        Ver detalle
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </article>
                    </a>
                @empty
                    {{-- Estado vacío --}}
                    <div class="bg-gradient-to-br from-zinc-900/80 to-zinc-800/80 border border-zinc-800 rounded-2xl p-12 text-center shadow-lg flex flex-col items-center justify-center">
                        <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-amber-400/20 to-orange-500/20 rounded-full grid place-items-center shadow">
                            <svg class="w-10 h-10 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <p class="text-amber-300 font-bold text-lg mb-1">No tienes notificaciones</p>
                        <p class="text-zinc-400 text-base">Cuando recibas notificaciones, aparecerán aquí.</p>
                    </div>
                @endforelse

                @if($notificaciones->hasPages())
                    <div class="mt-6">{{ $notificaciones->links() }}</div>
                @endif
            </section>
        </main>
    </div>
</x-layouts.app>
