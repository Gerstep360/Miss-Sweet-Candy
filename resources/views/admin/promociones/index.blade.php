{{-- resources/views/admin/promociones/index.blade.php --}}
<x-layouts.app :title="__('Gestión de Promociones - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-4 sm:mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-white mb-1 sm:mb-2">🎁 Gestión de Promociones</h1>
                        <p class="text-sm sm:text-base text-zinc-300">Administra promociones, happy hours y combos especiales</p>
                    </div>
                    @can('crear-promociones')
                    <a href="{{ route('promociones.create') }}" 
                       class="w-full sm:w-auto bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 active:from-amber-600 active:to-amber-700 text-black font-medium py-2.5 sm:py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2 min-h-[44px] touch-manipulation shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="text-sm sm:text-base font-semibold">Nueva Promoción</span>
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Filtros Mejorados -->
            <div class="dashboard-card mb-4 sm:mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <h3 class="text-white font-medium text-sm sm:text-base">Filtrar por:</h3>
                </div>
                <div class="overflow-x-auto -mx-2 px-2 sm:mx-0 sm:px-0">
                    <div class="flex gap-2 sm:gap-3 min-w-max sm:min-w-0" id="filtros-promociones">
                        <button data-filter="todas" class="filter-btn active bg-gradient-to-r from-amber-500 to-amber-600 text-black font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm whitespace-nowrap shadow-lg transition-all">
                            📋 Todas
                        </button>
                        <button data-filter="vigentes" class="filter-btn bg-zinc-700 hover:bg-zinc-600 active:bg-zinc-500 text-white font-medium py-2 px-3 sm:px-4 rounded-lg transition-all text-xs sm:text-sm whitespace-nowrap">
                            ✅ Vigentes
                        </button>
                        <button data-filter="no-vigentes" class="filter-btn bg-zinc-700 hover:bg-zinc-600 active:bg-zinc-500 text-white font-medium py-2 px-3 sm:px-4 rounded-lg transition-all text-xs sm:text-sm whitespace-nowrap">
                            ❌ No Vigentes
                        </button>
                        <button data-filter="activas" class="filter-btn bg-zinc-700 hover:bg-zinc-600 active:bg-zinc-500 text-white font-medium py-2 px-3 sm:px-4 rounded-lg transition-all text-xs sm:text-sm whitespace-nowrap">
                            🟢 Activas
                        </button>
                        <button data-filter="inactivas" class="filter-btn bg-zinc-700 hover:bg-zinc-600 active:bg-zinc-500 text-white font-medium py-2 px-3 sm:px-4 rounded-lg transition-all text-xs sm:text-sm whitespace-nowrap">
                            ⚫ Inactivas
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de promociones -->
            <div class="grid gap-4 sm:gap-6" id="promociones-lista">
                @forelse($promociones as $promocion)
                <div class="dashboard-card promocion-item hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300" 
                     data-vigente="{{ $promocion->esta_vigente ? 'true' : 'false' }}"
                     data-activo="{{ $promocion->activo ? 'true' : 'false' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Contenido principal -->
                        <div class="flex items-start gap-3 sm:gap-4 flex-1 min-w-0">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-amber-500/30 to-amber-600/20 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-2">
                                    <h3 class="text-base sm:text-lg font-semibold text-white truncate flex-1">{{ $promocion->nombre }}</h3>
                                    @if($promocion->esta_vigente)
                                        <span class="flex-shrink-0 w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-1.5 sm:gap-2 mt-2">
                                    <span class="bg-gradient-to-r from-amber-500/20 to-amber-600/10 text-amber-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full capitalize font-medium border border-amber-500/30">
                                        {{ $promocion->tipo }}
                                    </span>
                                    <span class="bg-gradient-to-r from-blue-500/20 to-blue-600/10 text-blue-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-blue-500/30">
                                        {{ $promocion->aplica_sobre }}
                                    </span>
                                    <span class="bg-gradient-to-r from-green-500/20 to-green-600/10 text-green-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full font-bold border border-green-500/30">
                                        @if($promocion->tipo == 'porcentaje')
                                            -{{ $promocion->valor }}%
                                        @elseif($promocion->tipo == 'monto_fijo')
                                            -${{ number_format($promocion->valor, 2) }}
                                        @else
                                            {{ ucfirst($promocion->tipo) }}
                                        @endif
                                    </span>
                                    @if($promocion->esta_vigente)
                                        <span class="bg-gradient-to-r from-green-500/20 to-green-600/10 text-green-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-green-500/30">
                                            ✓ Vigente Ahora
                                        </span>
                                    @else
                                        <span class="bg-gradient-to-r from-red-500/20 to-red-600/10 text-red-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-red-500/30">
                                            ✕ No Vigente
                                        </span>
                                    @endif
                                    @if($promocion->activo)
                                        <span class="bg-gradient-to-r from-emerald-500/20 to-emerald-600/10 text-emerald-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-emerald-500/30">
                                            🟢 Activa
                                        </span>
                                    @else
                                        <span class="bg-gradient-to-r from-gray-500/20 to-gray-600/10 text-gray-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-gray-500/30">
                                            ⚫ Inactiva
                                        </span>
                                    @endif
                                    <span class="bg-zinc-700/50 text-zinc-300 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-zinc-600/30">
                                        🎯 Prioridad {{ $promocion->prioridad }}
                                    </span>
                                </div>
                                @if($promocion->fecha_inicio || $promocion->fecha_fin)
                                <p class="text-zinc-400 text-xs sm:text-sm mt-2 flex items-center gap-1 truncate">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-zinc-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $promocion->fecha_inicio ? $promocion->fecha_inicio->format('d/m/Y') : 'Sin inicio' }} - {{ $promocion->fecha_fin ? $promocion->fecha_fin->format('d/m/Y') : 'Sin fin' }}</span>
                                </p>
                                @endif
                                @if($promocion->hora_inicio || $promocion->hora_fin)
                                <p class="text-zinc-400 text-xs sm:text-sm mt-1 flex items-center gap-1 truncate">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-zinc-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $promocion->hora_inicio ?? '00:00' }} - {{ $promocion->hora_fin ?? '23:59' }}</span>
                                </p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="flex items-center gap-2 justify-end sm:justify-start flex-shrink-0">
                            <a href="{{ route('promociones.show', $promocion) }}" 
                               class="bg-blue-600/80 hover:bg-blue-500 active:bg-blue-700 text-white py-2 px-3 rounded-lg transition-all min-h-[44px] min-w-[44px] flex items-center justify-center touch-manipulation shadow-lg hover:shadow-blue-500/30" 
                               title="Ver detalles">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @can('editar-promociones')
                            <a href="{{ route('promociones.edit', $promocion) }}" 
                               class="bg-zinc-700/80 hover:bg-zinc-600 active:bg-zinc-500 text-white py-2 px-3 rounded-lg transition-all min-h-[44px] min-w-[44px] flex items-center justify-center touch-manipulation shadow-lg hover:shadow-zinc-500/30" 
                               title="Editar">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @endcan
                            @can('eliminar-promociones')
                            <form action="{{ route('promociones.destroy', $promocion) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600/80 hover:bg-red-500 active:bg-red-700 text-white py-2 px-3 rounded-lg transition-all min-h-[44px] min-w-[44px] flex items-center justify-center touch-manipulation shadow-lg hover:shadow-red-500/30" 
                                        onclick="return confirm('¿Estás seguro de eliminar esta promoción?')" 
                                        title="Eliminar">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
                @empty
                <div class="dashboard-card text-center py-8 sm:py-12">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-zinc-800 rounded-xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-medium mb-2 text-sm sm:text-base">No se encontraron promociones</h3>
                    <p class="text-zinc-400 mb-4 text-xs sm:text-sm">Aún no hay promociones creadas.</p>
                    @can('crear-promociones')
                    <a href="{{ route('promociones.create') }}" 
                       class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-medium py-2 px-4 rounded-lg transition-all inline-flex items-center gap-2 text-sm sm:text-base min-h-[44px] touch-manipulation shadow-lg shadow-amber-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Crear Primera Promoción
                    </a>
                    @endcan
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- JavaScript para filtros --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const promocionItems = document.querySelectorAll('.promocion-item');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.dataset.filter;
                
                // Actualizar estado activo de botones
                filterButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-gradient-to-r', 'from-amber-500', 'to-amber-600', 'text-black', 'shadow-lg');
                    btn.classList.add('bg-zinc-700', 'text-white');
                });
                this.classList.remove('bg-zinc-700', 'text-white');
                this.classList.add('active', 'bg-gradient-to-r', 'from-amber-500', 'to-amber-600', 'text-black', 'shadow-lg');
                
                // Filtrar promociones
                promocionItems.forEach(item => {
                    const esVigente = item.dataset.vigente === 'true';
                    const esActivo = item.dataset.activo === 'true';
                    let mostrar = false;
                    
                    switch(filter) {
                        case 'todas':
                            mostrar = true;
                            break;
                        case 'vigentes':
                            mostrar = esVigente;
                            break;
                        case 'no-vigentes':
                            mostrar = !esVigente;
                            break;
                        case 'activas':
                            mostrar = esActivo;
                            break;
                        case 'inactivas':
                            mostrar = !esActivo;
                            break;
                    }
                    
                    if (mostrar) {
                        item.style.display = '';
                        item.classList.add('fade-in');
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('fade-in');
                    }
                });
            });
        });
    });
    </script>

    <style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    </style>
</x-layouts.app>