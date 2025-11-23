<x-layouts.app :title="__('Estadísticas de Feedback')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">Estadísticas de Feedback</h1>
                                <p class="text-sm text-zinc-400">Análisis detallado de satisfacción del cliente</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('feedback.index') }}" 
                           class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Ver Lista
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filtro de Período -->
            <div class="dashboard-card mb-6">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <label class="text-zinc-300 font-medium">Período:</label>
                    <select name="periodo" 
                            onchange="this.form.submit()"
                            class="bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500">
                        <option value="7" {{ $periodo == '7' ? 'selected' : '' }}>Últimos 7 días</option>
                        <option value="30" {{ $periodo == '30' ? 'selected' : '' }}>Últimos 30 días</option>
                        <option value="90" {{ $periodo == '90' ? 'selected' : '' }}>Últimos 3 meses</option>
                        <option value="180" {{ $periodo == '180' ? 'selected' : '' }}>Últimos 6 meses</option>
                        <option value="todo" {{ $periodo == 'todo' ? 'selected' : '' }}>Todo el tiempo</option>
                    </select>
                </form>
            </div>

            @if($estadisticas['total'] === 0)
                <div class="dashboard-card text-center py-12">
                    <div class="w-20 h-20 mx-auto mb-4 bg-zinc-800 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">No hay feedbacks en este período</h3>
                    <p class="text-zinc-400">Intenta seleccionar un período más amplio</p>
                </div>
            @else
                <div class="space-y-6">
                    
                    <!-- Resumen General (Cards) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Total Feedbacks -->
                        <div class="dashboard-card bg-gradient-to-br from-blue-500/10 to-blue-600/10 border-blue-500/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-300 text-sm mb-1">Total Feedbacks</p>
                                    <p class="text-3xl font-bold text-white">{{ $estadisticas['total'] }}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Promedio -->
                        <div class="dashboard-card bg-gradient-to-br from-amber-500/10 to-amber-600/10 border-amber-500/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-amber-300 text-sm mb-1">Calificación Promedio</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-3xl font-bold text-white">{{ $estadisticas['promedio'] }}</p>
                                        <span class="text-2xl">
                                            @if($estadisticas['promedio'] >= 4.5) 😍
                                            @elseif($estadisticas['promedio'] >= 3.5) 😊
                                            @elseif($estadisticas['promedio'] >= 2.5) 😐
                                            @elseif($estadisticas['promedio'] >= 1.5) 😞
                                            @else 😡
                                            @endif
                                        </span>
                                    </div>
                                    <p class="text-amber-400 text-xs mt-1">de 5.0 estrellas</p>
                                </div>
                                <div class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Satisfacción -->
                        <div class="dashboard-card bg-gradient-to-br from-green-500/10 to-green-600/10 border-green-500/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-300 text-sm mb-1">Satisfechos</p>
                                    <p class="text-3xl font-bold text-white">{{ $estadisticas['porcentaje_satisfaccion'] }}%</p>
                                    <p class="text-green-400 text-xs mt-1">{{ $estadisticas['satisfaccion'] }} clientes (4-5⭐)</p>
                                </div>
                                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                                    <span class="text-2xl">😊</span>
                                </div>
                            </div>
                        </div>

                        <!-- Insatisfacción -->
                        <div class="dashboard-card bg-gradient-to-br from-red-500/10 to-red-600/10 border-red-500/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-300 text-sm mb-1">Insatisfechos</p>
                                    <p class="text-3xl font-bold text-white">{{ $estadisticas['porcentaje_insatisfaccion'] }}%</p>
                                    <p class="text-red-400 text-xs mt-1">{{ $estadisticas['insatisfaccion'] }} clientes (1-2⭐)</p>
                                </div>
                                <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                                    <span class="text-2xl">😞</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Distribución de Calificaciones -->
                    <div class="dashboard-card">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Distribución de Calificaciones
                        </h2>
                        <div class="space-y-4">
                            @foreach([5,4,3,2,1] as $star)
                                @php
                                    $count = $estadisticas['distribucion'][$star];
                                    $percent = $estadisticas['porcentajes'][$star];
                                    $colors = [
                                        5 => 'bg-emerald-500',
                                        4 => 'bg-green-500',
                                        3 => 'bg-yellow-500',
                                        2 => 'bg-orange-500',
                                        1 => 'bg-red-500'
                                    ];
                                    $emojis = [5 => '😍', 4 => '😊', 3 => '😐', 2 => '😞', 1 => '😡'];
                                @endphp
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-2xl">{{ $emojis[$star] }}</span>
                                        <span class="text-white font-medium w-20">{{ $star }} estrella{{ $star > 1 ? 's' : '' }}</span>
                                        <div class="flex-1 bg-zinc-800 rounded-full h-4 overflow-hidden">
                                            <div class="{{ $colors[$star] }} h-full rounded-full transition-all duration-500" 
                                                 style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span class="text-zinc-400 font-semibold w-16 text-right">{{ $percent }}%</span>
                                        <span class="text-zinc-500 w-12 text-right">({{ $count }})</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tendencias Mensuales -->
                    <div class="dashboard-card">
                        <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            Tendencias Mensuales (Últimos 6 meses)
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-zinc-700">
                                        <th class="text-left text-zinc-400 py-3 px-4">Mes</th>
                                        <th class="text-center text-zinc-400 py-3 px-4">Total Feedbacks</th>
                                        <th class="text-center text-zinc-400 py-3 px-4">Promedio</th>
                                        <th class="text-left text-zinc-400 py-3 px-4">Tendencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tendencias as $index => $mes)
                                        <tr class="border-b border-zinc-800 hover:bg-zinc-800/50 transition">
                                            <td class="py-3 px-4 text-white font-medium">{{ $mes['mes'] }}</td>
                                            <td class="py-3 px-4 text-center text-zinc-300">{{ $mes['total'] }}</td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="text-white font-bold">{{ $mes['promedio'] }}</span>
                                                    <span class="text-lg">
                                                        @if($mes['promedio'] >= 4.5) 😍
                                                        @elseif($mes['promedio'] >= 3.5) 😊
                                                        @elseif($mes['promedio'] >= 2.5) 😐
                                                        @elseif($mes['promedio'] >= 1.5) 😞
                                                        @elseif($mes['promedio'] > 0) 😡
                                                        @else -
                                                        @endif
                                                    </span>
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                @if($index > 0 && $mes['promedio'] > 0 && $tendencias[$index-1]['promedio'] > 0)
                                                    @php
                                                        $diff = $mes['promedio'] - $tendencias[$index-1]['promedio'];
                                                    @endphp
                                                    @if($diff > 0)
                                                        <span class="text-green-400 flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                                            </svg>
                                                            +{{ number_format($diff, 2) }}
                                                        </span>
                                                    @elseif($diff < 0)
                                                        <span class="text-red-400 flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                                            </svg>
                                                            {{ number_format($diff, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-zinc-500">Sin cambio</span>
                                                    @endif
                                                @else
                                                    <span class="text-zinc-600">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Productos con Mejor y Peor Calificación -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Mejor Calificados -->
                        <div class="dashboard-card bg-gradient-to-br from-green-500/5 to-emerald-500/5 border-green-500/20">
                            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                <span class="text-2xl">🏆</span>
                                Productos Mejor Calificados
                            </h2>
                            @if($productosMejorCalificados->isEmpty())
                                <p class="text-zinc-500 text-sm">No hay suficientes datos</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($productosMejorCalificados as $producto)
                                        <div class="bg-zinc-800/50 rounded-lg p-3 flex items-center justify-between">
                                            <div class="flex-1">
                                                <p class="text-white font-medium">{{ $producto->nombre }}</p>
                                                <p class="text-zinc-500 text-xs">{{ $producto->total_feedbacks }} feedbacks</p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-green-400 font-bold text-lg">{{ number_format($producto->promedio, 2) }}</span>
                                                <span class="text-xl">⭐</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Peor Calificados -->
                        <div class="dashboard-card bg-gradient-to-br from-red-500/5 to-orange-500/5 border-red-500/20">
                            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                <span class="text-2xl">⚠️</span>
                                Productos a Mejorar
                            </h2>
                            @if($productosPeorCalificados->isEmpty())
                                <p class="text-zinc-500 text-sm">No hay suficientes datos</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($productosPeorCalificados as $producto)
                                        <div class="bg-zinc-800/50 rounded-lg p-3 flex items-center justify-between">
                                            <div class="flex-1">
                                                <p class="text-white font-medium">{{ $producto->nombre }}</p>
                                                <p class="text-zinc-500 text-xs">{{ $producto->total_feedbacks }} feedbacks</p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-red-400 font-bold text-lg">{{ number_format($producto->promedio, 2) }}</span>
                                                <span class="text-xl">⭐</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
