<x-layouts.app :title="__('Editar Especial del Día')">
  <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-700 bg-red-900/30 text-red-200 p-3">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
          </ul>
        </div>
      @endif

      <div class="dashboard-card mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-white mb-2">Editar Especial del Día</h1>
            <p class="text-zinc-300">Modifica la configuración del especial para
              {{ $especial->producto->nombre ?? 'producto eliminado' }}</p>
          </div>
          <a href="{{ route('especial_dia.index') }}"
             class="bg-zinc-600 hover:bg-zinc-500 text-white py-2 px-4 rounded-lg">Volver</a>
        </div>
      </div>

      @php
        $tipoEspecialActual = old('tipo_especial',
            $especial->dia_semana ? 'dia_semana' :
            ($especial->fecha_especifica ? 'fecha_especifica' : ($especial->fecha_inicio ? 'rango_fechas' : ''))
        );
        $tipoDescuentoActual = old('tipo_descuento',
            $especial->descuento_porcentaje ? 'porcentaje' :
            ($especial->precio_especial ? 'precio_fijo' : '')
        );
      @endphp

      <div class="dashboard-card">
        <form method="POST" action="{{ route('especial_dia.update', $especial) }}" class="grid gap-6">
          @csrf
          @method('PUT')

          {{-- Producto --}}
          <div>
            <label class="block text-sm font-medium text-zinc-300 mb-2">Producto</label>
            <select name="producto_id" required
              class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white focus:border-amber-500 focus:ring-amber-500">
              <option value="">Selecciona un producto</option>
              @foreach($productos as $producto)
                <option value="{{ $producto->id }}"
                  @selected(old('producto_id', $especial->producto_id) == $producto->id)>
                  {{ $producto->nombre }} — ${{ number_format($producto->precio,2) }}
                  ({{ $producto->categoria->nombre ?? 'Sin categoría' }})
                </option>
              @endforeach
            </select>
            @error('producto_id')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Tipo de especial --}}
          <div class="grid md:grid-cols-3 gap-4">
            <div class="md:col-span-3">
              <label class="block text-sm font-medium text-zinc-300 mb-2">Tipo de especial</label>
              <select name="tipo_especial" required
                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white focus:border-amber-500 focus:ring-amber-500"
                x-data x-on:change="$dispatch('tipo-cambiado', {v: $event.target.value})">
                <option value="">Selecciona…</option>
                <option value="dia_semana" @selected($tipoEspecialActual=='dia_semana')>Por día de la semana</option>
                <option value="fecha_especifica" @selected($tipoEspecialActual=='fecha_especifica')>Fecha específica</option>
                <option value="rango_fechas" @selected($tipoEspecialActual=='rango_fechas')>Rango de fechas</option>
              </select>
            </div>

            {{-- Día de la semana --}}
            <div x-data="{show:'{{ $tipoEspecialActual }}'}"
                 x-on:tipo-cambiado.window="show=$event.detail.v"
                 class="md:col-span-1">
              <label class="block text-sm font-medium text-zinc-300 mb-2">Día de la semana</label>
              <select name="dia_semana"
                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white"
                x-show="show==='dia_semana'">
                <option value="">Selecciona…</option>
                @foreach($diasSemana as $key => $dia)
                  <option value="{{ $key }}" @selected(old('dia_semana',$especial->dia_semana)==$key)>
                    {{ $dia }}
                  </option>
                @endforeach
              </select>
              @error('dia_semana')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Fecha específica --}}
            <div x-data="{show:'{{ $tipoEspecialActual }}'}"
                 x-on:tipo-cambiado.window="show=$event.detail.v"
                 class="md:col-span-1">
              <label class="block text-sm font-medium text-zinc-300 mb-2">Fecha específica</label>
              <input type="date" name="fecha_especifica"
                value="{{ old('fecha_especifica', optional($especial->fecha_especifica)->format('Y-m-d')) }}"
                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white"
                x-show="show==='fecha_especifica'">
              @error('fecha_especifica')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Rango de fechas --}}
            <div x-data="{show:'{{ $tipoEspecialActual }}'}"
                 x-on:tipo-cambiado.window="show=$event.detail.v"
                 class="md:col-span-1">
              <label class="block text-sm font-medium text-zinc-300 mb-2">Rango</label>
              <div class="grid grid-cols-2 gap-2" x-show="show==='rango_fechas'">
                <input type="date" name="fecha_inicio"
                  value="{{ old('fecha_inicio', optional($especial->fecha_inicio)->format('Y-m-d')) }}"
                  class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
                <input type="date" name="fecha_fin"
                  value="{{ old('fecha_fin', optional($especial->fecha_fin)->format('Y-m-d')) }}"
                  class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
              </div>
              @error('fecha_inicio')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
              @error('fecha_fin')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
          </div>

          {{-- Descuento --}}
          <div class="grid md:grid-cols-3 gap-4">
            <div class="md:col-span-3">
              <label class="block text-sm font-medium text-zinc-300 mb-2">Tipo de descuento</label>
              <select name="tipo_descuento" required
                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white focus:border-amber-500 focus:ring-amber-500"
                x-data x-on:change="$dispatch('desc-cambiado', {v: $event.target.value})">
                <option value="">Selecciona…</option>
                <option value="porcentaje" @selected($tipoDescuentoActual=='porcentaje')>Porcentaje (%)</option>
                <option value="precio_fijo" @selected($tipoDescuentoActual=='precio_fijo')>Precio fijo</option>
              </select>
            </div>

            <div x-data="{show:'{{ $tipoDescuentoActual }}'}"
                 x-on:desc-cambiado.window="show=$event.detail.v"
                 class="md:col-span-1">
              <label class="block text-sm font-medium text-zinc-300 mb-2">% Descuento</label>
              <input type="number" name="descuento_porcentaje" min="1" max="99" step="0.01"
                value="{{ old('descuento_porcentaje', $especial->descuento_porcentaje) }}"
                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white"
                x-show="show==='porcentaje'">
              @error('descuento_porcentaje')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div x-data="{show:'{{ $tipoDescuentoActual }}'}"
                 x-on:desc-cambiado.window="show=$event.detail.v"
                 class="md:col-span-1">
              <label class="block text-sm font-medium text-zinc-300 mb-2">Precio especial</label>
              <input type="number" name="precio_especial" min="0" step="0.01"
                value="{{ old('precio_especial', $especial->precio_especial) }}"
                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white"
                x-show="show==='precio_fijo'">
              @error('precio_especial')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-1">
              <label class="block text-sm font-medium text-zinc-300 mb-2">Prioridad</label>
              <select name="prioridad"
                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white">
                @for($i=1;$i<=10;$i++)
                  <option value="{{ $i }}" @selected(old('prioridad', $especial->prioridad ?? 1)==$i)>
                    {{ $i }} {{ $i==1 ? '(Más alta)' : ($i==10 ? '(Más baja)' : '') }}
                  </option>
                @endfor
              </select>
              @error('prioridad')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
          </div>

          {{-- Descripción / Activo --}}
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2">Descripción</label>
              <textarea name="descripcion_especial" rows="3"
                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white resize-none"
              >{{ old('descripcion_especial', $especial->descripcion_especial) }}</textarea>
              @error('descripcion_especial')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-end">
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="activo" value="1"
                  @checked(old('activo', $especial->activo)) class="w-4 h-4">
                <span class="text-zinc-300">Especial activo</span>
              </label>
            </div>
          </div>

          {{-- Acciones --}}
          <div class="flex justify-end gap-3 pt-4 border-t border-zinc-700">
            <a href="{{ route('especial_dia.index') }}"
               class="bg-zinc-700 hover:bg-zinc-600 text-white py-3 px-6 rounded-lg">Cancelar</a>
            <button type="submit"
              class="bg-amber-600 hover:bg-amber-500 text-white py-3 px-6 rounded-lg font-medium">
              Actualizar Especial
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>

  {{-- Alpine para los toggles (si no lo tienes, añade <script src="//unpkg.com/alpinejs" defer></script> en tu layout) --}}
</x-layouts.app>
