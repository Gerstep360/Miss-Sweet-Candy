{{-- filepath: c:\Users\German\Documents\Proyectos\PHP\Cafeteria\cafeteria\resources\views\cajero\cobro_caja\index.blade.php --}}
<x-layouts.app :title="__('Cobros en Caja')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Cobros en Caja</h1>
                        <p class="text-zinc-300">Registra pagos y consolida ingresos diarios</p>
                    </div>
                </div>
            </div>

            {{-- Pendientes de cobro --}}
            <h2 class="text-xl font-bold text-white mb-4">Pendientes de Cobro</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($pendientesMostrador as $pedido)
                    <div class="dashboard-card hover:bg-zinc-800/40 transition-colors">
                        {{-- ...info del pedido mostrador... --}}
                        <div class="mb-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center border-2 border-black text-black text-xl font-bold">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-white">Mostrador #{{ $pedido->id }}</h3>
                                    <p class="text-sm text-zinc-400">Cliente: {{ $pedido->cliente->name ?? 'Sin cliente' }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded capitalize font-medium">
                                    {{ ucfirst($pedido->estado) }}
                                </span>
                                <span class="bg-zinc-700/50 text-zinc-300 text-xs px-2 py-1 rounded">
                                    Total: ${{ number_format($pedido->subtotal, 2) }}
                                </span>
                            </div>
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
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('cobro_caja.create', ['tipo' => 'mostrador', 'id' => $pedido->id]) }}"
                               class="bg-emerald-600 hover:bg-emerald-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                                <i class="fas fa-cash-register"></i> Cobrar
                            </a>
                            <a href="{{ route('cobro_caja.show', ['tipo' => 'mostrador', 'id' => $pedido->id]) }}"
                               class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full dashboard-card text-center py-12">
                        <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-store text-zinc-500 text-2xl"></i>
                        </div>
                        <h3 class="text-white font-medium mb-2">No hay pedidos de mostrador pendientes</h3>
                        <p class="text-zinc-400 mb-4">Aún no se han registrado pedidos para cobrar.</p>
                    </div>
                @endforelse

                @forelse($pendientesMesa as $pedido)
                    <div class="dashboard-card hover:bg-zinc-800/40 transition-colors">
                        {{-- ...info del pedido mesa... --}}
                        <div class="mb-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center border-2 border-black text-black text-xl font-bold">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-white">Mesa #{{ $pedido->id }}</h3>
                                    <p class="text-sm text-zinc-400">Mesa: {{ $pedido->mesa->nombre ?? '-' }}</p>
                                    <p class="text-sm text-zinc-400">Cliente: {{ $pedido->cliente->name ?? 'Sin cliente' }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded capitalize font-medium">
                                    {{ ucfirst($pedido->estado) }}
                                </span>
                                <span class="bg-zinc-700/50 text-zinc-300 text-xs px-2 py-1 rounded">
                                    Total: ${{ number_format($pedido->subtotal, 2) }}
                                </span>
                            </div>
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
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('cobro_caja.create', ['tipo' => 'mesa', 'id' => $pedido->id]) }}"
                               class="bg-emerald-600 hover:bg-emerald-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                                <i class="fas fa-cash-register"></i> Cobrar
                            </a>
                            <a href="{{ route('cobro_caja.show', ['tipo' => 'mesa', 'id' => $pedido->id]) }}"
                               class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full dashboard-card text-center py-12">
                        <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-utensils text-zinc-500 text-2xl"></i>
                        </div>
                        <h3 class="text-white font-medium mb-2">No hay pedidos de mesa pendientes</h3>
                        <p class="text-zinc-400 mb-4">Aún no se han registrado pedidos para cobrar.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagados --}}
            <h2 class="text-xl font-bold text-white mt-8 mb-4">Pagados</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($pagadosMostrador as $pedido)
                    <div class="dashboard-card hover:bg-zinc-800/40 transition-colors">
                        {{-- ...info del pedido mostrador pagado... --}}
                        <div class="mb-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center border-2 border-black text-black text-xl font-bold">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-white">Mostrador #{{ $pedido->id }}</h3>
                                    <p class="text-sm text-zinc-400">Cliente: {{ $pedido->cliente->name ?? 'Sin cliente' }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="bg-green-600/20 text-green-400 text-xs px-2 py-1 rounded capitalize font-medium">
                                    {{ ucfirst($pedido->estado) }}
                                </span>
                                <span class="bg-zinc-700/50 text-zinc-300 text-xs px-2 py-1 rounded">
                                    Total: ${{ number_format($pedido->subtotal, 2) }}
                                </span>
                            </div>
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
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('cobro_caja.show', ['tipo' => 'mostrador', 'id' => $pedido->id]) }}"
                               class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full dashboard-card text-center py-12">
                        <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-store text-zinc-500 text-2xl"></i>
                        </div>
                        <h3 class="text-white font-medium mb-2">No hay pedidos de mostrador pagados</h3>
                        <p class="text-zinc-400 mb-4">Aún no se han registrado cobros pagados.</p>
                    </div>
                @endforelse

                @forelse($pagadosMesa as $pedido)
                    <div class="dashboard-card hover:bg-zinc-800/40 transition-colors">
                        {{-- ...info del pedido mesa pagado... --}}
                        <div class="mb-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center border-2 border-black text-black text-xl font-bold">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-white">Mesa #{{ $pedido->id }}</h3>
                                    <p class="text-sm text-zinc-400">Mesa: {{ $pedido->mesa->nombre ?? '-' }}</p>
                                    <p class="text-sm text-zinc-400">Cliente: {{ $pedido->cliente->name ?? 'Sin cliente' }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="bg-green-600/20 text-green-400 text-xs px-2 py-1 rounded capitalize font-medium">
                                    {{ ucfirst($pedido->estado) }}
                                </span>
                                <span class="bg-zinc-700/50 text-zinc-300 text-xs px-2 py-1 rounded">
                                    Total: ${{ number_format($pedido->subtotal, 2) }}
                                </span>
                            </div>
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
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('cobro_caja.show', ['tipo' => 'mesa', 'id' => $pedido->id]) }}"
                               class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full dashboard-card text-center py-12">
                        <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-utensils text-zinc-500 text-2xl"></i>
                        </div>
                        <h3 class="text-white font-medium mb-2">No hay pedidos de mesa pagados</h3>
                        <p class="text-zinc-400 mb-4">Aún no se han registrado cobros pagados.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>