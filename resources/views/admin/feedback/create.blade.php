<x-layouts.app :title="__('Enviar Feedback')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800" x-data="feedbackForm()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">¡Tu opinión nos importa!</h1>
                    <p class="text-zinc-400 max-w-2xl mx-auto">
                        Ayúdanos a mejorar tu experiencia compartiendo tus comentarios sobre nuestros servicios, instalaciones y atención
                    </p>
                </div>
            </div>

            <form action="{{ route('feedback.store') }}" method="POST" class="space-y-6" @submit="validarFormulario($event)">
                @csrf

                <!-- Tipo de Feedback -->
                <div class="dashboard-card">
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                        <span class="text-2xl">📋</span>
                        ¿Sobre qué te gustaría opinar?
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="tipo" value="general" x-model="tipo" class="peer hidden" required>
                            <div class="bg-zinc-800/50 border-2 border-zinc-700 rounded-xl p-4 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-500/10 hover:border-zinc-600">
                                <span class="text-3xl block mb-2">💬</span>
                                <span class="text-sm font-medium text-zinc-300 peer-checked:text-purple-300">General</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="tipo" value="pedido" x-model="tipo" class="peer hidden">
                            <div class="bg-zinc-800/50 border-2 border-zinc-700 rounded-xl p-4 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-500/10 hover:border-zinc-600">
                                <span class="text-3xl block mb-2">🛒</span>
                                <span class="text-sm font-medium text-zinc-300 peer-checked:text-purple-300">Mi Pedido</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="tipo" value="servicio" x-model="tipo" class="peer hidden">
                            <div class="bg-zinc-800/50 border-2 border-zinc-700 rounded-xl p-4 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-500/10 hover:border-zinc-600">
                                <span class="text-3xl block mb-2">👨‍🍳</span>
                                <span class="text-sm font-medium text-zinc-300 peer-checked:text-purple-300">Servicio</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="tipo" value="local" x-model="tipo" class="peer hidden">
                            <div class="bg-zinc-800/50 border-2 border-zinc-700 rounded-xl p-4 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-500/10 hover:border-zinc-600">
                                <span class="text-3xl block mb-2">🏪</span>
                                <span class="text-sm font-medium text-zinc-300 peer-checked:text-purple-300">Local</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="tipo" value="web" x-model="tipo" class="peer hidden">
                            <div class="bg-zinc-800/50 border-2 border-zinc-700 rounded-xl p-4 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-500/10 hover:border-zinc-600">
                                <span class="text-3xl block mb-2">🌐</span>
                                <span class="text-sm font-medium text-zinc-300 peer-checked:text-purple-300">Página Web</span>
                            </div>
                        </label>
                    </div>
                    @error('tipo')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Selector de Pedido (solo si tipo=pedido) -->
                <div x-show="tipo === 'pedido'" x-transition class="dashboard-card">
                    <h3 class="text-lg font-semibold text-white mb-3">Selecciona el pedido</h3>
                    @if($pedidosSinFeedback->isEmpty())
                        <p class="text-zinc-400 text-sm">No tienes pedidos completados sin feedback</p>
                    @else
                        <select name="pedido_id" 
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500">
                            <option value="">Seleccionar pedido...</option>
                            @foreach($pedidosSinFeedback as $p)
                                <option value="{{ $p->id }}">
                                    Pedido #{{ $p->id }} - {{ $p->created_at->format('d/m/Y') }} - ${{ number_format($p->total, 2) }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Calificaciones por Categoría -->
                <div class="dashboard-card" id="calificaciones-section">
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                        <span class="text-2xl">⭐</span>
                        Califica tu experiencia
                    </h2>
                    <p class="text-zinc-400 text-sm mb-2">Selecciona las estrellas para cada aspecto (1 = Malo, 5 = Excelente)</p>
                    <p class="text-purple-400 text-xs font-medium mb-6">* Debes calificar al menos un aspecto</p>
                    
                    @error('calificacion')
                        <div class="mb-4 bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-red-400 text-sm font-medium">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror
                    
                    <div class="space-y-6">
                        <!-- Comida -->
                        <div>
                            <label class="block text-zinc-300 font-medium mb-3 flex items-center gap-2">
                                <span class="text-2xl">🍽️</span>
                                <span>Calidad de la Comida</span>
                            </label>
                            <div class="flex gap-1">
                                <template x-for="star in 5" :key="star">
                                    <button type="button" 
                                            @click="calificacion_comida = star"
                                            @mouseenter="hover_comida = star"
                                            @mouseleave="hover_comida = 0"
                                            class="transition-all duration-200 ease-in-out focus:outline-none">
                                        <svg class="w-10 h-10 transition-all duration-200" 
                                             :class="star <= (hover_comida || calificacion_comida) ? 'text-yellow-400 scale-110' : 'text-zinc-700 scale-100'"
                                             fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p class="text-zinc-500 text-xs mt-2" x-show="calificacion_comida > 0" x-transition>
                                <span x-text="calificacion_comida"></span> de 5 estrellas seleccionadas
                            </p>
                            <input type="hidden" name="calificacion_comida" :value="calificacion_comida">
                        </div>

                        <!-- Servicio -->
                        <div>
                            <label class="block text-zinc-300 font-medium mb-3 flex items-center gap-2">
                                <span class="text-2xl">👨‍💼</span>
                                <span>Atención y Servicio</span>
                            </label>
                            <div class="flex gap-1">
                                <template x-for="star in 5" :key="star">
                                    <button type="button" 
                                            @click="calificacion_servicio = star"
                                            @mouseenter="hover_servicio = star"
                                            @mouseleave="hover_servicio = 0"
                                            class="transition-all duration-200 ease-in-out focus:outline-none">
                                        <svg class="w-10 h-10 transition-all duration-200" 
                                             :class="star <= (hover_servicio || calificacion_servicio) ? 'text-blue-400 scale-110' : 'text-zinc-700 scale-100'"
                                             fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p class="text-zinc-500 text-xs mt-2" x-show="calificacion_servicio > 0" x-transition>
                                <span x-text="calificacion_servicio"></span> de 5 estrellas seleccionadas
                            </p>
                            <input type="hidden" name="calificacion_servicio" :value="calificacion_servicio">
                        </div>

                        <!-- Ambiente -->
                        <div>
                            <label class="block text-zinc-300 font-medium mb-3 flex items-center gap-2">
                                <span class="text-2xl">🏠</span>
                                <span>Ambiente y Comodidad</span>
                            </label>
                            <div class="flex gap-1">
                                <template x-for="star in 5" :key="star">
                                    <button type="button" 
                                            @click="calificacion_ambiente = star"
                                            @mouseenter="hover_ambiente = star"
                                            @mouseleave="hover_ambiente = 0"
                                            class="transition-all duration-200 ease-in-out focus:outline-none">
                                        <svg class="w-10 h-10 transition-all duration-200" 
                                             :class="star <= (hover_ambiente || calificacion_ambiente) ? 'text-green-400 scale-110' : 'text-zinc-700 scale-100'"
                                             fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p class="text-zinc-500 text-xs mt-2" x-show="calificacion_ambiente > 0" x-transition>
                                <span x-text="calificacion_ambiente"></span> de 5 estrellas seleccionadas
                            </p>
                            <input type="hidden" name="calificacion_ambiente" :value="calificacion_ambiente">
                        </div>

                        <!-- Precio -->
                        <div>
                            <label class="block text-zinc-300 font-medium mb-3 flex items-center gap-2">
                                <span class="text-2xl">💰</span>
                                <span>Relación Calidad-Precio</span>
                            </label>
                            <div class="flex gap-1">
                                <template x-for="star in 5" :key="star">
                                    <button type="button" 
                                            @click="calificacion_precio = star"
                                            @mouseenter="hover_precio = star"
                                            @mouseleave="hover_precio = 0"
                                            class="transition-all duration-200 ease-in-out focus:outline-none">
                                        <svg class="w-10 h-10 transition-all duration-200" 
                                             :class="star <= (hover_precio || calificacion_precio) ? 'text-emerald-400 scale-110' : 'text-zinc-700 scale-100'"
                                             fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p class="text-zinc-500 text-xs mt-2" x-show="calificacion_precio > 0" x-transition>
                                <span x-text="calificacion_precio"></span> de 5 estrellas seleccionadas
                            </p>
                            <input type="hidden" name="calificacion_precio" :value="calificacion_precio">
                        </div>

                        <!-- Limpieza -->
                        <div>
                            <label class="block text-zinc-300 font-medium mb-3 flex items-center gap-2">
                                <span class="text-2xl">✨</span>
                                <span>Limpieza e Higiene</span>
                            </label>
                            <div class="flex gap-1">
                                <template x-for="star in 5" :key="star">
                                    <button type="button" 
                                            @click="calificacion_limpieza = star"
                                            @mouseenter="hover_limpieza = star"
                                            @mouseleave="hover_limpieza = 0"
                                            class="transition-all duration-200 ease-in-out focus:outline-none">
                                        <svg class="w-10 h-10 transition-all duration-200" 
                                             :class="star <= (hover_limpieza || calificacion_limpieza) ? 'text-purple-400 scale-110' : 'text-zinc-700 scale-100'"
                                             fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p class="text-zinc-500 text-xs mt-2" x-show="calificacion_limpieza > 0" x-transition>
                                <span x-text="calificacion_limpieza"></span> de 5 estrellas seleccionadas
                            </p>
                            <input type="hidden" name="calificacion_limpieza" :value="calificacion_limpieza">
                        </div>

                        <!-- Web (solo si tipo=web) -->
                        <div x-show="tipo === 'web'" x-transition>
                            <label class="block text-zinc-300 font-medium mb-3 flex items-center gap-2">
                                <span class="text-2xl">🌐</span>
                                <span>Experiencia en el Sitio Web</span>
                            </label>
                            <div class="flex gap-1">
                                <template x-for="star in 5" :key="star">
                                    <button type="button" 
                                            @click="calificacion_web = star"
                                            @mouseenter="hover_web = star"
                                            @mouseleave="hover_web = 0"
                                            class="transition-all duration-200 ease-in-out focus:outline-none">
                                        <svg class="w-10 h-10 transition-all duration-200" 
                                             :class="star <= (hover_web || calificacion_web) ? 'text-cyan-400 scale-110' : 'text-zinc-700 scale-100'"
                                             fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p class="text-zinc-500 text-xs mt-2" x-show="calificacion_web > 0" x-transition>
                                <span x-text="calificacion_web"></span> de 5 estrellas seleccionadas
                            </p>
                            <input type="hidden" name="calificacion_web" :value="calificacion_web">
                        </div>
                    </div>
                </div>

                <!-- Comentarios Detallados -->
                <div class="dashboard-card">
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                        <span class="text-2xl">💭</span>
                        Cuéntanos más (opcional)
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- Elogios -->
                        <div>
                            <label class="block text-green-400 font-medium mb-2">😊 ¿Qué te gustó más?</label>
                            <textarea name="elogios" 
                                      rows="3" 
                                      class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500"
                                      placeholder="Escribe aquí lo que más te gustó de tu experiencia..."></textarea>
                        </div>

                        <!-- Sugerencias -->
                        <div>
                            <label class="block text-blue-400 font-medium mb-2">💡 ¿Qué podemos mejorar?</label>
                            <textarea name="sugerencias" 
                                      rows="3" 
                                      class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                      placeholder="Tus sugerencias nos ayudan a mejorar..."></textarea>
                        </div>

                        <!-- Quejas -->
                        <div>
                            <label class="block text-red-400 font-medium mb-2">😔 ¿Tuviste algún problema?</label>
                            <textarea name="quejas" 
                                      rows="3" 
                                      class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500"
                                      placeholder="Si algo no estuvo bien, háznoslo saber..."></textarea>
                        </div>

                        <!-- Comentario General -->
                        <div>
                            <label class="block text-zinc-300 font-medium mb-2">📝 Comentario general</label>
                            <textarea name="comentario" 
                                      rows="2" 
                                      maxlength="255"
                                      class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500"
                                      placeholder="Cualquier otro comentario..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="dashboard-card">
                    <h2 class="text-xl font-bold text-white mb-4">📊 Información adicional</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Recomendarías -->
                        <div>
                            <label class="block text-zinc-300 font-medium mb-3">¿Recomendarías Miss Sweet Candy? <span class="text-red-400">*</span></label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="recomendaria" value="1" checked class="w-4 h-4 text-purple-500" required>
                                    <span class="text-green-400">✅ Sí</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="recomendaria" value="0" class="w-4 h-4 text-purple-500">
                                    <span class="text-red-400">❌ No</span>
                                </label>
                            </div>
                            @error('recomendaria')
                                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Frecuencia de Visita -->
                        <div>
                            <label class="block text-zinc-300 font-medium mb-3">¿Con qué frecuencia nos visitas? <span class="text-red-400">*</span></label>
                            <select name="frecuencia_visita" 
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500"
                                    required>
                                <option value="primera_vez">Es mi primera vez</option>
                                <option value="ocasional">De vez en cuando</option>
                                <option value="frecuente">Con frecuencia</option>
                                <option value="regular">Soy cliente regular</option>
                            </select>
                            @error('frecuencia_visita')
                                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-4">
                    <a href="{{ route('dashboard') }}" 
                       class="flex-1 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-white py-3 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white py-3 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 font-medium shadow-lg shadow-purple-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enviar Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function feedbackForm() {
            return {
                tipo: 'general',
                calificacion_comida: 0,
                calificacion_servicio: 0,
                calificacion_ambiente: 0,
                calificacion_precio: 0,
                calificacion_limpieza: 0,
                calificacion_web: 0,
                hover_comida: 0,
                hover_servicio: 0,
                hover_ambiente: 0,
                hover_precio: 0,
                hover_limpieza: 0,
                hover_web: 0,
                
                validarFormulario(event) {
                    // Verificar que al menos una calificación esté seleccionada
                    const tieneCalificacion = 
                        this.calificacion_comida > 0 ||
                        this.calificacion_servicio > 0 ||
                        this.calificacion_ambiente > 0 ||
                        this.calificacion_precio > 0 ||
                        this.calificacion_limpieza > 0 ||
                        this.calificacion_web > 0;
                    
                    if (!tieneCalificacion) {
                        event.preventDefault();
                        alert('⭐ Por favor, califica al menos un aspecto seleccionando las estrellas.');
                        // Hacer scroll al área de calificaciones
                        document.querySelector('#calificaciones-section').scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                        return false;
                    }
                    
                    return true;
                }
            }
        }
    </script>
</x-layouts.app>
