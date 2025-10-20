{{-- filepath: resources/views/inventario/edit-stock.blade.php --}}
<x-layouts.app :title="__('Ajustar Stock')">
    <div class="min-h-screen bg-zinc-950 text-white flex items-center justify-center py-8">
        <main class="w-full max-w-2xl px-4 sm:px-8">
            <div class="mb-8 flex items-center gap-3">
                <a href="{{ route('inventario.show', $inventario->producto_id) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-amber-500 hover:bg-amber-400 text-black rounded-lg shadow transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Volver') }}
                </a>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-100">Ajustar Stock</h1>
            </div>

            <div class="bg-zinc-900/95 border border-zinc-800 rounded-2xl shadow-xl p-8">
                {{-- Producto info --}}
                <div class="bg-zinc-800/50 rounded-xl p-4 mb-6">
                    <p class="text-xs text-zinc-400 mb-1">{{ __('Producto') }}</p>
                    <p class="text-lg font-bold text-zinc-100">{{ $inventario->producto->nombre }}</p>
                    <p class="text-sm text-zinc-400">Stock actual: <span class="font-bold text-amber-400">{{ $inventario->stock_actual }}</span></p>
                </div>

                {{-- Formulario --}}
                <form action="{{ route('inventario.update-stock', $inventario->producto_id) }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Tipo de movimiento --}}
                    <div>
                        <label for="tipo_movimiento" class="block text-sm font-bold text-zinc-200 mb-2">
                            {{ __('Tipo de Movimiento') }} <span class="text-red-400">*</span>
                        </label>
                        <select name="tipo_movimiento" id="tipo_movimiento" required class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-xl text-zinc-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                            <option value="">{{ __('Seleccionar tipo') }}</option>
                            <option value="ENTRADA">Entrada (+) - Agregar stock</option>
                            <option value="SALIDA">Salida (-) - Retirar stock</option>
                            <option value="AJUSTE">Ajuste Manual - Establecer cantidad exacta</option>
                        </select>
                        <p class="text-xs text-zinc-400 mt-1">
                            <strong>Entrada:</strong> suma la cantidad al stock actual<br>
                            <strong>Salida:</strong> resta la cantidad del stock actual<br>
                            <strong>Ajuste:</strong> establece el stock exacto que indiques
                        </p>
                        @error('tipo_movimiento')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cantidad --}}
                    <div>
                        <label for="cantidad" class="block text-sm font-bold text-zinc-200 mb-2">
                            {{ __('Cantidad') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="cantidad" id="cantidad" min="1" value="{{ old('cantidad') }}" required class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-xl text-zinc-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" placeholder="Ej: 50">
                        @error('cantidad')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-black font-bold rounded-xl shadow-lg transition-all transform hover:scale-105">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('Guardar Movimiento') }}
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
        </main>
    </div>
</x-layouts.app>
