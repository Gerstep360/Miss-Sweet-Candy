{{-- resources/views/alergenos/gestionar-producto.blade.php --}}
<x-layouts.app :title="__('Gestionar Alérgenos - ' . $producto->nombre)">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('productos.index') }}" class="inline-flex items-center gap-2 text-zinc-400 hover:text-zinc-300 transition-colors mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Productos
                </a>
                
                <h1 class="text-3xl font-bold text-zinc-100 flex items-center gap-3">
                    <span class="text-4xl">🏥</span>
                    Gestionar Alérgenos
                </h1>
                <p class="text-zinc-400 mt-2">Producto: <span class="font-semibold text-zinc-200">{{ $producto->nombre }}</span></p>
            </div>

            {{-- Formulario --}}
            <form action="{{ route('productos.alergenos.actualizar', $producto) }}" method="POST" 
                  class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-2xl p-8">
                @csrf

                <div class="mb-6">
                    <h2 class="text-xl font-bold text-zinc-100 mb-4">Selecciona los alérgenos presentes en este producto</h2>
                    <p class="text-zinc-400 text-sm">Marca todos los alérgenos que contenga el producto e indica el nivel de presencia</p>
                </div>

                @if($alergenos->isEmpty())
                    <div class="bg-zinc-800/50 rounded-xl p-8 text-center">
                        <p class="text-zinc-400">No hay alérgenos activos en el sistema</p>
                        <a href="{{ route('alergenos.create') }}" class="mt-4 inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Crear alérgeno
                        </a>
                    </div>
                @else
                    <div class="space-y-4" id="alergenos-container">
                        @foreach($alergenos as $alergeno)
                            @php
                                $isChecked = in_array($alergeno->id, $alergenosAsignados);
                                $nivelActual = $isChecked ? ($producto->alergenos->find($alergeno->id)->pivot->nivel_presencia ?? 'contiene') : 'contiene';
                            @endphp
                            <div class="bg-zinc-800/30 border border-zinc-700 rounded-xl p-5 hover:border-zinc-600 transition-colors">
                                <div class="flex items-start gap-4">
                                    {{-- Checkbox --}}
                                    <input 
                                        type="checkbox" 
                                        name="alergenos[]" 
                                        id="alergeno_{{ $alergeno->id }}"
                                        value="{{ $alergeno->id }}"
                                        {{ $isChecked ? 'checked' : '' }}
                                        class="w-5 h-5 mt-1 rounded border-zinc-600 text-amber-500 focus:ring-amber-500 focus:ring-offset-zinc-900"
                                        onchange="toggleNivel({{ $alergeno->id }})"
                                    />

                                    {{-- Info del alérgeno --}}
                                    <div class="flex-1">
                                        <label for="alergeno_{{ $alergeno->id }}" class="cursor-pointer">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="text-3xl">{{ $alergeno->icono ?? '⚠️' }}</span>
                                                <div>
                                                    <h3 class="text-white font-bold text-lg">{{ $alergeno->nombre }}</h3>
                                                    @if($alergeno->descripcion)
                                                        <p class="text-zinc-400 text-sm mt-1">{{ $alergeno->descripcion }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>

                                        {{-- Selector de nivel de presencia --}}
                                        <div id="nivel_{{ $alergeno->id }}" class="mt-4 {{ $isChecked ? '' : 'hidden' }}">
                                            <label class="block text-zinc-300 text-sm font-medium mb-2">Nivel de presencia:</label>
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                <label class="cursor-pointer">
                                                    <input 
                                                        type="radio" 
                                                        name="niveles_presencia[{{ $alergeno->id }}]" 
                                                        value="contiene"
                                                        {{ $nivelActual === 'contiene' ? 'checked' : '' }}
                                                        class="peer sr-only"
                                                    />
                                                    <div class="border-2 border-zinc-700 peer-checked:border-red-500 peer-checked:bg-red-500/20 rounded-lg p-3 transition-all hover:border-red-500/50">
                                                        <p class="text-white font-semibold text-sm">🚨 Contiene</p>
                                                        <p class="text-zinc-400 text-xs mt-1">Presencia directa</p>
                                                    </div>
                                                </label>

                                                <label class="cursor-pointer">
                                                    <input 
                                                        type="radio" 
                                                        name="niveles_presencia[{{ $alergeno->id }}]" 
                                                        value="puede_contener"
                                                        {{ $nivelActual === 'puede_contener' ? 'checked' : '' }}
                                                        class="peer sr-only"
                                                    />
                                                    <div class="border-2 border-zinc-700 peer-checked:border-orange-500 peer-checked:bg-orange-500/20 rounded-lg p-3 transition-all hover:border-orange-500/50">
                                                        <p class="text-white font-semibold text-sm">⚠️ Puede contener</p>
                                                        <p class="text-zinc-400 text-xs mt-1">Posible presencia</p>
                                                    </div>
                                                </label>

                                                <label class="cursor-pointer">
                                                    <input 
                                                        type="radio" 
                                                        name="niveles_presencia[{{ $alergeno->id }}]" 
                                                        value="trazas"
                                                        {{ $nivelActual === 'trazas' ? 'checked' : '' }}
                                                        class="peer sr-only"
                                                    />
                                                    <div class="border-2 border-zinc-700 peer-checked:border-yellow-500 peer-checked:bg-yellow-500/20 rounded-lg p-3 transition-all hover:border-yellow-500/50">
                                                        <p class="text-white font-semibold text-sm">⚡ Trazas</p>
                                                        <p class="text-zinc-400 text-xs mt-1">Presencia mínima</p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center gap-4 mt-8 pt-6 border-t border-zinc-800">
                        <button type="submit" class="flex-1 sm:flex-initial bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Alérgenos
                        </button>
                        <a href="{{ route('productos.index') }}" class="text-zinc-400 hover:text-zinc-300 font-medium py-2 px-4 transition-colors">
                            Cancelar
                        </a>
                    </div>
                @endif
            </form>

            {{-- Info --}}
            <div class="mt-6 bg-blue-500/10 border border-blue-500/30 rounded-xl p-6">
                <h3 class="text-blue-300 font-semibold mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Niveles de presencia
                </h3>
                <ul class="text-zinc-400 text-sm space-y-1 ml-7">
                    <li>• <strong class="text-red-400">Contiene:</strong> El alérgeno está presente como ingrediente directo</li>
                    <li>• <strong class="text-orange-400">Puede contener:</strong> Posible presencia por ingredientes variables</li>
                    <li>• <strong class="text-yellow-400">Trazas:</strong> Posible contaminación cruzada en producción</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function toggleNivel(alergenoId) {
            const checkbox = document.getElementById('alergeno_' + alergenoId);
            const nivelDiv = document.getElementById('nivel_' + alergenoId);
            
            if (checkbox.checked) {
                nivelDiv.classList.remove('hidden');
            } else {
                nivelDiv.classList.add('hidden');
            }
        }
    </script>
</x-layouts.app>
