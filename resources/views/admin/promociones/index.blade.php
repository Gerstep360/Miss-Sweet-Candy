{{-- resources/views/admin/promociones/index.blade.php --}}
<x-layouts.app :title="__('Gestión de Promociones - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">

            {{-- Header --}}
            <div class="dashboard-card mb-4 sm:mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-white mb-1 sm:mb-2">🎁 Gestión de Promociones</h1>
                        <p class="text-sm sm:text-base text-zinc-300">
                            Administra promociones, happy hours y combos especiales
                        </p>
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

            {{-- Controles: Filtros + Búsqueda + Orden --}}
            <div class="dashboard-card mb-4 sm:mb-6">
                <div class="flex flex-col gap-3">

                    {{-- Filtros --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <h3 class="text-white font-medium text-sm sm:text-base">Filtrar por:</h3>
                        </div>
                        <div class="overflow-x-auto -mx-2 px-2 sm:mx-0 sm:px-0">
                            <div class="flex gap-2 sm:gap-3 min-w-max sm:min-w-0" id="filtros-promociones" role="tablist" aria-label="Filtros de promociones">
                                <button data-filter="todas" class="filter-btn active bg-gradient-to-r from-amber-500 to-amber-600 text-black font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm whitespace-nowrap shadow-lg transition-all"
                                        aria-pressed="true">📋 Todas <span class="ml-1 text-[10px] opacity-80" id="count-todas"></span></button>
                                <button data-filter="vigentes" class="filter-btn bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg transition-all text-xs sm:text-sm whitespace-nowrap"
                                        aria-pressed="false">✅ Vigentes <span class="ml-1 text-[10px] opacity-80" id="count-vigentes"></span></button>
                                <button data-filter="no-vigentes" class="filter-btn bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg transition-all text-xs sm:text-sm whitespace-nowrap"
                                        aria-pressed="false">❌ No Vigentes <span class="ml-1 text-[10px] opacity-80" id="count-no-vigentes"></span></button>
                                <button data-filter="activas" class="filter-btn bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg transition-all text-xs sm:text-sm whitespace-nowrap"
                                        aria-pressed="false">🟢 Activas <span class="ml-1 text-[10px] opacity-80" id="count-activas"></span></button>
                                <button data-filter="inactivas" class="filter-btn bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg transition-all text-xs sm:text-sm whitespace-nowrap"
                                        aria-pressed="false">⚫ Inactivas <span class="ml-1 text-[10px] opacity-80" id="count-inactivas"></span></button>
                                <button id="btn-reset" class="bg-zinc-800 hover:bg-zinc-700 text-white font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm whitespace-nowrap transition-all">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Búsqueda y orden --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label for="search" class="sr-only">Buscar promoción</label>
                            <div class="flex items-center gap-2 bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2">
                                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                                </svg>
                                <input id="search" type="text" placeholder="Buscar por nombre…"
                                       class="w-full bg-transparent outline-none text-white placeholder-zinc-500">
                                <button id="clear-search" class="text-zinc-400 hover:text-white text-xs">Limpiar</button>
                            </div>
                        </div>
                        <div>
                            <label for="sort" class="sr-only">Ordenar por</label>
                            <select id="sort" class="w-full bg-zinc-800 text-white border border-zinc-700 rounded-lg px-3 py-2">
                                <option value="recent">Ordenar: más recientes</option>
                                <option value="prioridad-asc">Prioridad: 1 → 10</option>
                                <option value="prioridad-desc">Prioridad: 10 → 1</option>
                                <option value="nombre-asc">Nombre: A → Z</option>
                                <option value="nombre-desc">Nombre: Z → A</option>
                                <option value="estado">Estado (Vigente/Activa)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de promociones --}}
            <div class="grid gap-4 sm:gap-6" id="promociones-lista">
                @forelse($promociones as $promocion)
                <div class="group relative dashboard-card promocion-item hover:shadow-2xl hover:shadow-amber-500/20 transition-all duration-300 hover:scale-[1.01] hover:border-amber-500/30 focus-within:ring-2 focus-within:ring-amber-500"
                     data-vigente="{{ $promocion->esta_vigente ? 'true' : 'false' }}"
                     data-activo="{{ $promocion->activo ? 'true' : 'false' }}"
                     data-nombre="{{ Str::lower($promocion->nombre) }}"
                     data-prioridad="{{ (int) $promocion->prioridad }}"
                     data-recent="{{ optional($promocion->updated_at ?? $promocion->created_at)->timestamp ?? now()->timestamp }}">
                    
                    {{-- Indicador lateral de estado --}}
                    @php
                        $barClass =
                            $promocion->esta_vigente && $promocion->activo ? 'bg-green-500' :
                            ($promocion->esta_vigente && !$promocion->activo ? 'bg-yellow-500' :
                            (!$promocion->esta_vigente && $promocion->activo ? 'bg-orange-500' : 'bg-gray-500'));
                    @endphp
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 rounded-l-lg transition-all duration-300 {{ $barClass }} group-hover:w-2"></div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        {{-- Contenido principal --}}
                        <div class="flex items-start gap-3 sm:gap-4 flex-1 min-w-0">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-amber-500/30 to-amber-600/20 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:from-amber-500/40 group-hover:to-amber-600/30 transition-all duration-300 group-hover:scale-110">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-amber-400 group-hover:text-amber-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-2">
                                    <h3 class="text-base sm:text-lg font-semibold text-white truncate flex-1 group-hover:text-amber-300 transition-colors">
                                        {{ $promocion->nombre }}
                                    </h3>
                                    @if($promocion->esta_vigente)
                                        <span class="flex-shrink-0 w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse shadow-lg shadow-green-400/50" aria-label="Vigente"></span>
                                    @endif
                                </div>

                                {{-- Badges --}}
                                <div class="flex flex-wrap gap-1.5 sm:gap-2 mt-2">
                                    <span class="bg-gradient-to-r from-amber-500/20 to-amber-600/10 text-amber-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full capitalize font-medium border border-amber-500/30">
                                        {{ $promocion->tipo }}
                                    </span>
                                    <span class="bg-gradient-to-r from-blue-500/20 to-blue-600/10 text-blue-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-blue-500/30">
                                        {{ $promocion->aplica_sobre == 'pedido' ? 'pedido completo' : 'items específicos' }}
                                    </span>
                                    <span class="bg-gradient-to-r from-green-500/20 to-green-600/10 text-green-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full font-bold border border-green-500/30">
                                        @if($promocion->tipo == 'porcentaje')
                                            -{{ rtrim(rtrim(number_format($promocion->valor, 2, '.', ''), '0'), '.') }}%
                                        @elseif($promocion->tipo == 'monto_fijo')
                                            -${{ number_format($promocion->valor, 2) }}
                                        @else
                                            {{ ucfirst($promocion->tipo) }}
                                        @endif
                                    </span>
                                    @if($promocion->esta_vigente)
                                        <span class="bg-gradient-to-r from-green-500/20 to-green-600/10 text-green-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-green-500/30">
                                            ✓ Vigente
                                        </span>
                                    @else
                                        <span class="bg-gradient-to-r from-red-500/20 to-red-600/10 text-red-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-red-500/30">
                                            ✕ No vigente
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

                                {{-- Fechas y horas --}}
                                @if($promocion->fecha_inicio || $promocion->fecha_fin)
                                <p class="text-zinc-400 text-xs sm:text-sm mt-2 flex items-center gap-1 truncate">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-zinc-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ optional($promocion->fecha_inicio)->format('d/m/Y') ?? 'Sin inicio' }} - {{ optional($promocion->fecha_fin)->format('d/m/Y') ?? 'Sin fin' }}</span>
                                </p>
                                @endif
                                @if($promocion->hora_inicio || $promocion->hora_fin)
                                <p class="text-zinc-400 text-xs sm:text-sm mt-1 flex items-center gap-1 truncate">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-zinc-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $promocion->hora_inicio ?? '00:00' }} - {{ $promocion->hora_fin ?? '23:59' }}</span>
                                </p>
                                @endif
                            </div>
                        </div>

                        {{-- Tooltip / Vista rápida accesible --}}
                        <div class="hidden md:block absolute left-full ml-4 top-0 z-50 w-80 bg-zinc-900 border-2 border-amber-500/50 rounded-xl p-4 shadow-2xl pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="text-xs space-y-2">
                                <div class="flex items-center gap-2 text-amber-400 font-semibold border-b border-zinc-700 pb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Vista Rápida
                                </div>
                                <div>
                                    <span class="text-zinc-400">Descuento:</span>
                                    <span class="text-white font-semibold ml-1">
                                        @if($promocion->tipo == 'porcentaje')
                                            {{ rtrim(rtrim(number_format($promocion->valor, 2, '.', ''), '0'), '.') }}% OFF
                                        @elseif($promocion->tipo == 'monto_fijo')
                                            ${{ number_format($promocion->valor, 2) }}
                                        @else
                                            {{ ucfirst($promocion->tipo) }}
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="text-zinc-400">Alcance:</span>
                                    <span class="text-white font-semibold ml-1">
                                        {{ $promocion->aplica_sobre == 'pedido' ? 'Todo el pedido' : 'Productos específicos' }}
                                    </span>
                                </div>
                                @if($promocion->productos->count() > 0 || $promocion->categorias->count() > 0)
                                <div>
                                    <span class="text-zinc-400">Aplica en:</span>
                                    <div class="text-white ml-1 mt-1">
                                        @if($promocion->productos->count() > 0)
                                            <span class="text-green-400">{{ $promocion->productos->count() }} productos</span>
                                        @endif
                                        @if($promocion->productos->count() > 0 && $promocion->categorias->count() > 0)
                                            <span class="text-zinc-500"> + </span>
                                        @endif
                                        @if($promocion->categorias->count() > 0)
                                            <span class="text-blue-400">{{ $promocion->categorias->count() }} categorías</span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                                <div>
                                    <span class="text-zinc-400">Prioridad:</span>
                                    <span class="text-white font-semibold ml-1">{{ $promocion->prioridad }}/10</span>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <div class="flex items-center gap-2 justify-end sm:justify-start flex-shrink-0">
                            <a href="{{ route('promociones.show', $promocion) }}"
                               class="bg-blue-600/80 hover:bg-blue-500 active:bg-blue-700 text-white py-2 px-3 rounded-lg transition-all min-h-[44px] min-w-[44px] flex items-center justify-center touch-manipulation shadow-lg hover:shadow-blue-500/30"
                               title="Ver detalles">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @can('editar-promociones')
                            <a href="{{ route('promociones.edit', $promocion) }}"
                               class="bg-zinc-700/80 hover:bg-zinc-600 active:bg-zinc-500 text-white py-2 px-3 rounded-lg transition-all min-h-[44px] min-w-[44px] flex items-center justify-center touch-manipulation shadow-lg hover:shadow-zinc-500/30"
                               title="Editar">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @endcan
                            @can('eliminar-promociones')
                            <form action="{{ route('promociones.destroy', $promocion) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta promoción?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-600/80 hover:bg-red-500 active:bg-red-700 text-white py-2 px-3 rounded-lg transition-all min-h-[44px] min-w-[44px] flex items-center justify-center touch-manipulation shadow-lg hover:shadow-red-500/30"
                                        title="Eliminar">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-medium mb-2 text-sm sm:text-base">No se encontraron promociones</h3>
                    <p class="text-zinc-400 mb-4 text-xs sm:text-sm">Aún no hay promociones creadas.</p>
                    @can('crear-promociones')
                    <a href="{{ route('promociones.create') }}"
                       class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-medium py-2 px-4 rounded-lg transition-all inline-flex items-center gap-2 text-sm sm:text-base min-h-[44px] touch-manipulation shadow-lg shadow-amber-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Crear Primera Promoción
                    </a>
                    @endcan
                </div>
                @endforelse
            </div>

            {{-- Paginación (si aplica) --}}
            @if(method_exists($promociones, 'links'))
                <div class="mt-6">{{ $promociones->links() }}</div>
            @endif

        </div>
    </div>

    {{-- JavaScript: filtros, búsqueda y orden --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const items = Array.from(document.querySelectorAll('.promocion-item'));
        const inputSearch = document.getElementById('search');
        const clearSearch = document.getElementById('clear-search');
        const sortSelect = document.getElementById('sort');
        const btnReset = document.getElementById('btn-reset');

        let state = {
            filter: 'todas',
            query: '',
            sort: 'recent',
        };

        const countsEls = {
            todas: document.getElementById('count-todas'),
            vigentes: document.getElementById('count-vigentes'),
            'no-vigentes': document.getElementById('count-no-vigentes'),
            activas: document.getElementById('count-activas'),
            inactivas: document.getElementById('count-inactivas'),
        };

        function matchesFilter(item) {
            const vigente = item.dataset.vigente === 'true';
            const activo = item.dataset.activo === 'true';
            switch (state.filter) {
                case 'vigentes': return vigente;
                case 'no-vigentes': return !vigente;
                case 'activas': return activo;
                case 'inactivas': return !activo;
                default: return true; // 'todas'
            }
        }

        function matchesQuery(item) {
            if (!state.query) return true;
            const nombre = item.dataset.nombre || '';
            return nombre.includes(state.query);
        }

        function applySort(visibleItems) {
            const sort = state.sort;
            const getNum = (el, key) => Number(el.dataset[key] || 0);
            const getText = (el) => (el.dataset.nombre || '');
            const getEstadoScore = (el) => {
                // Mayor score primero: vigente+activo (2), vigente inactivo (1), no vigente activo (1), no vigente inactivo (0)
                const v = el.dataset.vigente === 'true';
                const a = el.dataset.activo === 'true';
                return (v && a) ? 2 : (v || a ? 1 : 0);
            };
            visibleItems.sort((a, b) => {
                if (sort === 'recent') return getNum(b, 'recent') - getNum(a, 'recent');
                if (sort === 'prioridad-asc') return getNum(a, 'prioridad') - getNum(b, 'prioridad');
                if (sort === 'prioridad-desc') return getNum(b, 'prioridad') - getNum(a, 'prioridad');
                if (sort === 'nombre-asc') return getText(a).localeCompare(getText(b));
                if (sort === 'nombre-desc') return getText(b).localeCompare(getText(a));
                if (sort === 'estado') return getEstadoScore(b) - getEstadoScore(a);
                return 0;
            });
            return visibleItems;
        }

        function updateCounts() {
            const totals = {
                todas: items.length,
                vigentes: items.filter(i => i.dataset.vigente === 'true').length,
                'no-vigentes': items.filter(i => i.dataset.vigente !== 'true').length,
                activas: items.filter(i => i.dataset.activo === 'true').length,
                inactivas: items.filter(i => i.dataset.activo !== 'true').length,
            };
            Object.keys(countsEls).forEach(k => {
                if (countsEls[k]) countsEls[k].textContent = `(${totals[k]})`;
            });
        }

        function render() {
            // Filtrar
            let visible = items.filter(i => matchesFilter(i) && matchesQuery(i));
            // Ordenar
            visible = applySort(visible);

            // Mostrar / ocultar con animación simple
            const setHidden = new Set(items);
            visible.forEach(i => {
                i.style.display = '';
                i.classList.add('fade-in');
                setHidden.delete(i);
            });
            setHidden.forEach(i => {
                i.style.display = 'none';
                i.classList.remove('fade-in');
            });

            // Estado vacío cuando no hay resultados (pero sí existen items)
            let emptyBanner = document.getElementById('empty-filter-result');
            if (!emptyBanner) {
                emptyBanner = document.createElement('div');
                emptyBanner.id = 'empty-filter-result';
                emptyBanner.className = 'dashboard-card text-center py-8 hidden';
                emptyBanner.innerHTML = `
                    <h3 class="text-white font-medium mb-1">Sin resultados</h3>
                    <p class="text-zinc-400 text-sm">Ajusta los filtros o la búsqueda.</p>
                `;
                document.getElementById('promociones-lista').prepend(emptyBanner);
            }
            emptyBanner.classList.toggle('hidden', visible.length !== 0);
        }

        // Filtros (chips)
        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;
                state.filter = filter;

                filterButtons.forEach(b => {
                    b.classList.remove('active', 'bg-gradient-to-r', 'from-amber-500', 'to-amber-600', 'text-black', 'shadow-lg');
                    b.classList.add('bg-zinc-700', 'text-white');
                    b.setAttribute('aria-pressed', 'false');
                });
                btn.classList.remove('bg-zinc-700', 'text-white');
                btn.classList.add('active', 'bg-gradient-to-r', 'from-amber-500', 'to-amber-600', 'text-black', 'shadow-lg');
                btn.setAttribute('aria-pressed', 'true');

                render();
            });
        });

        // Búsqueda con debounce
        let t;
        inputSearch.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => {
                state.query = (inputSearch.value || '').trim().toLowerCase();
                render();
            }, 120);
        });
        clearSearch.addEventListener('click', (e) => {
            e.preventDefault();
            inputSearch.value = '';
            state.query = '';
            render();
        });

        // Orden
        sortSelect.addEventListener('change', () => {
            state.sort = sortSelect.value;
            render();
        });

        // Reset
        btnReset.addEventListener('click', (e) => {
            e.preventDefault();
            state = { filter: 'todas', query: '', sort: 'recent' };
            inputSearch.value = '';
            sortSelect.value = 'recent';
            // activar botón "todas"
            filterButtons.forEach(b => {
                const isTodas = b.dataset.filter === 'todas';
                b.classList.toggle('active', isTodas);
                b.classList.toggle('bg-gradient-to-r', isTodas);
                b.classList.toggle('from-amber-500', isTodas);
                b.classList.toggle('to-amber-600', isTodas);
                b.classList.toggle('text-black', isTodas);
                b.classList.toggle('shadow-lg', isTodas);
                b.classList.toggle('bg-zinc-700', !isTodas);
                b.classList.toggle('text-white', !isTodas);
                b.setAttribute('aria-pressed', isTodas ? 'true' : 'false');
            });
            render();
        });

        updateCounts();
        render();
    });
    </script>

    <style>
    /* Tarjeta base (fallback por si .dashboard-card no está global) */
    .dashboard-card {
        @apply bg-zinc-900/40 border border-zinc-800 rounded-xl p-4 sm:p-6;
    }

    /* Animación */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .fade-in { animation: fadeIn 0.25s ease-out; }

    /* Accesibilidad: ocultar label visibles solo para screen readers */
    .sr-only {
        position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
        overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
    }
    </style>
</x-layouts.app>
