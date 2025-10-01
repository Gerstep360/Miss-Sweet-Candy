
<x-layouts.app :title="__('Editar Pedido de Mesa #' . $pedido->id)">
    <!-- Script DEBE ir ANTES del HTML -->
    <script>
        function pedidoMesaForm() {
            return {
                items: @json($itemsJson),
                errors: {},

                init() {
                    // Escuchar evento de productos seleccionados
                    window.addEventListener('products-selected', (event) => {
                        if (event.detail && Array.isArray(event.detail)) {
                            this.items = event.detail;
                            console.log('✅ Productos actualizados:', this.items.length);
                        }
                    });
                    
                    // Exponer globalmente para sincronización
                    window.pedidoFormData = this;
                    
                    console.log('✏️ Formulario de edición iniciado con', this.items.length, 'productos');
                },

                get total() {
                    return this.items.reduce((sum, item) => {
                        return sum + (item.cantidad * item.precio);
                    }, 0);
                },

                get cantidadTotal() {
                    return this.items.reduce((sum, item) => sum + item.cantidad, 0);
                },

                eliminarProducto(productoId) {
                    if (confirm('¿Estás seguro de eliminar este producto?')) {
                        this.items = this.items.filter(item => item.producto_id !== productoId);
                        
                        // Sincronizar con el selector de productos
                        if (window.productSelectorData) {
                            window.productSelectorData.items = [...this.items];
                        }
                        
                        console.log('❌ Producto eliminado. Total items:', this.items.length);
                    }
                },

                limpiarCarrito() {
                    if (confirm('¿Estás seguro de limpiar todos los productos?')) {
                        this.items = [];
                        
                        // Sincronizar con el selector de productos
                        if (window.productSelectorData) {
                            window.productSelectorData.items = [];
                        }
                        
                        console.log('🗑️ Carrito limpiado');
                    }
                },

                onSubmit(e) {
                    if (this.items.length === 0) {
                        e.preventDefault();
                        alert('Debes tener al menos un producto en el pedido.');
                        return false;
                    }
                    console.log('📝 Actualizando pedido con', this.items.length, 'productos');
                }
            }
        }
    </script>

    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">Editar Pedido #{{ $pedido->id }}</h1>
                                <p class="text-sm text-zinc-400">Pedido de Mesa - {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('pedidos.show', $pedido) }}" 
                           class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="dashboard-card bg-red-500/10 border-red-500/30 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-red-400 font-semibold mb-2">Hay errores en el formulario:</h3>
                            <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('pedidos.update', $pedido) }}" 
                  method="POST"
                  x-data="pedidoMesaForm()"
                  x-init="init()"
                  @submit="onSubmit">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <!-- Columna izquierda: Información del pedido -->
                    <div class="xl:col-span-2 space-y-6">
                        <!-- Información básica -->
                        <div class="dashboard-card">
                            <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Información del Pedido
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-zinc-300 font-medium mb-2">
                                        Mesa <span class="text-red-400">*</span>
                                    </label>
                                    <select name="mesa_id" 
                                            required 
                                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                        <option value="">Seleccionar mesa...</option>
                                        @foreach($mesas as $mesa)
                                            <option value="{{ $mesa->id }}" {{ $pedido->mesa_id == $mesa->id ? 'selected' : '' }}>
                                                {{ $mesa->nombre }} - {{ $mesa->capacidad }} personas
                                                @if($mesa->estado === 'ocupada' && $mesa->id !== $pedido->mesa_id) (Ocupada) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-zinc-300 font-medium mb-2">Cliente</label>
                                    <select name="cliente_id" 
                                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                        <option value="">Sin cliente...</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}" {{ $pedido->cliente_id == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-zinc-300 font-medium mb-2">Notas del pedido</label>
                                    <textarea name="notas" 
                                              rows="3" 
                                              class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                              placeholder="Alergias, preferencias, instrucciones especiales...">{{ $pedido->notas }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Selector de productos -->
                        <div class="dashboard-card">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    Productos del Pedido
                                    <span x-show="items.length > 0" 
                                          x-text="`(${items.length})`" 
                                          class="text-amber-400 text-base"
                                          x-transition></span>
                                </h2>
                                <button type="button" 
                                        @click="$dispatch('open-modal', 'product-selector')"
                                        class="bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg shadow-amber-500/30 hover:scale-105 active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Agregar/Modificar Productos
                                </button>
                            </div>

                            <!-- Lista de productos (igual que create-mesa) -->
                            <div class="space-y-3 max-h-[500px] overflow-y-auto custom-scrollbar">
                                <template x-for="(item, index) in items" :key="item.producto_id">
                                    <div class="bg-gradient-to-br from-zinc-800/50 to-zinc-700/30 rounded-xl p-4 border border-zinc-700/50 hover:border-amber-500/40 transition-all duration-300 group">
                                        <div class="flex items-center gap-4">
                                            <div class="relative flex-shrink-0">
                                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-zinc-700 ring-2 ring-zinc-600/50 group-hover:ring-amber-500/30 transition-all">
                                                    <img :src="item.imagen ? `/storage/${item.imagen}` : '/storage/img/none/none.png'" 
                                                         :alt="item.nombre" 
                                                         class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300"
                                                         onerror="this.src='/storage/img/none/none.png'">
                                                </div>
                                                <div class="absolute -top-2 -right-2 bg-gradient-to-br from-amber-500 to-amber-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shadow-lg shadow-amber-500/30">
                                                    <span x-text="item.cantidad"></span>
                                                </div>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <h5 class="text-white font-semibold text-base mb-1 group-hover:text-amber-400 transition-colors" 
                                                    x-text="item.nombre"></h5>
                                                <div class="flex items-center gap-2 flex-wrap text-sm">
                                                    <span class="text-zinc-400">
                                                        $<span x-text="item.precio.toFixed(2)"></span> c/u
                                                    </span>
                                                    <span class="text-zinc-600">•</span>
                                                    <span class="text-amber-400 font-medium bg-amber-500/10 px-2 py-0.5 rounded">
                                                        <span x-text="item.cantidad"></span>x = $<span x-text="(item.cantidad * item.precio).toFixed(2)"></span>
                                                    </span>
                                                </div>

                                                <div class="mt-2">
                                                    <input type="text"
                                                           x-model="item.notas"
                                                           :name="`productos[${index}][notas]`"
                                                           placeholder="Notas del producto..."
                                                           class="w-full bg-zinc-700/50 border border-zinc-600/50 rounded-lg px-2 py-1 text-white text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all">
                                                </div>

                                                <input type="hidden" :name="`productos[${index}][producto_id]`" :value="item.producto_id">
                                                <input type="hidden" :name="`productos[${index}][cantidad]`" :value="item.cantidad">
                                            </div>

                                            <button type="button"
                                                    @click="eliminarProducto(item.producto_id)" 
                                                    class="flex-shrink-0 text-red-400 hover:text-red-300 w-10 h-10 bg-red-500/10 hover:bg-red-500/20 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="items.length === 0" 
                                     class="text-center py-12"
                                     x-transition>
                                    <div class="w-20 h-20 mx-auto mb-4 bg-zinc-700/30 rounded-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <p class="text-base text-zinc-400 font-medium mb-2">No hay productos seleccionados</p>
                                    <p class="text-sm text-zinc-500">Haz clic en "Agregar/Modificar Productos"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: Resumen -->
                    <div class="xl:col-span-1">
                        <div class="dashboard-card sticky top-6">
                            <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Resumen del Pedido
                            </h2>

                            <div class="space-y-4">
                                <div class="bg-gradient-to-br from-zinc-800/80 to-zinc-700/50 rounded-xl p-4 border border-zinc-700/50">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-zinc-400 text-sm">Productos</span>
                                        <span class="text-white font-semibold" x-text="items.length"></span>
                                    </div>
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-zinc-400 text-sm">Cantidad total</span>
                                        <span class="text-white font-semibold" x-text="cantidadTotal"></span>
                                    </div>
                                    <div class="pt-3 border-t border-zinc-600/50">
                                        <div class="flex justify-between items-center">
                                            <span class="text-white font-semibold">Total</span>
                                            <span class="text-2xl font-bold text-amber-400">
                                                $<span x-text="total.toFixed(2)"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <button type="submit" 
                                            :disabled="items.length === 0"
                                            class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 disabled:from-zinc-700 disabled:to-zinc-600 disabled:cursor-not-allowed text-white py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-blue-500/30 disabled:shadow-none hover:scale-105 active:scale-95 disabled:hover:scale-100 font-semibold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Guardar Cambios
                                    </button>

                                    <button type="button" 
                                            @click="limpiarCarrito()"
                                            :disabled="items.length === 0"
                                            class="w-full bg-zinc-700 hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed text-white py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95 disabled:hover:scale-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Limpiar Todo
                                    </button>
                                </div>

                                <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3">
                                    <p class="text-blue-300 text-xs leading-relaxed">
                                        <strong>Nota:</strong> Los cambios se aplicarán inmediatamente al pedido. Si cambias de mesa, la mesa anterior quedará libre.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal selector de productos -->
    <x-product-selector :productos="$productos" :categorias="$categorias" :selectedItems="$pedido->items" />

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
    </style>
</x-layouts.app>