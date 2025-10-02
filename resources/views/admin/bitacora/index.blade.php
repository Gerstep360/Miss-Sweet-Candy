{{-- filepath: resources/views/admin/bitacora/index.blade.php --}}
<x-layouts.app :title="__('Bitácora de Auditoría')">
    <div class="min-h-screen bg-zinc-950 text-white">
        <header class="sticky top-0 z-20 bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg grid place-items-center">
                            <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <h1 class="text-lg sm:text-xl font-semibold">Bitácora de Auditoría</h1>
                    </div>

                    <a href="{{ route('dashboard') }}"
                       class="hidden sm:inline-flex items-center gap-2 bg-amber-500 text-black px-3 py-2 rounded-lg font-medium hover:bg-amber-400 transition-colors">
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </header>

        <main class="py-8">
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5 sm:p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            <span class="w-2 h-2 bg-amber-400 rounded-full mr-2"></span>
                            Historial de actividad del sistema
                        </div>
                        <div class="text-zinc-400 text-sm">
                            Mostrando <span class="text-white font-semibold">{{ $auditorias->count() }}</span> de
                            <span class="text-white font-semibold">{{ $auditorias->total() }}</span> registros
                        </div>
                    </div>
                </div>

                @forelse ($auditorias as $log)
@php
    $accion = strtolower($log->accion ?? '');
    $badge = 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40'; // default

    if (str_contains($accion, 'elimin')) {
        $badge = 'bg-red-500/20 text-red-400 ring-1 ring-red-500/30';
    } elseif (str_contains($accion, 'crea') || $accion === 'login') {
        $badge = 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30';
    } elseif (str_contains($accion, 'actual') || str_contains($accion, 'modif') || str_contains($accion, 'update')) {
        $badge = 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30';
    }
@endphp

                    <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-4 sm:p-5 mb-3 hover:border-amber-500/30 transition">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                            <!-- Col izquierda -->
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] sm:text-xs font-bold {{ $badge }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        {{ strtoupper($log->accion) }}
                                    </span>

                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] sm:text-xs font-medium bg-zinc-800/80 text-zinc-300 ring-1 ring-zinc-700">
                                        {{ $log->entidad ?? 'Entidad' }} @if($log->entidad_id) #{{ $log->entidad_id }} @endif
                                    </span>

                                    <span class="text-zinc-500 text-[11px] sm:text-xs font-mono">ID: {{ $log->id }}</span>
                                </div>

                                <div class="grid md:grid-cols-2 gap-2 text-sm">
                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>
                                            {{ optional($log->usuario)->name ?? 'Sistema' }}
                                            <span class="text-zinc-500">
                                                {{ optional($log->usuario)->email ? '· ' . $log->usuario->email : '' }}
                                            </span>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $log->created_at_formatted ?? '—' }}</span>
                                    </div>

                                    <div class="flex items-center gap-2 text-zinc-400">
                                        <svg class="w-4 h-4 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 12l4-4m-4 4l4 4"/>
                                        </svg>
                                        <span class="font-mono">{{ $log->ip ?? '—' }}</span>
                                    </div>

                                    <div class="text-zinc-400 truncate">
                                        <span class="text-xs sm:text-[13px]">{{ $log->user_agent ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Col derecha (acciones) -->
                            <div class="flex items-center gap-2 lg:min-w-[180px] lg:justify-end">
                                <a href="{{ route('bitacora.show', $log->id) }}"
                                   class="inline-flex items-center gap-2 bg-amber-500 text-black px-3 py-2 rounded-lg font-medium hover:bg-amber-400 transition">
                                    Ver detalle
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-10 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-zinc-800 rounded-full grid place-items-center">
                            <svg class="w-8 h-8 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            </svg>
                        </div>
                        <p class="text-zinc-300 font-medium">Aún no hay registros en la bitácora</p>
                        <p class="text-zinc-500 text-sm">Cuando haya actividad, aparecerá aquí.</p>
                    </div>
                @endforelse

                @if ($auditorias->hasPages())
                    <div class="mt-6">{{ $auditorias->links() }}</div>
                @endif
            </section>
        </main>
    </div>
</x-layouts.app>
