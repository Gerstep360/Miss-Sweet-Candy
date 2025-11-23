{{-- filepath: resources/views/inventario/edit-umbrales.blade.php --}}
<x-layouts.app :title="__('Configurar Umbrales')">
    <div class="min-h-screen bg-zinc-950 text-white flex items-center justify-center py-8">
        <main class="w-full max-w-2xl px-4 sm:px-8">
            <div class="mb-8 flex items-center gap-3">
                <a href="{{ route('inventario.show', $inventario->producto_id) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-amber-500 hover:bg-amber-400 text-black rounded-lg shadow transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Volver') }}
                </a>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-100">Configurar Umbrales</h1>
            </div>

            <div class="bg-zinc-900/95 border border-zinc-800 rounded-2xl shadow-xl p-8">
                {{-- Producto info --}}
                <div class="bg-zinc-800/50 rounded-xl p-4 mb-6">
                    <p class="text-xs text-zinc-400 mb-1">{{ __('Producto') }}</p>
                    <p class="text-lg font-bold text-zinc-100">{{ $inventario->producto->nombre }}</p>
                    <p class="text-sm text-zinc-400">{{ $inventario->producto->categoria->nombre }}</p>
                </div>

                {{-- Info box --}}
                <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 mb-6">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-300">
                            <p class="font-bold mb-1">Sobre los umbrales:</p>
                            <ul class="space-y-1 text-blue-200/80">
                                <li>• <strong>Stock Mínimo:</strong> Nivel crítico que genera alerta urgente</li>
                                <li>• <strong>Punto de Reposición:</strong> Nivel recomendado para realizar pedido</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Formulario --}}
                <form action="{{ route('inventario.update-umbrales', $inventario->producto_id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Stock mínimo --}}
                    <div>
                        <label for="stock_minimo" class="block text-sm font-bold text-zinc-200 mb-2">
                            {{ __('Stock Mínimo') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="stock_minimo" id="stock_minimo" min="0" value="{{ old('stock_minimo', $inventario->stock_minimo) }}" required class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-xl text-zinc-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" placeholder="Ej: 5">
                        <p class="text-xs text-zinc-400 mt-1">Nivel crítico que requiere reposición urgente</p>
                        @error('stock_minimo')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Punto de reposición --}}
                    <div>
                        <label for="punto_reposicion" class="block text-sm font-bold text-zinc-200 mb-2">
                            {{ __('Punto de Reposición') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="punto_reposicion" id="punto_reposicion" min="0" value="{{ old('punto_reposicion', $inventario->punto_reposicion) }}" required class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-xl text-zinc-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" placeholder="Ej: 10">
                        <p class="text-xs text-zinc-400 mt-1">Nivel recomendado para realizar un nuevo pedido</p>
                        @error('punto_reposicion')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ubicación --}}
                    <div>
                        <label for="ubicacion" class="block text-sm font-bold text-zinc-200 mb-2">
                            {{ __('Ubicación en Almacén') }}
                        </label>
                        <input type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion', $inventario->ubicacion) }}" class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-xl text-zinc-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" placeholder="Ej: Estante A - Nivel 2">
                        <p class="text-xs text-zinc-400 mt-1">Ubicación física del producto en el almacén</p>
                        @error('ubicacion')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-black font-bold rounded-xl shadow-lg transition-all transform hover:scale-105">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('Guardar Configuración') }}
                        </button>
                        <a href="{{ route('inventario.show', $inventario->producto_id) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 font-semibold rounded-xl shadow transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>

            {{-- Vista previa del estado actual --}}
            <div class="mt-6 bg-zinc-900/95 border border-zinc-800 rounded-2xl shadow-xl p-6">
                <h2 class="text-sm font-bold text-zinc-300 mb-4">Estado Actual</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-zinc-800/50 rounded-lg p-3">
                        <p class="text-xs text-zinc-400 mb-1">Stock Actual</p>
                        <p class="text-2xl font-bold text-zinc-100">{{ $inventario->stock_actual }}</p>
                    </div>
                    <div class="bg-zinc-800/50 rounded-lg p-3">
                        <p class="text-xs text-zinc-400 mb-1">Estado</p>
                        @if($inventario->estado_stock === 'OK')
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30">
                                OK
                            </span>
                        @elseif($inventario->estado_stock === 'BAJO')
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                BAJO
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30 animate-pulse">
                                CRÍTICO
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
