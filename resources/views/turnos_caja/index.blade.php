{{-- filepath: resources/views/turnos_caja/index.blade.php --}}
<x-layouts.app :title="__('Historial de Turnos')">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 text-white">

    {{-- ===== Encabezado ===== --}}
    <header class="mb-6">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Historial de Turnos</h1>
          <p class="text-zinc-400 text-sm">Registro de todos los turnos de caja del sistema</p>
        </div>

        @can('iniciar-turno')
          <a href="{{ route('turnos_caja.iniciar.form') }}"
             class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-black font-semibold rounded-xl hover:bg-amber-400 transition shadow-lg shadow-amber-500/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Iniciar Nuevo Turno
          </a>
        @endcan
      </div>
    </header>

    @php
      // Para los chips de estado sin perder otros filtros
      $qs = request()->query();
      $qsTodos = $qs; unset($qsTodos['estado']);
      $qsActivo = array_merge($qs, ['estado' => 'activo']);
      $qsCerrado = array_merge($qs, ['estado' => 'cerrado']);
      $estado = request('estado');
    @endphp

    {{-- ===== Barra de filtros (chips + formulario) ===== --}}
    <section class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-4 sm:p-6 mb-6">
      {{-- Chips de estado rápidos --}}
      <div class="flex flex-wrap items-center gap-2 mb-4">
        <a href="{{ route('turnos_caja.index', $qsTodos) }}"
           class="px-3 py-1.5 rounded-full border text-xs font-semibold
                  {{ $estado ? 'border-zinc-700 text-zinc-300 hover:border-zinc-500' : 'border-amber-500/40 bg-amber-500/10 text-amber-300' }}">
          Todos
        </a>
        <a href="{{ route('turnos_caja.index', $qsActivo) }}"
           class="px-3 py-1.5 rounded-full border text-xs font-semibold
                  {{ $estado === 'activo' ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300' : 'border-zinc-700 text-zinc-300 hover:border-zinc-500' }}">
          Activos
        </a>
        <a href="{{ route('turnos_caja.index', $qsCerrado) }}"
           class="px-3 py-1.5 rounded-full border text-xs font-semibold
                  {{ $estado === 'cerrado' ? 'border-zinc-500/60 bg-zinc-500/10 text-zinc-200' : 'border-zinc-700 text-zinc-300 hover:border-zinc-500' }}">
          Cerrados
        </a>

        <span class="ml-auto text-xs text-zinc-400">
          Mostrando <strong class="text-zinc-200">{{ $turnos->count() }}</strong> de
          <strong class="text-zinc-200">{{ $turnos->total() }}</strong> resultados
        </span>
      </div>

      {{-- Formulario de filtros detallados --}}
      <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Cajero --}}
        <div>
          <label for="cajero_id" class="block text-xs font-medium text-zinc-400 mb-1">Cajero</label>
          <select id="cajero_id" name="cajero_id"
                  class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 placeholder-zinc-500 focus:border-amber-500 focus:ring-amber-500">
            <option value="">Todos los cajeros</option>
            @foreach($cajeros as $cajero)
              <option value="{{ $cajero->id }}" {{ request('cajero_id') == $cajero->id ? 'selected' : '' }}>
                {{ $cajero->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Estado (detallado) --}}
        <div>
          <label for="estado" class="block text-xs font-medium text-zinc-400 mb-1">Estado</label>
          <select id="estado" name="estado"
                  class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-amber-500 focus:ring-amber-500">
            <option value="">Todos</option>
            <option value="activo"  {{ $estado === 'activo'  ? 'selected' : '' }}>Activo</option>
            <option value="cerrado" {{ $estado === 'cerrado' ? 'selected' : '' }}>Cerrado</option>
          </select>
        </div>

        {{-- Fecha desde --}}
        <div>
          <label for="fecha_inicio" class="block text-xs font-medium text-zinc-400 mb-1">Fecha desde</label>
          <input type="date" id="fecha_inicio" name="fecha_inicio"
                 value="{{ request('fecha_inicio', now()->startOfMonth()->format('Y-m-d')) }}"
                 class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-amber-500 focus:ring-amber-500">
        </div>

        {{-- Fecha hasta --}}
        <div>
          <label for="fecha_fin" class="block text-xs font-medium text-zinc-400 mb-1">Fecha hasta</label>
          <input type="date" id="fecha_fin" name="fecha_fin"
                 value="{{ request('fecha_fin', now()->format('Y-m-d')) }}"
                 class="w-full rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 focus:border-amber-500 focus:ring-amber-500">
        </div>

        {{-- Botones --}}
        <div class="md:col-span-4 flex justify-end gap-3 pt-1">
          <a href="{{ route('turnos_caja.index') }}"
             class="px-4 py-2 rounded-lg border border-zinc-700 text-zinc-300 hover:bg-zinc-900 transition">
            Limpiar
          </a>
          <button type="submit"
                  class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-amber-500 text-black font-semibold hover:bg-amber-400 transition shadow-lg shadow-amber-500/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Filtrar
          </button>
        </div>
      </form>
    </section>

    {{-- ===== Tabla ===== --}}
    <section class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="sticky top-0 z-10 bg-zinc-950/90 backdrop-blur border-b border-zinc-800">
            <tr class="text-xs uppercase tracking-wider text-zinc-400">
              <th scope="col" class="px-6 py-3 text-left">Cajero</th>
              <th scope="col" class="px-6 py-3 text-left">Inicio</th>
              <th scope="col" class="px-6 py-3 text-left">Fin</th>
              <th scope="col" class="px-6 py-3 text-left">Duración</th>
              <th scope="col" class="px-6 py-3 text-left">Monto inicial</th>
              <th scope="col" class="px-6 py-3 text-left">Estado</th>
              <th scope="col" class="px-6 py-3 text-left">Cierre</th>
              <th scope="col" class="px-6 py-3 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-800">
            @forelse($turnos as $turno)
              <tr class="hover:bg-zinc-900/60 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-500/20 grid place-items-center text-amber-300 font-bold">
                      {{ strtoupper(substr($turno->cajero->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                      <div class="text-sm font-semibold">{{ $turno->cajero->name }}</div>
                      <div class="text-xs text-zinc-400 truncate">{{ $turno->cajero->email }}</div>
                    </div>
                  </div>
                </td>

                <td class="px-6 py-4 text-sm">{{ $turno->inicio->format('d/m/Y H:i') }}</td>

                <td class="px-6 py-4 text-sm">
                  @if($turno->fin)
                    {{ $turno->fin->format('d/m/Y H:i') }}
                  @else
                    <span class="text-amber-300 font-medium">En curso</span>
                  @endif
                </td>

                <td class="px-6 py-4 text-sm">{{ $turno->duracion_formateada }}</td>

                <td class="px-6 py-4 text-sm font-semibold">
                  Bs. {{ number_format($turno->monto_inicial, 2) }}
                </td>

                <td class="px-6 py-4">
                  @if($turno->estado === 'activo')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Activo
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-zinc-500/15 text-zinc-200 border border-zinc-500/30">
                      <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span> Cerrado
                    </span>
                  @endif
                </td>

                <td class="px-6 py-4 text-sm">
                  @if($turno->cierre)
                    <a href="{{ route('cierres_caja.show', $turno->cierre->id) }}"
                       class="text-amber-300 hover:text-amber-200 font-medium">
                      Ver cierre →
                    </a>
                  @else
                    <span class="text-zinc-500">Sin cierre</span>
                  @endif
                </td>

                <td class="px-6 py-4 text-right text-sm">
                  <a href="{{ route('turnos_caja.show', $turno) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-300 hover:border-amber-500/40 hover:text-white transition">
                    Ver detalles
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-6 py-16">
                  <div class="max-w-md mx-auto text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/10 grid place-items-center border border-amber-500/20">
                      <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5"/>
                      </svg>
                    </div>
                    <h3 class="text-lg font-semibold">No se encontraron turnos</h3>
                    <p class="text-zinc-400 text-sm mt-1">Ajusta los filtros o inicia un nuevo turno.</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      @if($turnos->hasPages())
        <div class="px-6 py-4 border-t border-zinc-800">
          {{ $turnos->appends(request()->query())->links() }}
        </div>
      @endif
    </section>
  </div>
</x-layouts.app>
