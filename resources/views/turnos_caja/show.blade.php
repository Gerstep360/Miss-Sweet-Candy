{{-- filepath: resources/views/turnos_caja/show.blade.php --}}
<x-layouts.app :title="__('Detalle de Turno #'.$turno->id)">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 text-white">
    {{-- ===== Encabezado ===== --}}
    <header class="mb-6">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex items-start gap-3">
          <div class="w-12 h-12 rounded-xl bg-amber-500 grid place-items-center text-black font-bold shadow-lg shadow-amber-500/30">
            {{ strtoupper(substr($turno->cajero->name,0,2)) }}
          </div>
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
              Turno #{{ $turno->id }}
            </h1>
            <p class="text-zinc-400 text-sm">
              Cajero: <span class="text-zinc-200 font-medium">{{ $turno->cajero->name }}</span>
              <span class="mx-2">•</span>
              Inicio: <span class="text-zinc-200">{{ $turno->inicio->format('d/m/Y H:i') }}</span>
            </p>
          </div>
        </div>

        <div class="flex flex-wrap gap-2">
          <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold
                       border
                       {{ $turno->estado === 'activo'
                           ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'
                           : 'bg-zinc-500/15 text-zinc-200 border-zinc-500/30' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $turno->estado === 'activo' ? 'bg-emerald-400' : 'bg-zinc-400' }}"></span>
            {{ ucfirst($turno->estado) }}
          </span>

          <a href="{{ route('turnos_caja.index') }}"
             class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-300 hover:border-amber-500/40 hover:text-white transition">
            ← Volver
          </a>

          @can('cerrar-turno')
            @if($turno->estado === 'activo' && $turno->cajero_id === auth()->id())
              <a href="{{ route('cierres_caja.create') }}"
                 class="px-4 py-1.5 rounded-lg bg-amber-500 text-black font-semibold hover:bg-amber-400 transition shadow-lg shadow-amber-500/20">
                Cerrar Turno
              </a>
            @endif
          @endcan
        </div>
      </div>
    </header>

    {{-- ===== Métricas ===== --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5">
        <p class="text-xs text-zinc-400 mb-1">Monto Inicial</p>
        <div class="text-2xl font-extrabold">Bs. {{ number_format($turno->monto_inicial, 2) }}</div>
      </article>

      <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5">
        <p class="text-xs text-zinc-400 mb-1">Duración</p>
        <div class="text-2xl font-extrabold">
          {{ $turno->duracion_formateada }}
          @if(is_null($turno->fin))
            <span class="text-xs font-medium text-emerald-300 align-middle">• en curso</span>
          @endif
        </div>
      </article>

      <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5">
        <p class="text-xs text-zinc-400 mb-1">Fin</p>
        <div class="text-2xl font-extrabold">
          {{ $turno->fin ? $turno->fin->format('d/m/Y H:i') : '—' }}
        </div>
      </article>
    </section>

    {{-- ===== Observaciones ===== --}}
    @if($turno->observaciones_apertura || $turno->observaciones_cierre)
      <section class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        @if($turno->observaciones_apertura)
          <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-5">
            <h3 class="font-semibold mb-2 text-amber-300">Observaciones de apertura</h3>
            <p class="text-zinc-300 text-sm leading-relaxed whitespace-pre-line">{{ $turno->observaciones_apertura }}</p>
          </div>
        @endif

        @if($turno->observaciones_cierre)
          <div class="bg-zinc-900/60 border border-zinc-800 rounded-2xl p-5">
            <h3 class="font-semibold mb-2 text-zinc-200">Observaciones de cierre</h3>
            <p class="text-zinc-300 text-sm leading-relaxed whitespace-pre-line">{{ $turno->observaciones_cierre }}</p>
          </div>
        @endif
      </section>
    @endif

    {{-- ===== Cierre (si existe) ===== --}}
    <section class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl mb-6 overflow-hidden">
      <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between">
        <h2 class="text-lg font-bold">Cierre de Caja</h2>
        @if($turno->cierre)
          <a href="{{ route('cierres_caja.show', $turno->cierre->id) }}"
             class="text-amber-300 hover:text-amber-200 font-medium">Ver cierre →</a>
        @endif
      </div>

      @if($turno->cierre)
        @php
          $detalles = $turno->cierre->detalles ?? collect();
          // Intento de agregación flexible (funciona si los campos existen)
          $porMetodo = $detalles->groupBy('metodo')->map(fn($items) => [
              'cantidad' => $items->count(),
              'total'    => $items->sum('importe') ?: $items->sum('monto')
          ]);
        @endphp

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
          @forelse($porMetodo as $metodo => $resumen)
            <div class="bg-zinc-950/40 border border-zinc-800 rounded-xl p-4">
              <p class="text-xs text-zinc-400 mb-1">Método</p>
              <div class="font-semibold mb-2">{{ $metodo ?: '—' }}</div>
              <div class="text-sm text-zinc-400">{{ $resumen['cantidad'] }} transacciones</div>
              <div class="text-xl font-extrabold mt-1">Bs. {{ number_format($resumen['total'] ?? 0, 2) }}</div>
            </div>
          @empty
            <p class="text-zinc-400 text-sm p-6">No hay desglose por método.</p>
          @endforelse
        </div>

        {{-- Lista “a prueba de esquema”: muestra lo que haya en detalles --}}
        @if($detalles->count())
          <div class="px-6 pb-6">
            <div class="overflow-x-auto">
              <table class="min-w-full">
                <thead class="bg-zinc-950/60 border-b border-zinc-800 text-xs uppercase tracking-wider text-zinc-400">
                  <tr>
                    <th class="text-left px-4 py-2">Concepto</th>
                    <th class="text-left px-4 py-2">Método</th>
                    <th class="text-left px-4 py-2">Observación</th>
                    <th class="text-right px-4 py-2">Importe</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                  @foreach($detalles as $d)
                    <tr class="hover:bg-zinc-900/60">
                      <td class="px-4 py-2 text-sm">{{ $d->concepto ?? '—' }}</td>
                      <td class="px-4 py-2 text-sm">{{ $d->metodo ?? '—' }}</td>
                      <td class="px-4 py-2 text-sm text-zinc-400">{{ $d->observacion ?? $d->nota ?? '—' }}</td>
                      <td class="px-4 py-2 text-sm text-right font-semibold">
                        Bs. {{ number_format($d->importe ?? $d->monto ?? 0, 2) }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @endif
      @else
        <div class="p-6">
          <div class="flex items-center gap-3 text-zinc-400">
            <div class="w-10 h-10 rounded-lg bg-amber-500/10 grid place-items-center border border-amber-500/20">
              <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1"/>
              </svg>
            </div>
            <p class="text-sm">Este turno aún no tiene cierre.</p>
          </div>
        </div>
      @endif
    </section>

    {{-- ===== Timeline simple ===== --}}
    <section class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
      <h3 class="font-bold mb-4">Linea de tiempo</h3>
      <ol class="relative border-s border-zinc-800 ps-5 space-y-6">
        <li>
          <div class="absolute -start-1.5 w-3 h-3 rounded-full bg-amber-400"></div>
          <p class="text-sm"><span class="font-semibold">Apertura:</span> {{ $turno->inicio->format('d/m/Y H:i') }}</p>
        </li>
        <li>
          <div class="absolute -start-1.5 w-3 h-3 rounded-full {{ $turno->fin ? 'bg-zinc-400' : 'bg-zinc-600' }}"></div>
          <p class="text-sm">
            <span class="font-semibold">Fin:</span>
            {{ $turno->fin ? $turno->fin->format('d/m/Y H:i') : '—' }}
          </p>
        </li>
        <li>
          <div class="absolute -start-1.5 w-3 h-3 rounded-full {{ $turno->cierre ? 'bg-emerald-400' : 'bg-zinc-600' }}"></div>
          <p class="text-sm">
            <span class="font-semibold">Cierre:</span>
            {{ $turno->cierre ? 'Registrado' : 'Pendiente' }}
          </p>
        </li>
      </ol>
    </section>
  </div>
</x-layouts.app>
