<x-layouts.app title="Mis Pedidos | Miss Sweet Candy">
    @php
        $pedidosData = $pedidos->map(function ($p) {
            return [
                'id' => $p->id,
                'token' => $p->token,
                'estado' => $p->estado,
                'eta_minutes' => $p->eta_minutes,
                'tipo' => $p->tipo,
                'created_at' => optional($p->created_at)->toISOString(),
                'cliente_id' => $p->cliente_id,
            ];
        });
    @endphp

    <div class="container mx-auto px-4 py-6" data-user-id="{{ auth()->id() }}">

        {{-- Data inicial para JS (hidden) --}}
        <script id="initial-pedidos-data" type="application/json">
        @json($pedidosData)
    </script>

        {{-- Header de pedidos activos --}}
        <div class="mb-6 {{ $pedidos->isEmpty() ? 'hidden' : '' }}" id="pedidos-header">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Mis Pedidos Activos
                <span id="pedidos-count" class="ml-2 text-lg font-normal text-gray-500">({{ $pedidos->count() }})</span>
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                <span class="inline-flex items-center gap-1">
                    <svg class="w-4 h-4 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <circle cx="10" cy="10" r="4" />
                    </svg>
                    Actualizando en tiempo real • Haz clic para ver detalles
                </span>
            </p>
        </div>

        {{-- Empty state --}}
        <div id="empty-state" class="text-center py-16 {{ $pedidos->isEmpty() ? '' : 'hidden' }}">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No tienes pedidos activos</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-sm mx-auto">
                Cuando realices un pedido, aparecerá aquí con su número de turno y estado en tiempo real.
            </p>
            <a href="{{ route('turnos.turnero.monitor') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Ver Monitor Público
            </a>
        </div>

        {{-- Grid de tarjetas (dinámico con JS) --}}
        <div id="cards-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

        {{-- Monitor link bottom --}}
        <div class="mt-8 text-center {{ $pedidos->isEmpty() ? 'hidden' : '' }}" id="monitor-link">
            <a href="{{ route('turnos.turnero.monitor') }}"
                class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Ver Monitor Completo
            </a>
        </div>
    </div>

    @vite(['resources/css/turnero/cliente-index.css', 'resources/js/turnero/cliente-index.js'])
</x-layouts.app>
