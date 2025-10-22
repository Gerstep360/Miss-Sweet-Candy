{{-- resources/views/admin/promociones/edit.blade.php --}}
<x-layouts.app :title="__('Editar Promoción - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-8">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-2xl font-bold text-white mb-6">Editar Promoción</h1>
            
            <!-- Vista previa de la promoción -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-amber-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-white">{{ $promocion->nombre }}</h2>
                        <div class="flex gap-2 mt-1">
                            <span class="bg-amber-500/20 text-amber-400 text-xs px-2 py-1 rounded capitalize">{{ $promocion->tipo }}</span>
                            <span class="bg-blue-500/20 text-blue-400 text-xs px-2 py-1 rounded">{{ $promocion->aplica_sobre }}</span>
                            <span class="bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded font-bold">
                                @if($promocion->tipo == 'porcentaje')
                                    {{ $promocion->valor }}%
                                @elseif($promocion->tipo == 'monto_fijo')
                                    ${{ number_format($promocion->valor, 2) }}
                                @else
                                    {{ ucfirst($promocion->tipo) }}
                                @endif
                            </span>
                            @if($promocion->esta_vigente)
                                <span class="bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded">Vigente</span>
                            @else
                                <span class="bg-red-500/20 text-red-400 text-xs px-2 py-1 rounded">No Vigente</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('promociones.update', $promocion) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Información Básica -->
                <div class="dashboard-card">
                    <h2 class="text-xl font-semibold text-white mb-4">Información de la Promoción</h2>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-zinc-300 font-medium mb-2">Nombre *</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $promocion->nombre) }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" 
                                   required maxlength="120" placeholder="Ej: 2x1 Tazas de Café Latte">
                            @error('nombre')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="tipo" class="block text-zinc-300 font-medium mb-2">Tipo *</label>
                            <select name="tipo" id="tipo" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required>
                                <option value="">Selecciona un tipo</option>
                                <option value="porcentaje" {{ old('tipo', $promocion->tipo) == 'porcentaje' ? 'selected' : '' }}>Porcentaje de descuento</option>
                                <option value="monto_fijo" {{ old('tipo', $promocion->tipo) == 'monto_fijo' ? 'selected' : '' }}>Monto fijo</option>
                                <option value="2x1" {{ old('tipo', $promocion->tipo) == '2x1' ? 'selected' : '' }}>2x1</option>
                                <option value="combo" {{ old('tipo', $promocion->tipo) == 'combo' ? 'selected' : '' }}>Combo especial</option>
                            </select>
                            @error('tipo')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="valor" class="block text-zinc-300 font-medium mb-2">Valor *</label>
                            <input type="number" name="valor" id="valor" value="{{ old('valor', $promocion->valor) }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" 
                                   required min="0" step="0.01" placeholder="15">
                            @error('valor')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="aplica_sobre" class="block text-zinc-300 font-medium mb-2">Aplica sobre *</label>
                            <select name="aplica_sobre" id="aplica_sobre" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required>
                                <option value="item" {{ old('aplica_sobre', $promocion->aplica_sobre) == 'item' ? 'selected' : '' }}>Producto individual</option>
                                <option value="pedido" {{ old('aplica_sobre', $promocion->aplica_sobre) == 'pedido' ? 'selected' : '' }}>Todo el pedido</option>
                            </select>
                            @error('aplica_sobre')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="prioridad" class="block text-zinc-300 font-medium mb-2">Prioridad *</label>
                        <input type="number" name="prioridad" id="prioridad" value="{{ old('prioridad', $promocion->prioridad) }}" 
                               class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" 
                               required min="1" max="10">
                        <p class="text-xs text-zinc-400 mt-1">Número entre 1 (más alta) y 10 (más baja)</p>
                        @error('prioridad')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="tope_descuento" class="block text-zinc-300 font-medium mb-2">Tope de descuento (opcional)</label>
                        <input type="number" name="tope_descuento" id="tope_descuento" value="{{ old('tope_descuento', $promocion->tope_descuento) }}" 
                               class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" 
                               min="0" step="0.01" placeholder="Máximo descuento aplicable">
                        <p class="text-xs text-zinc-400 mt-1">Solo para descuentos porcentuales</p>
                        @error('tope_descuento')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Vigencia -->
                <div class="dashboard-card">
                    <h2 class="text-xl font-semibold text-white mb-4">Vigencia</h2>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="fecha_inicio" class="block text-zinc-300 font-medium mb-2">Fecha inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio', $promocion->fecha_inicio?->format('Y-m-d')) }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500">
                            @error('fecha_inicio')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="fecha_fin" class="block text-zinc-300 font-medium mb-2">Fecha fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin', $promocion->fecha_fin?->format('Y-m-d')) }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500">
                            @error('fecha_fin')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="hora_inicio" class="block text-zinc-300 font-medium mb-2">Hora inicio</label>
                            <input type="time" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio', $promocion->hora_inicio) }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500">
                            @error('hora_inicio')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="hora_fin" class="block text-zinc-300 font-medium mb-2">Hora fin</label>
                            <input type="time" name="hora_fin" id="hora_fin" value="{{ old('hora_fin', $promocion->hora_fin) }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500">
                            @error('hora_fin')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-zinc-300 font-medium mb-2">Días de la semana</label>
                        <div class="grid grid-cols-4 md:grid-cols-7 gap-2">
                            @foreach(['lun' => 'Lun', 'mar' => 'Mar', 'mie' => 'Mié', 'jue' => 'Jue', 'vie' => 'Vie', 'sab' => 'Sáb', 'dom' => 'Dom'] as $value => $label)
                            <label class="flex items-center">
                                <input type="checkbox" name="dias_semana[]" value="{{ $value }}" 
                                       {{ in_array($value, old('dias_semana', $promocion->dias_semana ?? [])) ? 'checked' : '' }}
                                       class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500">
                                <span class="ml-2 text-zinc-300 text-sm">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('dias_semana')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Productos y Categorías -->
                <div class="dashboard-card">
                    <h2 class="text-xl font-semibold text-white mb-4">Aplicación</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-zinc-300 font-medium mb-2">Productos específicos</label>
                            <div class="max-h-48 overflow-y-auto border border-zinc-700 rounded-lg p-2 bg-zinc-800">
                                @foreach($productos as $producto)
                                <label class="flex items-center p-2 hover:bg-zinc-700 rounded">
                                    <input type="checkbox" name="productos[]" value="{{ $producto->id }}" 
                                           {{ in_array($producto->id, old('productos', $promocion->productos->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500">
                                    <span class="ml-2 text-zinc-300 text-sm">{{ $producto->nombre }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('productos')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-zinc-300 font-medium mb-2">Categorías</label>
                            <div class="max-h-48 overflow-y-auto border border-zinc-700 rounded-lg p-2 bg-zinc-800">
                                @foreach($categorias as $categoria)
                                <label class="flex items-center p-2 hover:bg-zinc-700 rounded">
                                    <input type="checkbox" name="categorias[]" value="{{ $categoria->id }}" 
                                           {{ in_array($categoria->id, old('categorias', $promocion->categorias->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500">
                                    <span class="ml-2 text-zinc-300 text-sm">{{ $categoria->nombre }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('categorias')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Estado -->
                <div class="dashboard-card">
                    <label class="flex items-center">
                        <input type="checkbox" name="activo" value="1" 
                               {{ old('activo', $promocion->activo) ? 'checked' : '' }}
                               class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500">
                        <span class="ml-2 text-zinc-300 font-medium">Promoción activa</span>
                    </label>
                    @error('activo')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('promociones.index') }}" class="text-zinc-400 hover:text-white">Cancelar</a>
                    <div class="flex gap-3">
                        <a href="{{ route('promociones.show', $promocion) }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
                            Ver Detalles
                        </a>
                        <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-semibold px-6 py-2 rounded-lg transition-colors">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>