<x-layouts.app :title="__('Ver Especial del Día')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Detalles del Especial</h1>
                        <p class="text-zinc-300">{{ $especial->producto->nombre ?? 'Producto no disponible' }}</p>
                    </div>
                    <div class="flex gap-3">
                        @can('editar-especiales')
                            <a href="{{ route('especial_dia.edit', $especial) }}" 
                               class="bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                                <i class="fas fa-edit"></i>
                                Editar
                            </a>
                        @endcan
                        <a href="{{ route('especial_dia.index') }}" 
                           class="bg-zinc-600 hover:bg-zinc-500 text-white py-2 px-4 rounded-lg transition-colors">
                            Volver
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenido del especial -->
            <div class="dashboard-card">
                @if($especial->producto)
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Imagen -->
                    <div>
                        <img src="{{ $especial->producto->imagen_url ?? '/img/default-product.jpg' }}" 
                             alt="{{ $especial->producto->nombre }}" 
                             class="w-full h-64 object-cover rounded-xl border border-zinc-700 bg-zinc-900">
                        
                        @if($especial->tieneDescuento())
                        <div class="mt-4 p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-tag text-red-400"></i>
                                <span class="text-red-400 font-medium">Oferta Especial</span>
                            </div>
                            @if($especial->descuento_porcentaje)
                                <p class="text-white text-lg font-bold">{{ $especial->descuento_porcentaje }}% de descuento</p>
                                <p class="text-zinc-300 text-sm">Ahorras ${{ number_format($especial->getDescuentoMonto(), 2) }}</p>
                            @else
                                <p class="text-white text-lg font-bold">Precio especial</p>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Información -->
                    <div>
                        <h2 class="text-xl font-bold text-white mb-4">{{ $especial->producto->nombre }}</h2>
                        
                        <div class="space-y-4">
                            <!-- Categoría -->
                            <div>
                                <label class="block text-sm font-medium text-zinc-400">Categoría</label>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="bg-amber-500/20 text-amber-400 text-sm px-3 py-1 rounded-full font-medium">
                                        {{ $especial->producto->categoria->nombre ?? 'Sin categoría' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tipo de especial -->
                            <div>
                                <label class="block text-sm font-medium text-zinc-400">Tipo de especial</label>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @if($especial->dia_semana)
                                        <span class="bg-blue-600/20 text-blue-400 text-sm px-3 py-1 rounded-full font-medium">
                                            <i class="fas fa-calendar-day mr-1"></i>
                                            {{ ucfirst($especial->dia_semana) }}
                                        </span>
                                    @endif
                                    
                                    @if($especial->fecha_especifica)
                                        <span class="bg-purple-600/20 text-purple-400 text-sm px-3 py-1 rounded-full font-medium">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($especial->fecha_especifica)->format('d/m/Y') }}
                                        </span>
                                    @endif
                                    
                                    @if($especial->fecha_inicio && $especial->fecha_fin)
                                        <span class="bg-indigo-600/20 text-indigo-400 text-sm px-3 py-1 rounded-full font-medium">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ \Carbon\Carbon::parse($especial->fecha_inicio)->format('d/m') }} - {{ \Carbon\Carbon::parse($especial->fecha_fin)->format('d/m/Y') }}
                                        </span>
                                    @endif

                                    @if(!$especial->dia_semana && !$especial->fecha_especifica && !$especial->fecha_inicio)
                                        <span class="bg-zinc-700/50 text-zinc-400 text-sm px-3 py-1 rounded-full font-medium">
                                            <i class="fas fa-question-circle mr-1"></i>
                                            Sin especificación de tiempo
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Precios -->
                            <div class="bg-zinc-800/50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-zinc-400 mb-2">Precios</label>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-zinc-300">Precio original:</span>
                                        <span class="text-white font-medium">${{ number_format($especial->producto->precio, 2) }}</span>
                                    </div>
                                    
                                    @if($especial->tieneDescuento())
                                    <div class="flex items-center justify-between">
                                        <span class="text-zinc-300">Descuento:</span>
                                        <span class="text-red-400 font-medium">-${{ number_format($especial->getDescuentoMonto(), 2) }}</span>
                                    </div>
                                    @endif
                                    
                                    <hr class="border-zinc-700">
                                    <div class="flex items-center justify-between">
                                        <span class="text-zinc-300 font-medium">Precio final:</span>
                                        <span class="text-green-400 font-bold text-xl">${{ number_format($especial->getPrecioFinal(), 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción especial -->
                            @if($especial->descripcion_especial)
                            <div>
                                <label class="block text-sm font-medium text-zinc-400">Descripción especial</label>
                                <p class="text-white mt-1 p-3 bg-zinc-800/50 rounded-lg">{{ $especial->descripcion_especial }}</p>
                            </div>
                            @endif

                            <!-- Prioridad y Estado -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-400">Prioridad</label>
                                    <span class="bg-zinc-700/50 text-zinc-300 text-sm px-3 py-1 rounded-full inline-block mt-1">
                                        {{ $especial->prioridad ?? 1 }}
                                    </span>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-zinc-400">Estado</label>
                                    <span class="inline-flex px-3 py-1 text-sm rounded-full mt-1 {{ $especial->activo ? 'bg-green-600/20 text-green-400' : 'bg-red-600/20 text-red-400' }}">
                                        <i class="fas fa-{{ $especial->activo ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                        {{ $especial->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Fechas de creación y modificación -->
                            <div class="text-xs text-zinc-500 space-y-1">
                                <p><i class="fas fa-plus-circle mr-1"></i> Creado: {{ $especial->created_at->format('d/m/Y H:i') }}</p>
                                @if($especial->updated_at != $especial->created_at)
                                <p><i class="fas fa-edit mr-1"></i> Modificado: {{ $especial->updated_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- Error: Producto no encontrado -->
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-exclamation-triangle text-red-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Producto no encontrado</h3>
                    <p class="text-zinc-400 mb-6">
                        El producto asociado a este especial ya no existe o fue eliminado.
                    </p>
                    <div class="flex gap-3 justify-center">
                        @can('eliminar-especiales')
                        <form action="{{ route('especial_dia.destroy', $especial) }}" method="POST" 
                              onsubmit="return confirm('¿Eliminar este especial que no tiene producto asociado?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded-lg transition-colors">
                                <i class="fas fa-trash mr-1"></i>
                                Eliminar especial
                            </button>
                        </form>
                        @endcan
                        
                        <a href="{{ route('especial_dia.index') }}" 
                           class="bg-zinc-600 hover:bg-zinc-500 text-white py-2 px-4 rounded-lg transition-colors">
                            Volver al listado
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>