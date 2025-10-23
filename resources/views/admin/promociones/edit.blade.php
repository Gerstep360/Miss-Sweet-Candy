{{-- resources/views/admin/promociones/edit.blade.php --}}
<x-layouts.app :title="__('Editar Promoción - Miss Sweet Candy')">

    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-4 sm:py-8">
        <div class="max-w-4xl mx-auto px-4">

            <h1 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">Editar Promoción</h1>

            <!-- Vista previa de la promoción -->
            <div class="dashboard-card mb-4 sm:mb-6 hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-amber-500/30 to-amber-600/20 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg sm:text-xl font-semibold text-white truncate">{{ $promocion->nombre }}</h2>

                        <div class="flex flex-wrap gap-1.5 sm:gap-2 mt-1.5 sm:mt-2">
                            <span class="bg-gradient-to-r from-amber-500/20 to-amber-600/10 text-amber-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full capitalize font-medium border border-amber-500/30">
                                {{ $promocion->tipo }}
                            </span>

                            <span class="bg-gradient-to-r from-blue-500/20 to-blue-600/10 text-blue-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-blue-500/30">
                                {{ $promocion->aplica_sobre }}
                            </span>

                            <span class="bg-gradient-to-r from-green-500/20 to-green-600/10 text-green-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full font-bold border border-green-500/30">
                                @if($promocion->tipo == 'porcentaje')
                                    -{{ $promocion->valor }}%
                                @elseif($promocion->tipo == 'monto_fijo')
                                    -${{ number_format($promocion->valor, 2) }}
                                @else
                                    {{ ucfirst($promocion->tipo) }}
                                @endif
                            </span>

                            @if($promocion->esta_vigente)
                                <span class="bg-gradient-to-r from-green-500/20 to-green-600/10 text-green-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full flex items-center gap-1 border border-green-500/30">
                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                                    Vigente Ahora
                                </span>
                            @else
                                <span class="bg-gradient-to-r from-red-500/20 to-red-600/10 text-red-400 text-[10px] sm:text-xs px-2.5 py-1 rounded-full border border-red-500/30">✕ No Vigente</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('promociones.update', $promocion) }}" method="POST" class="space-y-4 sm:space-y-6">
                @csrf {{-- CSRF field (requerido por middleware) --}}
                @method('PUT') {{-- Method spoofing para PUT --}}
                {{-- Docs: @csrf y @method en formularios Blade. --}}
                {{-- https://laravel.com/docs/12.x/blade (Forms > CSRF Field / Method Field) --}}

                <!-- Información Básica -->
                <div class="dashboard-card">
                    <h2 class="text-lg sm:text-xl font-semibold text-white mb-3 sm:mb-4">Información de la Promoción</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label for="nombre" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Nombre *</label>
                            <input
                                type="text"
                                name="nombre"
                                id="nombre"
                                value="{{ old('nombre', $promocion->nombre) }}"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base"
                                required maxlength="120"
                                placeholder="Ej: 2x1 Tazas de Café Latte">
                            @error('nombre')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tipo" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Tipo *</label>
                            <select
                                name="tipo"
                                id="tipo"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base"
                                required>
                                <option value="">Selecciona un tipo</option>
                                <option value="porcentaje" {{ old('tipo', $promocion->tipo) == 'porcentaje' ? 'selected' : '' }}>Porcentaje de descuento</option>
                                <option value="monto_fijo" {{ old('tipo', $promocion->tipo) == 'monto_fijo' ? 'selected' : '' }}>Monto fijo</option>
                                <option value="2x1"        {{ old('tipo', $promocion->tipo) == '2x1' ? 'selected' : '' }}>2x1</option>
                                <option value="combo"      {{ old('tipo', $promocion->tipo) == 'combo' ? 'selected' : '' }}>Combo especial</option>
                            </select>
                            @error('tipo')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mt-3 sm:mt-4">
                        <div>
                            <label for="valor" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Valor *</label>
                            <input
                                type="number"
                                name="valor"
                                id="valor"
                                value="{{ old('valor', $promocion->valor) }}"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base"
                                required min="0" step="0.01" placeholder="15">
                            @error('valor')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="aplica_sobre" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Aplica sobre *</label>
                            <select
                                name="aplica_sobre"
                                id="aplica_sobre"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base"
                                required>
                                <option value="item"   {{ old('aplica_sobre', $promocion->aplica_sobre) == 'item' ? 'selected' : '' }}>Producto individual</option>
                                <option value="pedido" {{ old('aplica_sobre', $promocion->aplica_sobre) == 'pedido' ? 'selected' : '' }}>Todo el pedido</option>
                            </select>
                            @error('aplica_sobre')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mt-3 sm:mt-4">
                        <div>
                            <label for="prioridad" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Prioridad *</label>
                            <input
                                type="number"
                                name="prioridad"
                                id="prioridad"
                                value="{{ old('prioridad', $promocion->prioridad) }}"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base"
                                required min="1" max="10">
                            <p class="text-xs text-zinc-400 mt-1">Número entre 1 (más alta) y 10 (más baja)</p>
                            @error('prioridad')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tope_descuento" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Tope de descuento</label>
                            <input
                                type="number"
                                name="tope_descuento"
                                id="tope_descuento"
                                value="{{ old('tope_descuento', $promocion->tope_descuento) }}"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base"
                                min="0" step="0.01" placeholder="Máximo descuento">
                            <p class="text-xs text-zinc-400 mt-1">Solo para descuentos porcentuales</p>
                            @error('tope_descuento')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Vigencia -->
                <div class="dashboard-card">
                    <h2 class="text-lg sm:text-xl font-semibold text-white mb-3 sm:mb-4"> Vigencia</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label for="fecha_inicio" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Fecha inicio</label>
                            <input
                                type="date"
                                name="fecha_inicio"
                                id="fecha_inicio"
                                value="{{ old('fecha_inicio', $promocion->fecha_inicio?->format('Y-m-d')) }}"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base">
                            @error('fecha_inicio')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Fecha fin</label>
                            <input
                                type="date"
                                name="fecha_fin"
                                id="fecha_fin"
                                value="{{ old('fecha_fin', $promocion->fecha_fin?->format('Y-m-d')) }}"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base">
                            @error('fecha_fin')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mt-3 sm:mt-4">
                        <div>
                            <label for="hora_inicio" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Hora inicio</label>
                            <input
                                type="time"
                                name="hora_inicio"
                                id="hora_inicio"
                                value="{{ old('hora_inicio', $promocion->hora_inicio) }}"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base">
                            @error('hora_inicio')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="hora_fin" class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base"> Hora fin</label>
                            <input
                                type="time"
                                name="hora_fin"
                                id="hora_fin"
                                value="{{ old('hora_fin', $promocion->hora_fin) }}"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500 text-sm sm:text-base">
                            @error('hora_fin')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3 sm:mt-4">
                        <label class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base"> Días de la semana</label>

                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-7 gap-2">
                            @foreach(['lun' => 'Lun', 'mar' => 'Mar', 'mie' => 'Mié', 'jue' => 'Jue', 'vie' => 'Vie', 'sab' => 'Sáb', 'dom' => 'Dom'] as $value => $label)
                                <label class="flex items-center gap-2 bg-zinc-800 p-2 rounded-lg hover:bg-zinc-700 transition cursor-pointer min-h-[44px] touch-manipulation">
                                    <input
                                        type="checkbox"
                                        name="dias_semana[]" value="{{ $value }}"
                                        {{ in_array($value, old('dias_semana', $promocion->dias_semana ?? [])) ? 'checked' : '' }}
                                        class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500">
                                    <span class="text-zinc-300 text-sm">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                        @error('dias_semana')
                            <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Productos y Categorías -->
                <div class="dashboard-card">
                    <h2 class="text-lg sm:text-xl font-semibold text-white mb-3 sm:mb-4">Aplicación</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Productos específicos</label>
                            <div class="max-h-48 overflow-y-auto border border-zinc-700 rounded-lg p-2 bg-zinc-800 scrollbar-thin scrollbar-thumb-zinc-700 scrollbar-track-zinc-800">
                                @foreach($productos as $producto)
                                    <label class="flex items-center p-2 hover:bg-zinc-700 rounded transition cursor-pointer min-h-[44px] touch-manipulation">
                                        <input
                                            type="checkbox"
                                            name="productos[]" value="{{ $producto->id }}"
                                            {{ in_array($producto->id, old('productos', $promocion->productos->pluck('id')->toArray())) ? 'checked' : '' }}
                                            class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500 flex-shrink-0">
                                        <span class="ml-2 text-zinc-300 text-xs sm:text-sm">{{ $producto->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('productos')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-zinc-300 font-medium mb-2 text-sm sm:text-base">Categorías</label>
                            <div class="max-h-48 overflow-y-auto border border-zinc-700 rounded-lg p-2 bg-zinc-800 scrollbar-thin scrollbar-thumb-zinc-700 scrollbar-track-zinc-800">
                                @foreach($categorias as $categoria)
                                    <label class="flex items-center p-2 hover:bg-zinc-700 rounded transition cursor-pointer min-h-[44px] touch-manipulation">
                                        <input
                                            type="checkbox"
                                            name="categorias[]" value="{{ $categoria->id }}"
                                            {{ in_array($categoria->id, old('categorias', $promocion->categorias->pluck('id')->toArray())) ? 'checked' : '' }}
                                            class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500 flex-shrink-0">
                                        <span class="ml-2 text-zinc-300 text-xs sm:text-sm">{{ $categoria->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('categorias')
                                <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Estado -->
                <div class="dashboard-card">
                    <label class="flex items-center cursor-pointer min-h-[44px] touch-manipulation">
                        <input
                            type="checkbox"
                            name="activo" value="1"
                            {{ old('activo', $promocion->activo) ? 'checked' : '' }}
                            class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500 w-5 h-5">
                        <span class="ml-3 text-zinc-300 font-medium text-sm sm:text-base">Promoción activa</span>
                    </label>
                    @error('activo')
                        <p class="text-red-400 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
                    <a href="{{ route('promociones.index') }}"
                       class="text-zinc-400 hover:text-white transition-colors text-center sm:text-left py-2 sm:py-0 text-sm sm:text-base">
                        ← Volver a la lista
                    </a>

                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <a href="{{ route('promociones.show', $promocion) }}"
                           class="bg-zinc-700/80 hover:bg-zinc-600 active:bg-zinc-500 text-white font-semibold px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition-all text-center min-h-[44px] flex items-center justify-center touch-manipulation shadow-lg text-sm sm:text-base">
                            Ver Detalles
                        </a>

                        <button type="submit"
                                class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 active:from-amber-600 active:to-amber-700 text-black font-semibold px-4 sm:px-6 py-2.5 sm:py-2 rounded-lg transition-all min-h-[44px] touch-manipulation shadow-lg shadow-amber-500/30 text-sm sm:text-base">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

</x-layouts.app>
