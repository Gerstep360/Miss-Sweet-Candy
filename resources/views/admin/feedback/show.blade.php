<x-layouts.app :title="__('Detalle de Feedback')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            
            <!-- Breadcrumb -->
            <div class="mb-6 flex items-center gap-3">
                <a href="{{ route('feedback.index') }}" 
                   class="inline-flex items-center gap-2 text-zinc-400 hover:text-purple-400 transition-colors duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver a feedbacks
                </a>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-sm">Feedback #{{ $feedback->id }}</span>
            </div>

            <!-- Calificación Principal -->
            <div class="dashboard-card mb-6 text-center bg-gradient-to-br {{ 
                $feedback->calificacion_general >= 4 ? 'from-green-500/10 via-green-600/5 to-emerald-600/10 border-green-500/30' : 
                ($feedback->calificacion_general >= 3 ? 'from-yellow-500/10 via-yellow-600/5 to-amber-600/10 border-yellow-500/30' : 
                'from-red-500/10 via-red-600/5 to-rose-600/10 border-red-500/30') 
            }} shadow-lg">
                <div class="py-8">
                    <div class="mb-6">
                        <span class="text-9xl">{{ $feedback->emoji }}</span>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center justify-center gap-2 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($feedback->calificacion_general))
                                    <svg class="w-10 h-10 {{ $feedback->calificacion_general >= 4 ? 'text-green-400' : ($feedback->calificacion_general >= 3 ? 'text-yellow-400' : 'text-red-400') }} drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-10 h-10 text-zinc-700" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <p class="text-4xl font-black text-white mb-2">{{ $feedback->calificacion_general }} de 5</p>
                        <span class="px-5 py-2.5 {{ $feedback->color_badge }} border-2 rounded-xl text-base font-bold inline-block shadow-lg">
                            {{ $feedback->texto_calificacion }}
                        </span>
                    </div>
                    <p class="text-zinc-400 text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $feedback->created_at->format('d/m/Y \a \l\a\s H:i') }}
                    </p>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <span class="px-4 py-2 {{ 
                            $feedback->tipo === 'general' ? 'bg-purple-500/20 text-purple-300 border-purple-500/40' : 
                            ($feedback->tipo === 'pedido' ? 'bg-blue-500/20 text-blue-300 border-blue-500/40' : 
                            ($feedback->tipo === 'servicio' ? 'bg-green-500/20 text-green-300 border-green-500/40' : 
                            ($feedback->tipo === 'local' ? 'bg-orange-500/20 text-orange-300 border-orange-500/40' : 
                            'bg-cyan-500/20 text-cyan-300 border-cyan-500/40'))) 
                        }} border-2 rounded-lg text-sm font-bold inline-flex items-center gap-2 shadow">
                            <span class="text-lg">{{ $feedback->tipo_icono }}</span>
                            <span>{{ $feedback->tipo_texto }}</span>
                        </span>

                        <span class="px-4 py-2 {{ $feedback->estado_color }} border-2 rounded-lg text-sm font-bold shadow">
                            {{ $feedback->estado_texto }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Calificaciones Detalladas -->
            @if($feedback->calificacion_comida || $feedback->calificacion_servicio || $feedback->calificacion_ambiente || $feedback->calificacion_precio || $feedback->calificacion_limpieza || $feedback->calificacion_web)
            <div class="dashboard-card mb-6 bg-gradient-to-br from-zinc-900/80 to-zinc-800/50">
                <h2 class="text-2xl font-black text-white mb-6 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">⭐</span>
                    </div>
                    Calificaciones Detalladas
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($feedback->calificacion_comida)
                        <div class="bg-gradient-to-br from-orange-500/10 to-orange-600/5 border border-orange-500/30 rounded-xl p-5 hover:scale-105 transition-transform duration-300 shadow-lg">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-orange-200 text-sm font-bold flex items-center gap-2">
                                    <span class="text-2xl">🍽️</span>
                                    Comida
                                </span>
                                <span class="text-white text-xl font-black">{{ $feedback->calificacion_comida }}/5</span>
                            </div>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-xl {{ $i <= $feedback->calificacion_comida ? 'text-orange-400 drop-shadow-lg' : 'text-zinc-700' }}">⭐</span>
                                @endfor
                            </div>
                        </div>
                    @endif

                    @if($feedback->calificacion_servicio)
                        <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/5 border border-blue-500/30 rounded-xl p-5 hover:scale-105 transition-transform duration-300 shadow-lg">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-blue-200 text-sm font-bold flex items-center gap-2">
                                    <span class="text-2xl">👨‍💼</span>
                                    Servicio
                                </span>
                                <span class="text-white text-xl font-black">{{ $feedback->calificacion_servicio }}/5</span>
                            </div>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-xl {{ $i <= $feedback->calificacion_servicio ? 'text-blue-400 drop-shadow-lg' : 'text-zinc-700' }}">⭐</span>
                                @endfor
                            </div>
                        </div>
                    @endif

                    @if($feedback->calificacion_ambiente)
                        <div class="bg-gradient-to-br from-green-500/10 to-green-600/5 border border-green-500/30 rounded-xl p-5 hover:scale-105 transition-transform duration-300 shadow-lg">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-green-200 text-sm font-bold flex items-center gap-2">
                                    <span class="text-2xl">🏠</span>
                                    Ambiente
                                </span>
                                <span class="text-white text-xl font-black">{{ $feedback->calificacion_ambiente }}/5</span>
                            </div>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-xl {{ $i <= $feedback->calificacion_ambiente ? 'text-green-400 drop-shadow-lg' : 'text-zinc-700' }}">⭐</span>
                                @endfor
                            </div>
                        </div>
                    @endif

                    @if($feedback->calificacion_precio)
                        <div class="bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border border-emerald-500/30 rounded-xl p-5 hover:scale-105 transition-transform duration-300 shadow-lg">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-emerald-200 text-sm font-bold flex items-center gap-2">
                                    <span class="text-2xl">💰</span>
                                    Precio
                                </span>
                                <span class="text-white text-xl font-black">{{ $feedback->calificacion_precio }}/5</span>
                            </div>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-xl {{ $i <= $feedback->calificacion_precio ? 'text-emerald-400 drop-shadow-lg' : 'text-zinc-700' }}">⭐</span>
                                @endfor
                            </div>
                        </div>
                    @endif

                    @if($feedback->calificacion_limpieza)
                        <div class="bg-gradient-to-br from-purple-500/10 to-purple-600/5 border border-purple-500/30 rounded-xl p-5 hover:scale-105 transition-transform duration-300 shadow-lg">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-purple-200 text-sm font-bold flex items-center gap-2">
                                    <span class="text-2xl">✨</span>
                                    Limpieza
                                </span>
                                <span class="text-white text-xl font-black">{{ $feedback->calificacion_limpieza }}/5</span>
                            </div>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-xl {{ $i <= $feedback->calificacion_limpieza ? 'text-purple-400 drop-shadow-lg' : 'text-zinc-700' }}">⭐</span>
                                @endfor
                            </div>
                        </div>
                    @endif

                    @if($feedback->calificacion_web)
                        <div class="bg-gradient-to-br from-cyan-500/10 to-cyan-600/5 border border-cyan-500/30 rounded-xl p-5 hover:scale-105 transition-transform duration-300 shadow-lg">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-cyan-200 text-sm font-bold flex items-center gap-2">
                                    <span class="text-2xl">🌐</span>
                                    Sitio Web
                                </span>
                                <span class="text-white text-xl font-black">{{ $feedback->calificacion_web }}/5</span>
                            </div>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-xl {{ $i <= $feedback->calificacion_web ? 'text-cyan-400 drop-shadow-lg' : 'text-zinc-700' }}">⭐</span>
                                @endfor
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Comentarios del Cliente -->
            @if($feedback->elogios || $feedback->sugerencias || $feedback->quejas || $feedback->comentario)
                <div class="dashboard-card mb-6 bg-gradient-to-br from-zinc-900/80 to-zinc-800/50">
                    <h2 class="text-2xl font-black text-white mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl">💭</span>
                        </div>
                        Comentarios del Cliente
                    </h2>
                    <div class="space-y-4">
                        @if($feedback->elogios)
                            <div class="bg-gradient-to-br from-green-500/10 to-emerald-600/5 border-2 border-green-500/40 rounded-xl p-5 shadow-lg hover:shadow-green-500/20 transition-all duration-300">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">😊</span>
                                    </div>
                                    <h3 class="text-green-300 font-black text-lg">Lo que más le gustó</h3>
                                </div>
                                <p class="text-zinc-200 leading-relaxed text-base pl-13">{{ $feedback->elogios }}</p>
                            </div>
                        @endif

                        @if($feedback->sugerencias)
                            <div class="bg-gradient-to-br from-blue-500/10 to-cyan-600/5 border-2 border-blue-500/40 rounded-xl p-5 shadow-lg hover:shadow-blue-500/20 transition-all duration-300">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">💡</span>
                                    </div>
                                    <h3 class="text-blue-300 font-black text-lg">Sugerencias de mejora</h3>
                                </div>
                                <p class="text-zinc-200 leading-relaxed text-base pl-13">{{ $feedback->sugerencias }}</p>
                            </div>
                        @endif

                        @if($feedback->quejas)
                            <div class="bg-gradient-to-br from-red-500/10 to-rose-600/5 border-2 border-red-500/40 rounded-xl p-5 shadow-lg hover:shadow-red-500/20 transition-all duration-300">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">😔</span>
                                    </div>
                                    <h3 class="text-red-300 font-black text-lg">Problemas reportados</h3>
                                </div>
                                <p class="text-zinc-200 leading-relaxed text-base pl-13">{{ $feedback->quejas }}</p>
                            </div>
                        @endif

                        @if($feedback->comentario)
                            <div class="bg-gradient-to-br from-purple-500/10 to-violet-600/5 border-2 border-purple-500/40 rounded-xl p-5 shadow-lg hover:shadow-purple-500/20 transition-all duration-300">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📝</span>
                                    </div>
                                    <h3 class="text-purple-300 font-black text-lg">Comentario adicional</h3>
                                </div>
                                <p class="text-zinc-200 leading-relaxed text-base italic pl-13">"{{ $feedback->comentario }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Información Adicional -->
            <div class="dashboard-card mb-6 bg-gradient-to-br from-zinc-900/80 to-zinc-800/50">
                <h2 class="text-2xl font-black text-white mb-6 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">📊</span>
                    </div>
                    Información Adicional
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br {{ $feedback->recomendaria ? 'from-green-500/10 to-emerald-600/5 border-green-500/30' : 'from-red-500/10 to-rose-600/5 border-red-500/30' }} border-2 rounded-xl p-5 shadow-lg">
                        <p class="text-zinc-400 text-xs font-bold uppercase tracking-wider mb-3">¿Recomendaría?</p>
                        <div class="flex items-center gap-3">
                            @if($feedback->recomendaria)
                                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                                    <span class="text-3xl">✅</span>
                                </div>
                                <span class="text-green-300 font-black text-xl">Sí</span>
                            @else
                                <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                                    <span class="text-3xl">❌</span>
                                </div>
                                <span class="text-red-300 font-black text-xl">No</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-500/10 to-cyan-600/5 border-2 border-blue-500/30 rounded-xl p-5 shadow-lg">
                        <p class="text-zinc-400 text-xs font-bold uppercase tracking-wider mb-3">Frecuencia de visita</p>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                                <span class="text-2xl">📅</span>
                            </div>
                            <span class="text-white font-bold text-lg capitalize">
                                {{ str_replace('_', ' ', $feedback->frecuencia_visita) }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500/10 to-violet-600/5 border-2 border-purple-500/30 rounded-xl p-5 shadow-lg">
                        <p class="text-zinc-400 text-xs font-bold uppercase tracking-wider mb-3">Estado</p>
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-2.5 {{ $feedback->estado_color }} border-2 rounded-xl text-sm font-black inline-flex items-center gap-2 shadow">
                                {{ $feedback->estado_texto }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="dashboard-card mb-6 bg-gradient-to-br from-zinc-900/80 to-zinc-800/50">
                <div class="flex items-start gap-4 mb-5">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white mb-1">Información del cliente</h3>
                        <p class="text-zinc-400 text-sm">Datos del cliente que realizó el feedback</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-lg p-4">
                        <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-2">Nombre</p>
                        <p class="text-white font-bold text-lg">{{ $feedback->cliente->name ?? 'Cliente no registrado' }}</p>
                    </div>
                    @if($feedback->cliente)
                        <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-lg p-4">
                            <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-2">Email</p>
                            <p class="text-white font-semibold">{{ $feedback->cliente->email }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detalles del Pedido (solo si existe) -->
            @if($feedback->pedido_id && $feedback->pedido)
            <div class="dashboard-card">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-lg font-semibold text-white">Detalles del pedido</h3>
                            <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded text-sm font-semibold">
                                #{{ $feedback->pedido_id }}
                            </span>
                        </div>
                        <p class="text-zinc-500 text-sm">
                            Realizado el {{ $feedback->pedido->created_at->format('d/m/Y \a \l\a\s H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Items del Pedido -->
                <div class="space-y-3">
                    <div class="border-t border-zinc-800 pt-4">
                        <p class="text-zinc-400 text-sm font-medium mb-3">Productos ordenados:</p>
                        <div class="space-y-2">
                            @foreach($feedback->pedido->items as $item)
                                <div class="flex items-center justify-between bg-zinc-800/30 rounded-lg p-3">
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="w-10 h-10 bg-zinc-700 rounded-lg flex items-center justify-center">
                                            <span class="text-white font-bold">{{ $item->cantidad }}</span>
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ $item->producto->nombre }}</p>
                                            <p class="text-zinc-500 text-xs">{{ $item->producto->categoria->nombre ?? 'Sin categoría' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-white font-semibold">${{ number_format($item->subtotal, 2) }}</p>
                                        <p class="text-zinc-500 text-xs">${{ number_format($item->precio_unitario, 2) }} c/u</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-t border-zinc-800 pt-4">
                        <div class="flex items-center justify-between bg-gradient-to-r from-purple-500/10 to-purple-600/10 rounded-lg p-4 border border-purple-500/30">
                            <p class="text-zinc-300 font-semibold">Total del pedido</p>
                            <p class="text-2xl font-bold text-white">${{ number_format($feedback->pedido->total, 2) }}</p>
                        </div>
                    </div>

                    <!-- Método de Pago y Estado -->
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div class="bg-zinc-800/30 rounded-lg p-3">
                            <p class="text-zinc-500 text-xs mb-1">Método de pago</p>
                            <p class="text-white font-medium capitalize">{{ $feedback->pedido->metodo_pago }}</p>
                        </div>
                        <div class="bg-zinc-800/30 rounded-lg p-3">
                            <p class="text-zinc-500 text-xs mb-1">Estado</p>
                            <span class="inline-flex px-2 py-1 rounded text-xs font-semibold
                                {{ $feedback->pedido->estado == 'completado' ? 'bg-green-500/20 text-green-300' : 'bg-zinc-700 text-zinc-300' }}">
                                {{ ucfirst($feedback->pedido->estado) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Botón para Administrador -->
            @can('ver-estadisticas-feedback')
                <div class="mt-6 flex gap-3">
                    <a href="{{ route('feedback.index') }}" 
                       class="flex-1 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-white py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        Ver todos los feedbacks
                    </a>
                    <a href="{{ route('feedback.estadisticas') }}" 
                       class="flex-1 bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-purple-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Ver estadísticas
                    </a>
                </div>
            @else
                <!-- Mensaje para Cliente -->
                <div class="mt-6 dashboard-card bg-gradient-to-br from-green-500/10 to-emerald-600/10 border-green-500/30 text-center">
                    <div class="py-6">
                        <div class="w-16 h-16 mx-auto mb-4 bg-green-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">¡Gracias por tu feedback!</h3>
                        <p class="text-zinc-300 mb-4">Tu opinión nos ayuda a mejorar nuestro servicio</p>
                        <a href="{{ route('pedidos.index') }}" 
                           class="inline-flex items-center gap-2 bg-green-500/20 text-green-300 hover:bg-green-500/30 px-6 py-2 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Ir a mis pedidos
                        </a>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-layouts.app>
