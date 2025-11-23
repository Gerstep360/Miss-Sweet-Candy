@props(['productos'])

<!-- Script Alpine.data() -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('modalSelectorProductos', () => ({
        open: false,
        itemsSeleccionados: [],
        categoriaActiva: 'todas',
        vistaGrid: true,
        busqueda: '',
        ordenarPor: 'nombre',
        productos: @json($productos),
        
        get categorias() {
            const categoriaMap = new Map();
            const iconos = ['fas fa-coffee', 'fas fa-hamburger', 'fas fa-cookie-bite', 'fas fa-pizza-slice', 'fas fa-ice-cream', 'fas fa-wine-glass'];
            
            this.productos.forEach(producto => {
                if (producto.categoria) {
                    const nombre = producto.categoria.nombre;
                    if (!categoriaMap.has(nombre)) {
                        categoriaMap.set(nombre, {
                            nombre: nombre,
                            cantidad: 0,
                            icono: iconos[categoriaMap.size % iconos.length]
                        });
                    }
                    categoriaMap.get(nombre).cantidad++;
                }
            });
            
            return Array.from(categoriaMap.values()).sort((a, b) => a.nombre.localeCompare(b.nombre));
        },
        
        get productosFiltrados() {
            let filtrados = [...this.productos];
            
            // Filtrar por categoría
            if (this.categoriaActiva !== 'todas') {
                filtrados = filtrados.filter(p => p.categoria?.nombre === this.categoriaActiva);
            }
            
            // Filtrar por búsqueda
            if (this.busqueda.trim()) {
                const busquedaLower = this.busqueda.toLowerCase();
                filtrados = filtrados.filter(p => 
                    p.nombre.toLowerCase().includes(busquedaLower)
                );
            }
            
            // Ordenar
            filtrados.sort((a, b) => {
                switch (this.ordenarPor) {
                    case 'precio':
                        return parseFloat(a.precio) - parseFloat(b.precio);
                    case 'categoria':
                        const catA = a.categoria?.nombre || '';
                        const catB = b.categoria?.nombre || '';
                        return catA.localeCompare(catB);
                    default: // nombre
                        return a.nombre.localeCompare(b.nombre);
                }
            });
            
            return filtrados;
        },
        
        get totalSeleccionado() {
            return this.itemsSeleccionados.reduce((total, item) => 
                total + (item.cantidad * item.precio), 0
            );
        },
        
        abrirModal(itemsExistentes = []) {
            this.itemsSeleccionados = [...itemsExistentes];
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        
        cerrarModal() {
            this.open = false;
            document.body.style.overflow = 'auto';
        },
        
        cambiarCategoria(categoria) {
            this.categoriaActiva = categoria;
        },
        
        cambiarVista(esGrid) {
            this.vistaGrid = esGrid;
        },
        
        esSeleccionado(productoId) {
            return this.itemsSeleccionados.some(item => item.producto_id === productoId);
        },
        
        getCantidad(productoId) {
            const item = this.itemsSeleccionados.find(item => item.producto_id === productoId);
            return item ? item.cantidad : 0;
        },
        
        toggleProducto(producto) {
            const existe = this.itemsSeleccionados.find(item => item.producto_id === producto.id);
            
            if (existe) {
                this.removerItem(producto.id);
            } else {
                this.agregarProducto(producto);
            }
        },
        
        agregarProducto(producto) {
            const item = {
                producto_id: producto.id,
                nombre: producto.nombre,
                precio: parseFloat(producto.precio),
                imagen: producto.imagen_url,
                cantidad: 1
            };
            
            this.itemsSeleccionados.push(item);
        },
        
        removerItem(productoId) {
            this.itemsSeleccionados = this.itemsSeleccionados.filter(item => item.producto_id !== productoId);
        },
        
        cambiarCantidad(productoId, cambio) {
            const item = this.itemsSeleccionados.find(item => item.producto_id === productoId);
            if (item) {
                const nuevaCantidad = item.cantidad + cambio;
                if (nuevaCantidad <= 0) {
                    this.removerItem(productoId);
                } else {
                    item.cantidad = nuevaCantidad;
                }
            }
        },
        
        limpiarSeleccion() {
            this.itemsSeleccionados = [];
        },
        
        confirmarSeleccion() {
            window.dispatchEvent(new CustomEvent('productos-seleccionados', {
                detail: this.itemsSeleccionados
            }));
            this.cerrarModal();
        },
        
        filtrarProductos() {
            // Esta función se ejecuta automáticamente debido a la reactividad de Alpine.js
        }
    }));
    
    // Funciones globales para compatibilidad
    window.modalSelectorProductos = {
        abrirModal(itemsExistentes = []) {
            const modalElement = document.querySelector('[x-data="modalSelectorProductos"]');
            if (modalElement && modalElement._x_dataStack) {
                const instancia = modalElement._x_dataStack[0];
                instancia.abrirModal(itemsExistentes);
            }
        },
        
        cerrarModal() {
            const modalElement = document.querySelector('[x-data="modalSelectorProductos"]');
            if (modalElement && modalElement._x_dataStack) {
                const instancia = modalElement._x_dataStack[0];
                instancia.cerrarModal();
            }
        }
    };
});
</script>

<!-- Modal de selección de productos -->
<!-- Modal de selección de productos -->
<div x-data="modalSelectorProductos" 
     x-show="open" 
     x-cloak
     @keydown.escape.window="cerrarModal()"
     class="fixed inset-0 z-[70] flex items-center justify-center p-4" 
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" 
         @click="cerrarModal()"></div>
    
    <!-- Modal Content -->
    <div class="relative z-10 w-full h-[95vh] max-w-none bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-700 overflow-hidden flex flex-col"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between p-6 border-b border-zinc-700 bg-zinc-800/50 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-utensils text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Seleccionar Productos</h2>
                        <p class="text-zinc-400 text-sm">
                            Agrega productos al pedido • 
                            <span x-text="itemsSeleccionados.length"></span> seleccionados
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-500/20 text-emerald-400 px-4 py-2 rounded-lg">
                        <span class="font-bold text-lg">$<span x-text="totalSeleccionado.toFixed(2)"></span></span>
                    </div>
                    <button @click="cerrarModal()" 
                            class="w-10 h-10 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 hover:text-white rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Barra de herramientas -->
            <div class="p-4 border-b border-zinc-700 bg-zinc-800/30 flex-shrink-0">
                <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-80">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-zinc-400"></i>
                            <input type="text" 
                                   x-model="busqueda"
                                   @input="filtrarProductos()"
                                   placeholder="Buscar productos..."
                                   class="w-full pl-10 pr-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                        </div>
                        <div class="flex bg-zinc-800 rounded-lg p-1 border border-zinc-700">
                            <button type="button" 
                                    @click="cambiarVista(true)"
                                    :class="vistaGrid ? 'bg-emerald-600 text-white' : 'text-zinc-400 hover:text-white'"
                                    class="p-2 rounded transition-colors">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button type="button" 
                                    @click="cambiarVista(false)"
                                    :class="!vistaGrid ? 'bg-emerald-600 text-white' : 'text-zinc-400 hover:text-white'"
                                    class="p-2 rounded transition-colors">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-zinc-400 text-sm">Ordenar:</span>
                        <select x-model="ordenarPor" 
                                @change="filtrarProductos()"
                                class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 transition-colors">
                            <option value="nombre">Nombre</option>
                            <option value="precio">Precio</option>
                            <option value="categoria">Categoría</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="flex flex-1 overflow-hidden">
                <!-- Sidebar categorías -->
                <div class="w-72 border-r border-zinc-700 bg-zinc-800/30 overflow-y-auto flex-shrink-0">
                    <div class="p-6">
                        <h3 class="text-white font-semibold mb-6 flex items-center gap-2">
                            <i class="fas fa-tags text-emerald-400"></i>
                            Categorías
                        </h3>
                        <div class="space-y-2">
                            <button type="button" 
                                    @click="cambiarCategoria('todas')"
                                    :class="categoriaActiva === 'todas' ? 'bg-emerald-500/20 border-emerald-500/30 text-emerald-400' : 'text-zinc-300 hover:bg-zinc-700/50'"
                                    class="w-full text-left p-3 rounded-lg transition-all duration-200 border border-transparent">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-th text-white text-xs"></i>
                                    </div>
                                    <span class="flex-1 font-medium">Todas</span>
                                    <span class="text-xs bg-zinc-700 px-2 py-1 rounded" x-text="productos.length"></span>
                                </div>
                            </button>
                            
                            <template x-for="categoria in categorias" :key="categoria.nombre">
                                <button type="button" 
                                        @click="cambiarCategoria(categoria.nombre)"
                                        :class="categoriaActiva === categoria.nombre ? 'bg-emerald-500/20 border-emerald-500/30 text-emerald-400' : 'text-zinc-300 hover:bg-zinc-700/50'"
                                        class="w-full text-left p-3 rounded-lg transition-all duration-200 border border-transparent">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                                            <i :class="categoria.icono" class="text-white text-xs"></i>
                                        </div>
                                        <span class="flex-1 text-sm font-medium" x-text="categoria.nombre"></span>
                                        <span class="text-xs bg-zinc-700 px-2 py-1 rounded" x-text="categoria.cantidad"></span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Lista de productos -->
                <div class="flex-1 overflow-y-auto">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-white font-medium">
                                <span x-text="productosFiltrados.length"></span> productos encontrados
                            </span>
                            <div x-show="categoriaActiva !== 'todas'" 
                                 x-transition
                                 class="flex items-center gap-2">
                                <span class="text-zinc-400 text-sm">en</span>
                                <span class="bg-amber-500/20 text-amber-400 px-3 py-1 rounded-lg text-sm font-medium" 
                                      x-text="categoriaActiva"></span>
                            </div>
                        </div>

                        <!-- Grid productos -->
                        <div :class="vistaGrid ? 'grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4' : 'grid gap-4 grid-cols-1'">
                            <template x-for="producto in productosFiltrados" :key="producto.id">
                                <div @click="toggleProducto(producto)" 
                                     :class="esSeleccionado(producto.id) ? 'ring-2 ring-emerald-500 bg-emerald-500/10' : ''"
                                     class="product-card group cursor-pointer bg-zinc-800/30 border border-zinc-700 rounded-xl hover:border-emerald-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10 hover:scale-105">
                                    
                                    <div class="p-4">
                                        <div class="relative mb-3">
                                            <div class="aspect-square rounded-xl overflow-hidden bg-zinc-800 border border-zinc-700">
                                                <template x-if="producto.imagen_url">
                                                    <img :src="producto.imagen_url" 
                                                         :alt="producto.nombre" 
                                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                                </template>
                                                <template x-if="!producto.imagen_url">
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <i class="fas fa-utensils text-zinc-500 text-2xl"></i>
                                                    </div>
                                                </template>
                                            </div>
                                            
                                            <!-- Badge seleccionado -->
                                            <div x-show="esSeleccionado(producto.id)" 
                                                 x-transition
                                                 class="absolute -top-2 -right-2 w-7 h-7 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg">
                                                <i class="fas fa-check text-white text-sm"></i>
                                            </div>
                                            
                                            <!-- Overlay cantidad -->
                                            <div x-show="esSeleccionado(producto.id)" 
                                                 x-transition
                                                 class="absolute inset-0 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                                                <div class="bg-white text-emerald-600 px-3 py-1 rounded-full font-bold" 
                                                     x-text="getCantidad(producto.id)"></div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-white font-medium text-sm mb-1 line-clamp-2" 
                                                x-text="producto.nombre"></h4>
                                            <p class="text-zinc-400 text-xs mb-2" 
                                               x-text="producto.categoria?.nombre || 'Sin categoría'"></p>
                                            <div class="flex items-center justify-between">
                                                <span class="text-emerald-400 font-bold text-lg">
                                                    $<span x-text="parseFloat(producto.precio).toFixed(2)"></span>
                                                </span>
                                                
                                                <!-- Controles cantidad -->
                                                <div x-show="esSeleccionado(producto.id)" 
                                                     x-transition
                                                     @click.stop
                                                     class="flex items-center gap-1">
                                                    <button type="button" 
                                                            @click="cambiarCantidad(producto.id, -1)"
                                                            class="w-6 h-6 bg-zinc-700 hover:bg-zinc-600 text-white rounded-full flex items-center justify-center transition-colors">
                                                        <i class="fas fa-minus text-xs"></i>
                                                    </button>
                                                    <span class="w-6 text-center text-white text-sm font-medium" 
                                                          x-text="getCantidad(producto.id)"></span>
                                                    <button type="button" 
                                                            @click="cambiarCantidad(producto.id, 1)"
                                                            class="w-6 h-6 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full flex items-center justify-center transition-colors">
                                                        <i class="fas fa-plus text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Estado vacío -->
                        <div x-show="productosFiltrados.length === 0" 
                             x-transition
                             class="text-center py-16">
                            <div class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search text-zinc-500 text-2xl"></i>
                            </div>
                            <h3 class="text-white font-medium mb-2">No se encontraron productos</h3>
                            <p class="text-zinc-400">Intenta cambiar los filtros o la búsqueda</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer con carrito y acciones -->
            <div class="border-t border-zinc-700 bg-zinc-800/50 p-6 flex-shrink-0">
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="flex-1">
                        <h3 class="text-white font-semibold mb-3 flex items-center gap-2">
                            <i class="fas fa-shopping-cart text-emerald-400"></i>
                            Productos Seleccionados (<span x-text="itemsSeleccionados.length"></span>)
                        </h3>
                        <div class="max-h-32 overflow-y-auto space-y-2">
                            <div x-show="itemsSeleccionados.length === 0" 
                                 class="text-center py-6 text-zinc-400">
                                <i class="fas fa-shopping-cart text-2xl mb-2"></i>
                                <p>No hay productos seleccionados</p>
                            </div>
                            
                            <template x-for="item in itemsSeleccionados" :key="item.producto_id">
                                <div class="flex items-center gap-3 bg-zinc-800 p-3 rounded-lg">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-zinc-700 flex-shrink-0">
                                        <template x-if="item.imagen">
                                            <img :src="item.imagen" 
                                                 :alt="item.nombre" 
                                                 class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!item.imagen">
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-utensils text-zinc-500 text-xs"></i>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-white font-medium text-sm block truncate" x-text="item.nombre"></span>
                                        <p class="text-zinc-400 text-xs">$<span x-text="item.precio.toFixed(2)"></span> c/u</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-emerald-400 font-medium text-sm" x-text="item.cantidad + ' x'"></span>
                                        <button type="button" 
                                                @click="removerItem(item.producto_id)"
                                                class="text-red-400 hover:text-red-300 w-6 h-6 flex items-center justify-center transition-colors">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="lg:w-80">
                        <div class="bg-zinc-800 rounded-xl p-4 space-y-4">
                            <div class="flex justify-between items-center text-lg">
                                <span class="text-white font-bold">Total:</span>
                                <span class="text-emerald-400 font-bold text-2xl">
                                    $<span x-text="totalSeleccionado.toFixed(2)"></span>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <button @click="limpiarSeleccion()" 
                                        :disabled="itemsSeleccionados.length === 0"
                                        :class="itemsSeleccionados.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-zinc-600'"
                                        class="bg-zinc-700 text-white py-3 px-4 rounded-lg transition-colors font-medium">
                                    <i class="fas fa-trash mr-2"></i>
                                    Limpiar
                                </button>
                                <button @click="confirmarSeleccion()" 
                                        :disabled="itemsSeleccionados.length === 0"
                                        :class="itemsSeleccionados.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-emerald-500'"
                                        class="bg-emerald-600 text-white py-3 px-4 rounded-lg transition-colors font-medium">
                                    <i class="fas fa-check mr-2"></i>
                                    Confirmar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

[x-cloak] { 
    display: none !important; 
}
</style>