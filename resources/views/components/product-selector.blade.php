{{-- resources\views\components\product-selector.blade.php ---}}
@props([
    'productos' => [],
    'categorias' => [],
    'selectedItems' => []
])

<div x-data="productSelector(@js($productos), @js($categorias), @js($selectedItems))"
     class="h-screen flex flex-col overflow-hidden"
     @click.stop
     @wheel.stop
     @touchmove.stop>
    <!-- Header -->
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
                        <div class="text-xl font-bold text-white" x-text="cantidadTotal"></div>
                    </div>
                </div>
                <div class="bg-white/20 rounded-lg px-4 py-2">
                    <div class="text-center">
                        <div class="text-sm text-amber-100">Total</div>
                        <div class="text-xl font-bold text-white">$<span x-text="total.toFixed(2)"></span></div>
                    </div>
                </div>
                <!-- Botón X para cerrar -->
                <button @click="cerrarModal()" 
                        class="text-white hover:text-amber-200 p-2 hover:bg-white/10 rounded-lg transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Panel de productos -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Filtros -->
            <div class="p-6 pb-4 flex-shrink-0 border-b border-zinc-700">
                <!-- Categorías -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <button @click.stop="categoriaActiva = 'todas'" 
                            :class="categoriaActiva === 'todas' ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all">
                        Todas (<span x-text="productos.length"></span>)
                    </button>

                    <template x-for="categoria in categorias" :key="categoria.id">
                        <button @click.stop="categoriaActiva = categoria.id" 
                                :class="categoriaActiva === categoria.id ? 'bg-amber-600 text-white' : 'bg-zinc-700 text-zinc-300 hover:bg-zinc-600'"
                                class="px-4 py-2 rounded-full text-sm font-medium transition-all">
                            <span x-text="categoria.nombre"></span> 
                            (<span x-text="productos.filter(p => p.categoria_id == categoria.id).length"></span>)
                        </button>
                    </template>
                </div>

                <!-- Búsqueda -->
                <div class="relative">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           x-model="busqueda"
                           @click.stop
                           placeholder="🔍 Buscar productos..."
                           class="w-full pl-10 pr-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-xl text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none">
                </div>
            </div>

            <!-- Grid de productos - SCROLL FUNCIONAL -->
            <div class="flex-1 overflow-y-auto custom-scrollbar" 
                 @wheel.stop 
                 @touchmove.stop
                 style="scroll-behavior: smooth;">
                <div class="p-6 pt-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                        <template x-for="producto in productosFiltrados" :key="producto.id">
                            <div @click.stop="toggleProducto(producto)"
                                 :class="esSeleccionado(producto.id) ? 'border-amber-500 bg-amber-500/10 ring-2 ring-amber-500/30' : 'border-zinc-700 hover:border-amber-500/50'"
                                 class="bg-zinc-800/40 border rounded-xl transition-all duration-300 cursor-pointer group hover:shadow-lg hover:-translate-y-1 relative">
                                
                                <!-- Badge de selección -->
                                <div x-show="esSeleccionado(producto.id)" 
                                     x-transition.scale.opacity.duration.200ms
                                     class="absolute top-2 right-2 w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center z-10">
                                    <svg class="w-3 h-3 text-black" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>

                                <!-- Imagen -->
                                <div class="p-3">
                                    <div class="aspect-square rounded-lg overflow-hidden bg-zinc-800 border border-zinc-600">
                                        <img :src="producto.imagen ? `/storage/${producto.imagen}` : '/storage/img/none/none.png'" 
                                             :alt="producto.nombre" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                             onerror="this.src='/storage/img/none/none.png'">
                                    </div>
                                </div>
                                
                                <!-- Info -->
                                <div class="p-3 pt-0">
                                    <h4 class="text-white font-medium mb-1 text-sm line-clamp-2" x-text="producto.nombre"></h4>
                                    <p class="text-zinc-400 text-xs mb-2" x-text="producto.categoria?.nombre || 'Sin categoría'"></p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-amber-400 font-bold text-base">$<span x-text="parseFloat(producto.precio || 0).toFixed(2)"></span></span>
                                        <div x-show="esSeleccionado(producto.id)" 
                                             x-transition.scale.opacity.duration.200ms
                                             class="flex items-center gap-1">
                                            <button @click.stop="cambiarCantidad(producto.id, -1)"
                                                    class="w-6 h-6 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-md flex items-center justify-center transition-all">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <span class="text-white font-medium text-sm w-8 text-center" x-text="getCantidad(producto.id)"></span>
                                            <button @click.stop="cambiarCantidad(producto.id, 1)"
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
                    <div x-show="productosFiltrados.length === 0" class="text-center py-12 text-zinc-400">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3 class="text-lg font-medium mb-2">No hay productos disponibles</h3>
                        <p class="text-sm">Intenta cambiar los filtros o la búsqueda</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel del carrito -->
        <div class="w-80 bg-zinc-800/50 border-l border-zinc-700 flex flex-col overflow-hidden">
            <x-product-selector.cart />
        </div>
    </div>
</div>

<!-- CSS personalizado para scrollbars -->
<style>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
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

/* Prevenir scroll del body cuando el modal está abierto */
body.modal-open {
    overflow: hidden !important;
}
</style>

<script>

function productSelector(productos, categorias, selectedItems) {
    return {
        productos: productos,
        categorias: categorias,
        items: selectedItems || [], // <-- Inicializa con los items seleccionados
        categoriaActiva: 'todas',
        busqueda: '',

        init() {
            // Exponer datos globalmente para sincronización
            window.productSelectorData = this;
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
                cantidad: 1
            });
        },

        eliminarProducto(productoId) {
            const index = this.items.findIndex(item => item.producto_id === productoId);
            if (index !== -1) {
                this.items.splice(index, 1);
            }
        },

        cambiarCantidad(productoId, cambio) {
            const item = this.items.find(item => item.producto_id === productoId);
            if (item) {
                item.cantidad += cambio;
                if (item.cantidad <= 0) {
                    this.eliminarProducto(productoId);
                }
            }
        },

        limpiarCarrito() {
            this.items = [];
        },

        confirmar() {
            if (this.items.length === 0) {
                alert('Selecciona al menos un producto');
                return;
            }
            
            console.log('Enviando productos:', this.items); // Para debug
            
            // Sincronizar con create
            if (window.pedidoFormData) {
                window.pedidoFormData.items = [...this.items];
            }
            
            // Enviar evento para edit
            this.$dispatch('products-selected', this.items);
            
            // Cerrar modal
            this.cerrarModal();
        },

        cerrarModal() {
            this.$dispatch('close-modal', 'product-selector');
            document.body.style.overflow = 'auto';
        }
    }
}
</script>