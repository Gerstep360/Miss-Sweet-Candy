<x-layouts.app>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6 text-white">Cumplimiento Sanitario (SENASAG)</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Card for Temperatures -->
            <a href="{{ route('sanitario.temperaturas') }}"
                class="block p-6 bg-zinc-800 rounded-lg border border-zinc-700 hover:border-amber-500 transition-colors">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-blue-500/10 rounded-lg text-blue-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-white">Registro de Temperaturas</h2>
                </div>
                <p class="text-zinc-400">Registrar temperaturas de equipos de frío y calor.</p>
            </a>

            <!-- Loop through Lists -->
            @foreach ($listas as $lista)
                <a href="{{ route('sanitario.show', $lista) }}"
                    class="block p-6 bg-zinc-800 rounded-lg border border-zinc-700 hover:border-amber-500 transition-colors">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-amber-500/10 rounded-lg text-amber-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-white">{{ $lista->nombre }}</h2>
                    </div>
                    <p class="text-zinc-400 mb-4">{{ $lista->descripcion ?? 'Lista de verificación sanitaria.' }}</p>
                    <div class="flex gap-2">
                        <span class="text-sm text-amber-500 hover:underline">Llenar ahora &rarr;</span>
                        <a href="{{ route('sanitario.historial', $lista) }}"
                            class="text-sm text-zinc-500 hover:text-white hover:underline ml-auto">Ver Historial</a>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.app>
