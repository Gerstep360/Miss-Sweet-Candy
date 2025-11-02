{{-- filepath: resources/views/inventario/show.blade.php --}}
<x-layouts.app :title="__('Detalle de Inventario')">
    <div class="min-h-screen bg-zinc-950 text-white flex items-center justify-center py-8">
        <main class="w-full max-w-2xl px-4 sm:px-8">
            <div class="mb-8 flex items-center gap-3">
                <a href="{{ route('inventario.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-amber-500 hover:bg-amber-400 text-black rounded-lg shadow transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Volver') }}
                </a>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-100">Detalle de Inventario</h1>
            </div>

            <div class="bg-zinc-900/95 border border-zinc-800 rounded-2xl shadow-xl p-8">
                {{-- Producto --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-zinc-700">
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-400/30 to-orange-500/30 rounded-xl grid place-items-center shadow">
                        <svg class="w-8 h-8 text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-zinc-100">{{ $inventario->producto->nombre }}</h2>
                        <p class="text-sm text-zinc-400">{{ $inventario->producto->categoria->nombre }}</p>
                    </div>
                </div>

                {{-- Información de stock --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div class="bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-xs text-zinc-400 mb-1">{{ __('Stock Actual') }}</p>
                        <p class="text-3xl font-bold text-zinc-100">{{ $inventario->stock_actual }}</p>
                    </div>
                    <div class="bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-xs text-zinc-400 mb-1">{{ __('Stock Mínimo') }}</p>
                        <p class="text-3xl font-bold text-zinc-100">{{ $inventario->stock_minimo }}</p>
                    </div>
                    <div class="bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-xs text-zinc-400 mb-1">{{ __('Punto de Reposición') }}</p>
                        <p class="text-3xl font-bold text-zinc-100">{{ $inventario->punto_reposicion }}</p>
                    </div>
                    <div class="bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-xs text-zinc-400 mb-1">{{ __('Estado') }}</p>
                        @if($inventario->estado_stock === 'OK')
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-green-500/20 text-green-400 border border-green-500/30">
                                OK
                            </span>
                        @elseif($inventario->estado_stock === 'BAJO')
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                BAJO
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-red-500/20 text-red-400 border border-red-500/30 animate-pulse">
                                CRÍTICO
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Ubicación --}}
                @if($inventario->ubicacion)
                    <div class="bg-zinc-800/50 rounded-xl p-4 mb-6">
                        <p class="text-xs text-zinc-400 mb-1">{{ __('Ubicación') }}</p>
                        <p class="text-sm font-medium text-zinc-100">{{ $inventario->ubicacion }}</p>
                    </div>
                @endif

                {{-- Acciones --}}
                @can('editar-inventario')
                    <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-zinc-700">
                        <a href="{{ route('inventario.edit-stock', $inventario->producto_id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-amber-500 hover:bg-amber-400 text-black font-semibold rounded-xl shadow transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ __('Ajustar Stock') }}
                        </a>
                        <a href="{{ route('inventario.edit-umbrales', $inventario->producto_id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 font-semibold rounded-xl shadow transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ __('Configurar Umbrales') }}
                        </a>
                    </div>
                @endcan
            </div>
        </main>
    </div>
</x-layouts.app>
