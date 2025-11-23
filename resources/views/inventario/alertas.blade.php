{{-- filepath: resources/views/inventario/alertas.blade.php --}}
<x-layouts.app :title="__('Alertas de Stock')">
    <div class="min-h-screen bg-zinc-950 text-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('inventario.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-amber-500 hover:bg-amber-400 text-black rounded-lg shadow transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            {{ __('Volver') }}
                        </a>
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight bg-gradient-to-r from-red-400 to-orange-500 bg-clip-text text-transparent">
                            Alertas de Stock
                        </h1>
                    </div>
                </div>
                <p class="text-zinc-400">Productos que requieren reposición urgente</p>
            </div>

            {{-- Resumen de alertas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="bg-gradient-to-br from-red-500/20 to-red-600/10 border border-red-500/30 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-red-300 mb-1">Stock Crítico</p>
                            <p class="text-3xl font-bold text-red-400">{{ $criticos->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-500/20 rounded-xl grid place-items-center animate-pulse">
                            <svg class="w-6 h-6 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 border border-yellow-500/30 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-yellow-300 mb-1">Stock Bajo</p>
                            <p class="text-3xl font-bold text-yellow-400">{{ $bajos->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-yellow-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de productos críticos --}}
            @if($criticos->count() > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-red-400 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Productos con Stock Crítico
                    </h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($criticos as $item)
                            <div class="bg-zinc-900/95 border border-red-500/30 rounded-xl p-5 hover:border-red-500/50 transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-zinc-100 mb-1">{{ $item->producto->nombre }}</h3>
                                        <p class="text-sm text-zinc-400">{{ $item->producto->categoria->nombre }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30 animate-pulse">
                                        CRÍTICO
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-3 mb-4">
                                    <div class="bg-zinc-800/50 rounded-lg p-2">
                                        <p class="text-xs text-zinc-400">Stock</p>
                                        <p class="text-lg font-bold text-red-400">{{ $item->stock_actual }}</p>
                                    </div>
                                    <div class="bg-zinc-800/50 rounded-lg p-2">
                                        <p class="text-xs text-zinc-400">Mínimo</p>
                                        <p class="text-lg font-bold text-zinc-300">{{ $item->stock_minimo }}</p>
                                    </div>
                                    <div class="bg-zinc-800/50 rounded-lg p-2">
                                        <p class="text-xs text-zinc-400">Reponer</p>
                                        <p class="text-lg font-bold text-zinc-300">{{ $item->punto_reposicion }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('inventario.show', $item->producto_id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 text-sm font-semibold rounded-lg transition-colors">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>
                                    @can('editar-inventario')
                                        <a href="{{ route('inventario.edit-stock', $item->producto_id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-amber-500 hover:bg-amber-400 text-black text-sm font-bold rounded-lg transition-colors">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Ajustar
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Lista de productos con stock bajo --}}
            @if($bajos->count() > 0)
                <div>
                    <h2 class="text-xl font-bold text-yellow-400 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Productos con Stock Bajo
                    </h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($bajos as $item)
                            <div class="bg-zinc-900/95 border border-yellow-500/30 rounded-xl p-5 hover:border-yellow-500/50 transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-zinc-100 mb-1">{{ $item->producto->nombre }}</h3>
                                        <p class="text-sm text-zinc-400">{{ $item->producto->categoria->nombre }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                        BAJO
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-3 mb-4">
                                    <div class="bg-zinc-800/50 rounded-lg p-2">
                                        <p class="text-xs text-zinc-400">Stock</p>
                                        <p class="text-lg font-bold text-yellow-400">{{ $item->stock_actual }}</p>
                                    </div>
                                    <div class="bg-zinc-800/50 rounded-lg p-2">
                                        <p class="text-xs text-zinc-400">Mínimo</p>
                                        <p class="text-lg font-bold text-zinc-300">{{ $item->stock_minimo }}</p>
                                    </div>
                                    <div class="bg-zinc-800/50 rounded-lg p-2">
                                        <p class="text-xs text-zinc-400">Reponer</p>
                                        <p class="text-lg font-bold text-zinc-300">{{ $item->punto_reposicion }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('inventario.show', $item->producto_id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 text-sm font-semibold rounded-lg transition-colors">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>
                                    @can('editar-inventario')
                                        <a href="{{ route('inventario.edit-stock', $item->producto_id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-amber-500 hover:bg-amber-400 text-black text-sm font-bold rounded-lg transition-colors">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Ajustar
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Empty state --}}
            @if($criticos->count() === 0 && $bajos->count() === 0)
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-green-500/20 rounded-full grid place-items-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-zinc-200 mb-2">¡Todo en orden!</h3>
                    <p class="text-zinc-400">No hay productos con alertas de stock en este momento</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
