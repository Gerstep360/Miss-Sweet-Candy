{{-- filepath: c:\Users\German\Documents\Proyectos\PHP\Cafeteria\cafeteria\resources\views\admin\pedidos\index.blade.php --}}
<x-layouts.app :title="__('Pedidos')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header con filtros -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Gestión de Pedidos</h1>
                        <p class="text-sm sm:text-base text-zinc-300">Administra todos los pedidos del sistema</p>
                    </div>
                    
                    <!-- Botones de acción - Solo para cajero y admin -->
                    @canany(['crear-pedidos-mesa', 'crear-pedidos-mostrador'])
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        @can('crear-pedidos-mesa')
                        <a href="{{ route('pedidos.mesa.create') }}" 
                           class="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span class="font-semibold">Pedido Mesa</span>
                        </a>
                        @endcan
                        
                        @can('crear-pedidos-mostrador')
                        <a href="{{ route('pedidos.mostrador.create') }}" 
                           class="bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-amber-500/30 hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span class="font-semibold">Pedido Mostrador</span>
                        </a>
                        @endcan
                    </div>
                    @endcanany
                </div>

                <!-- Filtros -->
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Tipo de Pedido</label>
                        <select name="tipo" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                            <option value="">Todos los tipos</option>
                            <option value="mesa" {{ request('tipo') == 'mesa' ? 'selected' : '' }}>Mesa</option>
                            <option value="mostrador" {{ request('tipo') == 'mostrador' ? 'selected' : '' }}>Mostrador</option>
                            <option value="web" {{ request('tipo') == 'web' ? 'selected' : '' }}>Web</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Estado</label>
                        <select name="estado" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                            <option value="en_preparacion" {{ request('estado') == 'en_preparacion' ? 'selected' : '' }}>En Preparación</option>
                            <option value="preparado" {{ request('estado') == 'preparado' ? 'selected' : '' }}>Preparado</option>
                            <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                            <option value="servido" {{ request('estado') == 'servido' ? 'selected' : '' }}>Servido</option>
                            <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-2 flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filtrar
                        </button>
                        <a href="{{ route('pedidos.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Lista de pedidos -->
            <div class="grid grid-cols-1 gap-4 sm:gap-6">
                @forelse($pedidos as $pedido)
                    <div class="dashboard-card hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 group">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                            <!-- Información principal -->
                            <div class="flex-1 space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Tipo de pedido badge -->
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                                        {{ $pedido->tipo === 'mesa' ? 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30' : '' }}
                                        {{ $pedido->tipo === 'mostrador' ? 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30' : '' }}
                                        {{ $pedido->tipo === 'web' ? 'bg-purple-500/20 text-purple-400 ring-1 ring-purple-500/30' : '' }}">
                                        @if($pedido->tipo === 'mesa')
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            MESA
                                        @elseif($pedido->tipo === 'mostrador')
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                            MOSTRADOR
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                            </svg>
                                            WEB
                                        @endif
                                    </span>

                                    <!-- Estado badge -->
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                                        {{ $pedido->estado === 'pendiente' ? 'bg-yellow-500/20 text-yellow-400 ring-1 ring-yellow-500/30' : '' }}
                                        {{ $pedido->estado === 'confirmado' ? 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30' : '' }}
                                        {{ $pedido->estado === 'en_preparacion' ? 'bg-orange-500/20 text-orange-400 ring-1 ring-orange-500/30' : '' }}
                                        {{ $pedido->estado === 'preparado' ? 'bg-cyan-500/20 text-cyan-400 ring-1 ring-cyan-500/30' : '' }}
                                        {{ in_array($pedido->estado, ['entregado', 'servido', 'retirado']) ? 'bg-green-500/20 text-green-400 ring-1 ring-green-500/30' : '' }}
                                        {{ in_array($pedido->estado, ['anulado', 'cancelado']) ? 'bg-red-500/20 text-red-400 ring-1 ring-red-500/30' : '' }}">
                                        <div class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></div>
                                        {{ strtoupper(str_replace('_', ' ', $pedido->estado)) }}
                                    </span>

                                    <!-- ID del pedido -->
                                    <span class="text-zinc-400 text-xs font-mono">#{{ $pedido->id }}</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                    @if($pedido->mesa)
                                        <div class="flex items-center gap-2 text-zinc-300">
                                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <span>{{ $pedido->mesa->numero }}</span>
                                        </div>
                                    @endif

                                    @if($pedido->cliente)
                                        <div class="flex items-center gap-2 text-zinc-300">
                                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span>{{ $pedido->cliente->name }}</span>
                                        </div>
                                    @endif

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
                                        <span>{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total y acciones -->
                            <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-3 lg:min-w-[200px]">
                                <div class="text-right">
                                    <p class="text-zinc-400 text-xs mb-1">Total</p>
                                    <p class="text-2xl sm:text-3xl font-bold text-amber-400">
                                        ${{ number_format($pedido->items->sum('subtotal_item'), 2) }}
                                    </p>
                                </div>

                                <div class="flex gap-2">
                                    @can('ver-pedidos')
                                    <a href="{{ route('pedidos.show', $pedido) }}" 
                                       class="bg-zinc-700 hover:bg-zinc-600 text-white p-2 rounded-lg transition-all duration-200 hover:scale-110 active:scale-95"
                                       title="Ver detalles">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @endcan

                                    @if(in_array($pedido->estado, ['pendiente', 'confirmado']))
                                        @if($pedido->tipo === 'mesa')
                                            @can('editar-pedidos-mesa')
                                            <a href="{{ route('pedidos.edit', $pedido) }}" 
                                               class="bg-blue-600 hover:bg-blue-500 text-white p-2 rounded-lg transition-all duration-200 hover:scale-110 active:scale-95"
                                               title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            @endcan
                                        @else
                                            @can('editar-pedidos-mostrador')
                                            <a href="{{ route('pedidos.edit', $pedido) }}" 
                                               class="bg-blue-600 hover:bg-blue-500 text-white p-2 rounded-lg transition-all duration-200 hover:scale-110 active:scale-95"
                                               title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            @endcan
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="dashboard-card text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-zinc-700/30 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-lg text-zinc-400 font-medium mb-2">No hay pedidos registrados</p>
                        <p class="text-sm text-zinc-500">
                            @can('crear-pedidos-mesa')
                                Crea tu primer pedido para comenzar
                            @else
                                Aquí aparecerán tus pedidos
                            @endcan
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if($pedidos->hasPages())
                <div class="mt-6">
                    {{ $pedidos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>