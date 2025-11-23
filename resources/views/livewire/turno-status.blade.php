<div wire:poll.30s="refreshTurno" class="mb-6 sm:mb-8">
    {{-- Sin turno activo --}}
    @if (! $hayTurno)
        <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-2 border-amber-500/30 rounded-xl sm:rounded-2xl p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-start gap-3 sm:gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-500/20 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg sm:text-xl font-bold text-white mb-1 sm:mb-2">No hay nadie de turno</h3>
                        <p class="text-zinc-400 text-sm sm:text-base mb-3 sm:mb-4">Debes iniciar un turno antes de registrar ventas y cobros.</p>
                        @can('iniciar-turno')
                            <a href="{{ route('turnos_caja.iniciar.form') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm sm:text-base font-semibold rounded-lg sm:rounded-xl hover:from-amber-600 hover:to-orange-600 active:from-amber-700 active:to-orange-700 transition-all transform hover:scale-105 active:scale-95 min-h-[44px] touch-manipulation w-full sm:w-auto">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>Iniciar Turno</span>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

    {{-- Turno de otro cajero --}}
    @elseif ($hayTurno && ! $esMiTurno)
        <div class="bg-gradient-to-r from-red-500/10 to-rose-500/10 border-2 border-red-500/30 rounded-xl sm:rounded-2xl p-4 sm:p-6">
            <div class="flex items-start gap-3 sm:gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-red-500/20 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg sm:text-xl font-bold text-white mb-2 sm:mb-3">
                        <span class="truncate">{{ $turno['cajero']['name'] ?? '—' }}</span> está de turno
                    </h3>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 text-xs sm:text-sm text-zinc-400 mb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="truncate">Iniciado {{ $turno['tiempo_transcurrido'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="truncate">Duración: {{ $turno['duracion_formateada'] ?? '—' }}</span>
                        </div>
                    </div>
                    <p class="text-zinc-400 text-xs sm:text-sm">
                        No puedes registrar ventas ni cobros hasta que cierre su turno.
                    </p>
                </div>
            </div>
        </div>

    {{-- Mi turno activo --}}
    @else
        <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border-2 border-green-500/30 rounded-xl sm:rounded-2xl p-4 sm:p-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="flex items-start gap-3 sm:gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-green-500/20 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                            <h3 class="text-lg sm:text-xl font-bold text-white">Tu turno está activo</h3>
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse flex-shrink-0"></div>
                        </div>
                        <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3 mb-3 sm:mb-4">
                            <div class="bg-black/20 rounded-lg p-2.5 sm:p-3">
                                <div class="flex items-center gap-1.5 sm:gap-2 text-zinc-400 text-[10px] sm:text-xs mb-1">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Iniciado</span>
                                </div>
                                <p class="text-white font-semibold text-xs sm:text-sm truncate">{{ $turno['tiempo_transcurrido'] ?? '—' }}</p>
                            </div>
                            <div class="bg-black/20 rounded-lg p-2.5 sm:p-3">
                                <div class="flex items-center gap-1.5 sm:gap-2 text-zinc-400 text-[10px] sm:text-xs mb-1">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    <span>Duración</span>
                                </div>
                                <p class="text-white font-semibold text-xs sm:text-sm truncate">{{ $turno['duracion_formateada'] ?? '—' }}</p>
                            </div>
                            <div class="bg-black/20 rounded-lg p-2.5 sm:p-3 xs:col-span-2 sm:col-span-1">
                                <div class="flex items-center gap-1.5 sm:gap-2 text-zinc-400 text-[10px] sm:text-xs mb-1">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 10v1"/>
                                    </svg>
                                    <span>Monto Inicial</span>
                                </div>
                                @php
                                    $m = $turno['monto_inicial'] ?? 0;
                                    $monto = number_format($m, 2, ',', '.'); // es-BO style básico
                                @endphp
                                <p class="text-white font-semibold text-xs sm:text-sm truncate">Bs. {{ $monto }}</p>
                            </div>
                        </div>
                        <p class="text-zinc-400 text-xs sm:text-sm">
                            Puedes registrar ventas y cobros. Al finalizar tu jornada, debes realizar el cierre de caja.
                        </p>
                    </div>
                </div>
                @can('cerrar-turno')
                    <a href="{{ route('cierres_caja.create') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-red-500 to-rose-500 text-white text-sm sm:text-base font-semibold rounded-lg sm:rounded-xl hover:from-red-600 hover:to-rose-600 active:from-red-700 active:to-rose-700 transition-all transform hover:scale-105 active:scale-95 min-h-[44px] touch-manipulation w-full lg:w-auto lg:flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Cerrar Turno</span>
                    </a>
                @endcan
            </div>
        </div>
    @endif
</div>
