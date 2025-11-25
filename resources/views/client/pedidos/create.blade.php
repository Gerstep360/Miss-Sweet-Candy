<x-layouts.app :title="__('Nuevo Pedido')">
    
    {{-- Lógica Alpine.js --}}
    <script>
        function pedidoWebForm() {
            return {
                items: [],
                horaRecogida: '',
                
                init() {
                    // Escuchar evento del modal de productos
                    window.addEventListener('products-selected', (event) => {
                        if (event.detail && Array.isArray(event.detail)) {
                            this.items = event.detail;
                            // Feedback visual (Toast)
                            if(window.Flux) Flux.toast({ text: 'Carrito actualizado', variant: 'success' });
                        }
                    });
                },

                get total() {
                    return this.items.reduce((sum, item) => sum + (item.cantidad * item.precio), 0);
                },

                get cantidadTotal() {
                    return this.items.reduce((sum, item) => sum + parseInt(item.cantidad), 0);
                },

                eliminarProducto(productoId) {
                    this.items = this.items.filter(item => item.producto_id !== productoId);
                    // Sincronizar estado con el modal selector
                    if (window.productSelectorData) {
                        window.productSelectorData.items = [...this.items];
                    }
                },

                limpiarCarrito() {
                    if (confirm('¿Estás seguro de vaciar el carrito?')) {
                        this.items = [];
                        if (window.productSelectorData) window.productSelectorData.items = [];
                    }
                },

                onSubmit(e) {
                    if (this.items.length === 0) {
                        e.preventDefault();
                        // Usamos un toast o alerta nativa
                        if(window.Flux) Flux.toast({ text: 'Tu carrito está vacío', variant: 'danger' });
                        else alert('Tu carrito está vacío');
                        return false;
                    }

                    // Loading States
                    const loader = '<svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                    
                    if(this.$refs.submitBtnDesktop) {
                        this.$refs.submitBtnDesktop.disabled = true;
                        this.$refs.submitBtnDesktop.innerHTML = loader + ' Confirmando...';
                    }
                    if(this.$refs.submitBtnMobile) {
                        this.$refs.submitBtnMobile.disabled = true;
                        this.$refs.submitBtnMobile.innerHTML = loader;
                    }
                }
            }
        }
    </script>

    {{-- Contenedor Principal --}}
    <div class="min-h-screen bg-zinc-950 pb-32 lg:pb-12" x-data="pedidoWebForm()">
        
        {{-- Header / Hero Section --}}
        <div class="relative bg-zinc-900 border-b border-zinc-800 pt-6 pb-8 mb-8">
            {{-- Patrón de fondo sutil --}}
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10 pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    
                    {{-- Títulos y Badges --}}
                    <div>
                        <a href="{{ route('pedidos.index') }}" class="inline-flex items-center gap-1 text-zinc-400 hover:text-white text-sm mb-3 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Volver a Mis Pedidos
                        </a>
                        <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight flex items-center gap-3">
                            Tu Pedido
                            <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-500 text-xs font-bold uppercase border border-amber-500/20 tracking-wide">Click & Collect</span>
                        </h1>
                        <p class="mt-2 text-zinc-400">Personaliza tu orden y recógela en barra sin filas.</p>
                    </div>

                    {{-- Resumen Rápido (Desktop) --}}
                    <div class="hidden md:flex items-center gap-6 bg-black/20 backdrop-blur-md border border-white/5 rounded-2xl p-4 px-6">
                        <div class="text-right">
                            <p class="text-xs text-zinc-400 uppercase tracking-wider font-medium">Estimado</p>
                            <div class="text-2xl font-bold text-white font-mono">
                                $<span x-text="total.toFixed(2)">0.00</span>
                            </div>
                        </div>
                        <div class="h-10 w-px bg-zinc-700"></div>
                        <div class="text-right">
                            <p class="text-xs text-zinc-400 uppercase tracking-wider font-medium">Items</p>
                            <div class="text-2xl font-bold text-amber-500 font-mono" x-text="cantidadTotal">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
            
            {{-- Manejo de Errores --}}
            @if($errors->any())
                <div class="mb-8 p-4 rounded-xl bg-red-500/10 border border-red-500/20 backdrop-blur-sm animate-pulse">
                    <div class="flex gap-4">
                        <div class="bg-red-500/20 p-2 rounded-lg h-fit">
                            <svg class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-red-400 font-bold">No pudimos procesar tu pedido</h3>
                            <ul class="mt-1 text-red-300/80 text-sm list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('pedidos.web.store') }}" method="POST" @submit="onSubmit" id="pedidoForm">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    {{-- SECCIÓN IZQUIERDA: Carrito --}}
                    <div class="lg:col-span-8 space-y-6">
                        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden shadow-xl">
                            
                            {{-- Header Carrito --}}
                            <div class="px-6 py-5 border-b border-zinc-800 flex justify-between items-center bg-zinc-800/30">
                                <h3 class="font-bold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Productos Seleccionados
                                </h3>
                                <button type="button" 
                                        @click="$dispatch('open-modal', 'product-selector')"
                                        class="text-xs font-bold bg-zinc-800 hover:bg-zinc-700 text-white py-2 px-4 rounded-lg border border-zinc-700 transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    AGREGAR
                                </button>
                            </div>

                            <div class="p-6 min-h-[300px] max-h-[60vh] overflow-y-auto custom-scrollbar bg-zinc-950/30 relative">
                                
                                {{-- Estado Vacío --}}
                                <div x-show="items.length === 0" class="absolute inset-0 flex flex-col items-center justify-center text-zinc-500" x-transition>
                                    <div class="w-24 h-24 bg-zinc-800/50 rounded-full flex items-center justify-center mb-4 ring-1 ring-white/5">
                                        <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                    <p class="text-lg font-medium text-zinc-400">Tu carrito está hambriento</p>
                                    <button type="button" @click="$dispatch('open-modal', 'product-selector')" class="mt-3 text-sm text-amber-500 hover:text-amber-400 font-medium hover:underline">
                                        Explorar el Menú
                                    </button>
                                </div>

                                {{-- Lista de Items --}}
                                <div class="space-y-4" x-show="items.length > 0" x-transition>
                                    <template x-for="(item, index) in items" :key="item.producto_id">
                                        <div class="group flex gap-4 p-4 rounded-xl bg-zinc-800/40 border border-zinc-800 hover:border-amber-500/30 hover:bg-zinc-800 transition-all duration-200 relative overflow-hidden">
                                            
                                            {{-- Imagen --}}
                                            <div class="w-20 h-20 rounded-lg bg-zinc-800 shrink-0 overflow-hidden relative shadow-inner">
                                                <img :src="item.imagen ? `/storage/${item.imagen}` : '/storage/img/none/none.png'" 
                                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                                     onerror="this.src='/storage/img/none/none.png'">
                                                <div class="absolute bottom-0 right-0 bg-amber-500 text-black font-bold px-2 py-0.5 text-[10px] rounded-tl-lg">
                                                    x<span x-text="item.cantidad"></span>
                                                </div>
                                            </div>

                                            {{-- Info y Notas --}}
                                            <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
                                                <div>
                                                    <div class="flex justify-between items-start gap-4">
                                                        <h4 class="text-white font-bold text-base truncate" x-text="item.nombre"></h4>
                                                        <span class="text-amber-400 font-bold font-mono" x-text="`$${(item.precio * item.cantidad).toFixed(2)}`"></span>
                                                    </div>
                                                    <p class="text-xs text-zinc-500" x-text="`Precio unitario: $${item.precio.toFixed(2)}`"></p>
                                                </div>

                                                <div class="mt-2">
                                                    <input type="text" 
                                                           :name="`productos[${index}][notas]`" 
                                                           x-model="item.notas"
                                                           placeholder="Nota para cocina (Ej: Sin hielo)" 
                                                           class="w-full bg-transparent border-0 border-b border-zinc-700 focus:border-amber-500 px-0 py-1 text-sm text-zinc-300 placeholder-zinc-600 focus:ring-0 transition-colors">
                                                </div>
                                            </div>

                                            {{-- Eliminar --}}
                                            <button type="button" 
                                                    @click="eliminarProducto(item.producto_id)"
                                                    class="absolute top-2 right-2 p-2 text-zinc-600 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>

                                            {{-- Inputs Ocultos --}}
                                            <input type="hidden" :name="`productos[${index}][producto_id]`" :value="item.producto_id">
                                            <input type="hidden" :name="`productos[${index}][cantidad]`" :value="item.cantidad">
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            {{-- Footer Carrito (Desktop) --}}
                            <div class="bg-zinc-900 p-5 border-t border-zinc-800 flex justify-between items-center" x-show="items.length > 0">
                                <button type="button" @click="limpiarCarrito()" class="text-xs font-medium text-red-400 hover:text-red-300 flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-red-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Vaciar Todo
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN DERECHA: Checkout --}}
                    <div class="lg:col-span-4 space-y-6">
                        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-2xl lg:sticky lg:top-6">
                            
                            <div class="flex items-center gap-2 mb-6 pb-4 border-b border-zinc-800">
                                <div class="bg-amber-500/10 p-2 rounded-lg text-amber-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="text-white font-bold">Detalles de Entrega</h3>
                            </div>

                            <div class="space-y-5">
                                {{-- Hora --}}
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Hora de recogida (Opcional)</label>
                                    <div class="relative">
                                        <input type="time" name="hora_recogida" 
                                               class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors [color-scheme:dark]"
                                               min="{{ now()->format('H:i') }}">
                                    </div>
                                    <p class="text-[10px] text-zinc-500 mt-1.5 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Preparamos tu pedido al instante si no especificas hora.
                                    </p>
                                </div>

                                {{-- Notas Generales --}}
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Instrucciones Generales</label>
                                    <textarea name="notas_generales" rows="3" 
                                              class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-sm text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors resize-none placeholder-zinc-600"
                                              placeholder="Ej: Servilletas extra, alérgico al maní..."></textarea>
                                </div>

                                {{-- Total Breakdown --}}
                                <div class="pt-6 border-t border-zinc-800 space-y-2">
                                    <div class="flex justify-between items-center text-sm text-zinc-400">
                                        <span>Subtotal</span>
                                        <span class="text-white font-medium">$<span x-text="total.toFixed(2)"></span></span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm text-zinc-400">
                                        <span>Servicio</span>
                                        <span class="text-green-400 font-medium">$0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 mt-2 border-t border-zinc-800/50">
                                        <span class="text-white font-bold text-lg">Total a Pagar</span>
                                        <span class="text-2xl font-black text-amber-500 tracking-tight">$<span x-text="total.toFixed(2)">0.00</span></span>
                                    </div>
                                    <p class="text-xs text-center text-zinc-500 mt-2 bg-zinc-950/50 py-2 rounded-lg border border-zinc-800">
                                        💳 Pago en caja al momento de recoger
                                    </p>
                                </div>

                                {{-- Botón Submit Desktop --}}
                                <button type="submit" 
                                        x-ref="submitBtnDesktop"
                                        :disabled="items.length === 0"
                                        class="hidden lg:flex w-full bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 disabled:from-zinc-700 disabled:to-zinc-700 disabled:text-zinc-500 disabled:cursor-not-allowed text-black font-black py-4 px-4 rounded-xl shadow-lg shadow-orange-500/20 transition-all justify-center items-center gap-2 text-lg transform active:scale-[0.98]">
                                    <span>CONFIRMAR PEDIDO</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        {{-- BARRA FIJA MÓVIL (Sticky Footer) --}}
        <div class="fixed bottom-0 left-0 right-0 bg-zinc-900/95 backdrop-blur-xl border-t border-zinc-800 p-4 lg:hidden z-40 shadow-[0_-4px_30px_rgba(0,0,0,0.6)] transform transition-transform duration-300"
             x-show="items.length > 0"
             x-transition:enter="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="translate-y-0"
             x-transition:leave-end="translate-y-full">
            
            <div class="flex gap-4 items-center max-w-7xl mx-auto">
                <div class="flex-1">
                    <div class="text-[10px] text-zinc-400 uppercase tracking-wide font-bold">Total a pagar</div>
                    <div class="text-2xl font-black text-white flex items-baseline gap-0.5">
                        <span class="text-amber-500 text-sm">$</span>
                        <span x-text="total.toFixed(2)"></span>
                    </div>
                </div>
                <button type="submit" form="pedidoForm" 
                        x-ref="submitBtnMobile"
                        class="flex-1 bg-amber-500 text-black font-bold py-3.5 rounded-xl shadow-lg shadow-amber-500/20 active:scale-95 transition-transform flex justify-center items-center gap-2">
                    CONFIRMAR
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>
        </div>

    </div>

    {{-- Componente Selector (Reutilizable) --}}
    <x-product-selector :productos="$productos" :categorias="$categorias" :promociones="$promociones" />

</x-layouts.app>