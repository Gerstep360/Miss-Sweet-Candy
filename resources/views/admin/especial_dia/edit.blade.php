<x-layouts.app :title="__('Editar Especial del Día')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Editar Especial del Día</h1>
                        <p class="text-zinc-300">
                            Modifica la configuración del especial para {{ $especial->producto->nombre ?? 'producto eliminado' }}
                        </p>
                    </div>
                    <a href="{{ route('especial_dia.index') }}"
                       class="bg-zinc-600 hover:bg-zinc-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <div class="dashboard-card">
                <form method="POST" action="{{ route('especial_dia.update', $especial) }}" class="grid gap-6">
                    @csrf
                    @method('PUT')

                    {{-- resumen de errores --}}
                    @if ($errors->any())
                        <div class="rounded-lg border border-red-700 bg-red-900/30 text-red-200 p-3">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- producto --}}
                    <div class="bg-zinc-800/50 rounded-lg p-4 border border-zinc-700">
                        <label class="block text-sm font-medium text-zinc-300 mb-3">
                            <i class="fas fa-box text-amber-400 mr-2"></i> Producto
                        </label>
                        <select name="producto_id" required
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">Selecciona un producto</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}"
                                    {{ old('producto_id', $especial->producto_id) == $producto->id ? 'selected' : '' }}>
                                    {{ $producto->nombre }} - ${{ number_format($producto->precio, 2) }}
                                    ({{ $producto->categoria->nombre ?? 'Sin categoría' }})
                                </option>
                            @endforeach
                        </select>
                        @error('producto_id') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- configuración temporal (sin JS) --}}
                    @php
                        $tipoEspecialActual = old('tipo_especial',
                            $especial->dia_semana ? 'dia_semana' :
                            ($especial->fecha_especifica ? 'fecha_especifica' :
                            ($especial->fecha_inicio ? 'rango_fechas' : ''))
                        );
                    @endphp

                    <div class="bg-zinc-800/50 rounded-lg p-4 border border-zinc-700 space-y-3">
                        <label class="block text-sm font-medium text-zinc-300">
                            <i class="fas fa-calendar text-blue-400 mr-2"></i> Configuración temporal
                        </label>

                        <select name="tipo_especial" required
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">Selecciona el tipo de especial</option>
                            <option value="dia_semana"    {{ $tipoEspecialActual=='dia_semana' ? 'selected' : '' }}>Por día de la semana</option>
                            <option value="fecha_especifica" {{ $tipoEspecialActual=='fecha_especifica' ? 'selected' : '' }}>Por fecha específica</option>
                            <option value="rango_fechas"  {{ $tipoEspecialActual=='rango_fechas' ? 'selected' : '' }}>Por rango de fechas</option>
                        </select>

                        <p class="text-xs text-zinc-400">Rellena solo los campos del tipo seleccionado; el backend ignorará el resto.</p>

                        {{-- dia_semana --}}
                        <details {{ $tipoEspecialActual=='dia_semana' ? 'open' : '' }} class="bg-zinc-900/40 rounded p-3 border border-zinc-700">
                            <summary class="cursor-pointer text-zinc-200 mb-2">Por día de la semana</summary>
                            <select name="dia_semana"
                                    class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">Selecciona un día</option>
                                @foreach($diasSemana as $key => $dia)
                                    <option value="{{ $key }}" {{ old('dia_semana', $especial->dia_semana) == $key ? 'selected' : '' }}>
                                        {{ $dia }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dia_semana') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </details>

                        {{-- fecha_especifica --}}
                        <details {{ $tipoEspecialActual=='fecha_especifica' ? 'open' : '' }} class="bg-zinc-900/40 rounded p-3 border border-zinc-700">
                            <summary class="cursor-pointer text-zinc-200 mb-2">Por fecha específica</summary>
                            <input type="date" name="fecha_especifica"
                                   value="{{ old('fecha_especifica', $especial->fecha_especifica ? \Carbon\Carbon::parse($especial->fecha_especifica)->format('Y-m-d') : '') }}"
                                   class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            @error('fecha_especifica') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </details>

                        {{-- rango_fechas --}}
                        <details {{ $tipoEspecialActual=='rango_fechas' ? 'open' : '' }} class="bg-zinc-900/40 rounded p-3 border border-zinc-700">
                            <summary class="cursor-pointer text-zinc-200 mb-2">Por rango de fechas</summary>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input type="date" name="fecha_inicio"
                                       value="{{ old('fecha_inicio', $especial->fecha_inicio ? \Carbon\Carbon::parse($especial->fecha_inicio)->format('Y-m-d') : '') }}"
                                       class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                       placeholder="Fecha inicio">
                                <input type="date" name="fecha_fin"
                                       value="{{ old('fecha_fin', $especial->fecha_fin ? \Carbon\Carbon::parse($especial->fecha_fin)->format('Y-m-d') : '') }}"
                                       class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                       placeholder="Fecha fin">
                            </div>
                            @error('fecha_inicio') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                            @error('fecha_fin')    <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </details>
                    </div>

                    {{-- descuento (sin JS) --}}
                    @php
                        $tipoDescuentoActual = old('tipo_descuento',
                            $especial->descuento_porcentaje ? 'porcentaje' :
                            ($especial->precio_especial ? 'precio_fijo' : '')
                        );
                    @endphp

                    <div class="bg-zinc-800/50 rounded-lg p-4 border border-zinc-700 space-y-3">
                        <label class="block text-sm font-medium text-zinc-300">
                            <i class="fas fa-tag text-green-400 mr-2"></i> Configuración de descuento
                        </label>

                        <select name="tipo_descuento" required
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">Selecciona el tipo de descuento</option>
                            <option value="porcentaje" {{ $tipoDescuentoActual=='porcentaje' ? 'selected' : '' }}>Descuento por porcentaje (%)</option>
                            <option value="precio_fijo" {{ $tipoDescuentoActual=='precio_fijo' ? 'selected' : '' }}>Precio fijo especial</option>
                        </select>

                        <details {{ $tipoDescuentoActual=='porcentaje' ? 'open' : '' }} class="bg-zinc-900/40 rounded p-3 border border-zinc-700">
                            <summary class="cursor-pointer text-zinc-200 mb-2">Descuento (%)</summary>
                            <div class="relative">
                                <input type="number" name="descuento_porcentaje" min="1" max="99" step="0.01"
                                       value="{{ old('descuento_porcentaje', $especial->descuento_porcentaje) }}"
                                       class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 pr-8 text-white focus:border-red-500 focus:ring-1 focus:ring-red-500"
                                       placeholder="15">
                                <span class="absolute right-3 top-2 text-zinc-400">%</span>
                            </div>
                            @error('descuento_porcentaje') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </details>

                        <details {{ $tipoDescuentoActual=='precio_fijo' ? 'open' : '' }} class="bg-zinc-900/40 rounded p-3 border border-zinc-700">
                            <summary class="cursor-pointer text-zinc-200 mb-2">Precio especial</summary>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-zinc-400">$</span>
                                <input type="number" name="precio_especial" min="0" step="0.01"
                                       value="{{ old('precio_especial', $especial->precio_especial) }}"
                                       class="w-full bg-zinc-700 border border-zinc-600 rounded-lg pl-8 pr-3 py-2 text-white focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                       placeholder="0.00">
                            </div>
                            @error('precio_especial') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </details>

                        <p class="text-xs text-zinc-400">El backend exige al menos uno: porcentaje o precio fijo.</p>
                    </div>

                    {{-- descripción / prioridad / activo --}}
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-zinc-800/50 rounded-lg p-4 border border-zinc-700">
                            <label class="block text-sm font-medium text-zinc-300 mb-3">
                                <i class="fas fa-align-left text-purple-400 mr-2"></i> Descripción especial
                            </label>
                            <textarea name="descripcion_especial" rows="3"
                                      class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 resize-none"
                                      placeholder="Describe por qué este producto es especial hoy...">{{ old('descripcion_especial', $especial->descripcion_especial) }}</textarea>
                            @error('descripcion_especial') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="bg-zinc-800/50 rounded-lg p-4 border border-zinc-700 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-2">
                                    <i class="fas fa-sort-numeric-up text-yellow-400 mr-2"></i> Prioridad
                                </label>
                                <select name="prioridad"
                                        class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-3 py-2 text-white focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500">
                                    @for($i=1;$i<=10;$i++)
                                        <option value="{{ $i }}" {{ old('prioridad', $especial->prioridad ?? 1) == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ $i==1 ? '(Más alta)' : ($i==10 ? '(Más baja)' : '') }}
                                        </option>
                                    @endfor
                                </select>
                                @error('prioridad') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="activo" value="1"
                                           {{ old('activo', $especial->activo) ? 'checked' : '' }}
                                           class="w-4 h-4 text-green-600 bg-zinc-700 border-zinc-600 rounded focus:ring-green-500 focus:ring-2">
                                    <span class="text-zinc-300"><i class="fas fa-power-off text-green-400 mr-2"></i> Especial activo</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- acciones --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-zinc-700">
                        <a href="{{ route('especial_dia.index') }}"
                           class="bg-zinc-700 hover:bg-zinc-600 text-white py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit"
                                class="bg-amber-600 hover:bg-amber-500 text-white py-3 px-6 rounded-lg transition-colors flex items-center gap-2 font-medium">
                            <i class="fas fa-save"></i> Actualizar Especial
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
