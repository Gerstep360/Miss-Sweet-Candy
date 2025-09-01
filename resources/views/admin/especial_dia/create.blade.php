<x-layouts.app :title="__('Nuevo Especial del Día')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="dashboard-card mb-8">
                <h1 class="text-2xl font-bold text-white mb-2">Nuevo Especial del Día</h1>
                <p class="text-zinc-300">Configura un nuevo especial para un producto</p>
            </div>
            <form method="POST" action="{{ route('especial_dia.store') }}" class="grid gap-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Producto</label>
                    <select name="producto_id" required class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
                        <option value="">Selecciona un producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                                {{ $producto->nombre }} ({{ $producto->categoria->nombre ?? 'Sin categoría' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Tipo de especial</label>
                    <select name="tipo_especial" id="tipo_especial" required class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
                        <option value="dia_semana" {{ old('tipo_especial') == 'dia_semana' ? 'selected' : '' }}>Por día de la semana</option>
                        <option value="fecha_especifica" {{ old('tipo_especial') == 'fecha_especifica' ? 'selected' : '' }}>Por fecha específica</option>
                        <option value="rango_fechas" {{ old('tipo_especial') == 'rango_fechas' ? 'selected' : '' }}>Por rango de fechas</option>
                    </select>
                </div>

                <div id="campo_dia_semana" style="display: none;">
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Día de la semana</label>
                    <select name="dia_semana" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
                        <option value="">Selecciona un día</option>
                        @foreach($diasSemana as $key => $dia)
                            <option value="{{ $key }}" {{ old('dia_semana') == $key ? 'selected' : '' }}>{{ $dia }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="campo_fecha_especifica" style="display: none;">
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Fecha específica</label>
                    <input type="date" name="fecha_especifica" value="{{ old('fecha_especifica') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
                </div>
                <div id="campo_rango_fechas" style="display: none;">
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Rango de fechas</label>
                    <div class="flex gap-2">
                        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}" class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white w-1/2" placeholder="Inicio">
                        <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}" class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white w-1/2" placeholder="Fin">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Tipo de descuento</label>
                    <select name="tipo_descuento" id="tipo_descuento" required class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
                        <option value="porcentaje" {{ old('tipo_descuento') == 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                        <option value="precio_fijo" {{ old('tipo_descuento') == 'precio_fijo' ? 'selected' : '' }}>Precio fijo</option>
                    </select>
                </div>
                <div id="campo_descuento_porcentaje" style="display: none;">
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Descuento (%)</label>
                    <input type="number" name="descuento_porcentaje" min="1" max="99" value="{{ old('descuento_porcentaje') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
                </div>
                <div id="campo_precio_especial" style="display: none;">
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Precio especial ($)</label>
                    <input type="number" name="precio_especial" min="0" step="0.01" value="{{ old('precio_especial') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Descripción (opcional)</label>
                    <textarea name="descripcion_especial" rows="2" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">{{ old('descripcion_especial') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Prioridad</label>
                    <input type="number" name="prioridad" min="1" max="10" value="{{ old('prioridad', 1) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }} class="rounded">
                    <label class="text-zinc-300">Activo</label>
                </div>
                <div class="flex justify-end gap-3">
                    <a href="{{ route('especial_dia.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors">Cancelar</a>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-colors">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function mostrarCampos() {
            let tipo = document.getElementById('tipo_especial').value;
            document.getElementById('campo_dia_semana').style.display = tipo === 'dia_semana' ? '' : 'none';
            document.getElementById('campo_fecha_especifica').style.display = tipo === 'fecha_especifica' ? '' : 'none';
            document.getElementById('campo_rango_fechas').style.display = tipo === 'rango_fechas' ? '' : 'none';
        }
        function mostrarCamposDescuento() {
            let tipo = document.getElementById('tipo_descuento').value;
            document.getElementById('campo_descuento_porcentaje').style.display = tipo === 'porcentaje' ? '' : 'none';
            document.getElementById('campo_precio_especial').style.display = tipo === 'precio_fijo' ? '' : 'none';
        }
        document.getElementById('tipo_especial').addEventListener('change', mostrarCampos);
        document.getElementById('tipo_descuento').addEventListener('change', mostrarCamposDescuento);
        window.onload = function() {
            mostrarCampos();
            mostrarCamposDescuento();
        }
    </script>
</x-layouts.app>