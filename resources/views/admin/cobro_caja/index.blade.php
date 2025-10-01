{{-- filepath: c:\Users\German\Documents\Proyectos\PHP\Cafeteria\cafeteria\resources\views\admin\cobro_caja\index.blade.php --}}
<x-layouts.app :title="__('Cobro en Caja')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">Cobro en Caja</h1>
                                <p class="text-sm text-zinc-400">Gestión de cobros y pagos</p>
                            </div>
                        </div>
                    </div>
                    @can('ver-reporte-caja')
                    <div class="flex gap-2">
                        <a href="{{ route('cobro_caja.reporte_diario') }}" 
                           class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Reporte Diario
                        </a>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- Filtros -->
            <div class="dashboard-card mb-6">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-zinc-300 font-medium mb-2 text-sm">Tipo de pedido</label>
                        <select name="tipo" 
                                onchange="this.form.submit()"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                            <option value="">Todos</option>
                            <option value="mesa" {{ request('tipo') === 'mesa' ? 'selected' : '' }}>Mesa</option>
                            <option value="mostrador" {{ request('tipo') === 'mostrador' ? 'selected' : '' }}>Mostrador</option>
                            <option value="web" {{ request('tipo') === 'web' ? 'selected' : '' }}>Web</option>
                        </select>
                    </div>
                </form>
            </div>

            @if(session('success'))
                <div class="dashboard-card bg-green-500/10 border-green-500/30 mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Tabs -->
            <div class="mb-6" x-data="{ tab: 'pendientes' }">
                <div class="flex gap-2 mb-4 overflow-x-auto pb-2">
                    <button @click="tab = 'pendientes'" 
                            :class="tab === 'pendientes' ? 'bg-green-500 text-white' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700'"
                            class="px-4 py-2 rounded-lg transition-all duration-200 whitespace-nowrap flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pendientes ({{ $pedidosPendientes->count() }})
                    </button>
                    <button @click="tab = 'cobrados'" 
                            :class="tab === 'cobrados' ? 'bg-green-500 text-white' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700'"
                            class="px-4 py-2 rounded-lg transition-all duration-200 whitespace-nowrap flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Cobrados ({{ $pedidosPagados->count() }})
                    </button>
                </div>

                <!-- Pedidos Pendientes -->
                <div x-show="tab === 'pendientes'" x-transition class="space-y-4">
                    @forelse($pedidosPendientes as $pedido)
                        <div class="dashboard-card hover:border-green-500/40 transition-all duration-300 group">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                                <!-- Info del pedido -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="text-lg font-bold text-white">Pedido #{{ $pedido->id }}</h3>
                                                @if($pedido->tipo === 'mesa')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-500/20 text-blue-400">
                                                        {{ $pedido->mesa->nombre ?? 'Mesa' }}
                                                    </span>
                                                @elseif($pedido->tipo === 'mostrador')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-500/20 text-amber-400">
                                                        Mostrador
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-500/20 text-purple-400">
                                                        Web
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-400">
                                                @if($pedido->cliente)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                        {{ $pedido->cliente->name }}
                                                    </span>
                                                @endif
                                                <span class="text-zinc-600">•</span>
                                                <span>{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-500/20 text-green-400">
                                            {{ ucfirst($pedido->estado) }}
                                        </span>
                                    </div>

                                    <!-- Items del pedido -->
                                    <div class="bg-zinc-800/50 rounded-lg p-3 mb-3">
                                        <div class="space-y-1 max-h-32 overflow-y-auto custom-scrollbar">
                                            @foreach($pedido->items as $item)
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-zinc-300">{{ $item->cantidad }}x {{ $item->producto->nombre }}</span>
                                                    <span class="text-zinc-400">${{ number_format($item->subtotal_item, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="flex justify-between items-center pt-2 border-t border-zinc-700/50">
                                        <span class="text-zinc-400">Total a cobrar:</span>
                                        <span class="text-2xl font-bold text-green-400">
                                            ${{ number_format($pedido->items->sum('subtotal_item'), 2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex lg:flex-col gap-2 lg:w-40">
                                    @can('crear-cobros')
                                    <a href="{{ route('cobro_caja.create', $pedido) }}" 
                                       class="flex-1 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-green-500/30 hover:scale-105 active:scale-95 font-semibold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Cobrar
                                    </a>
                                    @endcan
                                    @can('ver-cobros')
                                    <a href="{{ route('cobro_caja.show', $pedido) }}" 
                                       class="flex-1 bg-zinc-700 hover:bg-zinc-600 text-white py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="dashboard-card text-center py-12">
                            <div class="w-20 h-20 mx-auto mb-4 bg-zinc-700/30 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-base text-zinc-400 font-medium mb-2">No hay pedidos pendientes de cobro</p>
                            <p class="text-sm text-zinc-500">Todos los pedidos han sido cobrados</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pedidos Cobrados -->
                <div x-show="tab === 'cobrados'" x-transition class="space-y-4">
                    @forelse($pedidosPagados as $pedido)
                        @php
                            $cobro = $pedido->cobros->where('estado', 'cobrado')->first();
                        @endphp
                        <div class="dashboard-card hover:border-zinc-600 transition-all duration-300">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                                <!-- Info del pedido -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="text-lg font-bold text-white">Pedido #{{ $pedido->id }}</h3>
                                                @if($pedido->tipo === 'mesa')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-500/20 text-blue-400">
                                                        {{ $pedido->mesa->nombre ?? 'Mesa' }}
                                                    </span>
                                                @elseif($pedido->tipo === 'mostrador')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-500/20 text-amber-400">
                                                        Mostrador
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-500/20 text-purple-400">
                                                        Web
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-400">
                                                @if($cobro)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ ucfirst($cobro->metodo) }}
                                                    </span>
                                                    <span class="text-zinc-600">•</span>
                                                    <span>{{ $cobro->created_at->format('d/m/Y H:i') }}</span>
                                                    @if($cobro->cajero)
                                                        <span class="text-zinc-600">•</span>
                                                        <span>{{ $cobro->cajero->name }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-500/20 text-green-400">
                                            Cobrado
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-zinc-400 text-sm">Comprobante: {{ $cobro->comprobante ?? 'N/A' }}</span>
                                        <span class="text-xl font-bold text-green-400">
                                            ${{ number_format($cobro->importe ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex lg:flex-col gap-2 lg:w-40">
                                    @if($cobro)
                                        @can('ver-cobros')
                                        <a href="{{ route('cobro_caja.comprobante', $cobro) }}" 
                                           class="flex-1 bg-zinc-700 hover:bg-zinc-600 text-white py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Comprobante
                                        </a>
                                        @endcan
                                    @endif
                                    @can('ver-cobros')
                                    <a href="{{ route('cobro_caja.show', $pedido) }}" 
                                       class="flex-1 bg-zinc-700 hover:bg-zinc-600 text-white py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="dashboard-card text-center py-12">
                            <div class="w-20 h-20 mx-auto mb-4 bg-zinc-700/30 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-base text-zinc-400 font-medium mb-2">No hay pedidos cobrados</p>
                            <p class="text-sm text-zinc-500">Los cobros registrados aparecerán aquí</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(39, 39, 42, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(34, 197, 94, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(34, 197, 94, 0.5);
        }
    </style>
</x-layouts.app>