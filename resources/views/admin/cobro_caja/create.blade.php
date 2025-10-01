<x-layouts.app :title="__('Cobrar Pedido #' . $pedido->id)">
    <script>
        function cobroForm() {
            return {
                metodo: 'efectivo',
                importe: {{ $pedido->items->sum('subtotal_item') }},
                total: {{ $pedido->items->sum('subtotal_item') }},
                recibido: '',
                cambio: 0,
                qrProveedor: '',
                qrReferencia: '',

                init() {
                    this.$watch('metodo', value => {
                        if (value !== 'efectivo') {
                            this.recibido = this.total;
                            this.cambio = 0;
                        } else {
                            this.recibido = '';
                            this.cambio = 0;
                        }
                        
                        // Resetear campos QR
                        if (value !== 'qr') {
                            this.qrProveedor = '';
                            this.qrReferencia = '';
                        }
                    });

                    this.$watch('recibido', value => {
                        if (this.metodo === 'efectivo' && value) {
                            const recibidoNum = parseFloat(value) || 0;
                            this.cambio = Math.max(0, recibidoNum - this.total);
                        }
                    });
                },

                get puedeConfirmar() {
                    if (this.metodo === 'efectivo') {
                        const recibidoNum = parseFloat(this.recibido) || 0;
                        return recibidoNum >= this.total;
                    }
                    if (this.metodo === 'qr') {
                        return this.qrProveedor.trim() !== '';
                    }
                    return true; // POS siempre puede confirmar
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('es-BO', {
                        style: 'currency',
                        currency: 'BOB',
                        minimumFractionDigits: 2
                    }).format(value).replace('BOB', 'Bs');
                }
            }
        }
    </script>

    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header mejorado -->
            <div class="dashboard-card mb-6 bg-gradient-to-r from-green-500/10 to-emerald-500/10 border-green-500/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('cobro_caja.index') }}" 
                           class="w-10 h-10 bg-zinc-700/50 hover:bg-zinc-600 rounded-lg flex items-center justify-center transition-all hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center ring-2 ring-green-500/30">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-2">
                                Cobrar Pedido
                                <span class="text-green-400">#{{ $pedido->id }}</span>
                            </h1>
                            <p class="text-sm text-zinc-400 flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">
                                    {{ ucfirst($pedido->tipo) }}
                                </span>
                                @if($pedido->mesa) 
                                    <span class="text-zinc-500">•</span>
                                    <span>{{ $pedido->mesa->nombre }}</span> 
                                @endif
                                <span class="text-zinc-500">•</span>
                                <span>{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Badge de total -->
                    <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-green-500/20 border border-green-500/30 rounded-lg">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-green-300">Total a cobrar</p>
                            <p class="text-lg font-bold text-green-400">Bs {{ number_format($pedido->items->sum('subtotal_item'), 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="dashboard-card bg-red-500/10 border-red-500/30 mb-6 animate-shake">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-red-400 font-semibold mb-2">Error al procesar el cobro:</h3>
                            <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('cobro_caja.store') }}" 
                  method="POST"
                  x-data="cobroForm()"
                  x-init="init()">
                @csrf
                <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
                <input type="hidden" name="importe" x-model="importe">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna izquierda: Detalle y método -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Detalle del pedido mejorado -->
                        <div class="dashboard-card">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Detalle del Pedido
                                </h2>
                                <span class="text-sm text-zinc-400">{{ $pedido->items->count() }} items</span>
                            </div>

                            <div class="space-y-2 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                                @foreach($pedido->items as $item)
                                    <div class="bg-gradient-to-br from-zinc-800/60 to-zinc-800/40 rounded-xl p-3 border border-zinc-700/50 hover:border-zinc-600/50 transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-14 h-14 rounded-lg overflow-hidden bg-zinc-700 ring-2 ring-zinc-600/50 flex-shrink-0">
                                                <img src="{{ $item->producto->imagen ? asset('storage/' . $item->producto->imagen) : asset('storage/img/none/none.png') }}" 
                                                     alt="{{ $item->producto->nombre }}" 
                                                     class="w-full h-full object-cover"
                                                     onerror="this.src='{{ asset('storage/img/none/none.png') }}'">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h5 class="text-white font-semibold truncate">{{ $item->producto->nombre }}</h5>
                                                <div class="flex items-center gap-2 text-sm mt-1">
                                                    <span class="px-2 py-0.5 bg-zinc-700 rounded text-zinc-300 text-xs font-medium">{{ $item->cantidad }}x</span>
                                                    <span class="text-zinc-400">Bs {{ number_format($item->precio_unitario, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <p class="text-lg font-bold text-green-400">Bs {{ number_format($item->subtotal_item, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Método de pago mejorado -->
                        <div class="dashboard-card">
                            <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Método de Pago
                            </h2>

                            <div class="grid grid-cols-3 gap-3 mb-6">
                                <!-- Efectivo -->
                                <label class="cursor-pointer group">
                                    <input type="radio" 
                                           name="metodo" 
                                           value="efectivo" 
                                           x-model="metodo"
                                           class="sr-only peer">
                                    <div class="bg-zinc-800 peer-checked:bg-gradient-to-br peer-checked:from-green-500/20 peer-checked:to-green-600/10 peer-checked:border-green-500 border-2 border-zinc-700 rounded-xl p-4 text-center transition-all hover:border-zinc-600 hover:scale-105 peer-checked:scale-105 peer-checked:shadow-lg peer-checked:shadow-green-500/20">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-zinc-400 peer-checked:text-green-400 group-hover:text-zinc-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-zinc-300 peer-checked:text-green-400">Efectivo</span>
                                        <p class="text-xs text-zinc-500 mt-1 peer-checked:text-green-300/70">Pago inmediato</p>
                                    </div>
                                </label>

                                <!-- POS/Tarjeta - DESHABILITADO -->
                                <div class="relative group">
                                    <div class="absolute inset-0 bg-zinc-900/80 backdrop-blur-sm rounded-xl z-10 flex items-center justify-center">
                                        <span class="text-zinc-400 text-xs font-semibold px-3 py-1 bg-zinc-800 rounded-full border border-zinc-700">Próximamente</span>
                                    </div>
                                    <div class="bg-zinc-800 border-2 border-zinc-700 rounded-xl p-4 text-center opacity-50">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-zinc-500">POS/Tarjeta</span>
                                        <p class="text-xs text-zinc-600 mt-1">En desarrollo</p>
                                    </div>
                                </div>

                                <!-- QR/Transferencia - DESHABILITADO -->
                                <div class="relative group">
                                    <div class="absolute inset-0 bg-zinc-900/80 backdrop-blur-sm rounded-xl z-10 flex items-center justify-center">
                                        <span class="text-zinc-400 text-xs font-semibold px-3 py-1 bg-zinc-800 rounded-full border border-zinc-700">Próximamente</span>
                                    </div>
                                    <div class="bg-zinc-800 border-2 border-zinc-700 rounded-xl p-4 text-center opacity-50">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-zinc-500">QR/Transfer</span>
                                        <p class="text-xs text-zinc-600 mt-1">En desarrollo</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Campos específicos por método -->
                            <div x-show="metodo === 'efectivo'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="space-y-4">
                                <div class="bg-gradient-to-br from-zinc-800/50 to-zinc-800/30 rounded-xl p-4 border border-zinc-700/50">
                                    <label class="block text-zinc-300 font-semibold mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Monto recibido del cliente
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 text-xl font-bold">Bs</span>
                                        <input type="number" 
                                               x-model="recibido"
                                               step="0.01"
                                               min="0"
                                               placeholder="0.00"
                                               autofocus
                                               class="w-full bg-zinc-900 border-2 border-zinc-700 focus:border-green-500 rounded-xl pl-14 pr-4 py-4 text-white text-2xl font-bold focus:ring-4 focus:ring-green-500/20 transition-all">
                                    </div>
                                    <p class="text-zinc-500 text-xs mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Ingrese el monto exacto que entrega el cliente
                                    </p>
                                </div>

                                <!-- Cambio positivo -->
                                <div x-show="cambio > 0" x-transition class="bg-gradient-to-br from-green-500/20 to-emerald-500/10 border-2 border-green-500/40 rounded-xl p-5 shadow-lg shadow-green-500/10">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-green-300 font-semibold flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Cambio a devolver
                                        </span>
                                        <span class="text-3xl font-bold text-green-400" x-text="`Bs ${cambio.toFixed(2)}`"></span>
                                    </div>
                                    <p class="text-green-200 text-sm">Devuelva este monto al cliente</p>
                                </div>

                                <!-- Monto insuficiente -->
                                <div x-show="recibido && parseFloat(recibido) < total" x-transition class="bg-gradient-to-br from-red-500/20 to-red-600/10 border-2 border-red-500/40 rounded-xl p-4 animate-pulse">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div>
                                            <p class="text-red-300 font-semibold">Monto insuficiente</p>
                                            <p class="text-red-200 text-sm">Faltan Bs <span x-text="(total - parseFloat(recibido || 0)).toFixed(2)"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mensaje para métodos deshabilitados -->
                            <div x-show="metodo !== 'efectivo'" x-transition class="bg-zinc-800/50 border border-zinc-700 rounded-xl p-6 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 bg-zinc-700/50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-zinc-400 mb-2">Método no disponible</h3>
                                <p class="text-sm text-zinc-500">Este método de pago estará disponible próximamente</p>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: Resumen mejorado -->
                    <div class="lg:col-span-1">
                        <div class="dashboard-card sticky top-6 space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Resumen del Cobro
                                </h2>

                                <!-- Info del cliente -->
                                @if($pedido->cliente)
                                    <div class="bg-gradient-to-br from-zinc-800/60 to-zinc-800/40 rounded-xl p-4 mb-4 border border-zinc-700/50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center ring-2 ring-green-500/30">
                                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-zinc-400 text-xs mb-0.5">Cliente</p>
                                                <p class="text-white font-semibold truncate">{{ $pedido->cliente->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Total animado -->
                                <div class="bg-gradient-to-br from-green-500/20 via-green-600/10 to-emerald-600/10 border-2 border-green-500/40 rounded-2xl p-6 shadow-xl shadow-green-500/20">
                                    <div class="text-center mb-4">
                                        <p class="text-green-300 text-sm font-semibold mb-1">Total del Pedido</p>
                                        <div class="flex items-center justify-center gap-2">
                                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-5xl font-black text-green-400" x-text="`Bs ${total.toFixed(2)}`"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="pt-4 border-t border-green-500/30">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-green-300">Cantidad de items:</span>
                                            <span class="text-white font-semibold">{{ $pedido->items->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción mejorados -->
                            <div class="space-y-3">
                                <button type="submit" 
                                        :disabled="!puedeConfirmar"
                                        class="w-full bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 disabled:from-zinc-700 disabled:to-zinc-600 disabled:cursor-not-allowed text-white py-4 px-6 rounded-xl transition-all duration-200 flex items-center justify-center gap-3 shadow-xl hover:shadow-green-500/40 disabled:shadow-none hover:scale-105 active:scale-95 disabled:hover:scale-100 font-bold text-lg disabled:opacity-50">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Confirmar Cobro
                                </button>

                                <a href="{{ route('cobro_caja.index') }}" 
                                   class="w-full bg-zinc-700 hover:bg-zinc-600 text-white py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95 font-medium">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancelar
                                </a>
                            </div>

                            <!-- Ayuda -->
                            <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-sm text-blue-300">
                                        <p class="font-semibold mb-1">Ayuda rápida</p>
                                        <ul class="text-blue-200 space-y-1 text-xs">
                                            <li>• Ingrese el monto recibido</li>
                                            <li>• El cambio se calcula automáticamente</li>
                                            <li>• Confirme cuando esté listo</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
            background: rgba(34, 197, 94, 0.4);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(34, 197, 94, 0.6);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</x-layouts.app>