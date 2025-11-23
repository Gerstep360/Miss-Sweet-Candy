{{-- filepath: resources/views/dashboard/barista.blade.php --}}
<x-layouts.app :title="__('Dashboard Barista')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="dashboard-card mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">☕ Dashboard Barista</h1>
                        <p class="text-zinc-400">
                            <span class="text-amber-400">{{ $now->format('l, d \d\e F Y') }}</span>
                            • {{ $now->format('H:i') }}
                        </p>
                    </div>
                    <div class="text-right">
                        @if($hours->isOpenAt($now))
                            <span class="px-4 py-2 bg-green-500/20 text-green-400 rounded-full text-sm font-bold border border-green-500/30">
                                🟢 Abierto
                            </span>
                        @else
                            <span class="px-4 py-2 bg-red-500/20 text-red-400 rounded-full text-sm font-bold border border-red-500/30">
                                🔴 Cerrado
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Métricas principales --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Items pendientes --}}
                <div class="dashboard-card bg-gradient-to-br from-red-500/10 to-red-600/5 border-red-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-red-300 mb-1">Items Pendientes</p>
                            <p class="text-3xl font-bold text-red-400">{{ $itemsPendientes }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Items en preparación --}}
                <div class="dashboard-card bg-gradient-to-br from-yellow-500/10 to-yellow-600/5 border-yellow-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-yellow-300 mb-1">En Preparación</p>
                            <p class="text-3xl font-bold text-yellow-400">{{ $itemsEnPreparacion }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Items completados hoy --}}
                <div class="dashboard-card bg-gradient-to-br from-green-500/10 to-green-600/5 border-green-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-300 mb-1">Completados Hoy</p>
                            <p class="text-3xl font-bold text-green-400">{{ $itemsCompletadosHoy }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Tiempo promedio --}}
                <div class="dashboard-card bg-gradient-to-br from-blue-500/10 to-blue-600/5 border-blue-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-300 mb-1">Tiempo Promedio</p>
                            <p class="text-3xl font-bold text-blue-400">{{ $tiempoPromedio ? round($tiempoPromedio) : 0 }}<span class="text-lg">min</span></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Columna izquierda: Pedidos pendientes --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Pedidos pendientes para barra --}}
                    <div class="dashboard-card">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Pedidos Pendientes
                            </h2>
                            <span class="px-3 py-1 bg-amber-500/20 text-amber-400 rounded-full text-sm font-bold">
                                {{ $pedidosPendientesBarra->count() }} pedidos
                            </span>
                        </div>

                        <div class="space-y-3 max-h-[600px] overflow-y-auto custom-scrollbar">
                            @forelse($pedidosPendientesBarra as $pedido)
                                <div class="bg-zinc-800/50 rounded-xl p-4 border {{ $pedido['urgente'] ? 'border-red-500/50 animate-pulse' : 'border-zinc-700' }}">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-lg font-bold text-white">Pedido #{{ $pedido['id'] }}</span>
                                                @if($pedido['urgente'])
                                                    <span class="px-2 py-0.5 bg-red-500/20 text-red-400 text-xs font-bold rounded border border-red-500/30">
                                                        🔥 URGENTE
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-3 text-sm text-zinc-400">
                                                <span>{{ $pedido['tipo'] }}</span>
                                                <span>•</span>
                                                <span>{{ $pedido['mesa'] }}</span>
                                                <span>•</span>
                                                <span>{{ $pedido['tiempo_transcurrido'] }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('pedidos.show', $pedido['id']) }}" 
                                           class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-black font-semibold rounded-lg text-sm transition-all">
                                            Ver Pedido
                                        </a>
                                    </div>

                                    <div class="space-y-2">
                                        @foreach($pedido['items'] as $item)
                                            <div class="flex items-center justify-between bg-zinc-900/50 rounded-lg p-3">
                                                <div class="flex-1">
                                                    <p class="text-white font-medium">{{ $item['cantidad'] }}x {{ $item['producto'] }}</p>
                                                    @if($item['notas'])
                                                        <p class="text-xs text-amber-400 mt-1">📝 {{ $item['notas'] }}</p>
                                                    @endif
                                                </div>
                                                @if($item['estado'] === 'pendiente')
                                                    <span class="px-2 py-1 bg-red-500/20 text-red-400 text-xs font-bold rounded">
                                                        Pendiente
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 text-xs font-bold rounded">
                                                        En preparación
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 mx-auto text-zinc-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <h3 class="text-lg font-medium text-zinc-400 mb-2">¡Todo listo! 🎉</h3>
                                    <p class="text-zinc-500">No hay pedidos pendientes en este momento</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Columna derecha: Estadísticas --}}
                <div class="space-y-6">
                    {{-- Top productos --}}
                    <div class="dashboard-card">
                        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Más Preparados (7 días)
                        </h2>
                        <div class="space-y-2">
                            @forelse($topProductosBarra->take(5) as $producto)
                                <div class="flex items-center justify-between p-3 bg-zinc-800/50 rounded-lg">
                                    <span class="text-sm text-zinc-300 flex-1">{{ $producto->nombre }}</span>
                                    <span class="text-amber-400 font-bold">{{ $producto->total_preparado }}</span>
                                </div>
                            @empty
                                <p class="text-center text-zinc-500 py-4">Sin datos</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Pedidos por hora --}}
                    <div class="dashboard-card">
                        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pedidos por Hora (7 días)
                        </h2>
                        <div class="space-y-2">
                            @forelse($pedidosPorHora as $hora)
                                <div class="flex items-center justify-between p-3 bg-zinc-800/50 rounded-lg">
                                    <span class="text-sm text-zinc-300">{{ str_pad($hora->hora, 2, '0', STR_PAD_LEFT) }}:00</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 h-2 bg-zinc-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-amber-500 to-orange-500" style="width: {{ min(($hora->cantidad / max($pedidosPorHora->max('cantidad'), 1)) * 100, 100) }}%"></div>
                                        </div>
                                        <span class="text-amber-400 font-bold text-sm w-8 text-right">{{ $hora->cantidad }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-zinc-500 py-4">Sin datos</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Especial del día --}}
                    @if($especialHoy)
                        <div class="dashboard-card bg-gradient-to-br from-amber-500/10 to-orange-500/5 border-amber-500/20">
                            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                Especial del Día
                            </h2>
                            <div class="bg-zinc-900/50 rounded-lg p-4">
                                <p class="text-xl font-bold text-amber-400 mb-1">{{ $especialHoy->producto->nombre }}</p>
                                <p class="text-sm text-zinc-400">{{ $especialHoy->descripcion_especial }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(39, 39, 42, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.5);
        }
    </style>
</x-layouts.app>
