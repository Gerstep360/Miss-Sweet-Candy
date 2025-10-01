<!-- filepath: c:\Users\German\Documents\Proyectos\PHP\Cafeteria\cafeteria\resources\views\components\product-selector\cart.blade.php -->

<!-- Header del carrito con gradiente -->
<div class="p-4 sm:p-6 pb-4 border-b border-zinc-700/50 flex-shrink-0 bg-gradient-to-br from-zinc-800/50 to-zinc-900/50">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white">Mi Carrito</h3>
                <p class="text-xs text-zinc-400" x-show="items.length > 0">
                    <span x-text="items.length"></span> <span x-text="items.length === 1 ? 'producto' : 'productos'"></span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="bg-gradient-to-br from-amber-500 to-amber-600 text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-lg shadow-amber-500/20" 
                  x-text="cantidadTotal"
                  x-show="cantidadTotal > 0"
                  x-transition></span>
        </div>
    </div>
</div>

<!-- Lista de productos del carrito - SCROLL OPTIMIZADO -->
<div class="flex-1 min-h-0 relative" style="height: calc(100vh - 320px);">
    <!-- Gradiente superior para indicar scroll -->
    <div class="absolute top-0 left-0 right-0 h-4 bg-gradient-to-b from-zinc-800 to-transparent pointer-events-none z-10"
         x-show="items.length > 3"
         x-transition></div>
    
    <div class="h-full overflow-y-auto overflow-x-hidden custom-scrollbar" 
         @wheel.stop.prevent="$el.scrollTop += $event.deltaY"
         @touchmove.stop>
        <div class="p-3 sm:p-6 pt-4 pb-4">
            <div class="space-y-2 sm:space-y-3">
                <template x-for="(item, index) in items" :key="item.producto_id">
                    <div class="bg-gradient-to-br from-zinc-700/40 to-zinc-800/40 rounded-xl p-3 sm:p-3.5 border border-zinc-600/30 hover:border-amber-500/40 hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-300 group"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100">
                        <div class="flex items-center gap-3">
                            <!-- Imagen del producto mejorada -->
                            <div class="relative flex-shrink-0">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden bg-zinc-700 ring-2 ring-zinc-600/50 group-hover:ring-amber-500/30 transition-all">
                                    <img :src="item.imagen ? `/storage/${item.imagen}` : '/storage/img/none/none.png'" 
                                         :alt="item.nombre" 
                                         class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300"
                                         onerror="this.src='/storage/img/none/none.png'">
                                </div>
                                <!-- Badge de cantidad en la imagen -->
                                <div class="absolute -top-2 -right-2 bg-gradient-to-br from-amber-500 to-amber-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shadow-lg shadow-amber-500/30">
                                    <span x-text="item.cantidad"></span>
                                </div>
                            </div>
                            
                            <!-- Información del producto -->
                            <div class="flex-1 min-w-0">
                                <h5 class="text-white font-semibold text-sm sm:text-base truncate mb-1 group-hover:text-amber-400 transition-colors" 
                                    x-text="item.nombre"></h5>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-zinc-400 text-xs">
                                        $<span x-text="item.precio.toFixed(2)"></span> c/u
                                    </span>
                                    <span class="text-zinc-500">•</span>
                                    <span class="text-xs text-amber-400 font-medium bg-amber-500/10 px-2 py-0.5 rounded">
                                        <span x-text="item.cantidad"></span>x = $<span x-text="(item.cantidad * item.precio).toFixed(2)"></span>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Botón de eliminar mejorado -->
                            <button @click.stop="eliminarProducto(item.producto_id)" 
                                    class="flex-shrink-0 text-red-400 hover:text-red-300 w-9 h-9 sm:w-10 sm:h-10 bg-red-500/10 hover:bg-red-500/20 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95 group/btn"
                                    title="Eliminar producto">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 group-hover/btn:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
                
                <!-- Estado vacío mejorado -->
                <div x-show="items.length === 0" 
                     class="text-center py-12 sm:py-16"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 bg-zinc-700/30 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="text-sm sm:text-base text-zinc-400 font-medium mb-2">Tu carrito está vacío</p>
                    <p class="text-xs sm:text-sm text-zinc-500">Agrega productos para comenzar</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gradiente inferior para indicar scroll -->
    <div class="absolute bottom-0 left-0 right-0 h-4 bg-gradient-to-t from-zinc-800 to-transparent pointer-events-none z-10"
         x-show="items.length > 3"
         x-transition></div>
</div>

<!-- Footer del carrito mejorado -->
<div class="p-4 sm:p-6 pt-4 border-t border-zinc-700/50 flex-shrink-0 bg-gradient-to-br from-zinc-800/50 to-zinc-900/50">
    <!-- Total con animación -->
    <div class="bg-gradient-to-br from-amber-500/10 to-amber-600/10 border border-amber-500/30 rounded-xl p-3 sm:p-4 mb-3 sm:mb-4"
         x-show="items.length > 0"
         x-transition>
        <div class="flex justify-between items-center">
            <div>
                <p class="text-zinc-400 text-xs sm:text-sm mb-1">Total a pagar</p>
                <p class="text-white font-bold text-xl sm:text-2xl">
                    $<span x-text="total.toFixed(2)" class="tabular-nums"></span>
                </p>
            </div>
            <div class="text-right">
                <p class="text-zinc-400 text-xs mb-1">Artículos</p>
                <p class="text-amber-400 font-bold text-lg sm:text-xl" x-text="cantidadTotal"></p>
            </div>
        </div>
    </div>
    
    <!-- Botones de acción responsivos -->
    <div class="grid grid-cols-2 gap-2 sm:gap-3">
        <button type="button" 
                @click.stop="limpiarCarrito()"
                :disabled="items.length === 0"
                class="bg-zinc-700/80 hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed text-white py-2.5 sm:py-3 px-4 rounded-lg sm:rounded-xl transition-all duration-200 text-xs sm:text-sm font-medium flex items-center justify-center gap-2 hover:scale-105 active:scale-95 disabled:hover:scale-100 group"
                title="Vaciar carrito">
            <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span class="hidden sm:inline">Limpiar</span>
            <span class="sm:hidden">Vaciar</span>
        </button>
        
        <button type="button" 
                @click.stop="confirmar()"
                :disabled="items.length === 0"
                class="bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 disabled:opacity-50 disabled:cursor-not-allowed text-white py-2.5 sm:py-3 px-4 rounded-lg sm:rounded-xl transition-all duration-200 text-xs sm:text-sm font-bold flex items-center justify-center gap-2 shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 hover:scale-105 active:scale-95 disabled:hover:scale-100 group"
                title="Confirmar pedido">
            <span>Confirmar</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </button>
    </div>
    
    <!-- Información adicional -->
    <p class="text-center text-xs text-zinc-500 mt-3" x-show="items.length > 0" x-transition>
        Los precios incluyen impuestos
    </p>
</div>

<!-- Estilos para scrollbar personalizada -->
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
    
    /* Animación suave para números */
    .tabular-nums {
        font-variant-numeric: tabular-nums;
    }
</style>