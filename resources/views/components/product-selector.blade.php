{{-- Modal wrapper con Alpine.js --}}
@props([
    // Espera arrays de productos (con categoria e inventario) y categorias
    // Opcionalmente puedes pasar selectedItems si reabres el modal para editar
    'productos' => [],
    'categorias' => [],
    'selectedItems' => [],
])

<div x-data="{ open: false }"
     @open-modal.window="if ($event.detail === 'product-selector') { open = true; document.body.style.overflow = 'hidden'; }"
     @close-modal.window="if ($event.detail === 'product-selector') { open = false; document.body.style.overflow = 'auto'; }"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-hidden"
     style="display: none;">

    <!-- Overlay Principal -->
    <div x-show="open"
         x-transition.opacity.duration.300ms
         @click="$dispatch('close-modal', 'product-selector')"
         class="fixed inset-0 bg-black/90 backdrop-blur-sm"
         id="product-selector-overlay"></div>

    <!-- Modal -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 flex items-center justify-center lg:p-4 xl:p-6 z-[100]"
         @click.stop>

        <div x-data="productSelector(@js($productos ?? []), @js($categorias ?? []), @js($selectedItems ?? []))"
             class="w-full h-full lg:max-w-7xl lg:max-h-[90vh] bg-zinc-900 lg:rounded-2xl shadow-2xl flex flex-col overflow-hidden">

            <!-- Header -->
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-3 py-2.5 lg:p-6 flex-shrink-0">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 lg:gap-4 min-w-0 flex-1">
                        <div class="w-8 h-8 lg:w-12 lg:h-12 bg-white/20 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-sm lg:text-2xl font-bold text-white truncate">Selector de Productos</h1>
                            <p class="text-xs text-amber-100 hidden lg:block">Selecciona los productos para tu pedido</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 lg:gap-6 flex-shrink-0">
                        <!-- Items/Total compacto en móvil -->
                        <div class="bg-white/20 rounded-lg px-2 py-1 lg:px-3 lg:py-2">
                            <div class="text-center">
                                <div class="text-[10px] lg:text-xs text-amber-100">Items</div>
                                <div class="text-xs lg:text-xl font-bold text-white" x-text="cantidadTotal"></div>
                            </div>
                        </div>
                        <div class="bg-white/20 rounded-lg px-2 py-1 lg:px-3 lg:py-2">
                            <div class="text-center">
                                <div class="text-[10px] lg:text-xs text-amber-100">Total</div>
                                <div class="text-xs lg:text-xl font-bold text-white">$<span x-text="total.toFixed(2)"></span></div>
                            </div>
                        </div>
                        <button @click="cerrarModal()"
                                class="text-white hover:text-amber-200 p-1.5 lg:p-2 hover:bg-white/10 rounded-lg transition-all flex-shrink-0">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="flex flex-col flex-1 overflow-hidden pb-16 lg:pb-0 lg:flex-row">
                <!-- Col productos -->
                <div class="flex-1 flex flex-col overflow-hidden">
                    <!-- Filtros -->
                    <div class="px-3 py-2 lg:p-6 lg:pb-4 flex-shrink-0 border-b border-zinc-700">
                        <!-- Categorías con scroll horizontal en móvil -->
                        <div class="overflow-x-auto custom-scrollbar-horizontal mb-2 lg:mb-4 -mx-3 px-3 lg:mx-0 lg:px-0">
                            <div class="flex gap-1.5 lg:flex-wrap lg:gap-2 min-w-max lg:min-w-0">
                                <button @click.stop="categoriaActiva = 'todas'"
                                        :class="categoriaActiva === 'todas' ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
                                        class="px-3 py-1.5 lg:px-4 lg:py-2 rounded-full text-xs lg:text-sm font-medium transition-all whitespace-nowrap flex-shrink-0">
                                    Todas (<span x-text="productos.length"></span>)
                                </button>

                                <template x-for="categoria in categorias" :key="categoria.id">
                                    <button @click.stop="categoriaActiva = categoria.id"
                                            :class="categoriaActiva === categoria.id ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
                                            class="px-3 py-1.5 lg:px-4 lg:py-2 rounded-full text-xs lg:text-sm font-medium transition-all whitespace-nowrap flex-shrink-0">
                                        <span x-text="categoria.nombre"></span>
                                        (<span x-text="productos.filter(p => p.categoria_id == categoria.id).length"></span>)
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Búsqueda -->
                        <div class="relative">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text"
                                   x-model="busqueda"
                                   @click.stop
                                   placeholder="🔍 Buscar productos..."
                                   class="w-full pl-10 pr-4 py-2.5 lg:py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg lg:rounded-xl text-sm lg:text-base text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none">
                        </div>
                    </div>

                    <!-- Grid -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <div class="p-2 lg:p-6 lg:pt-4">
                            <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-2 lg:gap-4">
                                <template x-for="producto in productosFiltrados" :key="producto.id">
                                    <div @click.stop="productoDisponible(producto) && toggleProducto(producto)"
                                         :class="[
                                            esSeleccionado(producto.id) ? 'border-amber-500 bg-amber-500/10 ring-2 ring-amber-500/30' : 'border-zinc-700',
                                            productoDisponible(producto) ? 'hover:border-amber-500/50 cursor-pointer active:scale-95' : 'opacity-60 cursor-not-allowed',
                                         ]"
                                         class="bg-zinc-800/40 border rounded-lg lg:rounded-xl transition-all duration-200 group relative">

                                        <!-- Badge agotado -->
                                        <div x-show="!productoDisponible(producto)" class="absolute top-1 left-1 lg:top-2 lg:left-2 z-10">
                                            <span class="inline-flex items-center gap-0.5 lg:gap-1 px-1.5 py-0.5 lg:px-2 lg:py-1 text-[9px] lg:text-xs font-bold bg-red-500 text-white rounded-full shadow-lg">
                                                <svg class="w-2.5 h-2.5 lg:w-3 lg:h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="hidden lg:inline">AGOTADO</span>
                                            </span>
                                        </div>

                                        <!-- Imagen -->
                                        <div class="p-1.5 lg:p-3">
                                            <div class="aspect-square rounded-md lg:rounded-lg overflow-hidden bg-zinc-800 border border-zinc-600 relative">
                                                <img :src="producto.imagen_url || (producto.imagen ? `/storage/${producto.imagen}` : '/storage/img/none/none.png')"
                                                     :alt="producto.nombre"
                                                     :class="productoDisponible(producto) ? '' : 'grayscale'"
                                                     class="w-full h-full object-cover transition-transform duration-300"
                                                     onerror="this.src='/storage/img/none/none.png'">
                                                <!-- Overlay agotado -->
                                                <div x-show="!productoDisponible(producto)" class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                                    <span class="text-white font-bold text-base lg:text-xl">✗</span>
                                                </div>
                                                <!-- Badge oferta -->
                                                <div x-show="(producto.tiene_oferta ?? tieneOfertaFallback(producto))" class="absolute top-1 right-1 lg:top-2 lg:right-2">
                                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 lg:px-2 text-[9px] lg:text-xs font-bold bg-green-500/20 text-green-400 rounded-full border border-green-500/30">
                                                        -<span x-text="producto.porcentaje_oferta ?? calcPorcentaje(producto)"></span>%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Info -->
                                        <div class="px-1.5 pb-1.5 lg:p-3 lg:pt-0">
                                            <h4 class="text-white font-medium mb-0.5 lg:mb-1 text-[11px] lg:text-sm line-clamp-2 leading-tight" x-text="producto.nombre"></h4>
                                            <p class="text-zinc-400 text-[9px] lg:text-xs mb-1 lg:mb-2 line-clamp-1" x-text="producto.categoria?.nombre || 'Sin categoría'"></p>

                                            <div class="flex items-center justify-between gap-1 mb-1">
                                                <!-- Precios -->
                                                <template x-if="(producto.tiene_oferta ?? tieneOfertaFallback(producto))">
                                                    <div class="flex flex-col lg:flex-row lg:items-end lg:gap-1">
                                                        <span class="text-zinc-500 line-through text-[9px] lg:text-[11px]">$<span x-text="toMoney(producto.precio)"></span></span>
                                                        <span class="text-amber-300 font-semibold text-xs lg:text-sm">
                                                            $<span x-text="toMoney(precioVigente(producto))"></span>
                                                        </span>
                                                    </div>
                                                </template>
                                                <template x-if="!(producto.tiene_oferta ?? tieneOfertaFallback(producto))">
                                                    <span class="text-amber-400 font-semibold text-xs lg:text-sm">
                                                        $<span x-text="toMoney(producto.precio)"></span>
                                                    </span>
                                                </template>

                                                <!-- Stock -->
                                                <span x-show="producto.inventario"
                                                      :class="getStockColorClass(producto)"
                                                      class="text-[9px] lg:text-xs font-medium px-1.5 py-0.5 rounded whitespace-nowrap">
                                                    <span class="hidden lg:inline">Stock: </span><span x-text="producto.inventario?.stock_actual || 0"></span>
                                                </span>
                                            </div>

                                            <!-- Controles -->
                                            <div x-show="esSeleccionado(producto.id) && productoDisponible(producto)"
                                                 x-transition.scale.opacity.duration.200ms
                                                 class="flex items-center gap-0.5 lg:gap-1 justify-center mt-1">
                                                <button @click.stop="cambiarCantidad(producto.id, -1)"
                                                        class="w-7 h-7 lg:w-8 lg:h-8 bg-red-500/20 active:bg-red-500/30 text-red-400 rounded flex items-center justify-center transition-all">
                                                    <svg class="w-3 h-3 lg:w-3.5 lg:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                <span class="text-white font-medium text-sm lg:text-base w-8 lg:w-10 text-center" x-text="getCantidad(producto.id)"></span>
                                                <button @click.stop="cambiarCantidad(producto.id, 1)"
                                                        :disabled="!puedeAgregarMas(producto)"
                                                        :class="!puedeAgregarMas(producto) ? 'opacity-50 cursor-not-allowed' : 'active:bg-green-500/30'"
                                                        class="w-7 h-7 lg:w-8 lg:h-8 bg-green-500/20 text-green-400 rounded flex items-center justify-center transition-all">
                                                    <svg class="w-3 h-3 lg:w-3.5 lg:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Empty -->
                            <div x-show="productosFiltrados.length === 0" class="text-center py-8 lg:py-12 text-zinc-400">
                                <svg class="w-12 h-12 lg:w-16 lg:h-16 mx-auto mb-3 lg:mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <h3 class="text-sm lg:text-lg font-medium mb-1 lg:mb-2">No hay productos disponibles</h3>
                                <p class="text-xs lg:text-sm">Intenta cambiar los filtros o la búsqueda</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carrito flotante en móvil, sidebar en desktop -->
                <div class="hidden lg:flex lg:w-80 xl:w-96 bg-zinc-800/50 border-l border-zinc-700 flex-col overflow-hidden">
                    <x-product-selector.cart />
                </div>

                <!-- Botón flotante carrito en móvil -->
                <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-gradient-to-t from-zinc-900 via-zinc-900/95 to-transparent backdrop-blur-sm border-t border-zinc-700 p-3 z-50"
                     x-show="items.length > 0"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-full"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <button @click="$dispatch('open-modal', 'mobile-cart')"
                            class="w-full bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white py-3.5 rounded-xl font-semibold shadow-lg shadow-amber-500/30 transition-all active:scale-95 flex items-center justify-between px-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <span>Ver Carrito</span>
                            <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm" x-text="cantidadTotal"></span>
                        </div>
                        <span class="font-bold text-lg">$<span x-text="total.toFixed(2)"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal carrito móvil -->
    <div x-data="{ openCart: false }"
         @open-modal.window="if ($event.detail === 'mobile-cart') { openCart = true; }"
         @close-modal.window="if ($event.detail === 'mobile-cart') { openCart = false; }"
         x-show="openCart"
         x-cloak
         class="lg:hidden fixed inset-0 z-[110] overflow-hidden"
         style="display: none;">
        
        <!-- Overlay -->
        <div x-show="openCart"
             x-transition.opacity.duration.300ms
             @click="$dispatch('close-modal', 'mobile-cart')"
             class="fixed inset-0 bg-black/90 backdrop-blur-sm"></div>

        <!-- Panel carrito -->
        <div x-show="openCart"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 bg-zinc-900 rounded-t-2xl shadow-2xl max-h-[85vh] flex flex-col"
             @click.stop>
            
            <!-- Header carrito móvil -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-700 flex-shrink-0">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Mi Carrito
                    <span class="bg-amber-500 text-white px-2 py-0.5 rounded-full text-xs font-bold" 
                          x-show="window.productSelectorData?.cantidadTotal > 0"
                          x-text="window.productSelectorData?.cantidadTotal || 0"></span>
                </h3>
                <button @click="$dispatch('close-modal', 'mobile-cart')"
                        class="text-zinc-400 hover:text-white p-2 hover:bg-zinc-800 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Contenido carrito con acceso a productSelectorData -->
            <div class="flex-1 overflow-hidden flex flex-col">
                <!-- Lista de productos -->
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <div class="p-3 space-y-2">
                        <template x-for="(item, index) in (window.productSelectorData?.items || [])" :key="item.producto_id">
                            <div class="bg-gradient-to-br from-zinc-700/40 to-zinc-800/40 rounded-lg p-2.5 border border-zinc-600/30"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100">
                                
                                <div class="flex items-center gap-2.5">
                                    <!-- Imagen -->
                                    <div class="relative flex-shrink-0">
                                        <div class="w-14 h-14 rounded-lg overflow-hidden bg-zinc-700 ring-2 ring-zinc-600/50">
                                            <img :src="item.imagen_url || (item.imagen ? `/storage/${item.imagen}` : '/storage/img/none/none.png')"
                                                 :alt="item.nombre"
                                                 class="w-full h-full object-cover"
                                                 onerror="this.src='/storage/img/none/none.png'">
                                        </div>
                                        <!-- Badge cantidad -->
                                        <div class="absolute -top-1.5 -right-1.5 bg-gradient-to-br from-amber-500 to-amber-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shadow-lg">
                                            <span x-text="item.cantidad"></span>
                                        </div>
                                    </div>

                                    <!-- Info -->
                                    <div class="flex-1 min-w-0">
                                        <h5 class="text-white font-semibold text-sm truncate mb-1" x-text="item.nombre"></h5>
                                        
                                        <!-- Precios -->
                                        <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                            <template x-if="((item.precio_base ?? item.precio) > item.precio)">
                                                <div class="flex items-center gap-1">
                                                    <span class="text-zinc-400 text-[10px] line-through">
                                                        $<span x-text="Number(item.precio_base ?? item.precio).toFixed(2)"></span>
                                                    </span>
                                                    <span class="text-amber-300 font-semibold text-xs">
                                                        $<span x-text="Number(item.precio).toFixed(2)"></span>
                                                    </span>
                                                    <span class="text-[9px] font-bold bg-green-500/15 text-green-400 px-1 py-0.5 rounded"
                                                          x-text="`-${Math.round(((Number(item.precio_base ?? item.precio) - Number(item.precio)) / Number(item.precio_base ?? item.precio)) * 100)}%`"></span>
                                                </div>
                                            </template>
                                            <template x-if="!((item.precio_base ?? item.precio) > item.precio)">
                                                <span class="text-amber-300 font-semibold text-xs">
                                                    $<span x-text="Number(item.precio).toFixed(2)"></span>
                                                </span>
                                            </template>
                                        </div>

                                        <!-- Total -->
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] text-zinc-400">
                                                <span x-text="item.cantidad"></span>x = $<span x-text="(Number(item.cantidad) * Number(item.precio)).toFixed(2)"></span>
                                            </span>
                                            <template x-if="((item.precio_base ?? item.precio) > item.precio)">
                                                <span class="text-[9px] text-green-400 font-medium bg-green-500/10 px-1.5 py-0.5 rounded">
                                                    Ahorras $<span x-text="Math.max(0, (Number(item.precio_base ?? item.precio) - Number(item.precio)) * Number(item.cantidad)).toFixed(2)"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Eliminar -->
                                    <button @click.stop="window.productSelectorData?.eliminarProducto(item.producto_id)"
                                            class="flex-shrink-0 text-red-400 w-9 h-9 bg-red-500/10 active:bg-red-500/20 rounded-lg flex items-center justify-center transition-all active:scale-95"
                                            title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Vacío -->
                        <div x-show="(window.productSelectorData?.items || []).length === 0"
                             class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-3 bg-zinc-700/30 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-zinc-400 font-medium mb-1">Tu carrito está vacío</p>
                            <p class="text-xs text-zinc-500">Agrega productos para comenzar</p>
                        </div>
                    </div>
                </div>

                <!-- Footer con resumen y botones -->
                <div class="border-t border-zinc-700 px-3 py-3 bg-zinc-800/50">
                    <div class="space-y-2" x-show="(window.productSelectorData?.items || []).length > 0">
                        <!-- Resumen compacto -->
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-zinc-800/40 border border-zinc-700/50 rounded-lg p-2">
                                <p class="text-zinc-400 text-[10px] mb-0.5">Subtotal</p>
                                <p class="text-white font-semibold text-sm" 
                                   x-text="`$${((window.productSelectorData?.items || []).reduce((acc, it) => acc + (Number(it.cantidad) * Number(it.precio_base ?? it.precio)), 0)).toFixed(2)}`"></p>
                            </div>
                            <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-2">
                                <p class="text-green-400 text-[10px] mb-0.5">Ahorro</p>
                                <p class="text-green-300 font-semibold text-sm" 
                                   x-text="`-$${((window.productSelectorData?.items || []).reduce((acc, it) => acc + Math.max(0, (Number(it.precio_base ?? it.precio) - Number(it.precio)) * Number(it.cantidad)), 0)).toFixed(2)}`"></p>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="bg-gradient-to-br from-amber-500/10 to-amber-600/10 border border-amber-500/30 rounded-lg p-2.5">
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-400 text-xs">Total a pagar</span>
                                <span class="text-white font-bold text-xl" 
                                      x-text="`$${(window.productSelectorData?.total || 0).toFixed(2)}`"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="grid grid-cols-2 gap-2 mt-3">
                        <button type="button"
                                @click.stop="window.productSelectorData?.limpiarCarrito()"
                                :disabled="(window.productSelectorData?.items || []).length === 0"
                                class="bg-zinc-700/80 active:bg-zinc-600 disabled:opacity-50 text-white py-2.5 px-3 rounded-lg text-xs font-medium flex items-center justify-center gap-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Vaciar
                        </button>

                        <button type="button"
                                @click.stop="window.productSelectorData?.confirmar(); $dispatch('close-modal', 'mobile-cart')"
                                :disabled="(window.productSelectorData?.items || []).length === 0"
                                class="bg-gradient-to-r from-amber-600 to-amber-500 active:from-amber-500 active:to-amber-400 disabled:opacity-50 text-white py-2.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 shadow-lg shadow-amber-500/30 active:scale-95">
                            Confirmar
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }

/* Scrollbar vertical */
.custom-scrollbar { 
    scrollbar-width: thin; 
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent; 
}
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { 
    background-color: rgba(156, 163, 175, 0.5); 
    border-radius: 4px; 
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover { 
    background-color: rgba(156, 163, 175, 0.7); 
}

/* Scrollbar horizontal para categorías */
.custom-scrollbar-horizontal {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
}
.custom-scrollbar-horizontal::-webkit-scrollbar { height: 3px; }
.custom-scrollbar-horizontal::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar-horizontal::-webkit-scrollbar-thumb { 
    background-color: rgba(156, 163, 175, 0.3);
    border-radius: 4px;
}
.custom-scrollbar-horizontal::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.5);
}

/* Móviles */
@media (max-width: 1024px) {
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    
    /* Ocultar scrollbar horizontal en móvil pero mantener scroll */
    .custom-scrollbar-horizontal { 
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .custom-scrollbar-horizontal::-webkit-scrollbar { 
        display: none;
    }
}

/* Touch feedback mejorado */
@media (hover: none) and (pointer: coarse) {
    button:active {
        transform: scale(0.95);
        transition: transform 0.1s;
    }
}

/* Scrollbar para modal carrito móvil */
#mobile-cart-scroll::-webkit-scrollbar { 
    width: 4px; 
}
#mobile-cart-scroll::-webkit-scrollbar-track { 
    background: transparent; 
}
#mobile-cart-scroll::-webkit-scrollbar-thumb { 
    background-color: rgba(245, 158, 11, 0.3);
    border-radius: 4px;
}
</style>

<script>
function productSelector(productos, categorias, selectedItems) {
  return {
    productos, categorias,
    items: selectedItems || [],
    categoriaActiva: 'todas',
    busqueda: '',

    init() {
      window.productSelectorData = this;

      // Normaliza y agrega campos "oferta" (fallback si backend no los manda)
      this.productos = (this.productos || []).map(p => {
        const hasVig = typeof p.precio_vigente !== 'undefined' && p.precio_vigente !== null;
        const vigente = hasVig ? Number(p.precio_vigente) : Number(p.precio);
        const base    = Number(p.precio);
        const tiene   = hasVig ? (vigente < base) : false;

        return {
          ...p,
          imagen_url: p.imagen_url || (p.imagen ? `/storage/${p.imagen}` : '/storage/img/none/none.png'),
          precio_vigente: isFinite(vigente) ? vigente : 0,
          tiene_oferta: typeof p.tiene_oferta !== 'undefined' ? p.tiene_oferta : (base > 0 && vigente < base),
          porcentaje_oferta: typeof p.porcentaje_oferta !== 'undefined'
            ? p.porcentaje_oferta
            : (base > 0 ? Math.round(((base - vigente) / base) * 100) : 0),
          ahorro_oferta: typeof p.ahorro_oferta !== 'undefined'
            ? p.ahorro_oferta
            : Math.max(0, base - vigente),
        };
      });

      // Intenta inyectar el especial-hoy si existe globalmente o por fetch
      if (window.ESPECIAL_HOY) this.applyEspecialHoy(window.ESPECIAL_HOY);
      this.tryFetchEspecialHoy();

      // Sincroniza con formulario externo
      if (window.pedidoFormData && window.pedidoFormData.items?.length) {
        this.items = [...window.pedidoFormData.items];
      }
    },

    /* ==== Especial del Día ==== */
    applyEspecialHoy(payload) {
      try {
        const e = payload?.data || payload?.especial || payload;
        if (!e) return;

        // Soporta formatos:
        // - WelcomeController::especialHoyJson => { success, data:{ producto_id, precio_final, ... } }
        // - EspecialDelDiaController::getEspecialHoy => { success, especial:{ producto:{id,precio_original,...}, precio_final,... } }
        let prodId = e.producto_id ?? e.producto?.id ?? null;
        let precioFinal = Number(e.precio_final ?? e.producto?.precio_final ?? e.precio ?? 0);
        let precioOriginal = Number(e.precio_original ?? e.producto?.precio_original ?? e.producto?.precio ?? 0);
        let porcentaje = 0;
        if (precioOriginal > 0 && precioFinal < precioOriginal) {
          porcentaje = Math.round(((precioOriginal - precioFinal) / precioOriginal) * 100);
        }

        if (!prodId) return;

        this.productos = this.productos.map(p => {
          if (p.id === prodId) {
            const base = Number(p.precio);
            const vigente = isFinite(precioFinal) && precioFinal > 0 ? precioFinal : base;
            const por = porcentaje || (base > 0 ? Math.round(((base - vigente)/base)*100) : 0);

            return {
              ...p,
              precio_vigente: vigente,
              tiene_oferta: vigente < base,
              porcentaje_oferta: por,
              ahorro_oferta: Math.max(0, base - vigente),
            };
          }
          return p;
        });
      } catch (_e) { /* silencioso */ }
    },

    async tryFetchEspecialHoy() {
      // Intenta endpoints; ignora 404 silenciosamente para evitar errores en consola
      const endpoints = [
        '/especial/hoy-json',
        '/api/especial-hoy',
      ];
      for (const url of endpoints) {
        try {
          const r = await fetch(url, { headers: { 'Accept': 'application/json' }});
          if (!r.ok) continue; // Ignora 404 silenciosamente
          const j = await r.json();
          if ((j?.success && (j?.data || j?.especial)) || j?.producto || j?.producto_id) {
            this.applyEspecialHoy(j);
            return; // Éxito, salir
          }
        } catch (_e) { 
          // Ignora errores de red/CORS silenciosamente
        }
      }
      // No se encontró especial, no es un error
    },

    /* ==== Helpers precio/oferta ==== */
    precioVigente(p) {
      const v = (typeof p.precio_vigente !== 'undefined' && p.precio_vigente !== null) ? Number(p.precio_vigente) : Number(p.precio);
      return isFinite(v) ? v : 0;
    },
    tieneOfertaFallback(p) {
      if (typeof p.tiene_oferta !== 'undefined') return !!p.tiene_oferta;
      const base = Number(p.precio || 0);
      const vig  = this.precioVigente(p);
      return base > 0 && vig < base;
    },
    calcPorcentaje(p) {
      if (typeof p.porcentaje_oferta !== 'undefined' && p.porcentaje_oferta !== null) return p.porcentaje_oferta;
      const base = Number(p.precio || 0);
      const vig  = this.precioVigente(p);
      return base > 0 ? Math.round(((base - vig) / base) * 100) : 0;
    },
    toMoney(n){ n = Number(n||0); return n.toFixed(2); },

    /* ==== Filtros / búsqueda ==== */
    get productosFiltrados() {
      let filtrados = this.productos || [];
      if (this.categoriaActiva !== 'todas') {
        filtrados = filtrados.filter(p => p.categoria_id == this.categoriaActiva);
      }
      if (this.busqueda.trim()) {
        const k = this.busqueda.toLowerCase();
        filtrados = filtrados.filter(p =>
          (p.nombre||'').toLowerCase().includes(k) ||
          ((p.categoria?.nombre)||'').toLowerCase().includes(k)
        );
      }
      return filtrados;
    },

    /* ==== Totales ==== */
    get total() {
      return this.items.reduce((sum, it) => sum + (Number(it.cantidad||0) * Number(it.precio||0)), 0);
    },
    get cantidadTotal() {
      return this.items.reduce((sum, it) => sum + Number(it.cantidad||0), 0);
    },
    get ahorroTotal() {
      return this.items.reduce((sum, it) => {
        const base = Number(it.precio_base ?? it.precio);
        const vigente = Number(it.precio);
        return sum + Math.max(0, (base - vigente)) * Number(it.cantidad||0);
      }, 0);
    },

    /* ==== Stock & selección ==== */
    esSeleccionado(id) { return this.items.some(it => it.producto_id === id); },
    getCantidad(id) { const it = this.items.find(it => it.producto_id === id); return it ? it.cantidad : 0; },

    productoDisponible(p) {
      if (!p.inventario) return true;
      return Number(p.inventario.stock_actual || 0) > 0;
    },
    esStockBajo(p) {
      if (!p.inventario) return false;
      const stock = Number(p.inventario.stock_actual||0);
      const min   = Number(p.inventario.stock_minimo||5);
      return stock > 0 && stock <= min;
    },
    getStockColorClass(p) {
      if (!p.inventario) return 'bg-zinc-700 text-zinc-300';
      const stock = Number(p.inventario.stock_actual||0);
      const min   = Number(p.inventario.stock_minimo||5);
      if (stock <= 0) return 'bg-red-500/20 text-red-400';
      if (stock <= min) return 'bg-yellow-500/20 text-yellow-400';
      return 'bg-green-500/20 text-green-400';
    },
    puedeAgregarMas(p) {
      if (!p.inventario) return true;
      const enCarrito = this.getCantidad(p.id);
      const stock     = Number(p.inventario.stock_actual||0);
      return enCarrito < stock;
    },

    /* ==== Carrito ==== */
    toggleProducto(p) {
      if (!this.productoDisponible(p)) { alert('⚠️ Este producto está agotado'); return; }
      const existe = this.items.find(it => it.producto_id === p.id);
      existe ? this.eliminarProducto(p.id) : this.agregarProducto(p);
    },
    agregarProducto(p) {
      if (!this.productoDisponible(p)) { alert('⚠️ Este producto está agotado'); return; }
      this.items.push({
        producto_id: p.id,
        nombre: p.nombre,
        // precio unitario vigente (aplica especial)
        precio: Number(this.precioVigente(p)),
        // conserva base/oferta si tu UI quiere mostrar tachado en el carrito
        precio_base: Number(p.precio),
        porcentaje_oferta: this.calcPorcentaje(p),
        imagen: p.imagen,
        imagen_url: p.imagen_url,
        cantidad: 1,
        notas: '',
        stock_disponible: p.inventario?.stock_actual ?? null
      });
    },
    eliminarProducto(id) {
      const i = this.items.findIndex(it => it.producto_id === id);
      if (i !== -1) this.items.splice(i, 1);
    },
    cambiarCantidad(id, delta) {
      const it = this.items.find(it => it.producto_id === id);
      if (!it) return;
      const nueva = Number(it.cantidad||0) + delta;

      if (delta > 0 && it.stock_disponible !== null && nueva > Number(it.stock_disponible)) {
        alert(`⚠️ Stock insuficiente. Disponible: ${it.stock_disponible}`);
        return;
      }
      it.cantidad = nueva;
      if (it.cantidad <= 0) this.eliminarProducto(id);
    },
    limpiarCarrito() {
      if (confirm('¿Estás seguro de limpiar el carrito?')) this.items = [];
    },

    /* ==== Confirmación ==== */
    confirmar() {
      if (!this.items.length) { alert('Selecciona al menos un producto'); return; }
      // Evento hacia fuera (para los forms de Mesa/Mostrador)
      window.dispatchEvent(new CustomEvent('products-selected', { detail: this.items }));
      // Sincroniza con form global si existe
      if (window.pedidoFormData) window.pedidoFormData.items = [...this.items];
      this.cerrarModal();
    },
    cerrarModal() { this.$dispatch('close-modal', 'product-selector'); },
  }
}
</script>
