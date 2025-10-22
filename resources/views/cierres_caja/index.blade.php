{{-- filepath: resources/views/cierres_caja/index.blade.php --}}
<x-layouts.app :title="__('Cierres de Caja')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="dashboard-card mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-2">
                            <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Cierres de Caja
                        </h1>
                        <p class="text-zinc-400">Historial de cierres y arqueos diarios</p>
                    </div>
                    @can('cerrar-caja')
                        <a href="{{ route('cierres_caja.create') }}" 
                           class="px-6 py-3 bg-amber-500 hover:bg-amber-400 text-black font-semibold rounded-lg transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nuevo Cierre
                        </a>
                    @endcan
                </div>
            </div>

            @if(session('success'))
                <div class="dashboard-card bg-green-500/10 border-green-500/30 mb-6">
                    <div class="flex items-center gap-3 text-green-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="dashboard-card bg-blue-500/10 border-blue-500/30 mb-6">
                    <div class="flex items-center gap-3 text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            {{-- Filtros --}}
            <div class="dashboard-card mb-6">
                <form action="{{ route('cierres_caja.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Fecha inicio --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Fecha Inicio</label>
                            <input type="date" 
                                   name="fecha_inicio" 
                                   value="{{ $fechaInicio }}"
                                   class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        </div>

                        {{-- Fecha fin --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Fecha Fin</label>
                            <input type="date" 
                                   name="fecha_fin" 
                                   value="{{ $fechaFin }}"
                                   class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        </div>

                        {{-- Cajero --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Cajero</label>
                            <select name="cajero_id" 
                                    class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                <option value="todos" {{ $cajeroId === 'todos' ? 'selected' : '' }}>Todos</option>
                                @foreach($cajeros as $cajero)
                                    <option value="{{ $cajero->id }}" {{ $cajeroId == $cajero->id ? 'selected' : '' }}>
                                        {{ $cajero->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Estado</label>
                            <select name="estado" 
                                    class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                <option value="todos" {{ $estado === 'todos' ? 'selected' : '' }}>Todos</option>
                                <option value="cuadrados" {{ $estado === 'cuadrados' ? 'selected' : '' }}>Cuadrados</option>
                                <option value="con_diferencias" {{ $estado === 'con_diferencias' ? 'selected' : '' }}>Con Diferencias</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" 
                                class="px-6 py-2 bg-amber-500 hover:bg-amber-400 text-black font-semibold rounded-lg transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Estadísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Total cierres --}}
                <div class="dashboard-card bg-gradient-to-br from-blue-500/10 to-blue-600/5 border-blue-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-300 mb-1">Total Cierres</p>
                            <p class="text-3xl font-bold text-blue-400">{{ $estadisticas['total_cierres'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total sistema --}}
                <div class="dashboard-card bg-gradient-to-br from-green-500/10 to-green-600/5 border-green-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-300 mb-1">Total Sistema</p>
                            <p class="text-3xl font-bold text-green-400">Bs {{ number_format($estadisticas['total_sistema'], 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Cierres cuadrados --}}
                <div class="dashboard-card bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border-emerald-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-emerald-300 mb-1">Cierres Cuadrados</p>
                            <p class="text-3xl font-bold text-emerald-400">{{ $estadisticas['cierres_cuadrados'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total diferencias --}}
                <div class="dashboard-card bg-gradient-to-br from-{{ abs($estadisticas['total_diferencias']) < 0.01 ? 'emerald' : ($estadisticas['total_diferencias'] < 0 ? 'red' : 'amber') }}-500/10 to-{{ abs($estadisticas['total_diferencias']) < 0.01 ? 'emerald' : ($estadisticas['total_diferencias'] < 0 ? 'red' : 'amber') }}-600/5 border-{{ abs($estadisticas['total_diferencias']) < 0.01 ? 'emerald' : ($estadisticas['total_diferencias'] < 0 ? 'red' : 'amber') }}-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-{{ abs($estadisticas['total_diferencias']) < 0.01 ? 'emerald' : ($estadisticas['total_diferencias'] < 0 ? 'red' : 'amber') }}-300 mb-1">Total Diferencias</p>
                            <p class="text-3xl font-bold text-{{ abs($estadisticas['total_diferencias']) < 0.01 ? 'emerald' : ($estadisticas['total_diferencias'] < 0 ? 'red' : 'amber') }}-400">
                                {{ $estadisticas['total_diferencias'] >= 0 ? '+' : '' }}Bs {{ number_format($estadisticas['total_diferencias'], 2) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-{{ abs($estadisticas['total_diferencias']) < 0.01 ? 'emerald' : ($estadisticas['total_diferencias'] < 0 ? 'red' : 'amber') }}-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-{{ abs($estadisticas['total_diferencias']) < 0.01 ? 'emerald' : ($estadisticas['total_diferencias'] < 0 ? 'red' : 'amber') }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de cierres --}}
            <div class="dashboard-card">
                <h3 class="text-xl font-bold text-white mb-4">Historial de Cierres</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-zinc-700">
                                <th class="text-left text-sm font-semibold text-zinc-300 py-3 px-4">ID</th>
                                <th class="text-left text-sm font-semibold text-zinc-300 py-3 px-4">Cajero</th>
                                <th class="text-left text-sm font-semibold text-zinc-300 py-3 px-4">Fecha/Hora</th>
                                <th class="text-right text-sm font-semibold text-zinc-300 py-3 px-4">Sistema</th>
                                <th class="text-right text-sm font-semibold text-zinc-300 py-3 px-4">Declarado</th>
                                <th class="text-right text-sm font-semibold text-zinc-300 py-3 px-4">Diferencia</th>
                                <th class="text-center text-sm font-semibold text-zinc-300 py-3 px-4">Estado</th>
                                <th class="text-center text-sm font-semibold text-zinc-300 py-3 px-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($cierres as $cierre)
                                <tr class="hover:bg-zinc-800/50 transition-colors">
                                    <td class="py-3 px-4 text-sm text-zinc-400">#{{ $cierre->id }}</td>
                                    <td class="py-3 px-4 text-sm text-white">{{ $cierre->cajero->name }}</td>
                                    <td class="py-3 px-4 text-sm text-zinc-300">
                                        <div>{{ $cierre->fin->format('d/m/Y') }}</div>
                                        <div class="text-xs text-zinc-500">
                                            {{ $cierre->inicio->format('H:i') }} - {{ $cierre->fin->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-right text-green-400 font-medium">
                                        Bs {{ number_format($cierre->total_sistema, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-right text-blue-400 font-medium">
                                        Bs {{ number_format($cierre->total_declarado, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-right font-bold text-{{ $cierre->color_diferencia }}-400">
                                        {{ $cierre->diferencia >= 0 ? '+' : '' }}Bs {{ number_format($cierre->diferencia, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $cierre->color_diferencia }}-500/20 text-{{ $cierre->color_diferencia }}-400">
                                            {{ $cierre->tipo_diferencia }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            @can('ver-cierres')
                                                <a href="{{ route('cierres_caja.show', $cierre->id) }}" 
                                                   class="p-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-all"
                                                   title="Ver detalle">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('cierres_caja.exportar-pdf', $cierre->id) }}" 
                                                   class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all"
                                                   title="Exportar PDF">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center">
                                        <div class="text-zinc-500">
                                            <svg class="w-16 h-16 mx-auto mb-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-lg font-medium">No hay cierres de caja registrados</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($cierres->hasPages())
                    <div class="mt-6">
                        {{ $cierres->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-layouts.app>
