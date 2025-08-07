<!-- Modal overlay - siempre presente pero oculto -->
<div x-show="$store.modal && $store.modal.mostrar" 
     x-cloak
     class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
     x-transition.opacity.duration.300ms
     @click="$store.modal.cerrar()"
     @keydown.escape.window="$store.modal.cerrar()">

    <!-- Modal content -->
    <div class="bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-7xl h-[90vh] overflow-hidden border border-zinc-700 flex flex-col"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform" 
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         @click.stop>
        <!-- Debug info (temporal) -->
        <div class="bg-red-500 text-white p-2 text-xs" style="display: none;" x-show="true">
            Debug: Modal mostrar = <span x-text="$store.modal?.mostrar"></span>, 
            Productos = <span x-text="($store.modal?.productos || []).length"></span>
        </div>
        <!-- Header del modal -->
        <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-6 flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">🛒 Selector de Productos</h1>
                        <p class="text-amber-100">Selecciona los productos para tu pedido</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="bg-white/20 rounded-lg px-4 py-2">
                        <div class="text-center">
                            <div class="text-sm text-amber-100">Items</div>
                            <div class="text-xl font-bold text-white" x-text="($store.modal?.cantidadTotal) || 0"></div>
                        </div>
                    </div>
                    <div class="bg-white/20 rounded-lg px-4 py-2">
                        <div class="text-center">
                            <div class="text-sm text-amber-100">Total</div>
                            <div class="text-xl font-bold text-white">$<span x-text="(($store.modal?.total) || 0).toFixed(2)"></span></div>
                        </div>
                    </div>
                    <button @click="$store.modal.cerrar()" class="text-white hover:text-amber-200 p-2 hover:bg-white/10 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenido del modal -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Panel izquierdo - Productos -->
            <div class="flex-1 flex flex-col overflow-hidden p-6">
                <!-- Categorías y búsqueda -->
                <div class="p-6 pb-4 flex-shrink-0 border-b border-zinc-700">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button @click="$store.modal.categoriaActiva = 'todas'" 
                                :class="$store.modal.categoriaActiva === 'todas' ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
                                class="px-4 py-2 rounded-full text-sm font-medium transition-all">
                            Todas (<span x-text="($store.modal.productos || []).length"></span>)
                        </button>

                        <template x-for="categoria in ($store.modal.categorias || [])" :key="categoria.id">
                            <button @click="$store.modal.categoriaActiva = categoria.id" 
                                    :class="$store.modal.categoriaActiva === categoria.id ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-all">
                                <span x-text="categoria.nombre"></span> (<span x-text="categoria.productos_count || 0"></span>)
                            </button>
                        </template>
                    </div>

                    <!-- Búsqueda -->
                    <div class="relative">
                        <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               x-model="$store.modal.busqueda"
                               placeholder="🔍 Buscar productos..."
                               class="w-full pl-10 pr-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-xl text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none">
                    </div>
                </div>

                <!-- Grid de productos -->
                <div class="flex-1 p-6 pt-4 overflow-y-auto">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                        <template x-for="producto in ($store.modal.productosFiltrados || [])" :key="producto.id">
                            <div @click="$store.modal.toggleProducto(producto)"
                                 :class="$store.modal.esSeleccionado(producto.id) ? 'border-amber-500 bg-amber-500/10 ring-2 ring-amber-500/30' : 'border-zinc-700 hover:border-amber-500/50'"
                                 class="bg-zinc-800/40 border rounded-xl transition-all duration-300 cursor-pointer group hover:shadow-lg hover:-translate-y-1 relative">
                                
                                <!-- Badge de selección -->
                                <div x-show="$store.modal.esSeleccionado(producto.id)" 
                                     x-transition.scale.opacity.duration.200ms
                                     class="absolute top-2 right-2 w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center z-10">
                                    <svg class="w-3 h-3 text-black" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>

                                <!-- Imagen del producto -->
                                <div class="p-3">
                                    <div class="aspect-square rounded-lg overflow-hidden bg-zinc-800 border border-zinc-600">
                                        <img :src="producto.imagen ? `/storage/${producto.imagen}` : '/storage/img/none/none.png'" 
                                             :alt="producto.nombre" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                             onerror="this.src='/storage/img/none/none.png'">
                                    </div>
                                </div>
                                
                                <!-- Info del producto -->
                                <div class="p-3 pt-0">
                                    <h4 class="text-white font-medium mb-1 text-sm line-clamp-2" x-text="producto.nombre"></h4>
                                    <p class="text-zinc-400 text-xs mb-2" x-text="producto.categoria?.nombre || 'Sin categoría'"></p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-amber-400 font-bold text-base">$<span x-text="parseFloat(producto.precio || 0).toFixed(2)"></span></span>
                                        <div x-show="$store.modal.esSeleccionado(producto.id)" 
                                             x-transition.scale.opacity.duration.200ms
                                             class="flex items-center gap-1">
                                            <button @click.stop="$store.modal.cambiarCantidad(producto.id, -1)"
                                                    class="w-6 h-6 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-md flex items-center justify-center transition-all">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <span class="text-white font-medium text-sm w-8 text-center" x-text="$store.modal.getCantidad(producto.id)"></span>
                                            <button @click.stop="$store.modal.cambiarCantidad(producto.id, 1)"
                                                    class="w-6 h-6 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-md flex items-center justify-center transition-all">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Mensaje si no hay productos -->
                    <div x-show="($store.modal.productosFiltrados || []).length === 0" class="text-center py-12 text-zinc-400">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3 class="text-lg font-medium mb-2">No hay productos disponibles</h3>
                        <p class="text-sm">Intenta cambiar los filtros o la búsqueda</p>
                    </div>
                </div>
            </div>

            <!-- Panel derecho - Carrito -->
            <div class="w-80 bg-zinc-800/50 border-l border-zinc-700 flex flex-col">
                <!-- Header del carrito -->
                <div class="p-6 pb-4 border-b border-zinc-700 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Carrito</h3>
                        <span class="bg-amber-500/20 text-amber-400 px-2 py-1 rounded-full text-xs font-bold" x-text="$store.modal.cantidadTotal || 0"></span>
                    </div>
                </div>
                
                <!-- Lista de productos del carrito -->
                <div class="flex-1 p-6 pt-4 pb-4 overflow-y-auto">
                    <div class="space-y-2">
                        <template x-for="item in ($store.modal.items || [])" :key="item.producto_id">
                            <div class="bg-zinc-700/40 rounded-lg p-2.5 border border-zinc-600/50 hover:border-amber-500/30 transition-all">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-zinc-700 flex-shrink-0">
                                        <img :src="item.imagen ? `/storage/${item.imagen}` : '/storage/img/none/none.png'" 
                                             :alt="item.nombre" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="text-white font-medium text-xs truncate" x-text="item.nombre"></h5>
                                        <p class="text-zinc-400 text-xs">$<span x-text="(item.precio || 0).toFixed(2)"></span> c/u</p>
                                        <p class="text-amber-400 font-bold text-xs">$<span x-text="((item.cantidad || 0) * (item.precio || 0)).toFixed(2)"></span></p>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="bg-amber-500/20 text-amber-400 px-1.5 py-0.5 rounded text-xs font-bold" x-text="(item.cantidad || 0) + 'x'"></span>
                                        <button @click="$store.modal.eliminarProducto(item.producto_id)" 
                                                class="text-red-400 hover:text-red-300 w-6 h-6 bg-red-500/20 hover:bg-red-500/30 rounded-md flex items-center justify-center transition-all">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="($store.modal.items || []).length === 0" class="text-center py-8 text-zinc-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.293 1.293a1 1 0 01-.707.293H5m2 8h6a2 2 0 002-2v-6a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm">Carrito vacío</p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer del carrito -->
                <div class="p-6 pt-4 border-t border-zinc-700 flex-shrink-0">
                    <div class="flex justify-between text-white font-bold text-lg mb-4">
                        <span>Total:</span>
                        <span class="text-amber-400">$<span x-text="($store.modal.total || 0).toFixed(2)"></span></span>
                    </div>
                    
                    <div class="space-y-2">
                        <button type="button" 
                                @click="$store.modal.limpiarCarrito()"
                                :disabled="($store.modal.items || []).length === 0"
                                class="w-full bg-zinc-700 hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed text-white py-2 px-4 rounded-lg transition-colors text-sm">
                            Limpiar
                        </button>
                        <button type="button" 
                                @click="$store.modal.confirmar()"
                                :disabled="($store.modal.items || []).length === 0"
                                class="w-full bg-amber-600 hover:bg-amber-500 disabled:opacity-50 disabled:cursor-not-allowed text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                            Confirmar Selección
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>