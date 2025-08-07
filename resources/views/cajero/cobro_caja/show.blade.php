{{-- filepath: c:\Users\German\Documents\Proyectos\PHP\Cafeteria\cafeteria\resources\views\cajero\cobro_caja\show.blade.php --}}
<x-layouts.app :title="__('Detalle de Cobro')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-8">
        <div class="max-w-2xl mx-auto px-4">
            <div class="dashboard-card mb-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-lg flex items-center justify-center border-2 border-black text-black text-3xl font-bold
                        @if($tipo === 'mostrador') bg-emerald-500 @else bg-blue-500 @endif">
                        @if($tipo === 'mostrador')
                            <i class="fas fa-store"></i>
                        @else
                            <i class="fas fa-utensils"></i>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-1">
                            {{ $tipo === 'mostrador' ? 'Mostrador' : 'Mesa' }} #{{ $pedido->id }}
                        </h1>
                        <p class="text-zinc-400 text-sm">
                            {{ $tipo === 'mostrador' ? 'Cliente: ' . ($pedido->cliente->name ?? 'Sin cliente') : 'Mesa: ' . ($pedido->mesa->nombre ?? '-') }}
                        </p>
                        @if($tipo === 'mesa')
                            <p class="text-zinc-400 text-sm">
                                Cliente: {{ $pedido->cliente->name ?? 'Sin cliente' }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="mb-4">
                    <span class="bg-green-600/20 text-green-400 text-xs px-2 py-1 rounded capitalize font-medium">
                        {{ ucfirst($pedido->estado) }}
                    </span>
                    <span class="bg-zinc-700/50 text-zinc-300 text-xs px-2 py-1 rounded ml-2">
                        Total: ${{ number_format($pedido->subtotal, 2) }}
                    </span>
                </div>
                <div class="mb-4 text-zinc-300">
                    <strong>Productos:</strong>
                    <ul class="list-disc ml-6 mt-1">
                        @foreach($pedido->items as $item)
                            <li>
                                {{ $item->producto->nombre ?? '-' }} ({{ $item->cantidad }})
                            </li>
                        @endforeach
                    </ul>
                </div>
                @if($cobro)
                <div class="border-t border-zinc-700 pt-4 mt-4">
                    <h2 class="text-lg font-semibold text-white mb-2 flex items-center gap-2">
                        <i class="fas fa-cash-register text-emerald-400"></i> Detalle del Cobro
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-zinc-300 text-sm">
                        <div>
                            <span class="font-medium text-zinc-400">Importe:</span>
                            ${{ number_format($cobro->importe, 2) }}
                        </div>
                        <div>
                            <span class="font-medium text-zinc-400">Método:</span>
                            <span class="capitalize">{{ $cobro->metodo }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-zinc-400">Estado:</span>
                            <span class="capitalize">{{ $cobro->estado }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-zinc-400">Comprobante:</span>
                            {{ $cobro->comprobante ?? '-' }}
                        </div>
                        <div>
                            <span class="font-medium text-zinc-400">Cajero:</span>
                            {{ $cobro->cajero->name ?? '-' }}
                        </div>
                        <div>
                            <span class="font-medium text-zinc-400">Fecha:</span>
                            {{ $cobro->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
                @else
                    <div class="mt-6 text-amber-400 text-center">
                        <i class="fas fa-exclamation-circle"></i> No hay cobro registrado para este pedido.
                    </div>
                @endif
            </div>
            <div class="flex justify-end">
                <a href="{{ route('cobro_caja.index') }}"
                   class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-6 rounded-lg transition-colors flex items-center gap-2 text-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>