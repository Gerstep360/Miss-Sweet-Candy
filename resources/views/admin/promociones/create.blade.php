{{-- resources/views/admin/promociones/create.blade.php - VERSIÓN MEJORADA SUPER INTUITIVA --}}
<x-layouts.app :title="__('Nueva Promoción - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-6">
        <div class="max-w-4xl mx-auto px-4">
            
            {{-- Header --}}
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('promociones.index') }}" 
                   class="bg-zinc-800 hover:bg-zinc-700 text-white p-2.5 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-white">🎁 Nueva Promoción</h1>
                    <p class="text-sm text-zinc-400">Sigue los pasos para crear tu promoción</p>
                </div>
            </div>

            {{-- Errores --}}
            @if ($errors->any())
                <div class="bg-red-900/20 border-2 border-red-500 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="text-red-400 font-semibold mb-2">Corrige estos errores:</h3>
                            <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('promociones.store') }}" method="POST" id="formPromocion" class="space-y-6">
                @csrf
                
                {{-- PASO 1: Información Básica --}}
                <div class="bg-zinc-900 rounded-xl border-2 border-zinc-800 p-6 shadow-xl">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            1
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Nombre de la Promoción</h2>
                            <p class="text-sm text-zinc-400">Dale un nombre llamativo</p>
                        </div>
                    </div>
                    
                    <input type="text" 
                           name="nombre" 
                           id="nombre" 
                           value="{{ old('nombre') }}" 
                           class="w-full px-4 py-4 rounded-xl bg-zinc-800 text-white text-lg border-2 border-zinc-700 focus:outline-none focus:border-amber-500 transition-colors placeholder-zinc-500" 
                           required 
                           placeholder="Ej: Happy Hour 2x1 en Cafés ☕"
                           autofocus>
                    @error('nombre')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PASO 2: Tipo de Descuento --}}
                <div class="bg-zinc-900 rounded-xl border-2 border-zinc-800 p-6 shadow-xl">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            2
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">¿Qué Tipo de Descuento?</h2>
                            <p class="text-sm text-zinc-400">Selecciona solo UNO</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Porcentaje --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" 
                                   name="tipo" 
                                   value="porcentaje" 
                                   class="peer hidden"
                                   {{ old('tipo') == 'porcentaje' ? 'checked' : '' }}
                                   required>
                            <div class="bg-zinc-800 border-3 border-zinc-700 rounded-xl p-6 hover:border-amber-500 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all hover:scale-105">
                                <div class="text-5xl mb-3 text-center">📊</div>
                                <div class="text-lg font-bold text-white text-center mb-1">Porcentaje</div>
                                <div class="text-sm text-zinc-400 text-center">Ej: 20% de descuento</div>
                            </div>
                        </label>

                        {{-- Monto Fijo --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" 
                                   name="tipo" 
                                   value="monto_fijo" 
                                   class="peer hidden"
                                   {{ old('tipo') == 'monto_fijo' ? 'checked' : '' }}>
                            <div class="bg-zinc-800 border-3 border-zinc-700 rounded-xl p-6 hover:border-amber-500 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all hover:scale-105">
                                <div class="text-5xl mb-3 text-center">💵</div>
                                <div class="text-lg font-bold text-white text-center mb-1">Monto Fijo</div>
                                <div class="text-sm text-zinc-400 text-center">Ej: $15 de descuento</div>
                            </div>
                        </label>

                        {{-- 2x1 --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" 
                                   name="tipo" 
                                   value="2x1" 
                                   class="peer hidden"
                                   {{ old('tipo') == '2x1' ? 'checked' : '' }}>
                            <div class="bg-zinc-800 border-3 border-zinc-700 rounded-xl p-6 hover:border-amber-500 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all hover:scale-105">
                                <div class="text-5xl mb-3 text-center">🎁</div>
                                <div class="text-lg font-bold text-white text-center mb-1">2x1</div>
                                <div class="text-sm text-zinc-400 text-center">Paga 1, lleva 2</div>
                            </div>
                        </label>

                        {{-- Combo --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" 
                                   name="tipo" 
                                   value="combo" 
                                   class="peer hidden"
                                   {{ old('tipo') == 'combo' ? 'checked' : '' }}>
                            <div class="bg-zinc-800 border-3 border-zinc-700 rounded-xl p-6 hover:border-amber-500 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all hover:scale-105">
                                <div class="text-5xl mb-3 text-center">🍰</div>
                                <div class="text-lg font-bold text-white text-center mb-1">Combo</div>
                                <div class="text-sm text-zinc-400 text-center">Descuento en combo</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- PASO 3: Valor del Descuento (se muestra dinámicamente) --}}
                <div id="seccionValor" class="bg-zinc-900 rounded-xl border-2 border-zinc-800 p-6 shadow-xl" style="display: none;">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            3
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">¿Cuánto Descuento?</h2>
                            <p class="text-sm text-zinc-400" id="valorAyuda">Ingresa el valor</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-white font-medium mb-2">
                                <span id="valorLabel">Valor</span> *
                            </label>
                            <input type="number" 
                                   name="valor" 
                                   id="valor" 
                                   value="{{ old('valor') }}" 
                                   class="w-full px-4 py-4 rounded-xl bg-zinc-800 text-white text-lg border-2 border-zinc-700 focus:outline-none focus:border-amber-500" 
                                   step="0.01" 
                                   min="0"
                                   placeholder="0.00">
                        </div>

                        <div id="seccionTope" style="display: none;">
                            <label class="block text-white font-medium mb-2">
                                Límite Máximo (Opcional)
                            </label>
                            <input type="number" 
                                   name="tope_descuento" 
                                   id="tope_descuento" 
                                   value="{{ old('tope_descuento') }}" 
                                   class="w-full px-4 py-4 rounded-xl bg-zinc-800 text-white text-lg border-2 border-zinc-700 focus:outline-none focus:border-amber-500" 
                                   step="0.01" 
                                   min="0"
                                   placeholder="Sin límite">
                            <p class="text-xs text-zinc-400 mt-1">Máximo descuento en dinero</p>
                        </div>
                    </div>
                </div>

                {{-- PASO 4: Dónde Aplica --}}
                <div id="seccionAplicacion" class="bg-zinc-900 rounded-xl border-2 border-zinc-800 p-6 shadow-xl" style="display: none;">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            4
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">¿Dónde se Aplica?</h2>
                            <p class="text-sm text-zinc-400">Selecciona el alcance</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <label class="relative cursor-pointer">
                            <input type="radio" 
                                   name="aplica_sobre" 
                                   value="pedido" 
                                   class="peer hidden"
                                   {{ old('aplica_sobre') == 'pedido' ? 'checked' : '' }}
                                   required>
                            <div class="bg-zinc-800 border-3 border-zinc-700 rounded-xl p-5 hover:border-amber-500 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="text-4xl">🛒</div>
                                    <div>
                                        <div class="text-lg font-bold text-white">Todo el Pedido</div>
                                        <div class="text-sm text-zinc-400">Descuento al total</div>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" 
                                   name="aplica_sobre" 
                                   value="item" 
                                   class="peer hidden"
                                   {{ old('aplica_sobre') == 'item' ? 'checked' : '' }}>
                            <div class="bg-zinc-800 border-3 border-zinc-700 rounded-xl p-5 hover:border-amber-500 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="text-4xl">🎯</div>
                                    <div>
                                        <div class="text-lg font-bold text-white">Productos Específicos</div>
                                        <div class="text-sm text-zinc-400">Solo ciertos productos</div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    {{-- Selección de Productos/Categorías --}}
                    <div id="seleccionProductos" style="display: none;" class="mt-6 bg-zinc-800/50 rounded-xl p-5 border border-zinc-700">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-amber-400 text-sm font-medium">Selecciona productos o categorías</p>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-white font-medium mb-3">Productos</label>
                                <div class="bg-zinc-900 rounded-lg p-3 max-h-60 overflow-y-auto border border-zinc-700 space-y-2">
                                    @foreach($productos as $producto)
                                        <label class="flex items-center gap-3 p-2.5 hover:bg-zinc-800 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" 
                                                   name="productos[]" 
                                                   value="{{ $producto->id }}"
                                                   class="rounded bg-zinc-800 border-zinc-600 text-amber-500 focus:ring-amber-500 w-5 h-5"
                                                   {{ in_array($producto->id, old('productos', [])) ? 'checked' : '' }}>
                                            <span class="text-white">{{ $producto->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-white font-medium mb-3">Categorías</label>
                                <div class="bg-zinc-900 rounded-lg p-3 max-h-60 overflow-y-auto border border-zinc-700 space-y-2">
                                    @foreach($categorias as $categoria)
                                        <label class="flex items-center gap-3 p-2.5 hover:bg-zinc-800 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" 
                                                   name="categorias[]" 
                                                   value="{{ $categoria->id }}"
                                                   class="rounded bg-zinc-800 border-zinc-600 text-amber-500 focus:ring-amber-500 w-5 h-5"
                                                   {{ in_array($categoria->id, old('categorias', [])) ? 'checked' : '' }}>
                                            <span class="text-white">{{ $categoria->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PASO 5: Vigencia (OPCIONAL) --}}
                <div id="seccionVigencia" class="bg-zinc-900 rounded-xl border-2 border-zinc-800 p-6 shadow-xl" style="display: none;">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                5
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Vigencia (Opcional)</h2>
                                <p class="text-sm text-zinc-400">Define cuándo aplica</p>
                            </div>
                        </div>
                        <button type="button" 
                                id="btnMostrarVigencia" 
                                class="bg-zinc-800 hover:bg-zinc-700 text-white px-4 py-2 rounded-lg transition-all">
                            Configurar
                        </button>
                    </div>
                    
                    <div id="opcionesVigencia" style="display: none;" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-white font-medium mb-2">Desde</label>
                                <input type="date" 
                                       name="fecha_inicio" 
                                       id="fecha_inicio" 
                                       value="{{ old('fecha_inicio') }}" 
                                       class="w-full px-4 py-3 rounded-lg bg-zinc-800 text-white border-2 border-zinc-700 focus:outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-white font-medium mb-2">Hasta</label>
                                <input type="date" 
                                       name="fecha_fin" 
                                       id="fecha_fin" 
                                       value="{{ old('fecha_fin') }}" 
                                       class="w-full px-4 py-3 rounded-lg bg-zinc-800 text-white border-2 border-zinc-700 focus:outline-none focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-white font-medium mb-2">Hora Inicio</label>
                                <input type="time" 
                                       name="hora_inicio" 
                                       id="hora_inicio" 
                                       value="{{ old('hora_inicio') }}" 
                                       class="w-full px-4 py-3 rounded-lg bg-zinc-800 text-white border-2 border-zinc-700 focus:outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-white font-medium mb-2">Hora Fin</label>
                                <input type="time" 
                                       name="hora_fin" 
                                       id="hora_fin" 
                                       value="{{ old('hora_fin') }}" 
                                       class="w-full px-4 py-3 rounded-lg bg-zinc-800 text-white border-2 border-zinc-700 focus:outline-none focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-white font-medium mb-3">Días de la Semana</label>
                            <div class="grid grid-cols-3 sm:grid-cols-7 gap-2">
                                @php
                                    $dias = [
                                        'lun' => 'L', 'mar' => 'M', 'mie' => 'X',
                                        'jue' => 'J', 'vie' => 'V', 'sab' => 'S', 'dom' => 'D'
                                    ];
                                @endphp
                                @foreach($dias as $value => $label)
                                    <label class="relative cursor-pointer">
                                        <input type="checkbox" 
                                               name="dias_semana[]" 
                                               value="{{ $value }}"
                                               class="peer hidden"
                                               {{ in_array($value, old('dias_semana', [])) ? 'checked' : '' }}>
                                        <div class="bg-zinc-800 border-2 border-zinc-700 rounded-lg p-3 text-center hover:border-blue-500 peer-checked:border-blue-500 peer-checked:bg-blue-500/20 transition-all">
                                            <span class="text-lg font-bold text-white">{{ $label }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PASO 6: Configuración Final --}}
                <div id="seccionFinal" class="bg-zinc-900 rounded-xl border-2 border-zinc-800 p-6 shadow-xl" style="display: none;">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            6
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Configuración Final</h2>
                            <p class="text-sm text-zinc-400">Últimos ajustes</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-white font-medium mb-2">
                                Prioridad
                                <span class="text-zinc-400 text-sm font-normal ml-2">(1 = mayor, 10 = menor)</span>
                            </label>
                            <input type="number" 
                                   name="prioridad" 
                                   id="prioridad" 
                                   value="{{ old('prioridad', 5) }}" 
                                   class="w-full md:w-64 px-4 py-3 rounded-lg bg-zinc-800 text-white border-2 border-zinc-700 focus:outline-none focus:border-green-500" 
                                   required 
                                   min="1" 
                                   max="10">
                        </div>

                        <label class="flex items-center gap-3 cursor-pointer bg-zinc-800/50 p-4 rounded-xl border-2 border-zinc-700 hover:border-green-500 transition-colors">
                            <input type="checkbox" 
                                   name="activo" 
                                   value="1" 
                                   checked
                                   class="rounded bg-zinc-800 border-zinc-600 text-green-500 focus:ring-green-500 w-6 h-6">
                            <div>
                                <span class="text-white font-medium text-lg">Activar Ahora</span>
                                <p class="text-sm text-zinc-400 mt-1">La promoción estará disponible inmediatamente</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex gap-3 justify-end">
                    <a href="{{ route('promociones.index') }}" 
                       class="bg-zinc-800 hover:bg-zinc-700 text-white font-medium px-6 py-3 rounded-xl transition-all">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-bold px-8 py-3 rounded-xl transition-all shadow-lg shadow-amber-500/30">
                        🎉 Crear Promoción
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript para lógica dinámica --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoRadios = document.querySelectorAll('input[name="tipo"]');
            const aplicaSobreRadios = document.querySelectorAll('input[name="aplica_sobre"]');
            
            // Secciones
            const seccionValor = document.getElementById('seccionValor');
            const seccionAplicacion = document.getElementById('seccionAplicacion');
            const seccionVigencia = document.getElementById('seccionVigencia');
            const seccionFinal = document.getElementById('seccionFinal');
            const seleccionProductos = document.getElementById('seleccionProductos');
            const opcionesVigencia = document.getElementById('opcionesVigencia');
            
            // Elementos
            const valorInput = document.getElementById('valor');
            const valorLabel = document.getElementById('valorLabel');
            const valorAyuda = document.getElementById('valorAyuda');
            const seccionTope = document.getElementById('seccionTope');
            const btnMostrarVigencia = document.getElementById('btnMostrarVigencia');

            // 1. Cuando selecciona tipo, mostrar valor
            tipoRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    seccionValor.style.display = 'block';
                    seccionAplicacion.style.display = 'block';
                    seccionVigencia.style.display = 'block';
                    seccionFinal.style.display = 'block';
                    
                    // Configurar según tipo
                    if (this.value === 'porcentaje') {
                        valorLabel.textContent = 'Porcentaje de Descuento';
                        valorAyuda.textContent = 'Ej: 20 para 20% de descuento';
                        valorInput.placeholder = '20';
                        valorInput.max = '100';
                        valorInput.value = '';
                        seccionTope.style.display = 'block'; // Mostrar tope solo para porcentaje
                    } else if (this.value === 'monto_fijo') {
                        valorLabel.textContent = 'Monto del Descuento';
                        valorAyuda.textContent = 'Ej: 15 para $15 de descuento';
                        valorInput.placeholder = '15.00';
                        valorInput.removeAttribute('max');
                        valorInput.value = '';
                        seccionTope.style.display = 'none';
                    } else if (this.value === '2x1') {
                        valorLabel.textContent = 'Valor del Descuento (Automático)';
                        valorAyuda.textContent = 'Para 2x1, el valor siempre es 50%';
                        valorInput.value = '50';
                        valorInput.readOnly = true;
                        seccionTope.style.display = 'none';
                    } else if (this.value === 'combo') {
                        valorLabel.textContent = 'Descuento del Combo';
                        valorAyuda.textContent = 'Puede ser porcentaje o monto fijo';
                        valorInput.placeholder = '10';
                        valorInput.removeAttribute('max');
                        valorInput.value = '';
                        valorInput.readOnly = false;
                        seccionTope.style.display = 'none';
                    }
                    
                    // Smooth scroll
                    seccionValor.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            });

            // 2. Cuando selecciona aplica_sobre
            aplicaSobreRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'item') {
                        seleccionProductos.style.display = 'block';
                    } else {
                        seleccionProductos.style.display = 'none';
                    }
                });
            });

            // 3. Botón para mostrar opciones de vigencia
            btnMostrarVigencia.addEventListener('click', function() {
                if (opcionesVigencia.style.display === 'none') {
                    opcionesVigencia.style.display = 'block';
                    this.textContent = 'Ocultar';
                } else {
                    opcionesVigencia.style.display = 'none';
                    this.textContent = 'Configurar';
                }
            });

            // 4. Validación de fechas
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaFin = document.getElementById('fecha_fin');

            fechaInicio.addEventListener('change', function() {
                if (fechaFin.value && this.value && fechaFin.value < this.value) {
                    alert('La fecha de fin debe ser posterior a la fecha de inicio');
                    fechaFin.value = '';
                }
                fechaFin.min = this.value;
            });

            // 5. Inicializar estados previos (old values)
            const tipoChecked = document.querySelector('input[name="tipo"]:checked');
            if (tipoChecked) {
                tipoChecked.dispatchEvent(new Event('change'));
            }

            const aplicaChecked = document.querySelector('input[name="aplica_sobre"]:checked');
            if (aplicaChecked) {
                aplicaChecked.dispatchEvent(new Event('change'));
            }
        });
    </script>

    <style>
        /* Animaciones suaves */
        [id^="seccion"] {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilos para scrollbar en listas */
        .overflow-y-auto::-webkit-scrollbar {
            width: 8px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #27272a;
            border-radius: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #52525b;
            border-radius: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #71717a;
        }
    </style>
</x-layouts.app>
