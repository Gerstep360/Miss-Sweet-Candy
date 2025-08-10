{{--resources\views\cajero\pedido_mostrador\index.blade.php --}}
<x-layouts.app :title="__('Pedidos Mostrador')">
    <div x-data="{ showModal: false, modalAction: '', modalUrl: '', modalMsg: '' }" class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Pedidos Mostrador</h1>
                        <p class="text-zinc-300">Administra los pedidos para llevar</p>
                    </div>
                    @can('crear-ordenes')
                        <a href="{{ route('pedido_mostrador.create') }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nuevo Pedido
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Lista de Pedidos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($pedidos as $pedido)
                <div class="dashboard-card hover:bg-zinc-800/40 transition-colors">
                    <div class="mb-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center border-2 border-black text-black text-xl font-bold">
                                #{{ $pedido->id }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-white">Cliente: {{ $pedido->cliente->name ?? 'Sin cliente' }}</h3>
                                <p class="text-sm text-zinc-400">Atendido por: {{ $pedido->atendidoPor->name ?? '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="
                                @if($pedido->estado == 'pendiente') bg-amber-500/20 text-amber-400
                                @elseif($pedido->estado == 'enviado') bg-blue-600/20 text-blue-400
                                @elseif($pedido->estado == 'anulado') bg-red-600/20 text-red-400
                                @elseif($pedido->estado == 'retirado') bg-green-600/20 text-green-400
                                @endif
                                text-xs px-2 py-1 rounded capitalize font-medium
                            ">
                                {{ ucfirst($pedido->estado) }}
                            </span>
                            <span class="bg-zinc-700/50 text-zinc-300 text-xs px-2 py-1 rounded">
                                Total: ${{ number_format($pedido->subtotal, 2) }}
                            </span>
                        </div>

                        <!-- Preview productos -->
                        <div class="text-xs text-zinc-400 mb-4">
                            <strong>Productos:</strong>
                            @foreach($pedido->items->take(2) as $item)
                                {{ $item->producto->nombre ?? '-' }} ({{ $item->cantidad }}){{ !$loop->last ? ', ' : '' }}
                            @endforeach
                            @if($pedido->items->count() > 2)
                                y {{ $pedido->items->count() - 2 }} más...
                            @endif
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-wrap gap-2">
                        @can('ver-ordenes')
                            <a href="{{ route('pedido_mostrador.show', $pedido) }}" 
                            class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-3 rounded-lg transition-colors flex items-center gap-1 text-sm"
                            title="Ver">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        @endcan
                        @can('editar-ordenes')
                            <a href="{{ route('pedido_mostrador.edit', $pedido) }}" 
                            class="bg-amber-600 hover:bg-amber-500 text-white py-2 px-3 rounded-lg transition-colors flex items-center gap-1 text-sm"
                            title="Editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endcan
                        @if($pedido->estado !== 'retirado')
                            @if($pedido->estado !== 'enviado')
                            @can('procesar-ordenes')
                                <button type="button"
                                    @click="showModal = true; modalAction = 'procesar'; modalUrl = '{{ route('pedido_mostrador.procesar', $pedido) }}'; modalMsg = '¿Enviar este pedido a cocina?';"
                                    class="bg-purple-600 hover:bg-purple-500 text-white py-2 px-3 rounded-lg transition-colors flex items-center gap-1 text-sm"
                                    title="Procesar">
                                    <i class="fas fa-paper-plane"></i> Procesar
                                </button>
                            @endcan
                            @endif
                            @can('completar-ordenes')
                                <button type="button"
                                    @click="showModal = true; modalAction = 'retirar'; modalUrl = '{{ route('pedido_mostrador.confirmarRetiro', $pedido) }}'; modalMsg = '¿Confirmar retiro del pedido?';"
                                    class="bg-green-600 hover:bg-green-500 text-white py-2 px-3 rounded-lg transition-colors flex items-center gap-1 text-sm"
                                    title="Retirar">
                                    <i class="fas fa-check"></i> Retirar
                                </button>
                            @endcan
                        @endif
                        @can('anular-ordenes')
                            <form action="{{ route('pedido_mostrador.destroy', $pedido) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    @click="showModal = true; modalAction = 'eliminar'; modalUrl = '{{ route('pedido_mostrador.destroy', $pedido) }}'; modalMsg = '¿Eliminar este pedido?';"
                                    class="bg-red-600 hover:bg-red-500 text-white py-2 px-3 rounded-lg transition-colors flex items-center gap-1 text-sm"
                                    title="Eliminar">
                                    <i class="fas fa-times"></i> Eliminar
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
                @empty
                <div class="col-span-full dashboard-card text-center py-12">
                    <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-bag text-zinc-500 text-2xl"></i>
                    </div>
                    <h3 class="text-white font-medium mb-2">No hay pedidos de mostrador</h3>
                    <p class="text-zinc-400 mb-4">Aún no se han registrado pedidos para llevar.</p>
                    @can('crear-ordenes')
                    <a href="{{ route('pedido_mostrador.create') }}" class="btn-emerald py-2 px-4 rounded-lg transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Crear primer pedido
                    </a>
                    @endcan
                </div>
                @endforelse
            </div>
        </div>

        <!-- Modal de confirmación con animación -->
        <div x-show="showModal"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div x-show="showModal"
                x-transition:enter="transition ease-out duration-300 delay-150"
                x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 transform scale-95 translate-y-4"
                class="bg-zinc-900 rounded-xl shadow-xl p-6 w-full max-w-md mx-auto">
                <h2 class="text-lg font-bold text-white mb-4">Confirmar acción</h2>
                <p class="text-zinc-300 mb-6" x-text="modalMsg"></p>
                <div class="flex justify-end gap-3">
                    <button @click="showModal = false"
                        class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <form :action="modalUrl" method="POST" class="inline-block">
                        @csrf
                        <template x-if="modalAction === 'eliminar'">
                            @method('DELETE')
                        </template>
                        <button type="submit"
                            class="py-2 px-4 rounded-lg transition-colors text-white"
                            :class="modalAction === 'retirar' ? 'bg-green-600 hover:bg-green-500' : (modalAction === 'eliminar' ? 'bg-red-600 hover:bg-red-500' : 'bg-purple-600 hover:bg-purple-500')">
                            <span x-text="modalAction === 'retirar' ? 'Sí, retirar' : (modalAction === 'eliminar' ? 'Sí, eliminar' : 'Sí, procesar')"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="//unpkg.com/alpinejs" defer></script>
</x-layouts.app>