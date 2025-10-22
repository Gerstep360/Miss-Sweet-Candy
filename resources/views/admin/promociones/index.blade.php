{{-- resources/views/admin/promociones/index.blade.php --}}
<x-layouts.app :title="__('Gestión de Promociones - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Gestión de Promociones</h1>
                        <p class="text-zinc-300">Administra promociones, happy hours y combos especiales</p>
                    </div>
                    @can('crear-promociones')
                    <a href="{{ route('promociones.create') }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nueva Promoción
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Filtros -->
            <div class="dashboard-card mb-6">
                <div class="flex gap-4">
                    <button class="bg-amber-500 text-black font-medium py-2 px-4 rounded-lg">Todas</button>
                    <button class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">Activas</button>
                    <button class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">Inactivas</button>
                    <button class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">Happy Hour</button>
                </div>
            </div>

            <!-- Lista de promociones -->
            <div class="grid gap-6">
                @forelse($promociones as $promocion)
                <div class="dashboard-card">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">{{ $promocion->nombre }}</h3>
                                <div class="flex gap-2 mt-1">
                                    <span class="bg-amber-500/20 text-amber-400 text-xs px-2 py-1 rounded capitalize">{{ $promocion->tipo }}</span>
                                    <span class="bg-blue-500/20 text-blue-400 text-xs px-2 py-1 rounded">{{ $promocion->aplica_sobre }}</span>
                                    <span class="bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded font-bold">
                                        @if($promocion->tipo == 'porcentaje')
                                            {{ $promocion->valor }}%
                                        @elseif($promocion->tipo == 'monto_fijo')
                                            ${{ number_format($promocion->valor, 2) }}
                                        @else
                                            {{ ucfirst($promocion->tipo) }}
                                        @endif
                                    </span>
                                    @if($promocion->esta_vigente)
                                        <span class="bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded">Vigente</span>
                                    @else
                                        <span class="bg-red-500/20 text-red-400 text-xs px-2 py-1 rounded">No Vigente</span>
                                    @endif
                                    <span class="bg-zinc-700 text-zinc-300 text-xs px-2 py-1 rounded">Prioridad: {{ $promocion->prioridad }}</span>
                                </div>
                                @if($promocion->fecha_inicio || $promocion->fecha_fin)
                                <p class="text-zinc-400 text-sm mt-1">
                                    {{ $promocion->fecha_inicio ? $promocion->fecha_inicio->format('d/m/Y') : 'Inicio indefinido' }} 
                                    - 
                                    {{ $promocion->fecha_fin ? $promocion->fecha_fin->format('d/m/Y') : 'Fin indefinido' }}
                                </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @can('editar-promociones')
                            <a href="{{ route('promociones.edit', $promocion) }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-3 rounded-lg transition-colors" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @endcan
                            @can('eliminar-promociones')
                            <form action="{{ route('promociones.destroy', $promocion) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-500 text-white py-2 px-3 rounded-lg transition-colors" onclick="return confirm('¿Estás seguro de eliminar esta promoción?')" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
                @empty
                <div class="dashboard-card text-center py-12">
                    <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-medium mb-2">No se encontraron promociones</h3>
                    <p class="text-zinc-400 mb-4">Aún no hay promociones creadas.</p>
                    @can('crear-promociones')
                    <a href="{{ route('promociones.create') }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center gap-2">
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
</x-layouts.app>