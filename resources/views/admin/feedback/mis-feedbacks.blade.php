<x-layouts.app :title="__('Mis Feedbacks')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            
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
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">Mis Feedbacks</h1>
                                <p class="text-sm text-purple-300/80">{{ $feedbacks->total() }} feedbacks enviados</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('feedback.create') }}" 
                       class="bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white py-2.5 px-5 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-purple-500/30 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo Feedback
                    </a>
                </div>
            </div>

            <!-- Lista de Feedbacks -->
            @if($feedbacks->isEmpty())
                <div class="dashboard-card text-center py-16 bg-gradient-to-br from-zinc-900/50 to-zinc-800/30">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-zinc-800 to-zinc-700 rounded-2xl flex items-center justify-center shadow-xl">
                        <svg class="w-12 h-12 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Aún no has enviado ningún feedback</h3>
                    <p class="text-zinc-400 mb-6">¡Comparte tu opinión sobre nuestros productos y servicios!</p>
                    <a href="{{ route('feedback.create') }}" 
                       class="inline-flex items-center gap-2 bg-purple-500 hover:bg-purple-400 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 shadow-lg shadow-purple-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Crear mi primer feedback
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($feedbacks as $feedback)
                        <div class="dashboard-card hover:border-purple-500/40 hover:shadow-lg hover:shadow-purple-500/10 transition-all duration-300">
                            <div class="flex flex-col lg:flex-row gap-4">
                                
                                <!-- Emoji y Calificación -->
                                <div class="flex-shrink-0 text-center lg:text-left">
                                    <div class="inline-block lg:block">
                                        <span class="text-5xl mb-2">{{ $feedback->emoji }}</span>
                                        <div class="flex items-center justify-center lg:justify-start gap-1 mt-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $feedback->calificacion_general ? 'text-amber-400' : 'text-zinc-700' }}" 
                                                     fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="text-2xl font-bold text-white mt-2">{{ $feedback->calificacion_general }}/5</p>
                                        <span class="inline-block px-3 py-1 {{ $feedback->color_badge }} rounded-lg text-xs font-bold mt-2">
                                            {{ $feedback->sentimiento }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Información del Feedback -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-3 py-1 {{ 
                                                $feedback->tipo === 'general' ? 'bg-blue-500/20 text-blue-300 border-blue-500/40' :
                                                ($feedback->tipo === 'pedido' ? 'bg-green-500/20 text-green-300 border-green-500/40' :
                                                ($feedback->tipo === 'servicio' ? 'bg-purple-500/20 text-purple-300 border-purple-500/40' :
                                                ($feedback->tipo === 'local' ? 'bg-orange-500/20 text-orange-300 border-orange-500/40' :
                                                'bg-cyan-500/20 text-cyan-300 border-cyan-500/40'))) 
                                            }} border rounded-lg text-xs font-bold">
                                                {{ ucfirst($feedback->tipo) }}
                                            </span>
                                            
                                            @if($feedback->pedido_id)
                                                <span class="px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/40 rounded-lg text-xs font-bold">
                                                    📦 Pedido #{{ $feedback->pedido_id }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-right">
                                            <p class="text-zinc-400 text-xs">
                                                {{ $feedback->created_at->format('d/m/Y') }}
                                            </p>
                                            <p class="text-zinc-500 text-xs">
                                                {{ $feedback->created_at->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Calificaciones Detalladas -->
                                    @if($feedback->calificacion_comida || $feedback->calificacion_servicio || $feedback->calificacion_ambiente || $feedback->calificacion_precio || $feedback->calificacion_limpieza)
                                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 mb-3">
                                            @if($feedback->calificacion_comida)
                                                <div class="bg-zinc-800/50 rounded-lg p-2 text-center">
                                                    <p class="text-zinc-400 text-xs mb-1">🍽️ Comida</p>
                                                    <p class="text-white font-bold">{{ $feedback->calificacion_comida }}/5</p>
                                                </div>
                                            @endif
                                            @if($feedback->calificacion_servicio)
                                                <div class="bg-zinc-800/50 rounded-lg p-2 text-center">
                                                    <p class="text-zinc-400 text-xs mb-1">👥 Servicio</p>
                                                    <p class="text-white font-bold">{{ $feedback->calificacion_servicio }}/5</p>
                                                </div>
                                            @endif
                                            @if($feedback->calificacion_ambiente)
                                                <div class="bg-zinc-800/50 rounded-lg p-2 text-center">
                                                    <p class="text-zinc-400 text-xs mb-1">🎵 Ambiente</p>
                                                    <p class="text-white font-bold">{{ $feedback->calificacion_ambiente }}/5</p>
                                                </div>
                                            @endif
                                            @if($feedback->calificacion_precio)
                                                <div class="bg-zinc-800/50 rounded-lg p-2 text-center">
                                                    <p class="text-zinc-400 text-xs mb-1">💰 Precio</p>
                                                    <p class="text-white font-bold">{{ $feedback->calificacion_precio }}/5</p>
                                                </div>
                                            @endif
                                            @if($feedback->calificacion_limpieza)
                                                <div class="bg-zinc-800/50 rounded-lg p-2 text-center">
                                                    <p class="text-zinc-400 text-xs mb-1">✨ Limpieza</p>
                                                    <p class="text-white font-bold">{{ $feedback->calificacion_limpieza }}/5</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Comentario (si existe) -->
                                    @if($feedback->comentario)
                                        <div class="bg-zinc-800/30 border border-zinc-700/50 rounded-lg p-3 mb-3">
                                            <p class="text-zinc-300 text-sm line-clamp-2">
                                                "{{ $feedback->comentario }}"
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Información adicional -->
                                    <div class="flex flex-wrap items-center gap-3 text-xs">
                                        @if($feedback->recomendaria)
                                            <span class="flex items-center gap-1 text-green-400">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                                Recomendaría
                                            </span>
                                        @endif
                                        
                                        <span class="text-zinc-500">
                                            Visita: {{ ucfirst(str_replace('_', ' ', $feedback->frecuencia_visita)) }}
                                        </span>

                                        <span class="ml-auto">
                                            <a href="{{ route('feedback.show', $feedback) }}" 
                                               class="inline-flex items-center gap-1 text-purple-400 hover:text-purple-300 font-medium transition-colors">
                                                Ver detalles
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </span>
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
