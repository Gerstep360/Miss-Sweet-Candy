<x-layouts.app :title="__('Hacer Pedido en Línea')">
    
    <script>
        function pedidoWebForm() {
            return {
                items: [],
                
                init() {
                    window.addEventListener('products-selected', (event) => {
                        if (event.detail && Array.isArray(event.detail)) {
                            this.items = event.detail;
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
                    if (window.productSelectorData) window.productSelectorData.items = [...this.items];
                },

                limpiarCarrito() {
                    if (confirm('¿Vaciar carrito?')) {
                        this.items = [];
                        if (window.productSelectorData) window.productSelectorData.items = [];
                    }
                },

                onSubmit(e) {
                    if (this.items.length === 0) {
                        e.preventDefault();
                        alert('Tu carrito está vacío.');
                        return false;
                    }
                    this.$refs.submitBtnDesktop.disabled = true;
                    this.$refs.submitBtnMobile.disabled = true;
                    const loader = '<svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                    this.$refs.submitBtnDesktop.innerHTML = loader + ' Procesando...';
                    this.$refs.submitBtnMobile.innerHTML = loader;
                }
            }
        }
    </script>

    <div class="min-h-screen bg-zinc-950 pb-24 lg:pb-10" x-data="pedidoWebForm()">
        
        {{-- Hero Section --}}
        <div class="relative bg-zinc-900 border-b border-zinc-800 pt-6 pb-8 lg:pt-10 lg:pb-12">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-400 text-[10px] font-bold uppercase border border-amber-500/20 tracking-wider">Click & Collect</span>
                        </div>
                        <h1 class="text-2xl lg:text-4xl font-black text-white tracking-tight">
                            Pedido <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">Web</span>
                        </h1>
                        <p class="text-zinc-400 text-sm lg:text-base mt-1 max-w-xl">
                            Sin filas. Elige, pide y recoge en caja.
                        </p>
                    </div>
                    
                    {{-- Resumen Desktop (Oculto en móvil) --}}
                    <div class="hidden lg:block bg-zinc-800/50 backdrop-blur border border-zinc-700/50 rounded-xl p-4 min-w-[250px]">
                        <div class="flex justify-between text-xs text-zinc-400 mb-1">
                            <span>Estimado</span>
                            <span class="bg-zinc-700 px-1.5 rounded text-white" x-text="`${cantidadTotal} items`"></span>
                        </div>
                        <div class="text-2xl font-bold text-white flex items-baseline gap-1">
                            <span class="text-amber-500">$</span>
                            <span x-text="total.toFixed(2)">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 relative z-20">
            
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <h3 class="text-red-400 font-semibold text-sm">Error en el pedido</h3>
                            <ul class="mt-1 text-red-300/80 text-xs list-disc list-inside">
                                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('pedidos.web.store') }}" method="POST" @submit="onSubmit" id="pedidoForm">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
                    
                    {{-- SECCIÓN IZQUIERDA (Carrito) --}}
                    <div class="lg:col-span-8 space-y-6">
                        
                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                            {{-- Header Carrito --}}
                            <div class="p-4 border-b border-zinc-800 flex justify-between items-center bg-zinc-800/30">
                                <h3 class="text-base font-bold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Tu Carrito
                                </h3>
                                <button type="button" 
                                        @click="$dispatch('open-modal', 'product-selector')"
                                        class="text-xs bg-zinc-800 hover:bg-zinc-700 text-white font-medium py-2 px-3 rounded-lg border border-zinc-700 transition-all flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Agregar
                                </button>
                            </div>

                            <div class="p-4 min-h-[200px] max-h-[60vh] overflow-y-auto custom-scrollbar bg-zinc-950/30">
                                {{-- Estado Vacío --}}
                                <div x-show="items.length === 0" class="flex flex-col items-center justify-center h-48 text-zinc-500">
                                    <div class="w-16 h-16 bg-zinc-800/50 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                    <p class="text-sm text-zinc-400">Carrito vacío</p>
                                    <button type="button" @click="$dispatch('open-modal', 'product-selector')" class="mt-2 text-xs text-amber-500 hover:text-amber-400 underline">Ver Menú</button>
                                </div>

                                {{-- Items --}}
                                <template x-for="(item, index) in items" :key="item.producto_id">
                                    <div class="flex gap-3 p-3 mb-3 rounded-lg bg-zinc-800/40 border border-zinc-800 relative group">
                                        {{-- Imagen --}}
                                        <div class="w-16 h-16 rounded-md bg-zinc-800 shrink-0 overflow-hidden relative">
                                            <img :src="item.imagen ? `/storage/${item.imagen}` : '/storage/img/none/none.png'" 
                                                 class="w-full h-full object-cover"
                                                 onerror="this.src='/storage/img/none/none.png'">
                                            <div class="absolute bottom-0 right-0 bg-black/70 px-1.5 text-[10px] text-white font-bold rounded-tl">
                                                x<span x-text="item.cantidad"></span>
                                            </div>
                                        </div>

                                        {{-- Info --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start">
                                                <h4 class="text-sm text-white font-medium truncate pr-6" x-text="item.nombre"></h4>
                                                <span class="text-sm text-amber-400 font-bold" x-text="`$${(item.precio * item.cantidad).toFixed(2)}`"></span>
                                            </div>
                                            <p class="text-[10px] text-zinc-500" x-text="`$${item.precio.toFixed(2)} c/u`"></p>
                                            
                                            {{-- Notas --}}
                                            <input type="text" 
                                                   :name="`productos[${index}][notas]`" 
                                                   x-model="item.notas"
                                                   placeholder="Nota: Sin hielo..." 
                                                   class="mt-2 w-full bg-zinc-900/50 border-0 border-b border-zinc-700 focus:border-amber-500 text-xs px-0 py-1 text-zinc-300 placeholder-zinc-600 focus:ring-0 transition-colors bg-transparent">
                                        </div>

                                        {{-- Eliminar --}}
                                        <button type="button" @click="eliminarProducto(item.producto_id)" class="absolute top-2 right-2 text-zinc-600 hover:text-red-400 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>

                                        <input type="hidden" :name="`productos[${index}][producto_id]`" :value="item.producto_id">
                                        <input type="hidden" :name="`productos[${index}][cantidad]`" :value="item.cantidad">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN DERECHA (Checkout) --}}
                    <div class="lg:col-span-4 space-y-6">
                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-5 shadow-lg lg:sticky lg:top-6">
                            <h3 class="text-white font-bold mb-4 text-sm uppercase tracking-wide text-zinc-400">Detalles de Entrega</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs text-zinc-500 mb-1">Hora estimada (Opcional)</label>
                                    <input type="time" name="hora_recogida" 
                                           class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                           min="{{ now()->format('H:i') }}">
                                </div>

                                <div>
                                    <label class="block text-xs text-zinc-500 mb-1">Instrucciones Generales</label>
                                    <textarea name="notas_generales" rows="2" 
                                              class="w-full bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 resize-none"
                                              placeholder="Ej: Servilletas extra..."></textarea>
                                </div>

                                <div class="pt-4 border-t border-zinc-800">
                                    <div class="flex justify-between items-center text-lg font-bold text-white">
                                        <span>Total</span>
                                        <span class="text-amber-400">$<span x-text="total.toFixed(2)">0.00</span></span>
                                    </div>
                                    <p class="text-[10px] text-zinc-500 mt-1 text-right">Pago en caja al recoger</p>
                                </div>

                                {{-- Botón Submit Desktop --}}
                                <button type="submit" 
                                        x-ref="submitBtnDesktop"
                                        :disabled="items.length === 0"
                                        class="hidden lg:flex w-full bg-amber-500 hover:bg-amber-400 disabled:bg-zinc-700 disabled:text-zinc-500 disabled:cursor-not-allowed text-black font-bold py-3 px-4 rounded-lg shadow-lg transition-all justify-center items-center gap-2 mt-4">
                                    <span>Confirmar Pedido</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        {{-- BARRA FIJA MÓVIL (Solo visible en < lg) --}}
        <div class="fixed bottom-0 left-0 right-0 bg-zinc-900 border-t border-zinc-800 p-4 lg:hidden z-30 shadow-[0_-4px_20px_rgba(0,0,0,0.5)]" x-show="items.length > 0" x-transition.slide.up>
            <div class="flex gap-4 items-center max-w-7xl mx-auto">
                <div class="flex-1">
                    <div class="text-[10px] text-zinc-400 uppercase">Total a pagar</div>
                    <div class="text-xl font-bold text-white flex items-baseline gap-0.5">
                        <span class="text-amber-500 text-sm">$</span>
                        <span x-text="total.toFixed(2)"></span>
                    </div>
                </div>
                <button type="submit" form="pedidoForm" 
                        x-ref="submitBtnMobile"
                        class="flex-1 bg-amber-500 text-black font-bold py-3 rounded-xl shadow-lg active:scale-95 transition-transform flex justify-center items-center gap-2">
                    Confirmar
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>
        </div>

    </div>

    <x-product-selector :productos="$productos" :categorias="$categorias" :promociones="$promociones" />

</x-layouts.app>