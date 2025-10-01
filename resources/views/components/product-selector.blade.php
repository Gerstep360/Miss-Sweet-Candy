{{-- Modal wrapper con Alpine.js --}}
@props([
    'productos' => [],
    'categorias' => [],
    'selectedItems' => []
])
<div x-data="{ open: false }"
     @open-modal.window="if ($event.detail === 'product-selector') { open = true; document.body.style.overflow = 'hidden'; }"
     @close-modal.window="if ($event.detail === 'product-selector') { open = false; document.body.style.overflow = 'auto'; }"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-hidden"
     style="display: none;">
    
    <!-- Overlay oscuro -->
    <div x-show="open"
         x-transition.opacity.duration.300ms
         @click="$dispatch('close-modal', 'product-selector')"
         class="fixed inset-0 bg-black/90 backdrop-blur-sm"></div>

    <!-- Modal Content - CENTRADO Y MÁS GRANDE -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 flex items-center justify-center p-2 sm:p-4 md:p-6 z-50"
         @click.stop>
    <div x-data="productSelector(@js($productos), @js($categorias), @js($selectedItems))"
        <div class="w-full h-full max-w-[95vw] max-h-[95vh] md:max-w-7xl md:max-h-[90vh] bg-zinc-900 rounded-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            <div x-data="productSelector(@js($productos ?? []), @js($categorias ?? []), [])"
                 class="h-full flex flex-col overflow-hidden">
                
                <!-- Header -->
                <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-3 sm:p-4 md:p-6 flex-shrink-0">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 sm:gap-3 md:gap-4 min-w-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h1 class="text-base sm:text-xl md:text-2xl font-bold text-white truncate">🛒 Selector de Productos</h1>
                                <p class="text-xs sm:text-sm text-amber-100 hidden sm:block">Selecciona los productos para tu pedido</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3 md:gap-6 flex-shrink-0">
                            <div class="bg-white/20 rounded-lg px-2 py-1 sm:px-3 sm:py-2">
                                <div class="text-center">
                                    <div class="text-xs text-amber-100">Items</div>
                                    <div class="text-sm sm:text-lg md:text-xl font-bold text-white" x-text="cantidadTotal"></div>
                                </div>
                            </div>
                            <div class="bg-white/20 rounded-lg px-2 py-1 sm:px-3 sm:py-2">
                                <div class="text-center">
                                    <div class="text-xs text-amber-100">Total</div>
                                    <div class="text-sm sm:text-lg md:text-xl font-bold text-white">$<span x-text="total.toFixed(2)"></span></div>
                                </div>
                            </div>
                            <button @click="cerrarModal()" 
                                    class="text-white hover:text-amber-200 p-1 sm:p-2 hover:bg-white/10 rounded-lg transition-all flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contenido principal -->
                <div class="flex flex-col lg:flex-row flex-1 overflow-hidden">
                    <!-- Panel de productos -->
                    <div class="flex-1 flex flex-col overflow-hidden">
                        <!-- Filtros -->
                        <div class="p-3 sm:p-4 md:p-6 pb-2 sm:pb-3 md:pb-4 flex-shrink-0 border-b border-zinc-700">
                            <!-- Categorías -->
                            <div class="flex flex-wrap gap-1 sm:gap-2 mb-2 sm:mb-3 md:mb-4">
                                <button @click.stop="categoriaActiva = 'todas'" 
                                        :class="categoriaActiva === 'todas' ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
                                        class="px-2 py-1 sm:px-3 sm:py-1.5 md:px-4 md:py-2 rounded-full text-xs sm:text-sm font-medium transition-all">
                                    Todas (<span x-text="productos.length"></span>)
                                </button>

                                <template x-for="categoria in categorias" :key="categoria.id">
                                    <button @click.stop="categoriaActiva = categoria.id" 
                                            :class="categoriaActiva === categoria.id ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
                                            class="px-2 py-1 sm:px-3 sm:py-1.5 md:px-4 md:py-2 rounded-full text-xs sm:text-sm font-medium transition-all">
                                        <span x-text="categoria.nombre"></span> 
                                        (<span x-text="productos.filter(p => p.categoria_id == categoria.id).length"></span>)
                                    </button>
                                </template>
                            </div>

                            <!-- Búsqueda -->
                            <div class="relative">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 absolute left-2 sm:left-3 top-1/2 transform -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" 
                                       x-model="busqueda"
                                       @click.stop
                                       placeholder="🔍 Buscar productos..."
                                       class="w-full pl-8 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-2.5 md:py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg md:rounded-xl text-sm sm:text-base text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none">
                            </div>
                        </div>

                        <!-- Grid de productos -->
                        <div class="flex-1 overflow-y-auto custom-scrollbar">
                            <div class="p-2 sm:p-3 md:p-6 pt-2 sm:pt-3 md:pt-4">
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 md:gap-4">
                                    <template x-for="producto in productosFiltrados" :key="producto.id">
                                        <div @click.stop="toggleProducto(producto)"
                                             :class="esSeleccionado(producto.id) ? 'border-amber-500 bg-amber-500/10 ring-2 ring-amber-500/30' : 'border-zinc-700 hover:border-amber-500/50'"
                                             class="bg-zinc-800/40 border rounded-lg md:rounded-xl transition-all duration-300 cursor-pointer group hover:shadow-lg hover:-translate-y-1 relative">
                                            
                                            <!-- Badge de selección -->
                                            <div x-show="esSeleccionado(producto.id)" 
                                                 x-transition.scale.opacity.duration.200ms
                                                 class="absolute top-1 right-1 sm:top-2 sm:right-2 w-5 h-5 sm:w-6 sm:h-6 bg-amber-500 rounded-full flex items-center justify-center z-10 shadow-lg">
                                                <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-black" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>

                                            <!-- Imagen -->
                                            <div class="p-2 sm:p-3">
                                                <div class="aspect-square rounded-md md:rounded-lg overflow-hidden bg-zinc-800 border border-zinc-600">
                                                    <img :src="producto.imagen ? `/storage/${producto.imagen}` : '/storage/img/none/none.png'" 
                                                         :alt="producto.nombre" 
                                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                                         onerror="this.src='/storage/img/none/none.png'">
                                                </div>
                                            </div>
                                            
                                            <!-- Info -->
                                            <div class="p-2 sm:p-3 pt-0">
                                                <h4 class="text-white font-medium mb-1 text-xs sm:text-sm line-clamp-2" x-text="producto.nombre"></h4>
                                                <p class="text-zinc-400 text-[10px] sm:text-xs mb-1 sm:mb-2 line-clamp-1" x-text="producto.categoria?.nombre || 'Sin categoría'"></p>
                                                <div class="flex items-center justify-between gap-1">
                                                    <span class="text-amber-400 font-bold text-xs sm:text-sm md:text-base">$<span x-text="parseFloat(producto.precio || 0).toFixed(2)"></span></span>
                                                    <div x-show="esSeleccionado(producto.id)" 
                                                         x-transition.scale.opacity.duration.200ms
                                                         class="flex items-center gap-0.5 sm:gap-1">
                                                        <button @click.stop="cambiarCantidad(producto.id, -1)"
                                                                class="w-5 h-5 sm:w-6 sm:h-6 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded flex items-center justify-center transition-all">
                                                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                            </svg>
                                                        </button>
                                                        <span class="text-white font-medium text-xs sm:text-sm w-6 sm:w-8 text-center" x-text="getCantidad(producto.id)"></span>
                                                        <button @click.stop="cambiarCantidad(producto.id, 1)"
                                                                class="w-5 h-5 sm:w-6 sm:h-6 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded flex items-center justify-center transition-all">
                                                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <div x-show="productosFiltrados.length === 0" class="text-center py-8 sm:py-12 text-zinc-400">
                                    <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 sm:mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <h3 class="text-sm sm:text-lg font-medium mb-1 sm:mb-2">No hay productos disponibles</h3>
                                    <p class="text-xs sm:text-sm">Intenta cambiar los filtros o la búsqueda</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel del carrito -->
                    <div class="w-full lg:w-80 xl:w-96 bg-zinc-800/50 border-t lg:border-t-0 lg:border-l border-zinc-700 flex flex-col overflow-hidden max-h-[40vh] lg:max-h-full">
                        <x-product-selector.cart />
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>

<style>
[x-cloak] { display: none !important; }

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.7);
}

@media (max-width: 640px) {
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
}
</style>

<script>
function productSelector(productos, categorias, selectedItems) {
    return {
        productos: productos,
        categorias: categorias,
        items: selectedItems || [],
        categoriaActiva: 'todas',
        busqueda: '',

        init() {
            window.productSelectorData = this;
            
            // Sincronizar con pedidoFormData si existe
            if (window.pedidoFormData && window.pedidoFormData.items.length > 0) {
                this.items = [...window.pedidoFormData.items];
            }

            console.log('🛒 Product Selector Iniciado');
            console.log('📦 Productos:', this.productos.length);
            console.log('🏷️ Categorías:', this.categorias.length);
        },

        get productosFiltrados() {
            let filtrados = this.productos;
            
            if (this.categoriaActiva !== 'todas') {
                filtrados = filtrados.filter(producto => 
                    producto.categoria_id == this.categoriaActiva
                );
            }
            
            if (this.busqueda.trim()) {
                const busqueda = this.busqueda.toLowerCase();
                filtrados = filtrados.filter(producto => 
                    producto.nombre.toLowerCase().includes(busqueda) ||
                    (producto.categoria?.nombre || '').toLowerCase().includes(busqueda)
                );
            }
            
            return filtrados;
        },

        get total() {
            return this.items.reduce((sum, item) => sum + (item.cantidad * item.precio), 0);
        },

        get cantidadTotal() {
            return this.items.reduce((sum, item) => sum + item.cantidad, 0);
        },

        esSeleccionado(productoId) {
            return this.items.some(item => item.producto_id === productoId);
        },

        getCantidad(productoId) {
            const item = this.items.find(item => item.producto_id === productoId);
            return item ? item.cantidad : 0;
        },

        toggleProducto(producto) {
            const existe = this.items.find(item => item.producto_id === producto.id);
            
            if (existe) {
                this.eliminarProducto(producto.id);
            } else {
                this.agregarProducto(producto);
            }
        },

        agregarProducto(producto) {
            this.items.push({
                producto_id: producto.id,
                nombre: producto.nombre,
                precio: parseFloat(producto.precio),
                imagen: producto.imagen,
                cantidad: 1,
                notas: ''
            });
            
            console.log('✅ Producto agregado:', producto.nombre);
        },

        eliminarProducto(productoId) {
            const index = this.items.findIndex(item => item.producto_id === productoId);
            if (index !== -1) {
                const producto = this.items[index];
                this.items.splice(index, 1);
                console.log('❌ Producto eliminado:', producto.nombre);
            }
        },

        cambiarCantidad(productoId, cambio) {
            const item = this.items.find(item => item.producto_id === productoId);
            if (item) {
                item.cantidad += cambio;
                if (item.cantidad <= 0) {
                    this.eliminarProducto(productoId);
                } else {
                    console.log('🔢 Cantidad actualizada:', item.nombre, item.cantidad);
                }
            }
        },

        limpiarCarrito() {
            if (confirm('¿Estás seguro de limpiar el carrito?')) {
                this.items = [];
                console.log('🗑️ Carrito limpiado');
            }
        },

        confirmar() {
            if (this.items.length === 0) {
                alert('Selecciona al menos un producto');
                return;
            }
            
            console.log('✅ Confirmando productos:', this.items.length);
            
            // Enviar evento con los productos seleccionados
            window.dispatchEvent(new CustomEvent('products-selected', {
                detail: this.items
            }));
            
            // Sincronizar con pedidoFormData
            if (window.pedidoFormData) {
                window.pedidoFormData.items = [...this.items];
            }
            
            this.cerrarModal();
        },

        cerrarModal() {
            this.$dispatch('close-modal', 'product-selector');
        }
    }
}
</script>