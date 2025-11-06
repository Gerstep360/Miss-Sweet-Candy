{{-- resources/views/perfil/consultar-todos.blade.php --}}
<x-layouts.app :title="__('Clientes con Alergias y Preferencias - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            {{-- Header --}}
            <div class="dashboard-card mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-3">
                        <span class="text-4xl">👥</span>
                        Perfiles de Clientes
                    </h1>
                    <p class="text-zinc-300">Consulta las alergias y preferencias alimentarias de los clientes</p>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="dashboard-card mb-6">
                <form method="GET" action="{{ route('perfil.consultar.todos') }}" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            name="buscar" 
                            value="{{ request('buscar') }}"
                            placeholder="Buscar por nombre, email o teléfono..."
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        />
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-6 rounded-lg transition-colors">
                            Buscar
                        </button>
                        @if(request('buscar'))
                            <a href="{{ route('perfil.consultar.todos') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla de clientes --}}
            <div class="dashboard-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-zinc-800/50 border-b border-zinc-700">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-300 uppercase">Cliente</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-300 uppercase">Contacto</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-300 uppercase">Alergias</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-300 uppercase">Preferencias</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-300 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse ($clientes as $cliente)
                                @php
                                    $perfil = $cliente->perfil;
                                @endphp
                                <tr class="hover:bg-zinc-800/30 transition-colors">
                                    {{-- Cliente --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">{{ $cliente->initials() }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-zinc-100">{{ $cliente->name }}</div>
                                                @if($perfil && $perfil->tieneAlergiasGraves())
                                                    <span class="inline-flex items-center gap-1 text-xs text-red-400 font-semibold animate-pulse">
                                                        🚨 ALERGIAS GRAVES
                                                    </span>
                                                @elseif($perfil && $perfil->tieneAlergias())
                                                    <span class="inline-flex items-center gap-1 text-xs text-orange-400">
                                                        ⚠️ Tiene alergias
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Contacto --}}
                                    <td class="px-6 py-4">
                                        <div class="text-zinc-400 text-sm">
                                            <div class="truncate max-w-xs" title="{{ $cliente->email }}">{{ $cliente->email }}</div>
                                            @if($perfil && $perfil->telefono)
                                                <div class="text-blue-400">{{ $perfil->telefono }}</div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Alergias --}}
                                    <td class="px-6 py-4">
                                        @if($perfil && $perfil->tieneAlergias())
                                            <div class="flex flex-wrap gap-1 justify-center">
                                                @foreach($perfil->alergias as $alergia)
                                                    @php
                                                        $severidad = $alergia['severidad'] ?? 'leve';
                                                        $color = \App\Models\ClientePerfil::getColorSeveridad($severidad);
                                                        $icono = \App\Models\ClientePerfil::getIconoSeveridad($severidad);
                                                    @endphp
                                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-{{ $color }}-500/20 text-{{ $color }}-300 border border-{{ $color }}-500/50 {{ $severidad === 'grave' ? 'animate-pulse' : '' }}" title="Severidad: {{ $severidad }}">
                                                        {{ $icono }} {{ $alergia['nombre'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <span class="text-green-400 text-sm">✓ Sin alergias</span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Preferencias --}}
                                    <td class="px-6 py-4">
                                        @if($perfil && $perfil->tienePreferencias())
                                            <div class="flex flex-wrap gap-1 justify-center">
                                                @foreach($perfil->preferencias as $preferencia)
                                                    @php
                                                        $badge = \App\Models\ClientePerfil::getBadgePreferencia($preferencia);
                                                    @endphp
                                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-{{ $badge['color'] }}-500/20 text-{{ $badge['color'] }}-300" title="{{ $badge['label'] }}">
                                                        {{ $badge['icon'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <span class="text-zinc-500 text-sm">—</span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('clientes.perfil.ver', $cliente->id) }}" 
                                           class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-500/20 text-blue-300 border border-blue-500/50 rounded-lg hover:bg-blue-500/30 transition-colors text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </div>
                                            <p class="text-zinc-400 text-lg">
                                                @if(request('buscar'))
                                                    No se encontraron clientes con el criterio "{{ request('buscar') }}"
                                                @else
                                                    No hay clientes registrados
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if ($clientes->hasPages())
                    <div class="px-6 py-4 border-t border-zinc-800">
                        {{ $clientes->links() }}
                    </div>
                @endif
            </div>

            {{-- Estadísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-4">
                    <div class="text-zinc-400 text-sm mb-1">Total clientes</div>
                    <div class="text-2xl font-bold text-white">{{ $clientes->total() }}</div>
                </div>
                <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4">
                    <div class="text-red-400 text-sm mb-1">Con alergias graves</div>
                    <div class="text-2xl font-bold text-red-300">{{ $stats['con_alergias_graves'] ?? 0 }}</div>
                </div>
                <div class="bg-orange-500/10 border border-orange-500/30 rounded-xl p-4">
                    <div class="text-orange-400 text-sm mb-1">Con alergias</div>
                    <div class="text-2xl font-bold text-orange-300">{{ $stats['con_alergias'] ?? 0 }}</div>
                </div>
                <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4">
                    <div class="text-green-400 text-sm mb-1">Con preferencias</div>
                    <div class="text-2xl font-bold text-green-300">{{ $stats['con_preferencias'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
