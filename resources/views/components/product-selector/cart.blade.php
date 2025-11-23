<!-- filepath: resources/views/components/product-selector/cart.blade.php -->

<!-- Header del carrito con gradiente -->
<div class="px-3 py-2.5 lg:p-6 lg:pb-4 border-b border-zinc-700/50 flex-shrink-0 bg-gradient-to-br from-zinc-800/50 to-zinc-900/50">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 lg:gap-3">
            <div class="w-8 h-8 lg:w-10 lg:h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm lg:text-lg font-bold text-white">Mi Carrito</h3>
                <p class="text-[10px] lg:text-xs text-zinc-400" x-show="items.length > 0">
                    <span x-text="items.length"></span>
                    <span x-text="items.length === 1 ? 'producto' : 'productos'"></span>
                </p>
            </div>
        </div>

        <span class="bg-gradient-to-br from-amber-500 to-amber-600 text-white px-2 py-1 lg:px-3 lg:py-1.5 rounded-full text-[10px] lg:text-xs font-bold shadow-lg shadow-amber-500/20"
              x-text="cantidadTotal"
              x-show="cantidadTotal > 0"
              x-transition></span>
    </div>
</div>

<!-- Lista de productos del carrito - SCROLL OPTIMIZADO -->
<div class="flex-1 min-h-0 relative overflow-hidden">
    <!-- Gradiente superior -->
    <div class="absolute top-0 left-0 right-0 h-3 bg-gradient-to-b from-zinc-800 to-transparent pointer-events-none z-10"
         x-show="items.length > 2" x-transition></div>

    <div class="h-full overflow-y-auto overflow-x-hidden custom-scrollbar"
         @wheel.stop
         @touchmove.stop>
        <div class="p-2 lg:p-6 lg:pt-4 lg:pb-4">
            <div class="space-y-2 lg:space-y-3">
                <template x-for="(item, index) in items" :key="item.producto_id">
                    <div class="bg-gradient-to-br from-zinc-700/40 to-zinc-800/40 rounded-lg lg:rounded-xl p-2 lg:p-3.5 border border-zinc-600/30 hover:border-amber-500/40 transition-all duration-300 group"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100">

                        <div class="flex items-center gap-2 lg:gap-3">
                            <!-- Imagen del producto con fallback -->
                            <div class="relative flex-shrink-0">
                                <div class="w-12 h-12 lg:w-16 lg:h-16 rounded-lg lg:rounded-xl overflow-hidden bg-zinc-700 ring-2 ring-zinc-600/50 group-hover:ring-amber-500/30 transition-all">
                                    <img :src="item.imagen_url || (item.imagen ? `/storage/${item.imagen}` : '/storage/img/none/none.png')"
                                         :alt="item.nombre"
                                         class="w-full h-full object-cover"
                                         onerror="this.src='/storage/img/none/none.png'">
                                </div>
                                <!-- Badge cantidad -->
                                <div class="absolute -top-1.5 -right-1.5 lg:-top-2 lg:-right-2 bg-gradient-to-br from-amber-500 to-amber-600 text-white w-5 h-5 lg:w-6 lg:h-6 rounded-full flex items-center justify-center text-[10px] lg:text-xs font-bold shadow-lg shadow-amber-500/30">
                                    <span x-text="item.cantidad"></span>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <h5 class="text-white font-semibold text-xs lg:text-base truncate mb-0.5 lg:mb-1 group-hover:text-amber-400 transition-colors"
                                    x-text="item.nombre"></h5>

                                <!-- Bloque de precios (con oferta / ahorro por ítem) -->
                                <div class="flex flex-col gap-0.5 lg:gap-1">
                                    <div class="flex items-center gap-1.5 lg:gap-2 flex-wrap">
                                        <template
                                            x-init="$nextTick(() => {})"
                                            x-if="((item.precio_base ?? item.precio) > item.precio)">
                                            <div class="flex items-center gap-1 lg:gap-2">
                                                <!-- Precio base tachado -->
                                                <span class="text-zinc-400 text-[10px] lg:text-xs line-through">
                                                    $<span x-text="Number(item.precio_base ?? item.precio).toFixed(2)"></span>
                                                </span>
                                                <!-- Precio vigente -->
                                                <span class="text-amber-300 font-semibold text-xs lg:text-sm">
                                                    $<span x-text="Number(item.precio).toFixed(2)"></span>
                                                </span>
                                                <!-- % OFF -->
                                                <span class="text-[9px] lg:text-[10px] font-bold bg-green-500/15 text-green-400 px-1 lg:px-1.5 py-0.5 rounded"
                                                      x-text="(() => {
                                                        const base = Number(item.precio_base ?? item.precio);
                                                        const vig  = Number(item.precio);
                                                        return base > 0 ? `-${Math.round(((base - vig)/base)*100)}%` : '';
                                                      })()"></span>
                                            </div>
                                        </template>

                                        <template x-if="!((item.precio_base ?? item.precio) > item.precio)">
                                            <span class="text-amber-300 font-semibold text-xs lg:text-sm">
                                                $<span x-text="Number(item.precio).toFixed(2)"></span>
                                            </span>
                                        </template>
                                    </div>

                                    <!-- Totales por ítem + ahorro -->
                                    <div class="flex items-center gap-1.5 lg:gap-2 flex-wrap">
                                        <span class="text-[10px] lg:text-xs text-zinc-400">
                                            <span x-text="item.cantidad"></span>x =
                                            $<span x-text="(Number(item.cantidad) * Number(item.precio)).toFixed(2)"></span>
                                        </span>
                                        <template x-if="((item.precio_base ?? item.precio) > item.precio)">
                                            <span class="text-[9px] lg:text-[11px] text-green-400 font-medium bg-green-500/10 px-1.5 py-0.5 rounded">
                                                Ahorras $<span
                                                    x-text="(() => {
                                                        const base = Number(item.precio_base ?? item.precio);
                                                        const vig  = Number(item.precio);
                                                        const cant = Number(item.cantidad);
                                                        return Math.max(0, (base - vig) * cant).toFixed(2);
                                                    })()"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Eliminar -->
                            <button @click.stop="eliminarProducto(item.producto_id)"
                                    class="flex-shrink-0 text-red-400 hover:text-red-300 w-8 h-8 lg:w-10 lg:h-10 bg-red-500/10 hover:bg-red-500/20 rounded-lg flex items-center justify-center transition-all duration-200 active:scale-95 group/btn"
                                    title="Eliminar producto">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Vacío -->
                <div x-show="items.length === 0"
                     class="text-center py-8 lg:py-16"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="w-16 h-16 lg:w-24 lg:h-24 mx-auto mb-3 lg:mb-4 bg-zinc-700/30 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 lg:w-12 lg:h-12 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="text-xs lg:text-base text-zinc-400 font-medium mb-1 lg:mb-2">Tu carrito está vacío</p>
                    <p class="text-[10px] lg:text-sm text-zinc-500">Agrega productos para comenzar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gradiente inferior -->
    <div class="absolute bottom-0 left-0 right-0 h-3 bg-gradient-to-t from-zinc-800 to-transparent pointer-events-none z-10"
         x-show="items.length > 2" x-transition></div>
</div>

<!-- Footer del carrito SIMPLIFICADO Y MEJORADO -->
<div class="px-2 py-2 lg:px-6 lg:py-4 border-t border-zinc-700/50 flex-shrink-0 bg-gradient-to-br from-zinc-800/50 to-zinc-900/50"
     x-data="{ 
        mostrarDesglose: false,
        get subtotalBase() {
            return this.items.reduce((acc, it) => acc + (Number(it.cantidad) * Number(it.precio_base ?? it.precio)), 0);
        },
        get ahorroEspeciales() {
            return this.items.reduce((acc, it) => {
                const base = Number(it.precio_base ?? it.precio);
                const vig = Number(it.precio);
                const cant = Number(it.cantidad);
                return acc + Math.max(0, (base - vig) * cant);
            }, 0);
        },
        get ahorroTotal() {
            return this.ahorroEspeciales + (this.descuentoPromociones || 0);
        }
     }">

    <!-- Resumen económico COMPACTO -->
    <div class="space-y-2" x-show="items.length > 0" x-transition>
        
        <!-- TOTAL DESTACADO (Lo más importante primero) -->
        <div class="bg-gradient-to-br from-amber-500/20 to-amber-600/20 border-2 border-amber-500/50 rounded-lg lg:rounded-xl p-2.5 lg:p-3 shadow-lg">
            <div class="flex justify-between items-center gap-2">
                <div>
                    <p class="text-zinc-300 text-[9px] lg:text-xs uppercase tracking-wide mb-0.5">Total a pagar</p>
                    <p class="text-amber-400 font-bold text-xl lg:text-3xl leading-none">
                        $<span class="tabular-nums" x-text="Number(totalConPromociones || total).toFixed(2)"></span>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-zinc-400 text-[9px] lg:text-xs uppercase mb-0.5">Items</p>
                    <p class="text-amber-400 font-bold text-lg lg:text-2xl leading-none" x-text="cantidadTotal"></p>
                </div>
            </div>
            
            <!-- Mostrar ahorro total si existe -->
            <div x-show="ahorroTotal > 0" 
                 x-transition
                 class="mt-2 pt-2 border-t border-amber-500/30 flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="text-sm lg:text-base">�</span>
                    <p class="text-green-400 font-semibold text-xs lg:text-sm">Estás ahorrando:</p>
                </div>
                <p class="text-green-300 font-bold text-base lg:text-xl">
                    -$<span class="tabular-nums" x-text="ahorroTotal.toFixed(2)"></span>
                </p>
            </div>
        </div>

        <!-- BOTÓN DESGLOSE (Solo si hay descuentos) -->
        <button x-show="ahorroTotal > 0"
                @click="mostrarDesglose = !mostrarDesglose"
                type="button"
                class="w-full bg-zinc-800/60 hover:bg-zinc-700/60 border border-zinc-600/50 rounded-lg p-2 lg:p-2.5 transition-colors flex items-center justify-between text-xs lg:text-sm">
            <span class="text-zinc-300 font-medium">Ver desglose de descuentos</span>
            <svg class="w-4 h-4 text-zinc-400 transition-transform"
                 :class="mostrarDesglose ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- DESGLOSE EXPANDIBLE (Acordeón) -->
        <div x-show="mostrarDesglose && ahorroTotal > 0"
             x-collapse
             x-transition
             class="space-y-1.5 lg:space-y-2">
            
            <!-- Subtotal Original -->
            <div class="bg-zinc-800/40 border border-zinc-700/50 rounded-lg p-2 lg:p-2.5">
                <div class="flex justify-between items-center text-xs lg:text-sm">
                    <p class="text-zinc-400">Subtotal original:</p>
                    <p class="text-white font-semibold">
                        $<span class="tabular-nums" x-text="subtotalBase.toFixed(2)"></span>
                    </p>
                </div>
            </div>

            <!-- Especiales del Día -->
            <div x-show="ahorroEspeciales > 0"
                 class="bg-gradient-to-br from-orange-900/15 to-amber-900/15 border border-orange-500/30 rounded-lg p-2 lg:p-2.5">
                <div class="flex justify-between items-center text-xs lg:text-sm">
                    <div class="flex items-center gap-1 lg:gap-1.5">
                        <span class="text-sm lg:text-base">⭐</span>
                        <p class="text-orange-300 font-medium">Especiales del Día</p>
                    </div>
                    <p class="text-green-400 font-bold">
                        -$<span class="tabular-nums" x-text="ahorroEspeciales.toFixed(2)"></span>
                    </p>
                </div>
            </div>

            <!-- Promociones -->
            <div x-show="promocionesAplicadas?.length > 0"
                 class="bg-gradient-to-br from-purple-900/15 to-pink-900/15 border border-purple-500/30 rounded-lg overflow-hidden">
                <div class="p-2 lg:p-2.5">
                    <div class="flex justify-between items-center mb-1.5">
                        <div class="flex items-center gap-1 lg:gap-1.5">
                            <span class="text-sm lg:text-base">🎁</span>
                            <p class="text-purple-300 font-medium text-xs lg:text-sm">
                                Promociones (<span x-text="promocionesAplicadas?.length || 0"></span>)
                            </p>
                        </div>
                        <p class="text-green-400 font-bold text-xs lg:text-sm">
                            -$<span class="tabular-nums" x-text="Number(descuentoPromociones || 0).toFixed(2)"></span>
                        </p>
                    </div>
                    
                    <!-- Lista de promociones -->
                    <div class="space-y-1 mt-2 pt-2 border-t border-purple-500/20">
                        <template x-for="promo in promocionesAplicadas" :key="promo.id">
                            <div class="flex items-center justify-between gap-2 text-[10px] lg:text-xs">
                                <p class="text-purple-200 truncate" x-text="promo.nombre"></p>
                                <p class="text-purple-300 font-semibold whitespace-nowrap">
                                    -$<span x-text="Number(promo.descuento).toFixed(2)"></span>
                                </p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Subtotal después de especiales -->
            <div x-show="ahorroEspeciales > 0"
                 class="bg-zinc-800/30 border border-zinc-700/40 rounded-lg p-2 lg:p-2.5">
                <div class="flex justify-between items-center text-xs lg:text-sm">
                    <p class="text-zinc-400">Subtotal c/especiales:</p>
                    <p class="text-zinc-300 font-semibold">
                        $<span class="tabular-nums" x-text="Number(total).toFixed(2)"></span>
                    </p>
                </div>
            </div>
        </div>

    <!-- Botones -->
    <div class="grid grid-cols-2 gap-2 lg:gap-3 mt-2 lg:mt-3">
        <button type="button"
                @click.stop="limpiarCarrito()"
                :disabled="items.length === 0"
                class="bg-zinc-700/80 hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed text-white py-2.5 lg:py-3 px-3 lg:px-4 rounded-lg lg:rounded-xl transition-all duration-200 text-xs lg:text-sm font-medium flex items-center justify-center gap-1.5 lg:gap-2 active:scale-95 disabled:active:scale-100 group"
                title="Vaciar carrito">
            <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>Vaciar</span>
        </button>

        <button type="button"
                @click.stop="confirmar()"
                :disabled="items.length === 0"
                class="bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 disabled:opacity-50 disabled:cursor-not-allowed text-white py-2.5 lg:py-3 px-3 lg:px-4 rounded-lg lg:rounded-xl transition-all duration-200 text-xs lg:text-sm font-bold flex items-center justify-center gap-1.5 lg:gap-2 shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 active:scale-95 disabled:active:scale-100 group"
                title="Confirmar pedido">
            <span>Confirmar</span>
            <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </button>
    </div>

    <!-- Nota -->
    <p class="text-center text-[10px] lg:text-xs text-zinc-500 mt-2 lg:mt-3" x-show="items.length > 0" x-transition>
        Los precios incluyen impuestos
    </p>
</div>

<!-- Estilos de scrollbar -->
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(39,39,42,.3); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(245,158,11,.3); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(245,158,11,.5); }
    .tabular-nums { font-variant-numeric: tabular-nums; }
</style>
