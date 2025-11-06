{{-- resources/views/alergenos/edit.blade.php --}}
<x-layouts.app :title="__('Editar Alérgeno - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <flux:button icon="arrow-left" href="{{ route('alergenos.index') }}" variant="ghost" class="mb-4">
                Volver a Alérgenos
            </flux:button>
            
            <h1 class="text-3xl font-bold text-zinc-100 flex items-center gap-3">
                <span class="text-4xl">{{ $alergeno->icono ?? '🏥' }}</span>
                Editar: {{ $alergeno->nombre }}
            </h1>
            <p class="text-zinc-400 mt-2">Modifica la información del alérgeno</p>
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
        <form action="{{ route('alergenos.update', $alergeno) }}" method="POST" class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-2xl p-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Nombre --}}
                <div>
                    <flux:label for="nombre" class="text-zinc-300 font-medium mb-2 flex items-center gap-2">
                        Nombre del Alérgeno
                        <span class="text-red-400">*</span>
                    </flux:label>
                    <flux:input 
                        type="text" 
                        name="nombre" 
                        id="nombre" 
                        value="{{ old('nombre', $alergeno->nombre) }}"
                        placeholder="Ej: Gluten, Lácteos, Nueces..."
                        required
                        class="w-full"
                    />
                    @error('nombre')
                        <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icono --}}
                <div>
                    <flux:label for="icono" class="text-zinc-300 font-medium mb-2">
                        Icono (Emoji)
                    </flux:label>
                    <flux:input 
                        type="text" 
                        name="icono" 
                        id="icono" 
                        value="{{ old('icono', $alergeno->icono) }}"
                        placeholder="🌾 🥛 🥜 🐟 🦐 🥚..."
                        maxlength="10"
                        class="w-full"
                    />
                    <p class="mt-2 text-zinc-500 text-sm">Usa un emoji representativo del alérgeno</p>
                    @error('icono')
                        <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Color de alerta --}}
                <div>
                    <flux:label class="text-zinc-300 font-medium mb-3 flex items-center gap-2">
                        Nivel de Alerta
                        <span class="text-red-400">*</span>
                    </flux:label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Crítico (Rojo) --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="color" value="red" 
                                   {{ old('color', $alergeno->color) === 'red' ? 'checked' : '' }}
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
                                   {{ old('color', $alergeno->color) === 'orange' ? 'checked' : '' }}
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
                                   {{ old('color', $alergeno->color) === 'yellow' ? 'checked' : '' }}
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
                    <flux:label for="descripcion" class="text-zinc-300 font-medium mb-2">
                        Descripción
                    </flux:label>
                    <flux:textarea 
                        name="descripcion" 
                        id="descripcion" 
                        rows="3"
                        placeholder="Describe el alérgeno y dónde suele estar presente..."
                        class="w-full"
                    >{{ old('descripcion', $alergeno->descripcion) }}</flux:textarea>
                    <p class="mt-2 text-zinc-500 text-sm">Información adicional sobre el alérgeno</p>
                    @error('descripcion')
                        <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estado --}}
                <div class="flex items-center gap-3 p-4 bg-zinc-800/50 rounded-xl border border-zinc-700">
                    <flux:checkbox 
                        name="activo" 
                        id="activo" 
                        value="1"
                        {{ old('activo', $alergeno->activo) ? 'checked' : '' }}
                    />
                    <div class="flex-1">
                        <flux:label for="activo" class="text-zinc-300 font-medium">
                            Alérgeno Activo
                        </flux:label>
                        <p class="text-zinc-500 text-sm mt-1">El alérgeno estará disponible para asignar a productos</p>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-zinc-800">
                <flux:button type="submit" variant="primary" icon="check" class="flex-1 sm:flex-initial">
                    Actualizar Alérgeno
                </flux:button>
                <flux:button type="button" variant="ghost" href="{{ route('alergenos.index') }}">
                    Cancelar
                </flux:button>
            </div>
        </form>

        {{-- Info de productos asociados --}}
        @if ($alergeno->productos()->count() > 0)
            <div class="mt-6 bg-amber-500/10 border border-amber-500/30 rounded-xl p-6 backdrop-blur-sm">
                <h3 class="text-amber-300 font-semibold mb-2 flex items-center gap-2">
                    <flux:icon.cube class="w-5 h-5" />
                    Productos Asociados
                </h3>
                <p class="text-zinc-400 text-sm">
                    Este alérgeno está asignado a <span class="font-semibold text-amber-300">{{ $alergeno->productos()->count() }}</span> producto(s).
                    <a href="{{ route('alergenos.productos', $alergeno) }}" class="text-amber-400 hover:text-amber-300 underline ml-1">
                        Ver productos →
                    </a>
                </p>
            </div>
        @endif
    </div>
</x-layouts.app>
