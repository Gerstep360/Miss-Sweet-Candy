<x-layouts.app :title="__('Detalle Pedido #' . $pedido->id)">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('cobro_caja.index') }}" 
                           class="w-10 h-10 bg-zinc-700 hover:bg-zinc-600 rounded-lg flex items-center justify-center transition-all hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Pedido #{{ $pedido->id }}</h1>
                            <p class="text-sm text-zinc-400">
                                {{ ucfirst($pedido->tipo) }}
                                @if($pedido->mesa) - {{ $pedido->mesa->nombre }} @endif
                            </p>
                        </div>
                    </div>

                    @if($cobroExistente)
                        <a href="{{ route('cobro_caja.comprobante', $cobroExistente) }}" 
                           class="bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center gap-2 hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Ver Comprobante
                        </a>
                    @else
                        <a href="{{ route('cobro_caja.create', $pedido) }}" 
                           class="bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center gap-2 hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Cobrar Pedido
                        </a>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="dashboard-card bg-green-500/10 border-green-500/30 mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna izquierda: Información del pedido -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Información general -->
                    <div class="dashboard-card">
                        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Información General
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-zinc-800/50 rounded-lg p-4">
                                <p class="text-zinc-400 text-sm mb-1">Tipo de pedido</p>
                                <p class="text-white font-semibold capitalize">{{ $pedido->tipo }}</p>
                            </div>

                            <div class="bg-zinc-800/50 rounded-lg p-4">
                                <p class="text-zinc-400 text-sm mb-1">Estado</p>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                    @if(in_array($pedido->estado, ['servido', 'entregado', 'retirado'])) bg-green-500/20 text-green-400
                                    @elseif($pedido->estado === 'preparado') bg-blue-500/20 text-blue-400
                                    @else bg-amber-500/20 text-amber-400
                                    @endif">
                                    {{ ucfirst($pedido->estado) }}
                                </span>
                            </div>

                            @if($pedido->cliente)
                                <div class="bg-zinc-800/50 rounded-lg p-4">
                                    <p class="text-zinc-400 text-sm mb-1">Cliente</p>
                                    <p class="text-white font-semibold">{{ $pedido->cliente->name }}</p>
                                    @if($pedido->cliente->email)
                                        <p class="text-zinc-400 text-xs mt-1">{{ $pedido->cliente->email }}</p>
                                    @endif
                                </div>
                            @endif

                            @if($pedido->telefono_contacto)
                                <div class="bg-zinc-800/50 rounded-lg p-4">
                                    <p class="text-zinc-400 text-sm mb-1">Teléfono</p>
                                    <p class="text-white font-semibold">{{ $pedido->telefono_contacto }}</p>
                                </div>
                            @endif

                            <div class="bg-zinc-800/50 rounded-lg p-4">
                                <p class="text-zinc-400 text-sm mb-1">Fecha de pedido</p>
                                <p class="text-white font-semibold">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            @if($pedido->atendidoPor)
                                <div class="bg-zinc-800/50 rounded-lg p-4">
                                    <p class="text-zinc-400 text-sm mb-1">Atendido por</p>
                                    <p class="text-white font-semibold">{{ $pedido->atendidoPor->name }}</p>
                                </div>
                            @endif
                        </div>

                        @if($pedido->notas)
                            <div class="mt-4 bg-amber-500/10 border border-amber-500/30 rounded-lg p-4">
                                <p class="text-amber-300 text-sm font-medium mb-1">Notas del pedido:</p>
                                <p class="text-amber-200">{{ $pedido->notas }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Detalle de productos -->
                    <div class="dashboard-card">
                        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Productos del Pedido
                        </h2>

                        <div class="space-y-3">
                            @foreach($pedido->items as $item)
                                <div class="bg-gradient-to-br from-zinc-800/50 to-zinc-700/30 rounded-xl p-4 border border-zinc-700/50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-lg overflow-hidden bg-zinc-700 ring-2 ring-zinc-600/50">
                                            <img src="{{ $item->producto->imagen ? asset('storage/' . $item->producto->imagen) : asset('storage/img/none/none.png') }}" 
                                                 alt="{{ $item->producto->nombre }}" 
                                                 class="w-full h-full object-cover"
                                                 onerror="this.src='{{ asset('storage/img/none/none.png') }}'">
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="text-white font-semibold mb-1">{{ $item->producto->nombre }}</h5>
                                            <div class="flex items-center gap-2 text-sm">
                                                <span class="text-zinc-400">{{ $item->cantidad }}x</span>
                                                <span class="text-zinc-400">${{ number_format($item->precio_unitario, 2) }}</span>
                                            </div>
                                            @if($item->notas)
                                                <p class="text-zinc-400 text-xs mt-1">Nota: {{ $item->notas }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-green-400">${{ number_format($item->subtotal_item, 2) }}</p>
                                            <span class="text-xs text-zinc-400 capitalize">{{ $item->estado_item }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Total -->
                        <div class="mt-4 pt-4 border-t border-zinc-700">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-white">TOTAL</span>
                                <span class="text-3xl font-bold text-green-400">
                                    ${{ number_format($pedido->items->sum('subtotal_item'), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Información de cobro -->
                <div class="lg:col-span-1">
                    @if($cobroExistente)
                        <div class="dashboard-card sticky top-6">
                            <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Información de Cobro
                            </h2>

                            <div class="space-y-4">
                                <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 text-center">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-green-300 font-semibold mb-1">Pedido Cobrado</p>
                                    <p class="text-2xl font-bold text-green-400">${{ number_format($cobroExistente->importe, 2) }}</p>
                                </div>

                                <div class="bg-zinc-800/50 rounded-lg p-3">
                                    <p class="text-zinc-400 text-sm mb-1">Método de pago</p>
                                    <p class="text-white font-semibold capitalize">
                                        @if($cobroExistente->metodo === 'efectivo')
                                            💵 Efectivo
                                        @elseif($cobroExistente->metodo === 'pos')
                                            💳 Tarjeta/POS
                                        @else
                                            📱 QR/Transferencia
                                        @endif
                                    </p>
                                </div>

                                <div class="bg-zinc-800/50 rounded-lg p-3">
                                    <p class="text-zinc-400 text-sm mb-1">Comprobante</p>
                                    <p class="text-white font-semibold">{{ $cobroExistente->comprobante }}</p>
                                </div>

                                @if($cobroExistente->cajero)
                                    <div class="bg-zinc-800/50 rounded-lg p-3">
                                        <p class="text-zinc-400 text-sm mb-1">Cajero</p>
                                        <p class="text-white font-semibold">{{ $cobroExistente->cajero->name }}</p>
                                    </div>
                                @endif

                                <div class="bg-zinc-800/50 rounded-lg p-3">
                                    <p class="text-zinc-400 text-sm mb-1">Fecha de cobro</p>
                                    <p class="text-white font-semibold">{{ $cobroExistente->created_at->format('d/m/Y H:i') }}</p>
                                </div>

                                @if($cobroExistente->esQr())
                                    <div class="bg-zinc-800/50 rounded-lg p-3">
                                        <p class="text-zinc-400 text-sm mb-1">Proveedor QR</p>
                                        <p class="text-white font-semibold capitalize">{{ $cobroExistente->qr_proveedor }}</p>
                                    </div>

                                    @if($cobroExistente->qr_tx_id)
                                        <div class="bg-zinc-800/50 rounded-lg p-3">
                                            <p class="text-zinc-400 text-sm mb-1">ID Transacción</p>
                                            <p class="text-white font-mono text-xs break-all">{{ $cobroExistente->qr_tx_id }}</p>
                                        </div>
                                    @endif

                                    @if($cobroExistente->qr_referencia)
                                        <div class="bg-zinc-800/50 rounded-lg p-3">
                                            <p class="text-zinc-400 text-sm mb-1">Referencia</p>
                                            <p class="text-white text-sm">{{ $cobroExistente->qr_referencia }}</p>
                                        </div>
                                    @endif

                                    <div class="bg-zinc-800/50 rounded-lg p-3">
                                        <p class="text-zinc-400 text-sm mb-1">Estado QR</p>
                                        @if($cobroExistente->qr_estado === 'aprobado')
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-500/20 text-green-400">
                                                ✓ Aprobado
                                            </span>
                                        @elseif($cobroExistente->qr_estado === 'pendiente')
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-amber-500/20 text-amber-400">
                                                ⏳ Pendiente
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-500/20 text-red-400">
                                                ✗ Rechazado
                                            </span>
                                        @endif
                                    </div>

                                    @if($cobroExistente->qrPendiente())
                                        <div class="space-y-2">
                                            <form action="{{ route('cobro_caja.confirmar_qr', $cobroExistente) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Confirmar Pago
                                                </button>
                                            </form>

                                            <button onclick="rechazarQrModal({{ $cobroExistente->id }})"
                                                    class="w-full bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Rechazar Pago
                                            </button>
                                        </div>
                                    @endif
                                @endif

                                @if($cobroExistente->created_at->isToday() && $cobroExistente->estaCobrado())
                                    <button onclick="cancelarCobroModal({{ $cobroExistente->id }})"
                                            class="w-full bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Cancelar Cobro
                                    </button>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="dashboard-card sticky top-6 text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-zinc-400 mb-4">Este pedido aún no ha sido cobrado</p>
                            <a href="{{ route('cobro_caja.create', $pedido) }}" 
                               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white py-3 px-6 rounded-lg transition-all duration-200 hover:scale-105 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Proceder a Cobrar
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cancelar cobro -->
    <div id="cancelarCobroModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-zinc-900 border border-zinc-700 rounded-xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-4">Cancelar Cobro</h3>
            <form id="cancelarCobroForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-zinc-300 font-medium mb-2">Motivo de cancelación *</label>
                    <textarea name="motivo" 
                              required
                              rows="3"
                              class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                              placeholder="Explique el motivo de la cancelación..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="button" 
                            onclick="document.getElementById('cancelarCobroModal').classList.add('hidden')"
                            class="flex-1 bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-all">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded-lg transition-all">
                        Confirmar Cancelación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para rechazar QR -->
    <div id="rechazarQrModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-zinc-900 border border-zinc-700 rounded-xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-4">Rechazar Pago QR</h3>
            <form id="rechazarQrForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-zinc-300 font-medium mb-2">Motivo de rechazo *</label>
                    <textarea name="motivo" 
                              required
                              rows="3"
                              class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                              placeholder="Explique el motivo del rechazo..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="button" 
                            onclick="document.getElementById('rechazarQrModal').classList.add('hidden')"
                            class="flex-1 bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-all">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded-lg transition-all">
                        Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function cancelarCobroModal(cobroId) {
            const modal = document.getElementById('cancelarCobroModal');
            const form = document.getElementById('cancelarCobroForm');
            form.action = `/cajero/cobros/${cobroId}/cancelar`;
            modal.classList.remove('hidden');
        }

        function rechazarQrModal(cobroId) {
            const modal = document.getElementById('rechazarQrModal');
            const form = document.getElementById('rechazarQrForm');
            form.action = `/cajero/cobros/${cobroId}/rechazar-qr`;
            modal.classList.remove('hidden');
        }
    </script>
</x-layouts.app>