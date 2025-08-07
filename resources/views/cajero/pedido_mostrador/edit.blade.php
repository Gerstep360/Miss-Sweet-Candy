{{-- filepath: c:\Users\German\Documents\Proyectos\PHP\Cafeteria\cafeteria\resources\views\cajero\pedido_mostrador\edit.blade.php --}}
<x-layouts.app :title="__('Editar Pedido Mostrador')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Editar Pedido Mostrador #{{ $pedido->id }}</h1>
                        <p class="text-zinc-300">Modifica los datos del pedido para llevar</p>
                    </div>
                    <a href="{{ route('pedido_mostrador.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <form action="{{ route('pedido_mostrador.update', $pedido) }}" method="POST"
                  x-data="pedidoForm(@js($itemsJson))"
                  @products-selected="handleProductsSelected($event)">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Información básica -->
                    <div class="dashboard-card">
                        <h2 class="text-lg font-semibold text-white mb-4">Información del Pedido</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-zinc-300 font-medium mb-2">Cliente</label>
                                <select name="cliente_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sin cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ $pedido->cliente_id == $cliente->id ? 'selected' : '' }}>{{ $cliente->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-zinc-300 font-medium mb-2">Atendido por *</label>
                                <select name="atendido_por" required class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500">
                                    <option value="">Seleccionar cajero</option>
                                    @foreach($empleados as $empleado)
                                        <option value="{{ $empleado->id }}" {{ $pedido->atendido_por == $empleado->id ? 'selected' : (auth()->id() == $empleado->id ? 'selected' : '') }}>{{ $empleado->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-zinc-300 font-medium mb-2">Estado *</label>
                                <select name="estado" required class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500">
                                    <option value="pendiente" {{ $pedido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="enviado" {{ $pedido->estado == 'enviado' ? 'selected' : '' }}>Enviado</option>
                                    <option value="anulado" {{ $pedido->estado == 'anulado' ? 'selected' : '' }}>Anulado</option>
                                    <option value="retirado" {{ $pedido->estado == 'retirado' ? 'selected' : '' }}>Retirado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-zinc-300 font-medium mb-2">Notas</label>
                                <textarea name="notas" rows="3" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500" placeholder="Notas adicionales...">{{ $pedido->notas }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Productos seleccionados -->
                    <div class="dashboard-card">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-white">Productos del Pedido</h2>
                            <button type="button" @click="$dispatch('open-modal', 'product-selector')"
                                    class="bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                                <i class="fas fa-edit"></i>
                                Editar Productos
                            </button>
                        </div>

                        <!-- Lista de productos seleccionados -->
                        <div class="space-y-3 max-h-80 overflow-y-auto">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="bg-zinc-800/50 p-4 rounded-lg border border-zinc-700/50">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-zinc-700 flex-shrink-0 border border-zinc-600">
                                            <img x-show="item.imagen" :src="`/storage/${item.imagen}`" :alt="item.nombre" class="w-full h-full object-cover" onerror="this.src='/storage/img/none/none.png'">
                                            <div x-show="!item.imagen" class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-utensils text-zinc-500"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-white font-medium" x-text="item.nombre"></h4>
                                            <p class="text-zinc-400 text-sm">$<span x-text="item.precio.toFixed(2)"></span> c/u</p>
                                            <input type="hidden" :name="`items[${index}][producto_id]`" :value="item.producto_id">
                                            <input type="hidden" :name="`items[${index}][precio]`" :value="item.precio">
                                            <input type="hidden" :name="`items[${index}][cantidad]`" :value="item.cantidad">
                                        </div>
                                        <div class="text-right">
                                            <p class="text-white font-semibold">Cantidad: <span x-text="item.cantidad"></span></p>
                                            <p class="text-emerald-400 font-bold">$<span x-text="(item.cantidad * item.precio).toFixed(2)"></span></p>
                                            <button type="button" @click="eliminarItem(index)" class="text-red-400 hover:text-red-300 text-sm mt-1">
                                                <i class="fas fa-trash-alt"></i>
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="items.length === 0" class="text-center py-12">
                                <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.293 1.293a1 1 0 01-.707.293H5m2 8h6a2 2 0 002-2v-6a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-white font-medium mb-2">Sin productos seleccionados</h3>
                                <p class="text-zinc-400 mb-4">Haz clic en "Editar Productos" para empezar</p>
                                <button type="button" @click="$dispatch('open-modal', 'product-selector')"
                                        class="bg-emerald-600 hover:bg-emerald-500 text-white py-2 px-4 rounded-lg transition-colors">
                                    Seleccionar Productos
                                </button>
                            </div>
                        </div>

                        <!-- Total -->
                        <div x-show="items.length > 0" class="border-t border-zinc-700 pt-4 mt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-white font-bold text-lg">Total del Pedido:</span>
                                <span class="text-emerald-400 font-bold text-2xl">$<span x-text="total.toFixed(2)"></span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones finales -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-6">
                    <a href="{{ route('pedido_mostrador.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-3 px-6 rounded-lg transition-colors text-center">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white py-3 px-6 rounded-lg transition-colors"
                            :disabled="items.length === 0">
                        <i class="fas fa-save mr-2"></i>
                        Actualizar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal selector de productos -->
    <x-modal name="product-selector" max-width="7xl">
        <x-product-selector
            :productos="$productos"
            :categorias="$categorias"
            :selected-items="$itemsJson"/>
    </x-modal>

    <script>
        window.pedidoForm = function(items) {
            return {
                items: items || [],
                init() {
                    window.pedidoFormData = this;
                },
                get total() {
                    return this.items.reduce((sum, item) => sum + (item.cantidad * item.precio), 0);
                },
                handleProductsSelected(event) {
                    this.items = [...event.detail];
                },
                eliminarItem(index) {
                    this.items.splice(index, 1);
                }
            }
        }
    </script>
</x-layouts.app>