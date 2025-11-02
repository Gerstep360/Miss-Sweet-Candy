@php
    // Evita "use ..." en Blade; usa FQN para que el parser no se confunda.
    $turnoActivo = \App\Models\TurnoCaja::turnoActivo();
    $esMiTurno = $turnoActivo && $turnoActivo->cajero_id === auth()->id();

    $turnoData = $turnoActivo ? [
        'id' => $turnoActivo->id,
        'cajero' => [
            'id' => optional($turnoActivo->cajero)->id,
            'name' => optional($turnoActivo->cajero)->name,
        ],
        'tiempo_transcurrido' => $turnoActivo->tiempo_transcurrido,
        'duracion_formateada' => $turnoActivo->duracion_formateada,
        'monto_inicial'      => (float) $turnoActivo->monto_inicial,
    ] : null;
@endphp

<div x-data="turnoStatus" class="mb-8">
    <!-- Sin turno activo -->
    <template x-if="!hayTurno">
        <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-2 border-amber-500/30 rounded-2xl p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-amber-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2">No hay nadie de turno</h3>
                        <p class="text-zinc-400 mb-4">Debes iniciar un turno antes de registrar ventas y cobros.</p>
                        @can('iniciar-turno')
                            <a href="{{ route('turnos_caja.iniciar.form') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>Iniciar Turno</span>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Turno de otro cajero -->
    <template x-if="hayTurno && !esMiTurno">
        <div class="bg-gradient-to-r from-red-500/10 to-rose-500/10 border-2 border-red-500/30 rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-red-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-2">
                        <span x-text="turno?.cajero?.name"></span> está de turno actualmente
                    </h3>
                    <div class="flex items-center gap-6 text-sm text-zinc-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Iniciado <span x-text="turno?.tiempo_transcurrido"></span></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Duración: <span x-text="turno?.duracion_formateada"></span></span>
                        </div>
                    </div>
                    <p class="text-zinc-400 mt-3">
                        No puedes registrar ventas ni cobros hasta que cierre su turno.
                    </p>
                </div>
            </div>
        </div>
    </template>

    <!-- Mi turno activo -->
    <template x-if="hayTurno && esMiTurno">
        <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border-2 border-green-500/30 rounded-2xl p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4 flex-1">
                    <div class="w-14 h-14 bg-green-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-xl font-bold text-white">Tu turno está activo</h3>
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                            <div class="bg-black/20 rounded-lg p-3">
                                <div class="flex items-center gap-2 text-zinc-400 text-xs mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    <span>Duración</span>
                                </div>
                                <p class="text-white font-semibold" x-text="turno?.duracion_formateada"></p>
                            </div>
                            <div class="bg-black/20 rounded-lg p-3">
                                <div class="flex items-center gap-2 text-zinc-400 text-xs mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 10v1"/>
                                    </svg>
                                    <span>Monto Inicial</span>
                                </div>
                                <p class="text-white font-semibold">Bs. <span x-text="formatCurrency(turno?.monto_inicial)"></span></p>
                            </div>
                            <div class="bg-black/20 rounded-lg p-3">
                                <div class="flex items-center gap-2 text-zinc-400 text-xs mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Iniciado</span>
                                </div>
                                <p class="text-white font-semibold" x-text="turno?.tiempo_transcurrido"></p>
                            </div>
                        </div>
                        <p class="text-zinc-400 text-sm">
                            Puedes registrar ventas y cobros. Al finalizar tu jornada, debes realizar el cierre de caja.
                        </p>
                    </div>
                </div>
                @can('cerrar-turno')
                    <a href="{{ route('cierres_caja.create') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-rose-500 text-white font-semibold rounded-xl hover:from-red-600 hover:to-rose-600 transition-all transform hover:scale-105 ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Cerrar Turno</span>
                    </a>
                @endcan
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('turnoStatus', () => ({
    // Estado inicial desde Blade
    hayTurno: @json((bool) $turnoActivo),
    esMiTurno: @json((bool) $esMiTurno),
    turno: {!! \Illuminate\Support\Js::from($turnoData) !!},

    // Hook: se ejecuta solo (no necesitas x-init) 
    // https://alpinejs.dev/directives/init#auto-evaluate-init-method
    init() {
      setInterval(() => this.fetchEstado(), 30000);
    },

    async fetchEstado() {
      try {
        const response = await fetch('{{ route('turnos_caja.api.estado') }}', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
        });
        const data = await response.json();

        this.hayTurno = !!data.hay_turno_activo;
        this.esMiTurno = !!data.es_mi_turno;
        this.turno = data.turno || null;
      } catch (error) {
        console.error('Error al obtener estado del turno:', error);
      }
    },

    formatCurrency(value) {
      if (value == null || isNaN(value)) return '0,00';
      return new Intl.NumberFormat('es-BO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(value);
    }
  }))
})
</script>
@endpush
