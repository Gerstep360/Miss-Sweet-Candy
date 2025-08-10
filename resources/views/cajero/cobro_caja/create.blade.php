{{-- resources\views\cajero\cobro_caja\create.blade.php --}}
<x-layouts.app :title="__('Registrar Cobro')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Registrar Cobro</h1>
                        <p class="text-zinc-300">Completa el pago y emite comprobante interno</p>
                    </div>
                    <a href="{{ route('cobro_caja.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Datos del Pedido -->
            <div class="dashboard-card mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Datos del Pedido</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div>
                            <span class="text-zinc-400 text-sm">Tipo:</span>
                            <span class="text-white font-medium">{{ ucfirst($tipo) }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 text-sm">Pedido #:</span>
                            <span class="text-white font-medium">{{ $pedido->id }}</span>
                        </div>
                        @if($tipo === 'mesa')
                        <div>
                            <span class="text-zinc-400 text-sm">Mesa:</span>
                            <span class="text-white font-medium">{{ $pedido->mesa->nombre ?? '-' }}</span>
                        </div>
                        @endif
                        <div>
                            <span class="text-zinc-400 text-sm">Cliente:</span>
                            <span class="text-white font-medium">{{ $pedido->cliente->name ?? 'Sin cliente' }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-400 text-sm">Estado:</span>
                            <span class="
                                @if($pedido->estado == 'pendiente') bg-amber-500/20 text-amber-400
                                @elseif($pedido->estado == 'enviado') bg-blue-600/20 text-blue-400
                                @elseif($pedido->estado == 'anulado') bg-red-600/20 text-red-400
                                @elseif($pedido->estado == 'retirado' || $pedido->estado == 'servido') bg-green-600/20 text-green-400
                                @endif
                                px-2 py-1 rounded text-sm font-medium capitalize
                            ">
                                {{ ucfirst($pedido->estado) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-zinc-400 text-sm">Total:</span>
                            <span class="text-emerald-400 font-bold text-lg">${{ number_format($pedido->subtotal, 2) }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-zinc-400 text-sm">Productos:</span>
                        <ul class="mt-2 space-y-1">
                            @foreach($pedido->items as $item)
                            <li class="flex items-center gap-2">
                                <img src="{{ $item->producto && $item->producto->imagen 
                                    ? asset('storage/' . $item->producto->imagen) 
                                    : asset('storage/img/none/none.png') }}"
                                    alt="{{ $item->producto->nombre ?? 'Sin imagen' }}"
                                    class="w-8 h-8 object-cover rounded-lg border border-zinc-700">
                                <span class="text-white text-sm">{{ $item->producto->nombre ?? '-' }}</span>
                                <span class="text-zinc-400 text-xs">x{{ $item->cantidad }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Formulario de Cobro -->
            <form action="{{ route('cobro_caja.store') }}" method="POST" class="dashboard-card space-y-6">
                @csrf
                <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
                <input type="hidden" name="tipo" value="{{ $tipo }}">

                <div>
                    <label class="block text-zinc-300 font-medium mb-2">Importe a cobrar *</label>
                    <input type="number" step="0.01" min="0" name="importe" value="{{ $pedido->subtotal }}" required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-emerald-500"
                        readonly>
                </div>

                <div>
                    <label class="block text-zinc-300 font-medium mb-2">Método de pago *</label>
                    <select name="metodo" required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-emerald-500">
                        <option value="">Seleccionar método</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="pos">POS</option>
                    </select>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-6">
                    <a href="{{ route('cobro_caja.index') }}"
                       class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-6 rounded-lg transition-colors text-center">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white py-2 px-6 rounded-lg transition-colors text-center font-bold">
                        <i class="fas fa-cash-register mr-2"></i>
                        Registrar Cobro
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>