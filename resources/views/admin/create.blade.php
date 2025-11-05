{{-- resources/views/admin/promociones/create.blade.php --}}
<x-layouts.app :title="__('Nueva Promoción - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-4 sm:py-8">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">Nueva Promoción</h1>
            
            <form action="{{ route('promociones.store') }}" method="POST" class="space-y-4 sm:space-y-6">
                @csrf
                
                <!-- Información Básica -->
                <div class="dashboard-card">
                    <h2 class="text-lg sm:text-xl font-semibold text-white mb-3 sm:mb-4">Información de la Promoción</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label for="nombre" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Nombre *</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" 
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base" 
                                   required maxlength="120" placeholder="Ej: 2x1 Tazas de Café Latte">
                        </div>
                        
                        <div>
                            <label for="tipo" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Tipo *</label>
                            <select name="tipo" id="tipo" class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base" required>
                                <option value="">Selecciona un tipo</option>
                                <option value="porcentaje" {{ old('tipo') == 'porcentaje' ? 'selected' : '' }}>Porcentaje de descuento</option>
                                <option value="monto_fijo" {{ old('tipo') == 'monto_fijo' ? 'selected' : '' }}>Monto fijo</option>
                                <option value="2x1" {{ old('tipo') == '2x1' ? 'selected' : '' }}>2x1</option>
                                <option value="combo" {{ old('tipo') == 'combo' ? 'selected' : '' }}>Combo especial</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mt-3 sm:mt-4">
                        <div>
                            <label for="valor" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Valor *</label>
                            <input type="number" name="valor" id="valor" value="{{ old('valor') }}" 
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base" 
                                   required min="0" step="0.01" placeholder="15">
                        </div>
                        
                        <div>
                            <label for="aplica_sobre" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Aplica sobre *</label>
                            <select name="aplica_sobre" id="aplica_sobre" class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base" required>
                                <option value="item" {{ old('aplica_sobre') == 'item' ? 'selected' : '' }}>Producto individual</option>
                                <option value="pedido" {{ old('aplica_sobre') == 'pedido' ? 'selected' : '' }}>Todo el pedido</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3 sm:mt-4">
                        <label for="prioridad" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Prioridad *</label>
                        <input type="number" name="prioridad" id="prioridad" value="{{ old('prioridad', 1) }}" 
                               class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base" 
                               required min="1" max="10">
                        <p class="text-xs text-zinc-400 mt-1">Número entre 1 (más alta) y 10 (más baja)</p>
                    </div>
                </div>

                <!-- Vigencia -->
                <div class="dashboard-card">
                    <h2 class="text-lg sm:text-xl font-semibold text-white mb-3 sm:mb-4">Vigencia</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label for="fecha_inicio" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Fecha inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" 
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base">
                        </div>
                        
                        <div>
                            <label for="fecha_fin" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Fecha fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}" 
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mt-3 sm:mt-4">
                        <div>
                            <label for="hora_inicio" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Hora inicio</label>
                            <input type="time" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio') }}" 
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base">
                        </div>
                        
                        <div>
                            <label for="hora_fin" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Hora fin</label>
                            <input type="time" name="hora_fin" id="hora_fin" value="{{ old('hora_fin') }}" 
                                   class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base">
                        </div>
                    </div>
                    
                    <div class="mt-3 sm:mt-4">
                        <label class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Días de la semana</label>
                        <div class="grid grid-cols-3 xs:grid-cols-4 md:grid-cols-7 gap-2">
                            @foreach(['lun' => 'Lun', 'mar' => 'Mar', 'mie' => 'Mié', 'jue' => 'Jue', 'vie' => 'Vie', 'sab' => 'Sáb', 'dom' => 'Dom'] as $value => $label)
                            <label class="flex items-center gap-2 bg-zinc-800 p-2 rounded-lg hover:bg-zinc-700 transition cursor-pointer min-h-[44px] touch-manipulation">
                                <input type="checkbox" name="dias_semana[]" value="{{ $value }}" 
                                       {{ in_array($value, old('dias_semana', [])) ? 'checked' : '' }}
                                       class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500">
                                <span class="text-zinc-300 text-sm">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Productos y Categorías -->
                <div class="dashboard-card">
                    <h2 class="text-lg sm:text-xl font-semibold text-white mb-3 sm:mb-4">Aplicación</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Productos específicos</label>
                            <div class="max-h-48 overflow-y-auto border border-zinc-700 rounded-lg p-2 bg-zinc-800">
                                @foreach($productos as $producto)
                                <label class="flex items-center p-2 hover:bg-zinc-700 rounded cursor-pointer min-h-[44px] touch-manipulation">
                                    <input type="checkbox" name="productos[]" value="{{ $producto->id }}" 
                                           class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500">
                                    <span class="ml-2 text-zinc-300 text-sm">{{ $producto->nombre }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Categorías</label>
                            <div class="max-h-48 overflow-y-auto border border-zinc-700 rounded-lg p-2 bg-zinc-800">
                                @foreach($categorias as $categoria)
                                <label class="flex items-center p-2 hover:bg-zinc-700 rounded cursor-pointer min-h-[44px] touch-manipulation">
                                    <input type="checkbox" name="categorias[]" value="{{ $categoria->id }}" 
                                           class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500">
                                    <span class="ml-2 text-zinc-300 text-sm">{{ $categoria->nombre }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado -->
                <div class="dashboard-card">
                    <label class="flex items-center gap-2 cursor-pointer min-h-[44px] touch-manipulation">
                        <input type="checkbox" name="activo" value="1" checked 
                               class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500 w-5 h-5">
                        <span class="text-zinc-300 font-medium text-sm sm:text-base">Promoción activa</span>
                    </label>
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <a href="{{ route('promociones.index') }}" 
                       class="text-zinc-400 hover:text-white text-center sm:text-left text-sm sm:text-base">Cancelar</a>
                    <button type="submit" 
                            class="w-full sm:w-auto bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-black font-semibold px-6 py-3 sm:py-2 rounded-lg transition-all min-h-[44px] touch-manipulation text-sm sm:text-base">
                        Crear Promoción
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>