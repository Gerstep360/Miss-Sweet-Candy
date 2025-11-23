// Esperar a que Alpine de Livewire esté disponible
document.addEventListener('DOMContentLoaded', () => {
    // Verificar si Alpine está disponible (desde Livewire)
    if (typeof Alpine !== 'undefined') {
        initModalStore();
    } else {
        // Si no está disponible, esperar al evento de Alpine
        document.addEventListener('alpine:init', initModalStore);
    }
});

function initModalStore() {
    Alpine.store('modal', {
        // Estado
        mostrar: false,
        items: [],
        categoriaActiva: 'todas',
        busqueda: '',
        productos: [],
        categorias: [],
        onConfirm: null,

        // Computed properties
        get total() {
            return this.items.reduce((sum, item) => sum + (item.cantidad * item.precio), 0);
        },

        get cantidadTotal() {
            return this.items.reduce((sum, item) => sum + item.cantidad, 0);
        },

        get productosFiltrados() {
            let filtrados = this.productos;
            
            // Filtrar por categoría
            if (this.categoriaActiva !== 'todas') {
                filtrados = filtrados.filter(producto => 
                    producto.categoria_id == this.categoriaActiva
                );
            }
            
            // Filtrar por búsqueda
            if (this.busqueda.trim()) {
                const busqueda = this.busqueda.toLowerCase();
                filtrados = filtrados.filter(producto => 
                    producto.nombre.toLowerCase().includes(busqueda) ||
                    (producto.categoria?.nombre || '').toLowerCase().includes(busqueda)
                );
            }
            
            return filtrados;
        },

        // Métodos
        abrir(productos, categorias, callback) {
            console.log('Abriendo modal con:', { productos: productos?.length, categorias: categorias?.length });
            this.productos = productos || [];
            this.categorias = categorias || [];
            this.onConfirm = callback;
            this.mostrar = true;
            document.body.style.overflow = 'hidden';
        },

        cerrar() {
            this.mostrar = false;
            this.busqueda = '';
            this.categoriaActiva = 'todas';
            document.body.style.overflow = 'auto';
        },

        confirmar() {
            if (this.items.length === 0) {
                alert('Selecciona al menos un producto');
                return;
            }

            if (this.onConfirm) {
                this.onConfirm(this.items);
            }

            this.cerrar();
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

        setItems(items) {
            this.items = Array.isArray(items) ? [...items] : [];
        }
    });
}