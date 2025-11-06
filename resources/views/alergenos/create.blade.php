{{-- resources/views/alergenos/create.blade.php --}}
<x-layouts.app :title="__('Crear Alérgeno - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('alergenos.index') }}" class="inline-flex items-center gap-2 text-zinc-400 hover:text-zinc-300 transition-colors mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Alérgenos
            </a>
            
            <h1 class="text-3xl font-bold text-zinc-100 flex items-center gap-3">
                <span class="text-4xl">🏥</span>
                Crear Nuevo Alérgeno
            </h1>
            <p class="text-zinc-400 mt-2">Registra un nuevo alérgeno en el sistema</p>
        </div>

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-500/20 border border-red-500/50 text-red-300 px-6 py-4 rounded-xl backdrop-blur-sm">
                <h3 class="font-semibold mb-2">❌ Hay errores en el formulario:</h3>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario --}}
        <form action="{{ route('alergenos.store') }}" method="POST" class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-2xl p-8">
            @csrf

            <div class="space-y-6">
                {{-- Nombre --}}
                <div>
                    <label for="nombre" class="block text-zinc-300 font-medium mb-2 flex items-center gap-2">
                        Nombre del Alérgeno
                        <span class="text-red-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nombre" 
                        id="nombre" 
                        value="{{ old('nombre') }}"
                        placeholder="Ej: Gluten, Lácteos, Nueces..."
                        required
                        class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                    />
                    @error('nombre')
                        <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icono --}}
                <div>
                    <label for="icono" class="block text-zinc-300 font-medium mb-2">
                        Icono (Emoji)
                    </label>
                    <input 
                        type="text" 
                        name="icono" 
                        id="icono" 
                        value="{{ old('icono') }}"
                        placeholder="🌾 🥛 🥜 🐟 🦐 🥚..."
                        maxlength="10"
                        class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                    />
                    <p class="mt-2 text-zinc-500 text-sm">Usa un emoji representativo del alérgeno</p>
                    @error('icono')
                        <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Color de alerta --}}
                <div>
                    <label class="block text-zinc-300 font-medium mb-3 flex items-center gap-2">
                        Nivel de Alerta
                        <span class="text-red-400">*</span>
                    </label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Crítico (Rojo) --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="color" value="red" 
                                   {{ old('color') === 'red' ? 'checked' : '' }}
                                   class="peer sr-only" required>
                            <div class="border-2 border-zinc-700 peer-checked:border-red-500 peer-checked:bg-red-500/20 
                                        rounded-xl p-6 transition-all hover:border-red-500/50 group-hover:bg-red-500/10">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="text-4xl">🚨</span>
                                    <span class="text-red-300 font-semibold">Crítico</span>
                                    <span class="text-zinc-400 text-sm text-center">Reacciones graves</span>
                                </div>
                            </div>
                        </label>

                        {{-- Moderado (Naranja) --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="color" value="orange" 
                                   {{ old('color') === 'orange' ? 'checked' : '' }}
                                   class="peer sr-only">
                            <div class="border-2 border-zinc-700 peer-checked:border-orange-500 peer-checked:bg-orange-500/20 
                                        rounded-xl p-6 transition-all hover:border-orange-500/50 group-hover:bg-orange-500/10">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="text-4xl">⚠️</span>
                                    <span class="text-orange-300 font-semibold">Moderado</span>
                                    <span class="text-zinc-400 text-sm text-center">Requiere precaución</span>
                                </div>
                            </div>
                        </label>

                        {{-- Leve (Amarillo) --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="color" value="yellow" 
                                   {{ old('color') === 'yellow' ? 'checked' : '' }}
                                   class="peer sr-only">
                            <div class="border-2 border-zinc-700 peer-checked:border-yellow-500 peer-checked:bg-yellow-500/20 
                                        rounded-xl p-6 transition-all hover:border-yellow-500/50 group-hover:bg-yellow-500/10">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="text-4xl">⚡</span>
                                    <span class="text-yellow-300 font-semibold">Leve</span>
                                    <span class="text-zinc-400 text-sm text-center">Intolerancia menor</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    
                    @error('color')
                        <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div>
                    <label for="descripcion" class="block text-zinc-300 font-medium mb-2">
                        Descripción
                    </label>
                    <textarea 
                        name="descripcion" 
                        id="descripcion" 
                        rows="3"
                        placeholder="Describe el alérgeno y dónde suele estar presente..."
                        class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-zinc-100 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors resize-none"
                    >{{ old('descripcion') }}</textarea>
                    <p class="mt-2 text-zinc-500 text-sm">Información adicional sobre el alérgeno</p>
                    @error('descripcion')
                        <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estado --}}
                <div class="flex items-center gap-3 p-4 bg-zinc-800/50 rounded-xl border border-zinc-700">
                    <input 
                        type="checkbox" 
                        name="activo" 
                        id="activo" 
                        value="1"
                        checked
                        class="w-5 h-5 rounded border-zinc-600 text-amber-500 focus:ring-amber-500 focus:ring-offset-zinc-900"
                    />
                    <div class="flex-1">
                        <label for="activo" class="text-zinc-300 font-medium cursor-pointer">
                            Alérgeno Activo
                        </label>
                        <p class="text-zinc-500 text-sm mt-1">El alérgeno estará disponible para asignar a productos</p>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-zinc-800">
                <button type="submit" class="flex-1 sm:flex-initial bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Crear Alérgeno
                </button>
                <a href="{{ route('alergenos.index') }}" class="text-zinc-400 hover:text-zinc-300 font-medium py-2 px-4 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>

        {{-- Info de seguridad --}}
        <div class="mt-6 bg-blue-500/10 border border-blue-500/30 rounded-xl p-6 backdrop-blur-sm">
            <h3 class="text-blue-300 font-semibold mb-2 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Importante
            </h3>
            <ul class="text-zinc-400 text-sm space-y-1 ml-7">
                <li>• El alérgeno se mostrará en los perfiles de clientes</li>
                <li>• Los cajeros verán alertas cuando un cliente tenga alergias registradas</li>
                <li>• Podrás asignar este alérgeno a productos del menú</li>
            </ul>
        </div>
    </div>
</x-layouts.app>
 