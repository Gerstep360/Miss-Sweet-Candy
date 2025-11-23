<x-layouts.app :title="__('Nuevo Especial del Día')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-2xl mx-auto px-3 sm:px-4 lg:px-8 py-4 sm:py-8">
            
            <!-- Header -->
            <div class="dashboard-card mb-4 sm:mb-8">
                <div class="flex items-center gap-3 sm:gap-4">
                    <a href="{{ route('especial_dia.index') }}" 
                       class="bg-zinc-700 hover:bg-zinc-600 active:bg-zinc-800 text-white p-2 sm:p-2.5 rounded-lg transition-colors touch-manipulation min-h-[44px] min-w-[44px] flex items-center justify-center">
                        <i class="fas fa-arrow-left text-sm sm:text-base"></i>
                    </a>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg sm:text-2xl font-bold text-white mb-1 sm:mb-2">Nuevo Especial del Día</h1>
                        <p class="text-xs sm:text-base text-zinc-300 truncate">Configura un nuevo especial</p>
                    </div>
                </div>
            </div>

            <!-- Mostrar errores -->
            @if($errors->any())
                <div class="dashboard-card mb-4 sm:mb-6 border-l-4 border-red-500 bg-red-500/10">
                    <div class="flex items-center gap-2 sm:gap-3 mb-2">
                        <i class="fas fa-exclamation-triangle text-red-400 text-sm sm:text-base"></i>
                        <h3 class="text-base sm:text-lg font-semibold text-white">Errores de validación</h3>
                    </div>
                    <ul class="text-red-300 text-xs sm:text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <i class="fas fa-circle text-[6px] mt-1.5"></i>
                                <span class="flex-1">{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('especial_dia.store') }}" class="space-y-3 sm:space-y-6">
                @csrf

                <!-- Producto -->
                <div class="dashboard-card">
                    <label class="block text-xs sm:text-sm font-medium text-zinc-300 mb-2">Producto *</label>
                    <select name="producto_id" required 
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base min-h-[44px]">
                        <option value="">Selecciona un producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                                {{ $producto->nombre }} - ${{ number_format($producto->precio, 2) }} ({{ $producto->categoria->nombre ?? 'Sin categoría' }})
                            </option>
                        @endforeach
                    </select>
                    @error('producto_id')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de especial -->
                <div class="dashboard-card">
                    <label class="block text-xs sm:text-sm font-medium text-zinc-300 mb-2">Tipo de especial *</label>
                    <select name="tipo_especial" id="tipo_especial" required 
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base min-h-[44px]">
                        <option value="dia_semana" {{ old('tipo_especial', 'dia_semana') == 'dia_semana' ? 'selected' : '' }}>Por día de la semana</option>
                        <option value="fecha_especifica" {{ old('tipo_especial') == 'fecha_especifica' ? 'selected' : '' }}>Por fecha específica</option>
                        <option value="rango_fechas" {{ old('tipo_especial') == 'rango_fechas' ? 'selected' : '' }}>Por rango de fechas</option>
                    </select>
                    @error('tipo_especial')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campos dinámicos para tipo de especial -->
                <div id="campo_dia_semana" class="dashboard-card hidden">
                    <label class="block text-xs sm:text-sm font-medium text-zinc-300 mb-2">Día de la semana *</label>
                    <select name="dia_semana" 
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base min-h-[44px]">
                        <option value="">Selecciona un día</option>
                        @foreach($diasSemana as $key => $dia)
                            <option value="{{ $key }}" {{ old('dia_semana') == $key ? 'selected' : '' }}>{{ $dia }}</option>
                        @endforeach
                    </select>
                    @error('dia_semana')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="campo_fecha_especifica" class="dashboard-card hidden">
                    <label class="block text-xs sm:text-sm font-medium text-zinc-300 mb-2">Fecha específica *</label>
                    <input type="date" name="fecha_especifica" value="{{ old('fecha_especifica') }}" 
                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base min-h-[44px]">
                    @error('fecha_especifica')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="campo_rango_fechas" class="dashboard-card hidden">
                    <label class="block text-xs sm:text-sm font-medium text-zinc-300 mb-2">Rango de fechas *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-[10px] sm:text-xs text-zinc-400 mb-1">Fecha inicio</label>
                            <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}" 
                                   class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base min-h-[44px]">
                            @error('fecha_inicio')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] sm:text-xs text-zinc-400 mb-1">Fecha fin</label>
                            <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}" 
                                   class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base min-h-[44px]">
                            @error('fecha_fin')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tipo de descuento -->
                <div class="dashboard-card">
                    <label class="block text-xs sm:text-sm font-medium text-zinc-300 mb-2">Tipo de descuento *</label>
                    <select name="tipo_descuento" id="tipo_descuento" required 
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base min-h-[44px]">
                        <option value="porcentaje" {{ old('tipo_descuento', 'porcentaje') == 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                        <option value="precio_fijo" {{ old('tipo_descuento') == 'precio_fijo' ? 'selected' : '' }}>Precio fijo</option>
                    </select>
                    @error('tipo_descuento')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campos dinámicos para descuento -->
                <div id="campo_descuento_porcentaje" class="dashboard-card">
                    <label class="block text-xs sm:text-sm font-medium text-zinc-300 mb-2">Descuento (%) *</label>
                    <input type="number" name="descuento_porcentaje" min="1" max="99" value="{{ old('descuento_porcentaje') }}" 
                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base min-h-[44px]"
                           placeholder="Ej: 15 para 15% de descuento">
                    @error('descuento_porcentaje')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="campo_precio_especial" class="dashboard-card hidden">
                    <label class="block text-xs sm:text-sm font-medium text-zinc-300 mb-2">Precio especial ($) *</label>
                    <input type="number" name="precio_especial" min="0" step="0.01" value="{{ old('precio_especial') }}" 
                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base min-h-[44px]"
                           placeholder="Ej: 12.50">
                    @error('precio_especial')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción y prioridad -->
                <div class="dashboard-card">
                    <label class="block text-xs sm:text-sm font-medium text-zinc-300 mb-2">Descripción (opcional)</label>
                    <textarea name="descripcion_especial" rows="3" 
                              class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base resize-none"
                              placeholder="Describe el especial...">{{ old('descripcion_especial') }}</textarea>
                    @error('descripcion_especial')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="dashboard-card">
                    <label class="block text-xs sm:text-sm font-medium text-zinc-300 mb-2">Prioridad</label>
                    <input type="number" name="prioridad" min="1" max="10" value="{{ old('prioridad', 1) }}" 
                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm sm:text-base min-h-[44px]">
                    <p class="text-zinc-400 text-[10px] sm:text-xs mt-1">1 = Mayor prioridad, 10 = Menor prioridad</p>
                    @error('prioridad')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div class="dashboard-card">
                    <label class="flex items-center gap-3 cursor-pointer touch-manipulation min-h-[44px]">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }} 
                               class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500 w-5 h-5">
                        <span class="text-zinc-300 font-medium text-sm sm:text-base">Activar especial inmediatamente</span>
                    </label>
                    @error('activo')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 justify-end pt-4 sm:pt-6 sticky bottom-0 sm:static bg-gradient-to-t sm:bg-none from-zinc-900 via-zinc-900 to-transparent pb-3 sm:pb-0 -mx-3 sm:mx-0 px-3 sm:px-0">
                    <a href="{{ route('especial_dia.index') }}" 
                       class="bg-zinc-700 hover:bg-zinc-600 active:bg-zinc-800 text-white py-3 px-6 rounded-lg transition-colors text-center text-sm sm:text-base min-h-[44px] flex items-center justify-center touch-manipulation">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-black py-3 px-8 rounded-lg transition-colors font-medium flex items-center gap-2 justify-center text-sm sm:text-base min-h-[44px] touch-manipulation">
                        <i class="fas fa-save"></i>
                        Crear Especial
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function mostrarCampos() {
            const tipo = document.getElementById('tipo_especial').value;
            
            // Ocultar todos los campos primero
            document.getElementById('campo_dia_semana').classList.add('hidden');
            document.getElementById('campo_fecha_especifica').classList.add('hidden');
            document.getElementById('campo_rango_fechas').classList.add('hidden');
            
            // Mostrar el campo correspondiente
            if (tipo === 'dia_semana') {
                document.getElementById('campo_dia_semana').classList.remove('hidden');
            } else if (tipo === 'fecha_especifica') {
                document.getElementById('campo_fecha_especifica').classList.remove('hidden');
            } else if (tipo === 'rango_fechas') {
                document.getElementById('campo_rango_fechas').classList.remove('hidden');
            }
        }

        function mostrarCamposDescuento() {
            const tipo = document.getElementById('tipo_descuento').value;
            
            document.getElementById('campo_descuento_porcentaje').classList.add('hidden');
            document.getElementById('campo_precio_especial').classList.add('hidden');
            
            if (tipo === 'porcentaje') {
                document.getElementById('campo_descuento_porcentaje').classList.remove('hidden');
            } else if (tipo === 'precio_fijo') {
                document.getElementById('campo_precio_especial').classList.remove('hidden');
            }
        }

        // Inicializar basado en el valor seleccionado, NO en old()
        document.addEventListener('DOMContentLoaded', function() {
            // Forzar el tipo a "dia_semana" inicialmente
            document.getElementById('tipo_especial').value = 'dia_semana';
            document.getElementById('tipo_descuento').value = 'porcentaje';
            
            mostrarCampos();
            mostrarCamposDescuento();
            
            // Event listeners
            document.getElementById('tipo_especial').addEventListener('change', mostrarCampos);
            document.getElementById('tipo_descuento').addEventListener('change', mostrarCamposDescuento);
        });

        // Solo mostrar campos basados en old() si hay errores específicos de fecha
        @if($errors->has('fecha_especifica') || $errors->has('fecha_inicio') || $errors->has('fecha_fin'))
            document.addEventListener('DOMContentLoaded', function() {
                // Si hay errores de fecha, mostrar los campos correspondientes
                @if($errors->has('fecha_especifica'))
                    document.getElementById('tipo_especial').value = 'fecha_especifica';
                @elseif($errors->has('fecha_inicio') || $errors->has('fecha_fin'))
                    document.getElementById('tipo_especial').value = 'rango_fechas';
                @endif
                mostrarCampos();
                mostrarCamposDescuento();
            });
        @endif
    </script>

    <style>
        /* Sticky button support */
        @supports (backdrop-filter: blur(8px)) {
            .sticky.bottom-0 {
                backdrop-filter: blur(8px);
            }
        }
        
        /* Touch feedback */
        @media (hover: none) {
            .touch-manipulation:active {
                transform: scale(0.98);
            }
        }
    </style>
</x-layouts.app>