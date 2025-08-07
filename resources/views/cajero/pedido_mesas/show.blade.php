{{-- filepath: c:\Users\German\Documents\Proyectos\PHP\Cafeteria\cafeteria\resources\views\cajero\pedido_mesas\show.blade.php --}}
<x-layouts.app :title="__('Detalle del Pedido')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Pedido #{{ $pedido->id }}</h1>
                        <p class="text-zinc-300">Mesa: {{ $pedido->mesa->nombre ?? '-' }}</p>
                    </div>
                    <a href="{{ route('pedido-mesas.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Info del pedido -->
            <div class="dashboard-card mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Información del Pedido</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div class="bg-zinc-800/50 p-3 rounded-lg">
                            <span class="text-zinc-400 text-sm">Cliente:</span>
                            <p class="text-white font-medium">{{ $pedido->cliente->name ?? 'Sin cliente' }}</p>
                        </div>
                        <div class="bg-zinc-800/50 p-3 rounded-lg">
                            <span class="text-zinc-400 text-sm">Atendido por:</span>
                            <p class="text-white font-medium">{{ $pedido->atendidoPor->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="bg-zinc-800/50 p-3 rounded-lg">
                            <span class="text-zinc-400 text-sm">Estado:</span>
                            <span class="
                                @if($pedido->estado == 'pendiente') bg-amber-500/20 text-amber-400
                                @elseif($pedido->estado == 'enviado') bg-blue-600/20 text-blue-400
                                @elseif($pedido->estado == 'anulado') bg-red-600/20 text-red-400
                                @elseif($pedido->estado == 'servido') bg-green-600/20 text-green-400
                                @endif
                                px-2 py-1 rounded text-sm font-medium capitalize
                            ">
                                {{ ucfirst($pedido->estado) }}
                            </span>
                        </div>
                        <div class="bg-zinc-800/50 p-3 rounded-lg">
                            <span class="text-zinc-400 text-sm">Notas:</span>
                            <p class="text-white">{{ $pedido->notas ?? 'Sin notas' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="dashboard-card">
                <h2 class="text-lg font-semibold text-white mb-4">Productos Pedidos</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-zinc-700">
                                <th class="text-left py-3 px-4 text-zinc-300 font-medium">Producto</th>
                                <th class="text-center py-3 px-4 text-zinc-300 font-medium">Cantidad</th>
                                <th class="text-right py-3 px-4 text-zinc-300 font-medium">Precio Unit.</th>
                                <th class="text-right py-3 px-4 text-zinc-300 font-medium">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedido->items as $item)
                            <tr class="border-b border-zinc-800">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        @if($item->producto && $item->producto->imagen_url)
                                            <img src="{{ $item->producto->imagen_url }}" alt="{{ $item->producto->nombre }}" class="w-10 h-10 object-cover rounded-lg">
                                        @else
                                            <div class="w-10 h-10 bg-zinc-800 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-image text-zinc-500"></i>
                                            </div>
                                        @endif
                                        <span class="text-white">{{ $item->producto->nombre ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center text-white">{{ $item->cantidad }}</td>
                                <td class="py-3 px-4 text-right text-white">${{ number_format($item->precio, 2) }}</td>
                                <td class="py-3 px-4 text-right text-white font-medium">${{ number_format($item->cantidad * $item->precio, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-zinc-600">
                                <td colspan="3" class="py-4 px-4 text-right text-white font-bold text-lg">Total:</td>
                                <td class="py-4 px-4 text-right text-green-400 font-bold text-lg">${{ number_format($pedido->subtotal, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>