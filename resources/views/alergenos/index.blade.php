{{-- resources/views/alergenos/index.blade.php --}}
<x-layouts.app :title="__('Gestión de Alérgenos - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header con acción --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-zinc-100 flex items-center gap-3">
                    <span class="text-4xl">🏥</span>
                    Gestión de Alérgenos
                </h1>
                <p class="text-zinc-400 mt-2">Administra los alérgenos de los productos del menú</p>
            </div>
            
            @can('crear-alergenos')
            <a href="{{ route('alergenos.create') }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Alérgeno
            </a>
            @endcan
        </div>

        {{-- Mensajes de notificación --}}
        @if (session('success'))
            <div class="mb-6 bg-green-500/20 border border-green-500/50 text-green-300 px-6 py-4 rounded-xl backdrop-blur-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-500/20 border border-red-500/50 text-red-300 px-6 py-4 rounded-xl backdrop-blur-sm">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-6 bg-orange-500/20 border border-orange-500/50 text-orange-300 px-6 py-4 rounded-xl backdrop-blur-sm">
                {{ session('warning') }}
            </div>
        @endif

        {{-- Tabla de alérgenos --}}
        <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-zinc-800/50 border-b border-zinc-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                                Alérgeno
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                                Color de Alerta
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                                Descripción
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                                Productos
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        @forelse ($alergenos as $alergeno)
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                {{-- Nombre e icono --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-3xl">{{ $alergeno->icono ?? '⚠️' }}</span>
                                        <div>
                                            <div class="text-zinc-100 font-medium">{{ $alergeno->nombre }}</div>
                                            <div class="text-xs text-zinc-500 mt-1">ID: {{ $alergeno->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Color de alerta --}}
                                <td class="px-6 py-4">
                                    @php
                                        $colores = [
                                            'red' => ['bg' => 'bg-red-500/20', 'text' => 'text-red-300', 'border' => 'border-red-500/50', 'label' => 'Crítico'],
                                            'orange' => ['bg' => 'bg-orange-500/20', 'text' => 'text-orange-300', 'border' => 'border-orange-500/50', 'label' => 'Moderado'],
                                            'yellow' => ['bg' => 'bg-yellow-500/20', 'text' => 'text-yellow-300', 'border' => 'border-yellow-500/50', 'label' => 'Leve'],
                                        ];
                                        $color = $colores[$alergeno->color] ?? $colores['red'];
                                    @endphp
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium border {{ $color['bg'] }} {{ $color['text'] }} {{ $color['border'] }}">
                                        <span class="w-2 h-2 rounded-full bg-current"></span>
                                        {{ $color['label'] }}
                                    </span>
                                </td>

                                {{-- Descripción --}}
                                <td class="px-6 py-4">
                                    <p class="text-zinc-400 text-sm max-w-xs truncate" title="{{ $alergeno->descripcion }}">
                                        {{ $alergeno->descripcion ?? 'Sin descripción' }}
                                    </p>
                                </td>

                                {{-- Productos asociados --}}
                                <td class="px-6 py-4 text-center">
                                    @if ($alergeno->productos_count > 0)
                                        <a href="{{ route('alergenos.productos', $alergeno) }}" 
                                           class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-500/20 text-amber-300 border border-amber-500/50 rounded-lg hover:bg-amber-500/30 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                            <span class="font-semibold">{{ $alergeno->productos_count }}</span>
                                        </a>
                                    @else
                                        <span class="text-zinc-600 text-sm">Sin productos</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-6 py-4 text-center">
                                    @can('editar-alergenos')
                                        <form action="{{ route('alergenos.toggle', $alergeno) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-all
                                                           {{ $alergeno->activo 
                                                              ? 'bg-green-500/20 text-green-300 border border-green-500/50 hover:bg-green-500/30' 
                                                              : 'bg-zinc-700/50 text-zinc-400 border border-zinc-600 hover:bg-zinc-700' }}">
                                                <span class="w-2 h-2 rounded-full {{ $alergeno->activo ? 'bg-green-400' : 'bg-zinc-500' }}"></span>
                                                {{ $alergeno->activo ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium
                                                     {{ $alergeno->activo 
                                                        ? 'bg-green-500/20 text-green-300 border border-green-500/50' 
                                                        : 'bg-zinc-700/50 text-zinc-400 border border-zinc-600' }}">
                                            <span class="w-2 h-2 rounded-full {{ $alergeno->activo ? 'bg-green-400' : 'bg-zinc-500' }}"></span>
                                            {{ $alergeno->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    @endcan
                                </td>

                                {{-- Acciones --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @can('editar-alergenos')
                                            <a href="{{ route('alergenos.edit', $alergeno) }}" 
                                               class="p-2 hover:bg-zinc-700/50 rounded-lg transition-colors" title="Editar">
                                                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        @endcan

                                        @can('eliminar-alergenos')
                                            <form action="{{ route('alergenos.destroy', $alergeno) }}" method="POST" 
                                                  onsubmit="return confirm('⚠️ ¿Estás seguro de eliminar el alérgeno \'{{ $alergeno->nombre }}\'?\n\nNota: No se puede eliminar si está asignado a productos.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 hover:bg-red-500/20 rounded-lg transition-colors" title="Eliminar">
                                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <span class="text-6xl">🏥</span>
                                        <p class="text-zinc-400 text-lg">No hay alérgenos registrados</p>
                                        @can('crear-alergenos')
                                            <a href="{{ route('alergenos.create') }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Crear primer alérgeno
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($alergenos->hasPages())
                <div class="px-6 py-4 border-t border-zinc-800">
                    {{ $alergenos->links() }}
                </div>
            @endif
        </div>

        {{-- Info adicional --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-6 backdrop-blur-sm">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-3xl">🚨</span>
                    <h3 class="text-red-300 font-semibold">Nivel Crítico</h3>
                </div>
                <p class="text-zinc-400 text-sm">Alergias que pueden causar reacciones graves o anafilaxia</p>
            </div>

            <div class="bg-orange-500/10 border border-orange-500/30 rounded-xl p-6 backdrop-blur-sm">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-3xl">⚠️</span>
                    <h3 class="text-orange-300 font-semibold">Nivel Moderado</h3>
                </div>
                <p class="text-zinc-400 text-sm">Alergias que requieren precaución y seguimiento</p>
            </div>

            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-6 backdrop-blur-sm">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-3xl">⚡</span>
                    <h3 class="text-yellow-300 font-semibold">Nivel Leve</h3>
                </div>
                <p class="text-zinc-400 text-sm">Intolerancias o sensibilidades menores</p>
            </div>
        </div>
    </div>
</x-layouts.app>
