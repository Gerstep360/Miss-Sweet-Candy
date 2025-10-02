{{-- filepath: resources/views/barista/pedidos/show.blade.php --}}
<x-layouts.app :title="__('Pedido #'.$pedido->id.' — Barista')">
@can('gestionar-pedidos-barista')
    @php
        // ===== Normalización de estado (acepta 'en preparacion' y 'en_preparacion') =====
        $estadoRaw   = strtolower(trim($pedido->estado ?? ''));
        $estadoNorm  = str_replace('_', ' ', $estadoRaw); // 'en_preparacion' -> 'en preparacion'

        // ===== Badges por tipo =====
        $tipo = strtolower($pedido->tipo ?? '');
        $tipoBadge = 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40';
        if ($tipo === 'mesa') {
            $tipoBadge = 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30';
        } elseif ($tipo === 'mostrador') {
            $tipoBadge = 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30';
        } elseif ($tipo === 'web') {
            $tipoBadge = 'bg-purple-500/20 text-purple-400 ring-1 ring-purple-500/30';
        }

        // ===== Badges por estado =====
        $estadoBadge = 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40';
        if ($estadoNorm === 'pendiente') {
            $estadoBadge = 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30';
        } elseif ($estadoNorm === 'en preparacion') {
            $estadoBadge = 'bg-orange-500/20 text-orange-400 ring-1 ring-orange-500/30';
        } elseif ($estadoNorm === 'preparado') {
            $estadoBadge = 'bg-cyan-500/20 text-cyan-400 ring-1 ring-cyan-500/30';
        }

        // Fecha/hora segura (evita format() sobre string nulo)
        $createdAtText = '—';
        if (!empty($pedido->created_at)) {
            try { $createdAtText = \Illuminate\Support\Carbon::parse($pedido->created_at)->format('d/m/Y H:i:s'); } catch (\Throwable $th) {}
        }

        // Mesa label con fallback seguro
        $mesaLabel = optional($pedido->mesa)->nombre
            ?? (optional($pedido->mesa)->numero ?? optional($pedido->mesa)->id);
    @endphp

    <div class="min-h-screen bg-zinc-950 text-white">
        <!-- Header -->
        <header class="sticky top-0 z-20 bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('barista.pedidos.index') }}"
                           class="inline-flex items-center gap-2 text-zinc-300 hover:text-white px-2 py-1 rounded-lg hover:bg-zinc-800 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Volver
                        </a>
                        <div class="w-8 h-8 bg-amber-500 rounded-lg grid place-items-center">
                            <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <h1 class="text-lg sm:text-xl font-semibold">Pedido #{{ $pedido->id }}</h1>
                    </div>

                    <a href="{{ route('dashboard') }}"
                       class="hidden sm:inline-flex items-center gap-2 bg-amber-500 text-black px-3 py-2 rounded-lg font-medium hover:bg-amber-400 transition-colors">
                        Dashboard
                    </a>
                </div>
            </div>
        </header>

        <main class="py-8">
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                {{-- Resumen --}}
                <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5 sm:p-6">
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $tipoBadge }}">
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

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $estadoBadge }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current @if($estadoNorm !== 'preparado') animate-pulse @endif"></span>
                            {{ strtoupper($estadoNorm) }}
                        </span>

                        <span class="text-zinc-500 text-xs font-mono">ID: {{ $pedido->id }}</span>
                    </div>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                            <p class="text-zinc-400 text-xs mb-1">Cliente</p>
                            <p class="font-medium">
                                {{ optional($pedido->cliente)->name ?? 'Cliente mostrador' }}
                                @if(optional($pedido->cliente)->email)
                                    <span class="text-zinc-500 block text-xs">{{ $pedido->cliente->email }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                            <p class="text-zinc-400 text-xs mb-1">Atendido por</p>
                            <p class="font-medium">
                                {{ optional($pedido->atendidoPor)->name ?? '—' }}
                                @if(optional($pedido->atendidoPor)->email)
                                    <span class="text-zinc-500 block text-xs">{{ $pedido->atendidoPor->email }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                            <p class="text-zinc-400 text-xs mb-1">Fecha y hora</p>
                            <p class="font-medium">{{ $createdAtText }}</p>
                        </div>

                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                            <p class="text-zinc-400 text-xs mb-1">Ubicación</p>
                            <p class="font-medium">
                                @if($tipo === 'mesa' && $pedido->mesa)
                                    Mesa {{ $mesaLabel }}
                                @else
                                    {{ ucfirst($tipo ?: '—') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Items del pedido --}}
                <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5 sm:p-6">
                    <h2 class="text-base sm:text-lg font-semibold mb-4">Items</h2>

                    @if($pedido->items->isEmpty())
                        <p class="text-zinc-400">Este pedido no tiene items.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-zinc-400 border-b border-zinc-800">
                                        <th class="py-2 pr-4">Producto</th>
                                        <th class="py-2 pr-4">Cantidad</th>
                                        <th class="py-2 pr-4">Estado</th>
                                        <th class="py-2 pr-4">Nota</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-800">
                                    @foreach($pedido->items as $item)
                                        @php
                                            $estadoItemRaw  = strtolower($item->estado_item ?? '');
                                            $estadoItemNorm = str_replace('_',' ', $estadoItemRaw);
                                            $itemBadge = 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40';
                                            if ($estadoItemNorm === 'en preparacion') {
                                                $itemBadge = 'bg-orange-500/20 text-orange-400 ring-1 ring-orange-500/30';
                                            } elseif ($estadoItemNorm === 'preparado') {
                                                $itemBadge = 'bg-cyan-500/20 text-cyan-400 ring-1 ring-cyan-500/30';
                                            } elseif ($estadoItemNorm === 'pendiente') {
                                                $itemBadge = 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="py-3 pr-4 text-white">
                                                {{ optional($item->producto)->nombre ?? 'Producto' }}
                                            </td>
                                            <td class="py-3 pr-4 text-zinc-200">
                                                {{ $item->cantidad ?? 1 }}
                                            </td>
                                            <td class="py-3 pr-4">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $itemBadge }}">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                    {{ strtoupper($estadoItemNorm ?: $estadoNorm) }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4 text-zinc-300">
                                                {{ $item->nota ?? ($item->observaciones ?? '—') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                        <div class="text-zinc-400 text-sm">
                            Estado actual: <span class="text-white font-semibold">{{ strtoupper($estadoNorm ?: '—') }}</span>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('barista.pedidos.index') }}"
                               class="inline-flex items-center justify-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-white px-4 py-2 rounded-lg transition">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Volver al listado
                            </a>

                            @if($estadoNorm === 'pendiente')
                                <form action="{{ route('barista.pedidos.preparar', $pedido) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 bg-amber-500 text-black px-4 py-2 rounded-lg font-medium hover:bg-amber-400 transition">
                                        Comenzar preparación
                                    </button>
                                </form>
                            @elseif($estadoNorm === 'en preparacion')
                                <form action="{{ route('barista.pedidos.servir', $pedido) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 bg-cyan-500 text-black px-4 py-2 rounded-lg font-medium hover:bg-cyan-400 transition">
                                        Marcar “Preparado”
                                    </button>
                                </form>
                            @else
                                <button type="button" disabled
                                        class="inline-flex items-center justify-center gap-2 bg-zinc-800 text-zinc-400 px-4 py-2 rounded-lg cursor-not-allowed">
                                    Sin acciones disponibles
                                </button>
                            @endif
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
            </section>
        </main>
    </div>
@else
    {{-- Fallback 403 ligero --}}
    <div class="min-h-screen bg-zinc-950 text-white flex items-center">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-6">
                <span class="w-2 h-2 bg-amber-400 rounded-full mr-2"></span>
                403 — No autorizado
            </div>
            <h1 class="text-4xl font-bold mb-3">No puedes pasar… por ahora ☕</h1>
            <p class="text-zinc-300 mb-6">No tienes permiso para ver este pedido.</p>
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
