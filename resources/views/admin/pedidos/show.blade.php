
<x-layouts.app :title="__('Detalle del Pedido #' . $pedido->id)">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 
                                {{ $pedido->tipo === 'mesa' ? 'bg-blue-500/20' : '' }}
                                {{ $pedido->tipo === 'mostrador' ? 'bg-amber-500/20' : '' }}
                                {{ $pedido->tipo === 'web' ? 'bg-purple-500/20' : '' }}
                                rounded-xl flex items-center justify-center">
                                @if($pedido->tipo === 'mesa')
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                @elseif($pedido->tipo === 'mostrador')
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">
                                    Pedido #{{ $pedido->id }}
                                    <span class="text-base font-normal text-zinc-400">
                                        ({{ ucfirst($pedido->tipo) }})
                                    </span>
                                </h1>
                                <p class="text-sm text-zinc-400">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('pedidos.index') }}" 
                           class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver
                        </a>
                        @if(in_array($pedido->estado, ['pendiente', 'confirmado']))
                            <a href="{{ route('pedidos.edit', $pedido) }}" 
                               class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Columna izquierda: Detalles del pedido -->
                <div class="xl:col-span-2 space-y-6">
                    <!-- Información general -->
                    <div class="dashboard-card">
                        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Información del Pedido
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Estado -->
                            <div>
                                <label class="block text-zinc-400 text-sm mb-1">Estado</label>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold
                                    {{ $pedido->estado === 'pendiente' ? 'bg-yellow-500/20 text-yellow-400 ring-1 ring-yellow-500/30' : '' }}
                                    {{ $pedido->estado === 'confirmado' ? 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30' : '' }}
                                    {{ $pedido->estado === 'en_preparacion' ? 'bg-orange-500/20 text-orange-400 ring-1 ring-orange-500/30' : '' }}
                                    {{ $pedido->estado === 'preparado' ? 'bg-cyan-500/20 text-cyan-400 ring-1 ring-cyan-500/30' : '' }}
                                    {{ in_array($pedido->estado, ['entregado', 'servido', 'retirado']) ? 'bg-green-500/20 text-green-400 ring-1 ring-green-500/30' : '' }}
                                    {{ in_array($pedido->estado, ['anulado', 'cancelado']) ? 'bg-red-500/20 text-red-400 ring-1 ring-red-500/30' : '' }}">
                                    <div class="w-2 h-2 rounded-full bg-current animate-pulse"></div>
                                    {{ strtoupper(str_replace('_', ' ', $pedido->estado)) }}
                                </span>
                            </div>

                            <!-- Tipo -->
                            <div>
                                <label class="block text-zinc-400 text-sm mb-1">Tipo de Pedido</label>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold
                                    {{ $pedido->tipo === 'mesa' ? 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30' : '' }}
                                    {{ $pedido->tipo === 'mostrador' ? 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30' : '' }}
                                    {{ $pedido->tipo === 'web' ? 'bg-purple-500/20 text-purple-400 ring-1 ring-purple-500/30' : '' }}">
                                    {{ strtoupper($pedido->tipo) }}
                                </span>
                            </div>

                            <!-- Mesa (solo para pedidos de mesa) -->
                            @if($pedido->mesa)
                                <div>
                                    <label class="block text-zinc-400 text-sm mb-1">Mesa</label>
                                    <p class="text-white font-medium">{{ $pedido->mesa->numero }}</p>
                                    <p class="text-zinc-400 text-sm">Capacidad: {{ $pedido->mesa->capacidad }} personas</p>
                                </div>
                            @endif

                            <!-- Cliente -->
                            <div>
                                <label class="block text-zinc-400 text-sm mb-1">Cliente</label>
                                @if($pedido->cliente)
                                    <p class="text-white font-medium">{{ $pedido->cliente->name }}</p>
                                    <p class="text-zinc-400 text-sm">{{ $pedido->cliente->email }}</p>
                                @else
                                    <p class="text-zinc-500 italic">Sin cliente asignado</p>
                                @endif
                            </div>

                            <!-- Teléfono (para mostrador) -->
                            @if($pedido->telefono_contacto)
                                <div>
                                    <label class="block text-zinc-400 text-sm mb-1">Teléfono de Contacto</label>
                                    <p class="text-white font-medium">{{ $pedido->telefono_contacto }}</p>
                                </div>
                            @endif

                            <!-- Atendido por -->
                            <div>
                                <label class="block text-zinc-400 text-sm mb-1">Atendido Por</label>
                                @if($pedido->atendidoPor)
                                    <p class="text-white font-medium">{{ $pedido->atendidoPor->name }}</p>
                                @else
                                    <p class="text-zinc-500 italic">N/A</p>
                                @endif
                            </div>

                            <!-- Canal -->
                            <div>
                                <label class="block text-zinc-400 text-sm mb-1">Canal</label>
                                <p class="text-white font-medium">{{ ucfirst($pedido->canal) }}</p>
                            </div>

                            <!-- Fechas -->
                            <div class="sm:col-span-2">
                                <label class="block text-zinc-400 text-sm mb-1">Fechas</label>
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <div>
                                        <span class="text-zinc-400">Creado:</span>
                                        <span class="text-white font-medium ml-1">{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-zinc-400">Actualizado:</span>
                                        <span class="text-white font-medium ml-1">{{ $pedido->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Notas -->
                            @if($pedido->notas)
                                <div class="sm:col-span-2">
                                    <label class="block text-zinc-400 text-sm mb-1">Notas del Pedido</label>
                                    <div class="bg-zinc-800/50 rounded-lg p-3 border border-zinc-700/50">
                                        <p class="text-white text-sm">{{ $pedido->notas }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Items del pedido -->
                    <div class="dashboard-card">
                        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Productos del Pedido
                            <span class="text-amber-400 text-base">({{ $pedido->items->count() }})</span>
                        </h2>

                        <div class="space-y-3 max-h-[600px] overflow-y-auto custom-scrollbar">
                            @foreach($pedido->items as $item)
                                <div class="bg-gradient-to-br from-zinc-800/50 to-zinc-700/30 rounded-xl p-4 border border-zinc-700/50 hover:border-amber-500/40 transition-all duration-300 group">
                                    <div class="flex items-center gap-4">
                                        <!-- Imagen del producto -->
                                        <div class="relative flex-shrink-0">
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-zinc-700 ring-2 ring-zinc-600/50 group-hover:ring-amber-500/30 transition-all">
                                                <img src="{{ $item->producto->imagen ? asset('storage/' . $item->producto->imagen) : asset('storage/img/none/none.png') }}" 
                                                     alt="{{ $item->producto->nombre }}" 
                                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300"
                                                     onerror="this.src='{{ asset('storage/img/none/none.png') }}'">
                                            </div>
                                            <div class="absolute -top-2 -right-2 bg-gradient-to-br from-amber-500 to-amber-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shadow-lg shadow-amber-500/30">
                                                {{ $item->cantidad }}
                                            </div>
                                        </div>

                                        <!-- Info del producto -->
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-white font-semibold text-base mb-1 group-hover:text-amber-400 transition-colors">
                                                {{ $item->producto->nombre }}
                                            </h5>
                                            <div class="flex items-center gap-2 flex-wrap text-sm mb-2">
                                                <span class="text-zinc-400">
                                                    ${{ number_format($item->precio_unitario, 2) }} c/u
                                                </span>
                                                <span class="text-zinc-600">•</span>
                                                <span class="text-amber-400 font-medium bg-amber-500/10 px-2 py-0.5 rounded">
                                                    {{ $item->cantidad }}x = ${{ number_format($item->subtotal_item, 2) }}
                                                </span>
                                                <span class="text-zinc-600">•</span>
                                                <span class="text-xs px-2 py-0.5 rounded
                                                    {{ $item->estado_item === 'pendiente' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                                    {{ $item->estado_item === 'preparando' ? 'bg-orange-500/20 text-orange-400' : '' }}
                                                    {{ $item->estado_item === 'listo' ? 'bg-green-500/20 text-green-400' : '' }}
                                                    {{ $item->estado_item === 'entregado' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                                    {{ $item->estado_item === 'anulado' ? 'bg-red-500/20 text-red-400' : '' }}">
                                                    {{ ucfirst($item->estado_item) }}
                                                </span>
                                            </div>

                                            @if($item->notas)
                                                <div class="mt-2 bg-zinc-700/50 rounded-lg px-3 py-2 border border-zinc-600/50">
                                                    <p class="text-zinc-300 text-sm">
                                                        <span class="text-zinc-500 font-medium">Nota:</span> {{ $item->notas }}
                                                    </p>
                                                </div>
                                            @endif

                                            <div class="mt-2 flex items-center gap-2 text-xs text-zinc-500">
                                                <span class="px-2 py-1 bg-zinc-700/50 rounded">
                                                    Destino: {{ ucfirst($item->destino) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Resumen y acciones -->
                <div class="xl:col-span-1">
                    <div class="dashboard-card sticky top-6 space-y-6">
                        <!-- Resumen financiero -->
                        <div>
                            <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Resumen
                            </h2>

                            <div class="bg-gradient-to-br from-zinc-800/80 to-zinc-700/50 rounded-xl p-4 border border-zinc-700/50 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-zinc-400 text-sm">Subtotal</span>
                                    <span class="text-white font-semibold">
                                        ${{ number_format($pedido->items->sum('subtotal_item'), 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-zinc-400 text-sm">Productos</span>
                                    <span class="text-white font-semibold">{{ $pedido->items->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-zinc-400 text-sm">Cantidad Total</span>
                                    <span class="text-white font-semibold">{{ $pedido->items->sum('cantidad') }}</span>
                                </div>
                                <div class="pt-3 border-t border-zinc-600/50">
                                    <div class="flex justify-between items-center">
                                        <span class="text-white font-bold">Total</span>
                                        <span class="text-2xl font-bold text-amber-400">
                                            ${{ number_format($pedido->items->sum('subtotal_item'), 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cambiar estado -->
                        @if(!in_array($pedido->estado, ['anulado', 'cancelado', 'entregado', 'servido', 'retirado']))
                            <div>
                                <h3 class="text-base font-semibold text-white mb-3">Cambiar Estado</h3>
                                <form action="{{ route('pedidos.cambiar-estado', $pedido) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    
                                    <select name="estado" 
                                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all mb-3">
                                        <option value="pendiente" {{ $pedido->estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="confirmado" {{ $pedido->estado === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                                        <option value="en_preparacion" {{ $pedido->estado === 'en_preparacion' ? 'selected' : '' }}>En Preparación</option>
                                        <option value="preparado" {{ $pedido->estado === 'preparado' ? 'selected' : '' }}>Preparado</option>
                                        @if($pedido->tipo === 'mesa')
                                            <option value="servido">Servido</option>
                                        @else
                                            <option value="retirado">Retirado</option>
                                            <option value="entregado">Entregado</option>
                                        @endif
                                        <option value="anulado">Anulado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>

                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-green-500/30 hover:scale-105 active:scale-95 font-semibold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Actualizar Estado
                                    </button>
                                </form>
                            </div>
                        @endif

                        <!-- Info adicional -->
                        <div class="
                            {{ $pedido->tipo === 'mesa' ? 'bg-blue-500/10 border-blue-500/30' : '' }}
                            {{ $pedido->tipo === 'mostrador' ? 'bg-amber-500/10 border-amber-500/30' : '' }}
                            {{ $pedido->tipo === 'web' ? 'bg-purple-500/10 border-purple-500/30' : '' }}
                            border rounded-lg p-3">
                            <p class="
                                {{ $pedido->tipo === 'mesa' ? 'text-blue-300' : '' }}
                                {{ $pedido->tipo === 'mostrador' ? 'text-amber-300' : '' }}
                                {{ $pedido->tipo === 'web' ? 'text-purple-300' : '' }}
                                text-xs leading-relaxed">
                                @if($pedido->tipo === 'mesa')
                                    <strong>Pedido de Mesa:</strong> Este pedido está asociado a la mesa {{ $pedido->mesa->numero }}. La mesa se liberará automáticamente cuando el pedido se complete.
                                @elseif($pedido->tipo === 'mostrador')
                                    <strong>Pedido de Mostrador:</strong> Este pedido es para llevar. El cliente puede retirarlo cuando esté listo.
                                @else
                                    <strong>Pedido Web:</strong> Este pedido fue realizado a través de la plataforma web.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(39, 39, 42, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.5);
        }
    </style>
</x-layouts.app>