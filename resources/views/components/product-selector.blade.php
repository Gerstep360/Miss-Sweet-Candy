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

    <!-- Overlay -->
    <div x-show="open"
         x-transition.opacity.duration.300ms
         @click="$dispatch('close-modal', 'product-selector')"
         class="fixed inset-0 bg-black/90 backdrop-blur-sm"></div>

    <!-- Modal -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 flex items-center justify-center p-2 sm:p-4 md:p-6 z-50"
         @click.stop>

        <div x-data="productSelector(@js($productos ?? []), @js($categorias ?? []), @js($selectedItems ?? []))"
             class="w-full h-full max-w-[95vw] max-h-[95vh] md:max-w-7xl md:max-h-[90vh] bg-zinc-900 rounded-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden">

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

            <!-- Body -->
            <div class="flex flex-col lg:flex-row flex-1 overflow-hidden">
                <!-- Col productos -->
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
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text"
                                   x-model="busqueda"
                                   @click.stop
                                   placeholder="🔍 Buscar productos..."
                                   class="w-full pl-8 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-2.5 md:py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg md:rounded-xl text-sm sm:text-base text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none">
                        </div>
                    </div>

                    <!-- Grid -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <div class="p-2 sm:p-3 md:p-6 pt-2 sm:pt-3 md:pt-4">
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 md:gap-4">
                                <template x-for="producto in productosFiltrados" :key="producto.id">
                                    <div @click.stop="productoDisponible(producto) && toggleProducto(producto)"
                                         :class="[
                                            esSeleccionado(producto.id) ? 'border-amber-500 bg-amber-500/10 ring-2 ring-amber-500/30' : 'border-zinc-700',
                                            productoDisponible(producto) ? 'hover:border-amber-500/50 cursor-pointer hover:shadow-lg hover:-translate-y-1' : 'opacity-60 cursor-not-allowed',
                                         ]"
                                         class="bg-zinc-800/40 border rounded-lg md:rounded-xl transition-all duration-300 group relative">

                                        <!-- Badge agotado -->
                                        <div x-show="!productoDisponible(producto)" class="absolute top-1 left-1 sm:top-2 sm:left-2 z-10">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] sm:text-xs font-bold bg-red-500 text-white rounded-full shadow-lg">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                AGOTADO
                                            </span>
                                        </div>

                                        <!-- Imagen -->
                                        <div class="p-2 sm:p-3">
                                            <div class="aspect-square rounded-md md:rounded-lg overflow-hidden bg-zinc-800 border border-zinc-600 relative">
                                                <img :src="producto.imagen_url || (producto.imagen ? `/storage/${producto.imagen}` : '/storage/img/none/none.png')"
                                                     :alt="producto.nombre"
                                                     :class="productoDisponible(producto) ? 'group-hover:scale-110' : 'grayscale'"
                                                     class="w-full h-full object-cover transition-transform duration-300"
                                                     onerror="this.src='/storage/img/none/none.png'">
                                                <!-- Overlay agotado -->
                                                <div x-show="!productoDisponible(producto)" class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                                    <span class="text-white font-bold text-lg sm:text-xl">✗</span>
                                                </div>
                                                <!-- Badge oferta -->
                                                <div x-show="(producto.tiene_oferta ?? tieneOfertaFallback(producto))" class="absolute top-1 right-1 sm:top-2 sm:right-2">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] sm:text-xs font-bold bg-green-500/20 text-green-400 rounded-full">
                                                        -<span x-text="producto.porcentaje_oferta ?? calcPorcentaje(producto)"></span>%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Info -->
                                        <div class="p-2 sm:p-3 pt-0">
                                            <h4 class="text-white font-medium mb-1 text-xs sm:text-sm line-clamp-2" x-text="producto.nombre"></h4>
                                            <p class="text-zinc-400 text-[10px] sm:text-xs mb-1 sm:mb-2 line-clamp-1" x-text="producto.categoria?.nombre || 'Sin categoría'"></p>

                                            <div class="flex items-center justify-between gap-1 mb-1">
                                                <!-- Precios -->
                                                <template x-if="(producto.tiene_oferta ?? tieneOfertaFallback(producto))">
                                                    <div class="flex items-end gap-1">
                                                        <span class="text-zinc-500 line-through text-[11px]">$<span x-text="toMoney(producto.precio)"></span></span>
                                                        <span class="text-amber-300 font-semibold text-sm">
                                                            $<span x-text="toMoney(precioVigente(producto))"></span>
                                                        </span>
                                                    </div>
                                                </template>
                                                <template x-if="!(producto.tiene_oferta ?? tieneOfertaFallback(producto))">
                                                    <span class="text-amber-400 font-semibold text-sm">
                                                        $<span x-text="toMoney(producto.precio)"></span>
                                                    </span>
                                                </template>

                                                <!-- Stock -->
                                                <span x-show="producto.inventario"
                                                      :class="getStockColorClass(producto)"
                                                      class="text-[10px] sm:text-xs font-medium px-2 py-0.5 rounded">
                                                    Stock: <span x-text="producto.inventario?.stock_actual || 0"></span>
                                                </span>
                                            </div>

                                            <!-- Controles -->
                                            <div x-show="esSeleccionado(producto.id) && productoDisponible(producto)"
                                                 x-transition.scale.opacity.duration.200ms
                                                 class="flex items-center gap-0.5 sm:gap-1 justify-center">
                                                <button @click.stop="cambiarCantidad(producto.id, -1)"
                                                        class="w-6 h-6 sm:w-7 sm:h-7 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded flex items-center justify-center transition-all">
                                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                <span class="text-white font-medium text-xs sm:text-sm w-8 sm:w-10 text-center" x-text="getCantidad(producto.id)"></span>
                                                <button @click.stop="cambiarCantidad(producto.id, 1)"
                                                        :disabled="!puedeAgregarMas(producto)"
                                                        :class="!puedeAgregarMas(producto) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-500/30'"
                                                        class="w-6 h-6 sm:w-7 sm:h-7 bg-green-500/20 text-green-400 rounded flex items-center justify-center transition-all">
                                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Empty -->
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

                <!-- Carrito (componente separado para reutilizar) -->
                <div class="w-full lg:w-80 xl:w-96 bg-zinc-800/50 border-t lg:border-t-0 lg:border-l border-zinc-700 flex flex-col overflow-hidden max-h-[40vh] lg:max-h-full">
                    <x-product-selector.cart />
                </div>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
.custom-scrollbar { scrollbar-width: thin; scrollbar-color: rgba(156, 163, 175, 0.5) transparent; }
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.5); border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: rgba(156, 163, 175, 0.7); }
@media (max-width: 640px) { .custom-scrollbar::-webkit-scrollbar { width: 4px; } }
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
      // Intenta una serie de endpoints; ignora errores silenciosamente
      const endpoints = [
        '/especial/hoy-json',
        window.location.origin + '/especial/hoy-json',
        '/api/especial-hoy',
        window.location.origin + '/api/especial-hoy',
      ];
      for (const url of endpoints) {
        try {
          const r = await fetch(url, { headers: { 'Accept': 'application/json' }});
          if (!r.ok) continue;
          const j = await r.json();
          if ((j?.success && (j?.data || j?.especial)) || j?.producto || j?.producto_id) {
            this.applyEspecialHoy(j);
            break;
          }
        } catch (_e) { /* ignora y prueba siguiente */ }
      }
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
