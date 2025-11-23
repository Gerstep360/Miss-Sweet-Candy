{{-- filepath: resources/views/admin/bitacora/show.blade.php --}}
<x-layouts.app :title="__('Detalle de Auditoría')">
    <div class="min-h-screen bg-zinc-950 text-white">
        <header class="sticky top-0 z-20 bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('bitacora.index') }}"
                           class="inline-flex items-center gap-2 text-zinc-300 hover:text-white px-2 py-1 rounded-lg hover:bg-zinc-800 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Volver
                        </a>
                        <div class="w-8 h-8 bg-amber-500 rounded-lg grid place-items-center">
                            <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <h1 class="text-lg sm:text-xl font-semibold">Detalle de Auditoría</h1>
                    </div>
                </div>
            </div>
        </header>

        <main class="py-8">
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Resumen -->
                @php
                    $accion = strtolower($auditoria->accion ?? '');
                    $badge  = 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40'; // default

                    if (str_contains($accion, 'elimin')) {
                        $badge = 'bg-red-500/20 text-red-400 ring-1 ring-red-500/30';
                    } elseif (str_contains($accion, 'crea') || $accion === 'login') {
                        $badge = 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30';
                    } elseif (str_contains($accion, 'actual') || str_contains($accion, 'modif') || str_contains($accion, 'update')) {
                        $badge = 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30';
                    }
                @endphp

                <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5 sm:p-6">
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ strtoupper($auditoria->accion) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-zinc-800/80 text-zinc-300 ring-1 ring-zinc-700">
                            {{ $auditoria->entidad ?? 'Entidad' }} @if($auditoria->entidad_id) #{{ $auditoria->entidad_id }} @endif
                        </span>
                        <span class="text-zinc-500 text-xs font-mono">Log ID: {{ $auditoria->id }}</span>
                    </div>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                            <p class="text-zinc-400 text-xs mb-1">Usuario</p>
                            <p class="font-medium">
                                {{ optional($auditoria->usuario)->name ?? 'Sistema' }}
                                <span class="text-zinc-500 block text-xs">
                                    {{ optional($auditoria->usuario)->email ?? '' }}
                                </span>
                            </p>
                        </div>
                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                            <p class="text-zinc-400 text-xs mb-1">Fecha y hora</p>
                            <p class="font-medium">
                                {{ $auditoria->created_at
                                    ? \Illuminate\Support\Carbon::parse($auditoria->created_at)->format('d/m/Y H:i:s')
                                    : '—' }}
                                </p>
                        </div>
                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                            <p class="text-zinc-400 text-xs mb-1">IP</p>
                            <p class="font-mono text-sm">{{ $auditoria->ip ?? '—' }}</p>
                        </div>
                        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                            <p class="text-zinc-400 text-xs mb-1">Entidad</p>
                            <p class="font-medium">
                                {{ $auditoria->entidad ?? '—' }}
                                @if($auditoria->entidad_id)
                                    <span class="text-zinc-500 text-sm">#{{ $auditoria->entidad_id }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Información del agente -->
                <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-5 sm:p-6">
                    <h2 class="text-base sm:text-lg font-semibold mb-3">Agente de Usuario</h2>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                        <p class="text-zinc-400 text-xs mb-2">User-Agent</p>
                        <div class="flex items-start justify-between gap-3">
                            <code class="text-zinc-300 text-xs sm:text-sm break-words">{{ $auditoria->user_agent ?? '—' }}</code>
                            @if($auditoria->user_agent)
                                <button type="button" onclick="copyUA()"
                                        class="shrink-0 inline-flex items-center gap-2 bg-amber-500 text-black px-3 py-2 rounded-lg font-medium hover:bg-amber-400 transition">
                                    Copiar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('bitacora.index') }}"
                       class="inline-flex items-center justify-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-white px-4 py-2 rounded-lg transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Volver al listado
                    </a>
                </div>
            </section>
        </main>
    </div>

    <script>
        function copyUA() {
            const text = @json($auditoria->user_agent ?? '');
            if (!text) return;
            navigator.clipboard?.writeText(text).then(() => {
                alert('User-Agent copiado al portapapeles');
            });
        }
    </script>
</x-layouts.app>
