{{-- resources/views/admin/especial_dia/index.blade.php --}}
<x-layouts.app :title="__('Especiales del Día')">
    <div x-data="{ 
        showModal: false, 
        modalAction: '', 
        modalUrl: '', 
        modalMsg: '', 
        modalEspecial: null,
        showLimitModal: false,
        diasCompletos: {{ $especiales->where('dia_semana', '!=', null)->groupBy('dia_semana')->count() >= 7 ? 'true' : 'false' }}
    }" class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- HEADER MEJORADO -->
            <div class="dashboard-card mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                            <i class="fas fa-star text-amber-400"></i>
                            Especiales del Día
                        </h1>
                        <p class="text-zinc-300 flex items-center gap-2">
                            <i class="fas fa-clock text-amber-400"></i>
                            Gestiona promociones diarias y ofertas temporales
                        </p>
                    </div>
                    
                    <!-- BOTÓN DE CREAR -->
                    <a href="{{ route('especial_dia.create') }}" 
                    class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2 shadow-lg hover:shadow-amber-500/25">
                        <i class="fas fa-plus"></i>
                        Crear Especial
                    </a>
                </div>
            </div>

            <!-- ESPECIAL DE HOY -->
            @php
                try {
                    $especial_hoy = \App\Models\EspecialDelDia::getEspecialHoy();
                } catch (\Exception $e) {
                    $especial_hoy = null;
                }
            @endphp
            
            @if($especial_hoy)
            <div class="dashboard-card mb-8 border-2 border-amber-500/30 bg-gradient-to-r from-amber-500/10 to-amber-600/5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-crown text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Especial de Hoy</h3>
                            <p class="text-amber-300">{{ $especial_hoy->producto->nombre ?? 'Producto no disponible' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-white font-bold text-xl">${{ number_format($especial_hoy->getPrecioFinal(), 2) }}</p>
                        @if($especial_hoy->getPrecioFinal() < $especial_hoy->producto->precio)
                            <p class="text-green-400 text-sm">¡Oferta especial!</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- FILTROS MEJORADOS -->
            <div class="dashboard-card mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fas fa-filter text-amber-400"></i>
                    <h3 class="text-lg font-semibold text-white">Filtros Avanzados</h3>
                </div>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <!-- Búsqueda -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2 flex items-center gap-2">
                            <i class="fas fa-search text-amber-400"></i>
                            Buscar producto
                        </label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Escribe para buscar..." 
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                    
                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Estado</label>
                        <div class="flex gap-2">
                            <label class="flex items-center gap-1">
                                <input type="radio" name="estado" value="" {{ !request('estado') ? 'checked' : '' }} class="text-amber-500">
                                <span class="text-zinc-300 text-sm">Todos</span>
                            </label>
                            <label class="flex items-center gap-1">
                                <input type="radio" name="estado" value="activo" {{ request('estado') == 'activo' ? 'checked' : '' }} class="text-amber-500">
                                <span class="text-zinc-300 text-sm">Activos</span>
                            </label>
                            <label class="flex items-center gap-1">
                                <input type="radio" name="estado" value="inactivo" {{ request('estado') == 'inactivo' ? 'checked' : '' }} class="text-amber-500">
                                <span class="text-zinc-300 text-sm">Inactivos</span>
                            </label>
                        </div>
                    </div>

                    <!-- Día -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Día de la semana</label>
                        <select name="dia" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">Todos los días</option>
                            <option value="lunes" {{ request('dia') == 'lunes' ? 'selected' : '' }}>Lunes</option>
                            <option value="martes" {{ request('dia') == 'martes' ? 'selected' : '' }}>Martes</option>
                            <option value="miercoles" {{ request('dia') == 'miercoles' ? 'selected' : '' }}>Miércoles</option>
                            <option value="jueves" {{ request('dia') == 'jueves' ? 'selected' : '' }}>Jueves</option>
                            <option value="viernes" {{ request('dia') == 'viernes' ? 'selected' : '' }}>Viernes</option>
                            <option value="sabado" {{ request('dia') == 'sabado' ? 'selected' : '' }}>Sábado</option>
                            <option value="domingo" {{ request('dia') == 'domingo' ? 'selected' : '' }}>Domingo</option>
                        </select>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex gap-2">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-filter"></i>
                            Aplicar Filtros
                        </button>
                        @if(request()->hasAny(['search', 'estado', 'dia']))
                            <a href="{{ route('especial_dia.index') }}" class="bg-zinc-600 hover:bg-zinc-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                                <i class="fas fa-broom"></i>
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- LISTA DE ESPECIALES CON BOTONES DE ACCIÓN -->
            <div class="space-y-4">
                @forelse($especiales as $especial)
                <div class="dashboard-card {{ !$especial->activo ? 'opacity-60' : '' }} hover:border-amber-500/30 transition-all duration-300">
                    <div class="flex items-center gap-6">
                        <!-- Imagen del producto -->
                        <div class="flex-shrink-0">
                            <img src="{{ $especial->producto->imagen_url ?? '/img/default-product.jpg' }}" 
                                alt="{{ $especial->producto->nombre }}" 
                                class="w-20 h-20 object-cover rounded-xl border border-zinc-700 bg-zinc-900">
                        </div>

                        <!-- Información del especial -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <!-- Nombre y estado -->
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-xl font-bold text-white">{{ $especial->producto->nombre }}</h3>
                                        <span class="{{ $especial->activo ? 'bg-green-600/20 text-green-400' : 'bg-red-600/20 text-red-400' }} text-xs px-2 py-1 rounded flex items-center gap-1">
                                            <i class="fas fa-circle text-xs"></i>
                                            {{ $especial->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                    
                                    <!-- Etiquetas informativas -->
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="bg-amber-500/20 text-amber-400 text-xs px-2 py-1 rounded font-medium">
                                            {{ $especial->producto->categoria->nombre ?? 'Sin categoría' }}
                                        </span>
                                        
                                        @if($especial->dia_semana)
                                            <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded font-medium">
                                                {{ ucfirst($especial->dia_semana) }}
                                            </span>
                                        @endif
                                        
                                        @if($especial->fecha_especifica)
                                            <span class="bg-purple-600/20 text-purple-400 text-xs px-2 py-1 rounded font-medium">
                                                {{ \Carbon\Carbon::parse($especial->fecha_especifica)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                        
                                        @if($especial->fecha_inicio && $especial->fecha_fin)
                                            <span class="bg-indigo-600/20 text-indigo-400 text-xs px-2 py-1 rounded font-medium">
                                                {{ \Carbon\Carbon::parse($especial->fecha_inicio)->format('d/m') }} - {{ \Carbon\Carbon::parse($especial->fecha_fin)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                        
                                        <span class="bg-zinc-700/50 text-zinc-300 text-xs px-2 py-1 rounded">
                                            Prioridad: {{ $especial->prioridad ?? 1 }}
                                        </span>
                                    </div>

                                    <!-- PRECIOS Y DESCUENTOS -->
                                    <div class="flex items-center gap-3 mb-2">
                                        @php
                                            $precioOriginal = $especial->producto->precio;
                                            $precioFinal = $especial->getPrecioFinal();
                                            $tieneDescuento = $precioFinal < $precioOriginal;
                                        @endphp

                                        @if($tieneDescuento)
                                            <!-- Con descuento -->
                                            <span class="text-zinc-400 line-through text-lg">${{ number_format($precioOriginal, 2) }}</span>
                                            <span class="text-green-400 font-bold text-2xl">${{ number_format($precioFinal, 2) }}</span>
                                            <span class="bg-red-500/20 text-red-400 text-sm px-3 py-1 rounded-full font-bold">
                                                @if($especial->descuento_porcentaje)
                                                    -{{ $especial->descuento_porcentaje }}% OFF
                                                @else
                                                    ¡Precio especial!
                                                @endif
                                            </span>
                                        @else
                                            <!-- Sin descuento (precio normal) -->
                                            <span class="text-green-400 font-bold text-2xl">${{ number_format($precioOriginal, 2) }}</span>
                                            <span class="bg-zinc-600/50 text-zinc-300 text-sm px-3 py-1 rounded-full">
                                                Precio regular
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Descripción -->
                                    @if($especial->descripcion_especial)
                                        <p class="text-zinc-400 text-sm">{{ $especial->descripcion_especial }}</p>
                                    @else
                                        <p class="text-zinc-500 text-sm italic">Sin descripción adicional</p>
                                    @endif
                                </div>

                                <!-- BOTONES DE ACCIÓN - EDICIÓN Y ELIMINACIÓN -->
                                <div class="flex flex-col gap-2 flex-shrink-0">
                                    <!-- Botón Ver -->
                                    <a href="{{ route('especial_dia.show', $especial) }}" 
                                    class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm justify-center min-w-[100px] group"
                                    title="Ver detalles">
                                        <i class="fas fa-eye group-hover:scale-110 transition-transform"></i> 
                                        <span>Ver</span>
                                    </a>
                                    
                                    <!-- Botón Editar -->
                                    <a href="{{ route('especial_dia.edit', $especial) }}" 
                                    class="bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm justify-center min-w-[100px] group"
                                    title="Editar especial">
                                        <i class="fas fa-edit group-hover:scale-110 transition-transform"></i> 
                                        <span>Editar</span>
                                    </a>

                                    <!-- Botón Activar/Desactivar -->
                                    <button type="button"
                                            @click="showModal = true; 
                                                    modalAction = 'toggle'; 
                                                    modalUrl = '{{ route('especial_dia.toggle', $especial) }}'; 
                                                    modalMsg = '¿{{ $especial->activo ? 'Desactivar' : 'Activar' }} el especial de {{ $especial->producto->nombre }}?'; 
                                                    modalEspecial = {{ $especial->id }};"
                                            class="py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm justify-center {{ $especial->activo ? 'bg-yellow-600 hover:bg-yellow-500' : 'bg-green-600 hover:bg-green-500' }} text-white min-w-[100px] group"
                                            title="{{ $especial->activo ? 'Desactivar' : 'Activar' }} especial">
                                        <i class="fas fa-{{ $especial->activo ? 'pause' : 'play' }} group-hover:scale-110 transition-transform"></i> 
                                        <span>{{ $especial->activo ? 'Desactivar' : 'Activar' }}</span>
                                    </button>

                                    <!-- Botón Eliminar -->
                                    <button type="button"
                                            @click="showModal = true; 
                                                    modalAction = 'eliminar'; 
                                                    modalUrl = '{{ route('especial_dia.destroy', $especial) }}'; 
                                                    modalMsg = '¿Eliminar permanentemente el especial de {{ $especial->producto->nombre }}? Esta acción no se puede deshacer.';"
                                            class="bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm justify-center min-w-[100px] group"
                                            title="Eliminar especial">
                                        <i class="fas fa-trash group-hover:scale-110 transition-transform"></i> 
                                        <span>Eliminar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <!-- Estado vacío -->
                <div class="dashboard-card text-center py-16">
                    <div class="w-24 h-24 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-star text-zinc-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">No hay especiales configurados</h3>
                    <p class="text-zinc-400 mb-6 max-w-md mx-auto">
                        Aún no has creado ningún especial del día. Comienza agregando tu primera oferta especial.
                    </p>
                    <a href="{{ route('especial_dia.create') }}" 
                       class="bg-amber-600 hover:bg-amber-500 text-white py-3 px-6 rounded-lg transition-colors inline-flex items-center gap-2 font-medium">
                        <i class="fas fa-plus"></i> Crear Primer Especial
                    </a>
                </div>
                @endforelse
            </div>

            <!-- PAGINACIÓN -->
            @if($especiales->hasPages())
            <div class="mt-8">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-zinc-400">
                        Mostrando {{ $especiales->firstItem() }} - {{ $especiales->lastItem() }} de {{ $especiales->total() }} especiales
                    </div>
                    {{ $especiales->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>

        <!-- MODAL DE CONFIRMACIÓN -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div class="bg-zinc-800 rounded-xl w-full max-w-md mx-4 border border-zinc-700 shadow-2xl">
                <div class="p-6 text-center">
                    <!-- Icono según la acción -->
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                         :class="{
                             'bg-red-500/20': modalAction === 'eliminar',
                             'bg-yellow-500/20': modalAction === 'toggle',
                             'bg-blue-500/20': modalAction === 'default'
                         }">
                        <i class="text-2xl"
                           :class="{
                               'fas fa-exclamation-triangle text-red-500': modalAction === 'eliminar',
                               'fas fa-power-off text-yellow-500': modalAction === 'toggle',
                               'fas fa-question-circle text-blue-500': modalAction === 'default'
                           }"></i>
                    </div>
                    
                    <h3 class="text-xl font-bold text-white mb-2" x-text="modalAction === 'eliminar' ? '¿Eliminar Especial?' : 'Confirmar Acción'"></h3>
                    <p class="text-zinc-300 mb-6" x-text="modalMsg"></p>
                    
                    <div class="flex gap-3 justify-center">
                        <button @click="showModal = false" 
                                class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        
                        <form :action="modalUrl" method="POST" class="inline" x-ref="modalForm">
                            @csrf
                            <template x-if="modalAction === 'eliminar'">
                                @method('DELETE')
                            </template>
                            <button type="submit" 
                                    class="py-2 px-6 rounded-lg transition-colors flex items-center gap-2 text-white"
                                    :class="{
                                        'bg-red-600 hover:bg-red-500': modalAction === 'eliminar',
                                        'bg-yellow-600 hover:bg-yellow-500': modalAction === 'toggle',
                                        'bg-blue-600 hover:bg-blue-500': modalAction === 'default'
                                    }">
                                <i class="fas" 
                                   :class="{
                                       'fa-trash': modalAction === 'eliminar',
                                       'fa-check': modalAction !== 'eliminar'
                                   }"></i> 
                                <span x-text="modalAction === 'eliminar' ? 'Eliminar' : 'Confirmar'"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DE LÍMITE DE DÍAS -->
        <div x-show="showLimitModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div class="bg-zinc-800 rounded-xl w-full max-w-md mx-4 border border-zinc-700">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Límite Alcanzado</h3>
                    <p class="text-zinc-300 mb-4">
                        Ya tienes especiales configurados para todos los días de la semana.
                    </p>
                    <p class="text-zinc-400 text-sm mb-6">
                        Para crear un nuevo especial por día de la semana, primero debes eliminar o modificar uno existente.
                    </p>
                    <button @click="showLimitModal = false" 
                            class="bg-amber-600 hover:bg-amber-500 text-white py-2 px-6 rounded-lg transition-colors">
                        Entendido
                    </button>
                    <a href="{{ route('especial_dia.create') }}" 
                        class="bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-colors">
                        Crear especial por fecha
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-card {
            background: rgba(39, 39, 42, 0.7);
            border: 1px solid rgba(63, 63, 70, 0.5);
            border-radius: 0.75rem;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
        }
        [x-cloak] { display: none !important; }
    </style>
</x-layouts.app>