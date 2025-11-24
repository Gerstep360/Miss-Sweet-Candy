{{-- filepath: resources/views/admin/auditoria/anomalias.blade.php --}}
<x-layouts.app :title="__('Detección de Anomalías')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('auditoria.index') }}" 
                           class="inline-flex items-center gap-2 text-zinc-300 hover:text-white px-2 py-1 rounded-lg hover:bg-zinc-800 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Volver
                        </a>
                        <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Detección de Anomalías</h1>
                            <p class="text-sm text-zinc-400">Patrones inusuales y actividades sospechosas</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('auditoria.anomalias.detectar') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white py-2.5 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-amber-500/30 hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Ejecutar Detección
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Lista de anomalías -->
            <div class="grid grid-cols-1 gap-4 sm:gap-6">
                @forelse($anomalias as $anomalia)
                    @php
                        $severidadColors = [
                            'critico' => 'bg-red-500/20 text-red-400 border-red-500/30',
                            'alto' => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                            'medio' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                            'bajo' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                        ];
                        $color = $severidadColors[$anomalia['severidad']] ?? $severidadColors['medio'];
                        $iconColors = [
                            'critico' => 'text-red-400',
                            'alto' => 'text-orange-400',
                            'medio' => 'text-yellow-400',
                            'bajo' => 'text-blue-400',
                        ];
                        $iconColor = $iconColors[$anomalia['severidad']] ?? $iconColors['medio'];
                    @endphp
                    <div class="dashboard-card border-2 {{ $color }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $color }}">
                                        {{ strtoupper($anomalia['severidad']) }}
                                    </span>
                                    <span class="text-sm text-zinc-400">{{ $anomalia['tipo'] }}</span>
                                </div>
                                <p class="font-medium text-white mb-3">{{ $anomalia['descripcion'] }}</p>
                                @if(isset($anomalia['detalles']))
                                    <div class="bg-zinc-900/50 rounded-lg p-3 space-y-1">
                                        @foreach($anomalia['detalles'] as $key => $value)
                                            <div class="text-xs text-zinc-300 flex items-center gap-2">
                                                <span class="font-medium text-zinc-400">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="text-white">{{ $value }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <span class="text-xs text-zinc-500 whitespace-nowrap ml-4">
                                {{ \Carbon\Carbon::parse($anomalia['fecha'])->format('d/m/Y H:i:s') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="dashboard-card text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-zinc-700/30 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-lg text-zinc-400 font-medium mb-2">No se detectaron anomalías</p>
                        <p class="text-sm text-zinc-500">El sistema está funcionando normalmente.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
