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
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Especiales del Día</h1>
                        <p class="text-zinc-300">Administra las ofertas especiales por día o fecha</p>
                        @if($especiales->where('dia_semana', '!=', null)->groupBy('dia_semana')->count() >= 7)
                            <p class="text-amber-400 text-sm mt-1">
                                <i class="fas fa-info-circle"></i> 
                                Todos los días tienen especiales configurados
                            </p>
                        @endif
                    </div>
                    @can('crear-especiales')
                        <button 
                            @click="diasCompletos ? showLimitModal = true : window.location.href = '{{ route('especial_dia.create') }}'"
                            class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nuevo Especial
                        </button>
                    @endcan
                </div>
            </div>

            <!-- Especial actual destacado -->
            @php
                try {
                    $especial_hoy = \App\Models\EspecialDelDia::getEspecialHoy();
                } catch (\Exception $e) {
                    $especial_hoy = null;
                }
            @endphp
            
            @if($especial_hoy)
            <div class="dashboard-card mb-8 border-2 border-amber-500/30 bg-gradient-to-r from-amber-500/10 to-amber-600/5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center text-black">
                        <i class="fas fa-star text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-amber-400">¡Especial de Hoy!</h2>
                        <p class="text-zinc-300">{{ ucfirst(\Carbon\Carbon::now()->locale('es')->translatedFormat('l, j \\d\\e F')) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <img src="{{ $especial_hoy->producto->imagen_url ?? '/img/default-product.jpg' }}" 
                         alt="{{ $especial_hoy->producto->nombre }}" 
                         class="w-16 h-16 object-cover rounded-lg border border-zinc-700">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">{{ $especial_hoy->producto->nombre }}</h3>
                        <p class="text-zinc-400">{{ $especial_hoy->descripcion_especial ?? 'Sin descripción' }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            @if($especial_hoy->tieneDescuento())
                                <span class="text-zinc-400 line-through">${{ number_format($especial_hoy->producto->precio, 2) }}</span>
                                <span class="text-amber-400 font-bold text-lg">${{ number_format($especial_hoy->getPrecioFinal(), 2) }}</span>
                                <span class="bg-red-500/20 text-red-400 text-xs px-2 py-1 rounded font-medium">
                                    @if($especial_hoy->descuento_porcentaje)
                                        -{{ $especial_hoy->descuento_porcentaje }}%
                                    @else
                                        Precio especial
                                    @endif
                                </span>
                            @else
                                <span class="text-amber-400 font-bold text-lg">${{ number_format($especial_hoy->producto->precio, 2) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Filtros y búsqueda -->
            <div class="dashboard-card mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Buscar</label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Buscar por producto..." 
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Estado</label>
                        <select name="estado" class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">Todos</option>
                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Día</label>
                        <select name="dia" class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
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
                    <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-colors">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'estado', 'dia']))
                        <a href="{{ route('especial_dia.index') }}" class="bg-zinc-600 hover:bg-zinc-500 text-white py-2 px-4 rounded-lg transition-colors">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <!-- Lista de especiales -->
            <div class="space-y-4">
                @forelse($especiales as $especial)
                <div class="dashboard-card {{ !$especial->activo ? 'opacity-60' : '' }}">
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
                                        @if(!$especial->activo)
                                            <span class="bg-red-600/20 text-red-400 text-xs px-2 py-1 rounded">Inactivo</span>
                                        @endif
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

                                    <!-- Precios -->
                                    <div class="flex items-center gap-3 mb-2">
                                        @if($especial->tieneDescuento())
                                            <span class="text-zinc-400 line-through text-lg">${{ number_format($especial->producto->precio, 2) }}</span>
                                            <span class="text-green-400 font-bold text-2xl">${{ number_format($especial->getPrecioFinal(), 2) }}</span>
                                            <span class="bg-red-500/20 text-red-400 text-sm px-3 py-1 rounded-full font-bold">
                                                @if($especial->descuento_porcentaje)
                                                    -{{ $especial->descuento_porcentaje }}% OFF
                                                @else
                                                    Precio especial
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-green-400 font-bold text-2xl">${{ number_format($especial->producto->precio, 2) }}</span>
                                        @endif
                                    </div>

                                    <!-- Descripción -->
                                    @if($especial->descripcion_especial)
                                        <p class="text-zinc-400 text-sm">{{ $especial->descripcion_especial }}</p>
                                    @endif
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex flex-col gap-2 flex-shrink-0">
                                    @can('ver-especiales')
                                        <a href="{{ route('especial_dia.show', $especial) }}" 
                                           class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm justify-center min-w-[100px]"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    @endcan
                                    
                                    @can('editar-especiales')
                                        <a href="{{ route('especial_dia.edit', $especial) }}" 
                                           class="bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm justify-center min-w-[100px]"
                                           title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    @endcan

                                    @can('activar-especiales')
                                        <button type="button"
                                                @click="showModal = true; modalAction = 'toggle'; modalUrl = '{{ route('especial_dia.toggle', $especial) }}'; modalMsg = '¿{{ $especial->activo ? 'Desactivar' : 'Activar' }} este especial?'; modalEspecial = {{ $especial->id }};"
                                                class="py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm justify-center {{ $especial->activo ? 'bg-yellow-600 hover:bg-yellow-500' : 'bg-green-600 hover:bg-green-500' }} text-white min-w-[100px]"
                                                title="{{ $especial->activo ? 'Desactivar' : 'Activar' }}">
                                            <i class="fas fa-{{ $especial->activo ? 'pause' : 'play' }}"></i> 
                                            {{ $especial->activo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    @endcan

                                    @can('eliminar-especiales')
                                        <button type="button"
                                                @click="showModal = true; modalAction = 'eliminar'; modalUrl = '{{ route('especial_dia.destroy', $especial) }}'; modalMsg = '¿Eliminar este especial permanentemente?';"
                                                class="bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm justify-center min-w-[100px]"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="dashboard-card text-center py-16">
                    <div class="w-20 h-20 bg-zinc-800 rounded-xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-star text-zinc-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">No hay especiales del día</h3>
                    <p class="text-zinc-400 mb-6 max-w-md mx-auto">
                        @if(request()->hasAny(['search', 'estado', 'dia']))
                            No se encontraron especiales que coincidan con los filtros aplicados.
                        @else
                            Aún no se han configurado ofertas especiales. Crea tu primer especial para comenzar.
                        @endif
                    </p>
                    @can('crear-especiales')
                        @if(request()->hasAny(['search', 'estado', 'dia']))
                            <a href="{{ route('especial_dia.index') }}" 
                               class="bg-zinc-600 hover:bg-zinc-500 text-white py-3 px-6 rounded-lg transition-colors inline-flex items-center gap-2 font-medium mr-3">
                                <i class="fas fa-filter"></i>
                                Limpiar filtros
                            </a>
                        @endif
                        <a href="{{ route('especial_dia.create') }}" 
                           class="bg-amber-500 hover:bg-amber-400 text-black py-3 px-6 rounded-lg transition-colors inline-flex items-center gap-2 font-medium">
                            <i class="fas fa-plus"></i>
                            {{ request()->hasAny(['search', 'estado', 'dia']) ? 'Crear especial' : 'Crear primer especial' }}
                        </a>
                    @endcan
                </div>
                @endforelse
            </div>

            <!-- Paginación -->
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

        <!-- Modal de confirmación -->
        <div x-show="showModal"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
            @click.self="showModal = false">
            <div x-show="showModal"
                x-transition:enter="transition ease-out duration-300 delay-150"
                x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 transform scale-95 translate-y-4"
                class="bg-zinc-900 rounded-xl shadow-xl p-6 w-full max-w-md mx-auto">
                <h2 class="text-lg font-bold text-white mb-4">Confirmar acción</h2>
                <p class="text-zinc-300 mb-6" x-text="modalMsg"></p>
                <div class="flex justify-end gap-3">
                    <button @click="showModal = false"
                        class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <form :action="modalUrl" method="POST" class="inline-block">
                        @csrf
                        <template x-if="modalAction === 'eliminar'">
                            @method('DELETE')
                        </template>
                        <template x-if="modalAction === 'toggle'">
                            <!-- La ruta toggle ahora es POST -->
                        </template>
                        <button type="submit"
                            class="py-2 px-4 rounded-lg transition-colors text-white"
                            :class="modalAction === 'eliminar' ? 'bg-red-600 hover:bg-red-500' : 'bg-amber-600 hover:bg-amber-500'">
                            <span x-text="modalAction === 'eliminar' ? 'Sí, eliminar' : 'Confirmar'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de límite de días -->
        <div x-show="showLimitModal"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
            @click.self="showLimitModal = false">
            <div x-show="showLimitModal"
                x-transition:enter="transition ease-out duration-300 delay-150"
                x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 transform scale-95 translate-y-4"
                class="bg-zinc-900 rounded-xl shadow-xl p-6 w-full max-w-lg mx-auto">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-amber-500 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">Límite alcanzado</h2>
                        <p class="text-zinc-400">Todos los días ya tienen especiales configurados</p>
                    </div>
                </div>
                
                <div class="bg-zinc-800/50 rounded-lg p-4 mb-6">
                    <p class="text-zinc-300 mb-3">
                        Ya tienes especiales configurados para todos los días de la semana. 
                        Para agregar un nuevo especial, puedes:
                    </p>
                    <ul class="space-y-2 text-zinc-400 text-sm">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-edit text-amber-500"></i>
                            Editar un especial existente
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-trash text-red-500"></i>
                            Eliminar un especial para liberar un día
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-calendar text-blue-500"></i>
                            Crear especiales por fecha específica o rangos de fechas
                        </li>
                    </ul>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button @click="showLimitModal = false"
                        class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors">
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
</x-layouts.app>