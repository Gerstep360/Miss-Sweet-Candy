{{-- filepath: resources/views/admin/auditoria/show.blade.php --}}
<x-layouts.app :title="__('Detalle de Auditoría')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('auditoria.index') }}" 
                           class="inline-flex items-center gap-2 text-zinc-300 hover:text-white px-2 py-1 rounded-lg hover:bg-zinc-800 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Volver
                        </a>
                        <div class="w-10 h-10 bg-amber-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Detalle de Auditoría</h1>
                            <p class="text-sm text-zinc-400">Información completa del registro</p>
                        </div>
                    </div>
                </div>

                @php
                    $accion = strtolower($auditoria->accion ?? '');
                    $badge = 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40';
                    if (str_contains($accion, 'elimin')) {
                        $badge = 'bg-red-500/20 text-red-400 ring-1 ring-red-500/30';
                    } elseif (str_contains($accion, 'crea') || $accion === 'login') {
                        $badge = 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30';
                    } elseif (str_contains($accion, 'actual') || str_contains($accion, 'modif') || str_contains($accion, 'update')) {
                        $badge = 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30';
                    }
                @endphp

                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ strtoupper($auditoria->accion) }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-zinc-800/80 text-zinc-300 ring-1 ring-zinc-700">
                        {{ $auditoria->entidad ?? 'Entidad' }} @if($auditoria->entidad_id) #{{ $auditoria->entidad_id }} @endif
                    </span>
                    <span class="text-zinc-500 text-xs font-mono">ID: {{ $auditoria->id }}</span>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                        <p class="text-zinc-400 text-xs mb-1">Usuario</p>
                        <p class="font-medium text-white">
                            {{ optional($auditoria->usuario)->name ?? 'Sistema' }}
                            <span class="text-zinc-500 block text-xs mt-1">
                                {{ optional($auditoria->usuario)->email ?? '' }}
                            </span>
                        </p>
                    </div>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                        <p class="text-zinc-400 text-xs mb-1">Fecha y hora</p>
                        <p class="font-medium text-white">{{ $auditoria->created_at_formatted ?? '—' }}</p>
                    </div>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                        <p class="text-zinc-400 text-xs mb-1">IP</p>
                        <p class="font-mono text-sm text-white">{{ $auditoria->ip ?? '—' }}</p>
                    </div>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                        <p class="text-zinc-400 text-xs mb-1">Entidad</p>
                        <p class="font-medium text-white">
                            {{ $auditoria->entidad ?? '—' }}
                            @if($auditoria->entidad_id)
                                <span class="text-zinc-500 text-sm">#{{ $auditoria->entidad_id }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- User Agent -->
            <div class="dashboard-card mb-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Agente de Usuario
                </h2>
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
                    <div class="flex items-start justify-between gap-3">
                        <code class="text-zinc-300 text-xs sm:text-sm break-words flex-1">{{ $auditoria->user_agent ?? '—' }}</code>
                        @if($auditoria->user_agent)
                            <button type="button" onclick="copyUA()" 
                                    class="shrink-0 inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white px-3 py-2 rounded-lg font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copiar
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Registros relacionados -->
            @if(isset($relacionados) && $relacionados->count() > 0)
                <div class="dashboard-card">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Registros Relacionados
                    </h2>
                    <div class="space-y-2">
                        @foreach($relacionados as $rel)
                            <a href="{{ route('auditoria.show', $rel->id) }}" 
                               class="block bg-zinc-900 border border-zinc-800 rounded-xl p-3 hover:border-amber-500/30 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-medium text-zinc-300">{{ $rel->accion }}</span>
                                        <span class="text-xs text-zinc-500 ml-2">{{ $rel->entidad }}</span>
                                    </div>
                                    <span class="text-xs text-zinc-500">{{ $rel->created_at_formatted }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyUA() {
            const text = @json($auditoria->user_agent ?? '');
            if (!text) return;
            navigator.clipboard?.writeText(text).then(() => {
                // Mostrar notificación temporal
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Copiado';
                btn.classList.add('bg-green-600', 'hover:bg-green-500');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('bg-green-600', 'hover:bg-green-500');
                }, 2000);
            });
        }
    </script>
</x-layouts.app>
