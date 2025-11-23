{{-- resources/views/barista/pedidos/index.blade.php --}}
<x-layouts.app :title="__('Órdenes — Barista')" >
@can('gestionar-pedidos-barista')
    <div class="min-h-screen bg-zinc-950 text-white">
        <!-- Header -->
        <header class="sticky top-0 z-20 bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg grid place-items-center">
                            <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <h1 class="text-lg sm:text-xl font-semibold">Órdenes para preparar</h1>
                    </div>

                    <a href="{{ route('dashboard') }}"
                       class="hidden sm:inline-flex items-center gap-2 bg-amber-500 text-black px-3 py-2 rounded-lg font-medium hover:bg-amber-400 transition-colors">
                        Dashboard
                    </a>
                </div>
            </div>
        </header>

        <main class="py-8">
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                {{-- Resumen y alertas --}}
                <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5 sm:p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            <span class="w-2 h-2 bg-amber-400 rounded-full mr-2"></span>
                            Pedidos en “Pendiente” o “En preparación”
                        </div>
                        <div class="text-zinc-400 text-sm">
                            Mostrando <span class="text-white font-semibold">{{ $pedidos->count() }}</span> de
                            <span class="text-white font-semibold">{{ $pedidos->total() }}</span> pedidos
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mt-3 rounded-lg border border-green-500/30 bg-green-500/10 text-green-300 px-4 py-2 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mt-3 rounded-lg border border-red-500/30 bg-red-500/10 text-red-300 px-4 py-2 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>

                {{-- Listado --}}
                @forelse ($pedidos as $pedido)
                    @php
                        $tipo = strtolower($pedido->tipo ?? '');
                        $tipoBadge = 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40';
                        if ($tipo === 'mesa') {
                            $tipoBadge = 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30';
                        } elseif ($tipo === 'mostrador') {
                            $tipoBadge = 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30';
                        } elseif ($tipo === 'web') {
                            $tipoBadge = 'bg-purple-500/20 text-purple-400 ring-1 ring-purple-500/30';
                        }

                        $estado = strtolower($pedido->estado ?? '');
                        $estadoBadge = 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40';
                        if ($estado === 'pendiente') {
                            $estadoBadge = 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30';
                        } elseif ($estado === 'en_preparacion') {
                            $estadoBadge = 'bg-orange-500/20 text-orange-400 ring-1 ring-orange-500/30';
                        } elseif ($estado === 'preparado') {
                            $estadoBadge = 'bg-cyan-500/20 text-cyan-400 ring-1 ring-cyan-500/30';
                        }
                    @endphp

                    <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-4 sm:p-5 mb-3 hover:border-amber-500/30 transition">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                            {{-- Columna izquierda: info --}}
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] sm:text-xs font-bold {{ $tipoBadge }}">
                                        @if($tipo === 'mesa')
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            MESA
                                        @elseif($tipo === 'mostrador')
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                            MOSTRADOR
                                        @elseif($tipo === 'web')
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9"/>
                                            </svg>
                                            WEB
                                        @else
                                            TIPO
                                        @endif
                                    </span>

                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] sm:text-xs font-bold {{ $estadoBadge }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                                        {{ strtoupper(str_replace('_',' ', $pedido->estado ?? '')) }}
                                    </span>

                                    <span class="text-zinc-500 text-[11px] sm:text-xs font-mono">#{{ $pedido->id }}</span>
                                </div>

                                <div class="grid md:grid-cols-2 gap-2 text-sm">
                                    @if($pedido->mesa)
                                        <div class="flex items-center gap-2 text-zinc-300">
                                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Mesa {{ $pedido->mesa->numero }}</span>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>{{ optional($pedido->cliente)->name ?? 'Cliente mostrador' }}</span>
                                    </div>

                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <span>{{ $pedido->items->count() }} items</span>
                                    </div>

                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>
                                            {{ $pedido->created_at
                                                ? \Illuminate\Support\Carbon::parse($pedido->created_at)->format('d/m/Y H:i')
                                                : '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Columna derecha: acciones --}}
                            <div class="flex flex-col sm:flex-row lg:flex-col items-stretch gap-2 lg:min-w-[220px]">
                                <a href="{{ route('barista.pedidos.show', $pedido) }}"
                                   class="inline-flex items-center justify-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-white px-3 py-2 rounded-lg transition">
                                    Ver pedido
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>

                                @if($estado === 'pendiente')
                                    <form action="{{ route('barista.pedidos.preparar', $pedido) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 bg-amber-500 text-black px-3 py-2 rounded-lg font-medium hover:bg-amber-400 transition">
                                            Comenzar preparación
                                        </button>
                                    </form>
                                @elseif($estado === 'en_preparacion')
                                    <form action="{{ route('barista.pedidos.servir', $pedido) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 bg-cyan-500 text-black px-3 py-2 rounded-lg font-medium hover:bg-cyan-400 transition">
                                            Marcar “Preparado”
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-10 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-zinc-800 rounded-full grid place-items-center">
                            <svg class="w-8 h-8 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            </svg>
                        </div>
                        <p class="text-zinc-300 font-medium">No hay pedidos pendientes para preparar</p>
                        <p class="text-zinc-500 text-sm">Cuando entren pedidos, aparecerán aquí.</p>
                    </div>
                @endforelse

                {{-- Paginación --}}
                @if ($pedidos->hasPages())
                    <div class="mt-6">{{ $pedidos->links() }}</div>
                @endif
            </section>
        </main>
    </div>
@else
    {{-- Fallback “No autorizado” (ligero) --}}
    <div class="min-h-screen bg-zinc-950 text-white flex items-center">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-6">
                <span class="w-2 h-2 bg-amber-400 rounded-full mr-2"></span>
                403 — No autorizado
            </div>
            <h1 class="text-4xl font-bold mb-3">No puedes pasar… por ahora ☕</h1>
            <p class="text-zinc-300 mb-6">No tienes permiso para ver las órdenes del barista.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url('/') }}" class="bg-amber-500 text-black px-6 py-3 rounded-lg font-semibold hover:bg-amber-400 transition-colors inline-flex items-center justify-center gap-2">
                    Ir al inicio
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="border-2 border-amber-500 text-amber-500 px-6 py-3 rounded-lg font-semibold hover:bg-amber-500/10 transition-colors">
                        Iniciar sesión
                    </a>
                @endif
            </div>
        </div>
    </div>
@endcan
</x-layouts.app>
