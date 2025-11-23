{{-- filepath: resources/views/inventario/index.blade.php --}}
<x-layouts.app :title="__('Inventario de Productos')">
    <div class="min-h-screen bg-zinc-950 text-white">
        <main class="py-8">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Encabezado --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl shadow-lg grid place-items-center">
                            <svg class="w-6 h-6 text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-100 drop-shadow">{{ __('Inventario de Productos') }}</h1>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('inventario.alertas') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-red-600 hover:bg-red-700 text-white rounded-xl shadow transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            {{ __('Alertas') }}
                        </a>
                        <a href="{{ route('inventario.exportar', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-amber-500 hover:bg-amber-400 text-black rounded-xl shadow transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            {{ __('Exportar') }}
                        </a>
                    </div>
                </div>

                {{-- Filtros --}}
                <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5 sm:p-6 mb-6">
                    <form method="GET" action="{{ route('inventario.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="buscar" class="block text-sm font-medium text-zinc-300 mb-2">{{ __('Buscar') }}</label>
                            <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" placeholder="Nombre del producto..." class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label for="categoria_id" class="block text-sm font-medium text-zinc-300 mb-2">{{ __('Categoría') }}</label>
                            <select name="categoria_id" id="categoria_id" class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <option value="">{{ __('Todas') }}</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-zinc-300 mb-2">{{ __('Estado') }}</label>
                            <select name="estado" id="estado" class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <option value="">{{ __('Todos') }}</option>
                                <option value="OK" {{ request('estado') == 'OK' ? 'selected' : '' }}>{{ __('OK') }}</option>
                                <option value="BAJO" {{ request('estado') == 'BAJO' ? 'selected' : '' }}>{{ __('BAJO') }}</option>
                                <option value="CRÍTICO" {{ request('estado') == 'CRÍTICO' ? 'selected' : '' }}>{{ __('CRÍTICO') }}</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-black font-semibold rounded-lg shadow transition-colors">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                {{ __('Filtrar') }}
                            </button>
                            <a href="{{ route('inventario.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 font-semibold rounded-lg shadow transition-colors">
                                {{ __('Limpiar') }}
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Tabla de inventario --}}
                <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl overflow-hidden shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-zinc-800 border-b border-zinc-700">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">{{ __('Producto') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-300 uppercase tracking-wider">{{ __('Categoría') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-zinc-300 uppercase tracking-wider">{{ __('Stock Actual') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-zinc-300 uppercase tracking-wider">{{ __('Stock Mínimo') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-zinc-300 uppercase tracking-wider">{{ __('Estado') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-zinc-300 uppercase tracking-wider">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-700">
                                @forelse($inventarios as $inventario)
                                    <tr class="hover:bg-zinc-800/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-zinc-100">{{ $inventario->producto->nombre }}</div>
                                            <div class="text-xs text-zinc-400">{{ $inventario->ubicacion ?? __('Sin ubicación') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-300">
                                            {{ $inventario->producto->categoria->nombre }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-lg font-bold text-zinc-100">{{ $inventario->stock_actual }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-zinc-400">
                                            {{ $inventario->stock_minimo }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($inventario->estado_stock === 'OK')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30">
                                                    OK
                                                </span>
                                            @elseif($inventario->estado_stock === 'BAJO')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                                    BAJO
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30 animate-pulse">
                                                    CRÍTICO
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('inventario.show', $inventario->id) }}" class="text-blue-400 hover:text-blue-300" title="{{ __('Ver detalle') }}">
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                @can('editar-inventario')
                                                    <a href="{{ route('inventario.edit-stock', $inventario->producto_id) }}" class="text-amber-400 hover:text-amber-300" title="{{ __('Ajustar stock') }}">
                                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('inventario.edit-umbrales', $inventario->producto_id) }}" class="text-purple-400 hover:text-purple-300" title="{{ __('Configurar umbrales') }}">
                                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        </svg>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-16 h-16 text-zinc-600 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                                <p class="text-zinc-400 text-lg">{{ __('No se encontraron productos en el inventario') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($inventarios->hasPages())
                        <div class="px-6 py-4 border-t border-zinc-700">
                            {{ $inventarios->links() }}
                        </div>
                    @endif
                </div>

            </section>
        </main>
    </div>
</x-layouts.app>
