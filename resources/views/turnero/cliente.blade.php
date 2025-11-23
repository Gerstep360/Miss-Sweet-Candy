<x-layouts.app title="Tu turno | Miss Sweet Candy">
    @php
        $estado = $pedido->estado ?? 'pendiente';

        $stepIndex = match ($estado) {
            'pendiente', 'confirmado' => 0,
            'en_preparacion' => 1,
            'preparado' => 2,
            default => 0,
        };

        $estadoLabel = match ($estado) {
            'pendiente' => 'EN COLA',
            'confirmado' => 'CONFIRMADO',
            'en_preparacion' => 'PREPARANDO',
            'preparado' => 'LISTO PARA RETIRAR',
            default => strtoupper($estado),
        };

        $estadoClass = match ($estado) {
            'pendiente', 'confirmado' => 'bg-amber-500/10 text-amber-200 border-amber-500/30',
            'en_preparacion' => 'bg-sky-500/10 text-sky-200 border-sky-500/30',
            'preparado' => 'bg-green-500/10 text-green-200 border-green-500/30',
            default => 'bg-zinc-500/10 text-zinc-200 border-zinc-500/30',
        };

        $tipo = strtoupper($pedido->tipo ?? 'WEB');
    @endphp

    <div class="min-h-screen bg-zinc-950 text-zinc-100 relative overflow-hidden" id="turnero-cliente"
        data-token="{{ $pedido->token }}" data-estado="{{ strtolower($estado) }}"
        data-eta="{{ $pedido->eta_minutes ?? '' }}" data-tipo="{{ strtolower($pedido->tipo ?? 'web') }}">
        {{-- fondo sutil tipo monitor --}}
        <div class="pointer-events-none absolute inset-0 opacity-30"
            style="background:
                radial-gradient(800px circle at 15% -10%, rgba(245,158,11,.18), transparent 40%),
                radial-gradient(900px circle at 85% 0%, rgba(34,197,94,.10), transparent 45%),
                linear-gradient(to bottom, rgba(255,255,255,.02), transparent 30%),
                repeating-linear-gradient(90deg, rgba(255,255,255,.03) 0, rgba(255,255,255,.03) 1px, transparent 1px, transparent 40px),
                repeating-linear-gradient(0deg, rgba(255,255,255,.03) 0, rgba(255,255,255,.03) 1px, transparent 1px, transparent 40px);">
        </div>

        <main
            class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 flex items-center justify-center min-h-screen">
            <section class="w-full max-w-md sm:max-w-lg">
                {{-- TICKET --}}
                <div
                    class="ticket-shell relative rounded-3xl border border-zinc-800 bg-zinc-950/90 backdrop-blur shadow-2xl overflow-hidden">
                    {{-- header --}}
                    <div class="px-5 sm:px-7 py-5 sm:py-6 flex items-center gap-3">
                        <span
                            class="w-11 h-11 bg-amber-500 rounded-2xl grid place-items-center ring-1 ring-black/10 shadow">
                            <svg class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a2 2 0 01-2-2v-4zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                            </svg>
                        </span>
                        <div>
                            <div class="text-lg sm:text-xl font-semibold tracking-tight text-zinc-50">Miss Sweet Candy
                            </div>
                            <div class="text-xs text-zinc-300/80 -mt-0.5">Seguimiento de tu pedido</div>
                        </div>
                    </div>

                    {{-- linea perforada --}}
                    <div class="ticket-divider"></div>

                    {{-- cuerpo --}}
                    <div class="px-5 sm:px-7 py-6 sm:py-7 space-y-6">
                        {{-- token + estado --}}
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-[11px] tracking-[0.35em] text-zinc-400 uppercase">Tu turno</div>
                                <div id="token" class="mt-1 font-black tracking-[0.18em] text-white leading-none"
                                    style="font-size: clamp(2.4rem, 6vw, 3.6rem);">
                                    {{ $pedido->token }}
                                </div>

                                <div id="estado-pill"
                                    class="mt-3 inline-flex items-center gap-2 px-2.5 py-1 rounded-full border text-xs font-bold tracking-widest {{ $estadoClass }}">
                                    {{ $estadoLabel }}
                                </div>
                            </div>

                            <div class="shrink-0 text-right">
                                <div class="text-[10px] tracking-widest text-zinc-400 uppercase">Tipo</div>
                                <div id="tipo"
                                    class="mt-1 inline-flex items-center px-2.5 py-1 rounded-full bg-zinc-900/60 border border-zinc-800 text-xs font-extrabold tracking-widest text-zinc-100">
                                    {{ $tipo }}
                                </div>
                            </div>
                        </div>

                        {{-- ETA --}}
                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/40 px-4 py-3">
                                <div class="text-[10px] tracking-widest text-zinc-400 uppercase">ETA</div>
                                <div id="eta"
                                    class="mt-1 text-2xl sm:text-3xl font-black text-amber-200 tabular-nums">
                                    {{ $pedido->eta_minutes ? ($pedido->eta_minutes <= 1 ? '1 min' : $pedido->eta_minutes . ' min') : '—' }}
                                </div>
                                <div class="text-xs text-zinc-400 mt-0.5">Tiempo estimado</div>
                            </div>

                            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/40 px-4 py-3">
                                <div class="text-[10px] tracking-widest text-zinc-400 uppercase">Hora</div>
                                <div id="hora" class="mt-1 text-lg sm:text-xl font-bold text-zinc-100">
                                    {{ optional($pedido->created_at)->format('H:i') ?? '—' }}
                                </div>
                                <div class="text-xs text-zinc-400 mt-0.5">Registro</div>
                            </div>
                        </div>

                        {{-- progreso mejorado con UX --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="text-[11px] tracking-[0.25em] text-zinc-400 uppercase">Progreso del Pedido
                                </div>
                                <div class="text-xs text-zinc-400">
                                    @if ($stepIndex === 0)
                                        <span class="text-amber-300">⏳ Esperando</span>
                                    @elseif($stepIndex === 1)
                                        <span class="text-sky-300">👨‍🍳 En proceso</span>
                                    @else
                                        <span class="text-green-300">✅ Completado</span>
                                    @endif
                                </div>
                            </div>

                            @php
                                $steps = [
                                    [
                                        'label' => 'En cola',
                                        'icon' =>
                                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                                        'color' => 'amber',
                                        'desc' => 'Pedido recibido',
                                    ],
                                    [
                                        'label' => 'Preparando',
                                        'icon' =>
                                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                                        'color' => 'sky',
                                        'desc' => 'En la cocina',
                                    ],
                                    [
                                        'label' => 'Listo',
                                        'icon' =>
                                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                                        'color' => 'green',
                                        'desc' => 'Listo para retirar',
                                    ],
                                ];
                            @endphp

                            {{-- Barra de progreso visual --}}
                            <div class="relative pt-2">
                                <div class="flex items-center justify-between mb-2">
                                    @foreach ($steps as $i => $s)
                                        <div class="flex flex-col items-center gap-1 flex-1 relative z-10">
                                            {{-- Icono del step --}}
                                            <div class="step-icon w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300
                                                @if ($i <= $stepIndex) bg-{{ $s['color'] }}-500 text-white shadow-lg shadow-{{ $s['color'] }}-500/50 scale-110
                                                @else
                                                    bg-zinc-800 text-zinc-600 @endif"
                                                title="{{ $s['desc'] }}">
                                                {!! $s['icon'] !!}
                                            </div>

                                            {{-- Label --}}
                                            <span
                                                class="text-[10px] sm:text-xs font-semibold text-center
                                                @if ($i <= $stepIndex) text-{{ $s['color'] }}-200
                                                @else
                                                    text-zinc-500 @endif">
                                                {{ $s['label'] }}
                                            </span>

                                            {{-- Descripción --}}
                                            <span class="hidden sm:block text-[9px] text-zinc-400 text-center">
                                                {{ $s['desc'] }}
                                            </span>
                                        </div>

                                        {{-- Connector line --}}
                                        @if ($i < count($steps) - 1)
                                            <div
                                                class="flex-1 h-1 mx-2 rounded-full relative -mt-8
                                                @if ($i < $stepIndex) bg-{{ $s['color'] }}-500
                                                @else
                                                    bg-zinc-800 @endif">
                                                @if ($i < $stepIndex)
                                                    <div
                                                        class="absolute inset-0 bg-gradient-to-r from-{{ $s['color'] }}-400 to-{{ $s['color'] }}-600 rounded-full animate-pulse">
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- mensaje --}}
                        <div
                            class="rounded-2xl bg-zinc-900/40 border border-zinc-800 px-4 py-3 text-sm text-zinc-200 leading-relaxed">
                            Mantén esta pantalla abierta. Tu turno cambiará automáticamente.
                            Cuando aparezca como <span class="text-green-200 font-semibold">LISTO</span>, puedes retirar
                            tu pedido.
                        </div>
                    </div>

                    {{-- footer ticket --}}
                    <div
                        class="px-5 sm:px-7 py-4 bg-zinc-950 border-t border-zinc-800 flex items-center justify-between text-xs text-zinc-400">
                        <span id="clock">—</span>
                        <span class="tracking-widest uppercase">Gracias por tu compra 💛</span>
                    </div>
                </div>
            </section>
        </main>

        @vite(['resources/css/turnero/cliente.css', 'resources/js/turnero/cliente.js'])
    </div>
</x-layouts.app>
