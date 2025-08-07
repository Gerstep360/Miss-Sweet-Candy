<!-- Header del carrito -->
<div class="p-6 pb-4 border-b border-zinc-700 flex-shrink-0">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-white">Carrito</h3>
        <span class="bg-amber-500/20 text-amber-400 px-2 py-1 rounded-full text-xs font-bold" x-text="cantidadTotal"></span>
    </div>
</div>

<!-- Lista de productos del carrito - SCROLL REAL -->
<div class="flex-1 min-h-0" style="height: calc(100vh - 300px);">
    <div class="h-full overflow-y-scroll" 
         @wheel.stop.prevent="$el.scrollTop += $event.deltaY"
         @touchmove.stop>
        <div class="p-6 pt-4 pb-4">
            <div class="space-y-2">
                <template x-for="item in items" :key="item.producto_id">
                    <div class="bg-zinc-700/40 rounded-lg p-2.5 border border-zinc-600/50 hover:border-amber-500/30 transition-all">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-zinc-700 flex-shrink-0">
                                <img :src="item.imagen ? `/storage/${item.imagen}` : '/storage/img/none/none.png'" 
                                     :alt="item.nombre" 
                                     class="w-full h-full object-cover"
                                     onerror="this.src='/storage/img/none/none.png'">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="text-white font-medium text-xs truncate" x-text="item.nombre"></h5>
                                <p class="text-zinc-400 text-xs">$<span x-text="item.precio.toFixed(2)"></span> c/u</p>
                                <p class="text-amber-400 font-bold text-xs">$<span x-text="(item.cantidad * item.precio).toFixed(2)"></span></p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="bg-amber-500/20 text-amber-400 px-1.5 py-0.5 rounded text-xs font-bold" x-text="item.cantidad + 'x'"></span>
                                <button @click.stop="eliminarProducto(item.producto_id)" 
                                        class="text-red-400 hover:text-red-300 w-6 h-6 bg-red-500/20 hover:bg-red-500/30 rounded-md flex items-center justify-center transition-all">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="items.length === 0" class="text-center py-8 text-zinc-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.293 1.293a1 1 0 01-.707.293H5m2 8h6a2 2 0 002-2v-6a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">Carrito vacío</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer del carrito -->
<div class="p-6 pt-4 border-t border-zinc-700 flex-shrink-0">
    <div class="flex justify-between text-white font-bold text-lg mb-4">
        <span>Total:</span>
        <span class="text-amber-400">$<span x-text="total.toFixed(2)"></span></span>
    </div>
    
    <div class="space-y-2">
        <button type="button" 
                @click.stop="limpiarCarrito()"
                :disabled="items.length === 0"
                class="w-full bg-zinc-700 hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed text-white py-2 px-4 rounded-lg transition-colors text-sm">
            Limpiar
        </button>
        <button type="button" 
                @click.stop="confirmar()"
                :disabled="items.length === 0"
                class="w-full bg-amber-600 hover:bg-amber-500 disabled:opacity-50 disabled:cursor-not-allowed text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
            Confirmar Selección
        </button>
    </div>
</div>