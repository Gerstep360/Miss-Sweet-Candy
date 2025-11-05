<x-layouts.app :title="__('Lista de Feedbacks')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            
            <!-- Header -->
            <div class="dashboard-card mb-6 bg-gradient-to-br from-purple-500/10 via-purple-600/5 to-zinc-900/50 border-purple-500/20">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">Feedbacks de Clientes</h1>
                                <p class="text-sm text-purple-300/80">{{ $feedbacks->total() }} feedbacks registrados</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('feedback.estadisticas') }}" 
                       class="bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white py-2.5 px-5 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-purple-500/30 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Estadísticas
                    </a>
                </div>
            </div>

            <!-- Filtros Mejorados -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                <form method="GET" class="lg:col-span-3 dashboard-card">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Filtro de Tipo -->
                        <div>
                            <label class="block text-zinc-400 text-sm font-medium mb-2">Tipo de Feedback</label>
                            <select name="tipo" 
                                    onchange="this.form.submit()"
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                                <option value="todos" {{ request('tipo', 'todos') == 'todos' ? 'selected' : '' }}>📋 Todos los tipos</option>
                                <option value="general" {{ request('tipo') == 'general' ? 'selected' : '' }}>💬 General</option>
                                <option value="pedido" {{ request('tipo') == 'pedido' ? 'selected' : '' }}>🛒 Pedido</option>
                                <option value="servicio" {{ request('tipo') == 'servicio' ? 'selected' : '' }}>👨‍🍳 Servicio</option>
                                <option value="local" {{ request('tipo') == 'local' ? 'selected' : '' }}>🏪 Local</option>
                                <option value="web" {{ request('tipo') == 'web' ? 'selected' : '' }}>🌐 Sitio Web</option>
                            </select>
                        </div>

                        <!-- Filtro de Estado -->
                        <div>
                            <label class="block text-zinc-400 text-sm font-medium mb-2">Estado</label>
                            <select name="estado" 
                                    onchange="this.form.submit()"
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                                <option value="todos" {{ request('estado', 'todos') == 'todos' ? 'selected' : '' }}>📊 Todos los estados</option>
                                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                <option value="revisado" {{ request('estado') == 'revisado' ? 'selected' : '' }}>👀 Revisado</option>
                                <option value="respondido" {{ request('estado') == 'respondido' ? 'selected' : '' }}>✅ Respondido</option>
                                <option value="resuelto" {{ request('estado') == 'resuelto' ? 'selected' : '' }}>🎉 Resuelto</option>
                            </select>
                        </div>

                        <!-- Filtro de Período -->
                        <div>
                            <label class="block text-zinc-400 text-sm font-medium mb-2">Período</label>
                            <select name="periodo" 
                                    onchange="this.form.submit()"
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                                <option value="7" {{ request('periodo', '7') == '7' ? 'selected' : '' }}>📅 Últimos 7 días</option>
                                <option value="30" {{ request('periodo') == '30' ? 'selected' : '' }}>📆 Últimos 30 días</option>
                                <option value="90" {{ request('periodo') == '90' ? 'selected' : '' }}>📊 Últimos 3 meses</option>
                                <option value="todo" {{ request('periodo') == 'todo' ? 'selected' : '' }}>🗂️ Todo el tiempo</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Resumen Rápido -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="dashboard-card bg-gradient-to-br from-blue-500/10 to-blue-600/10 border-blue-500/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-300 text-xs font-medium mb-1">Promedio General</p>
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
                        </div>
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card bg-gradient-to-br from-purple-500/10 to-purple-600/10 border-purple-500/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-300 text-xs font-medium mb-1">Total Feedbacks</p>
                            <p class="text-3xl font-bold text-white">{{ $estadisticas['total'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">💭</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card bg-gradient-to-br from-green-500/10 to-green-600/10 border-green-500/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-300 text-xs font-medium mb-1">Positivos</p>
                            <p class="text-3xl font-bold text-white">{{ $estadisticas['positivos'] ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">😊</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card bg-gradient-to-br from-red-500/10 to-red-600/10 border-red-500/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-300 text-xs font-medium mb-1">Negativos</p>
                            <p class="text-3xl font-bold text-white">{{ $estadisticas['negativos'] ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">😞</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Feedbacks -->
            @if($feedbacks->isEmpty())
                <div class="dashboard-card text-center py-16 bg-gradient-to-br from-zinc-900/50 to-zinc-800/30">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-zinc-800 to-zinc-700 rounded-2xl flex items-center justify-center shadow-xl">
                        <svg class="w-12 h-12 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">No hay feedbacks en este período</h3>
                    <p class="text-zinc-400 mb-6">Intenta ajustar los filtros o seleccionar un período más amplio</p>
                    <a href="{{ route('feedback.index') }}" class="inline-flex items-center gap-2 text-purple-400 hover:text-purple-300 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Limpiar filtros
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($feedbacks as $feedback)
                        <div class="dashboard-card hover:border-purple-500/40 hover:shadow-lg hover:shadow-purple-500/10 transition-all duration-300 group">
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Calificación Grande -->
                                <div class="flex lg:flex-col items-center lg:items-start gap-3 lg:gap-2 lg:min-w-[110px]">
                                    <div class="w-20 h-20 {{ $feedback->color_badge }} border-2 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-300">
                                        <span class="text-4xl">{{ $feedback->emoji }}</span>
                                    </div>
                                    <div class="text-center lg:text-left">
                                        <p class="text-3xl font-bold text-white">{{ $feedback->calificacion_general }}</p>
                                        <p class="text-xs text-zinc-500 font-medium">de 5.0</p>
                                    </div>
                                </div>

                                <!-- Información -->
                                <div class="flex-1 space-y-3">
                                    <!-- Header -->
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="flex-1">
                                            <div class="flex items-center flex-wrap gap-2 mb-2">
                                                <!-- Badge de Tipo -->
                                                <span class="px-3 py-1.5 {{ 
                                                    $feedback->tipo === 'general' ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' : 
                                                    ($feedback->tipo === 'pedido' ? 'bg-blue-500/20 text-blue-300 border-blue-500/30' : 
                                                    ($feedback->tipo === 'servicio' ? 'bg-green-500/20 text-green-300 border-green-500/30' : 
                                                    ($feedback->tipo === 'local' ? 'bg-orange-500/20 text-orange-300 border-orange-500/30' : 
                                                    'bg-cyan-500/20 text-cyan-300 border-cyan-500/30'))) 
                                                }} border rounded-lg text-xs font-bold inline-flex items-center gap-1.5">
                                                    <span>{{ $feedback->tipo_icono }}</span>
                                                    <span>{{ $feedback->tipo_texto }}</span>
                                                </span>

                                                <!-- Badge de Estado -->
                                                <span class="px-3 py-1.5 {{ $feedback->estado_color }} border rounded-lg text-xs font-bold">
                                                    {{ $feedback->estado_texto }}
                                                </span>

                                                @if($feedback->pedido_id)
                                                    <span class="bg-zinc-700/50 text-zinc-300 px-2.5 py-1 rounded-lg text-xs font-semibold">
                                                        🛒 Pedido #{{ $feedback->pedido_id }}
                                                    </span>
                                                @endif

                                                <span class="text-zinc-500 text-xs">
                                                    📅 {{ $feedback->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                            
                                            <p class="text-white font-semibold text-lg mb-1">
                                                {{ $feedback->cliente->name ?? 'Cliente Anónimo' }}
                                            </p>
                                            @if($feedback->cliente)
                                                <p class="text-zinc-400 text-sm">📧 {{ $feedback->cliente->email }}</p>
                                            @endif
                                        </div>

                                        <!-- Badge de Calificación -->
                                        <span class="px-4 py-2 {{ $feedback->color_badge }} border-2 rounded-xl text-sm font-bold whitespace-nowrap shadow-lg">
                                            {{ $feedback->texto_calificacion }}
                                        </span>
                                    </div>

                                    <!-- Calificaciones Específicas -->
                                    @if($feedback->calificacion_comida || $feedback->calificacion_servicio || $feedback->calificacion_ambiente)
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($feedback->calificacion_comida)
                                                <div class="bg-zinc-800/50 px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5">
                                                    <span>🍽️</span>
                                                    <span class="text-zinc-400">Comida:</span>
                                                    <span class="text-white font-bold">{{ $feedback->calificacion_comida }}/5</span>
                                                </div>
                                            @endif
                                            @if($feedback->calificacion_servicio)
                                                <div class="bg-zinc-800/50 px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5">
                                                    <span>👨‍💼</span>
                                                    <span class="text-zinc-400">Servicio:</span>
                                                    <span class="text-white font-bold">{{ $feedback->calificacion_servicio }}/5</span>
                                                </div>
                                            @endif
                                            @if($feedback->calificacion_ambiente)
                                                <div class="bg-zinc-800/50 px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5">
                                                    <span>🏠</span>
                                                    <span class="text-zinc-400">Ambiente:</span>
                                                    <span class="text-white font-bold">{{ $feedback->calificacion_ambiente }}/5</span>
                                                </div>
                                            @endif
                                            @if($feedback->calificacion_web)
                                                <div class="bg-zinc-800/50 px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5">
                                                    <span>🌐</span>
                                                    <span class="text-zinc-400">Web:</span>
                                                    <span class="text-white font-bold">{{ $feedback->calificacion_web }}/5</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Comentarios Preview -->
                                    <div class="space-y-2">
                                        @if($feedback->elogios)
                                            <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-3">
                                                <p class="text-green-300 text-xs font-semibold mb-1 flex items-center gap-1">
                                                    <span>😊</span> Lo que más gustó
                                                </p>
                                                <p class="text-zinc-300 text-sm line-clamp-2">{{ $feedback->elogios }}</p>
                                            </div>
                                        @endif

                                        @if($feedback->quejas)
                                            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-3">
                                                <p class="text-red-300 text-xs font-semibold mb-1 flex items-center gap-1">
                                                    <span>😔</span> Problemas reportados
                                                </p>
                                                <p class="text-zinc-300 text-sm line-clamp-2">{{ $feedback->quejas }}</p>
                                            </div>
                                        @endif

                                        @if($feedback->sugerencias)
                                            <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3">
                                                <p class="text-blue-300 text-xs font-semibold mb-1 flex items-center gap-1">
                                                    <span>💡</span> Sugerencias
                                                </p>
                                                <p class="text-zinc-300 text-sm line-clamp-2">{{ $feedback->sugerencias }}</p>
                                            </div>
                                        @endif

                                        @if($feedback->comentario && !$feedback->elogios && !$feedback->quejas && !$feedback->sugerencias)
                                            <div class="bg-zinc-800/50 rounded-lg p-3 border-l-4 {{ 
                                                $feedback->calificacion_general >= 4 ? 'border-green-500' : 
                                                ($feedback->calificacion_general >= 3 ? 'border-yellow-500' : 'border-red-500') 
                                            }}">
                                                <p class="text-zinc-300 text-sm italic line-clamp-2">"{{ $feedback->comentario }}"</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Footer -->
                                    <div class="flex items-center justify-between pt-2 border-t border-zinc-800">
                                        <div class="flex items-center gap-3 text-xs">
                                            @if($feedback->recomendaria)
                                                <span class="text-green-400 flex items-center gap-1">
                                                    <span>✅</span>
                                                    <span class="font-medium">Recomendaría</span>
                                                </span>
                                            @else
                                                <span class="text-red-400 flex items-center gap-1">
                                                    <span>❌</span>
                                                    <span class="font-medium">No recomendaría</span>
                                                </span>
                                            @endif
                                            <span class="text-zinc-500">•</span>
                                            <span class="text-zinc-400 capitalize">{{ str_replace('_', ' ', $feedback->frecuencia_visita) }}</span>
                                        </div>

                                        <a href="{{ route('feedback.show', $feedback) }}" 
                                           class="inline-flex items-center gap-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 hover:text-purple-200 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 border border-purple-500/30 hover:border-purple-400/50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver completo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-6">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
