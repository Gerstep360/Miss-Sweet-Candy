{{-- filepath: resources/views/reportes/exportar/index.blade.php --}}
<x-layouts.app :title="__('Exportar Reportes')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="dashboard-card mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-2">
                            <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Exportar Reportes
                        </h1>
                        <p class="text-zinc-400">Genera y descarga reportes en formato PDF o Excel</p>
                    </div>
                </div>
            </div>

            {{-- Tarjetas de reportes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                {{-- Reporte de Inventario --}}
                <a href="{{ route('reportes.exportar.inventario.form') }}" class="dashboard-card group hover:border-amber-500/40 transition-all transform hover:scale-105">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl grid place-items-center group-hover:bg-purple-500/30 transition-colors">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <svg class="w-5 h-5 text-zinc-600 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Inventario</h3>
                    <p class="text-sm text-zinc-400">Stock actual, productos críticos y bajos</p>
                </a>

                {{-- Reporte de Cobros Caja --}}
                <a href="{{ route('reportes.exportar.cobro-caja.form') }}" class="dashboard-card group hover:border-amber-500/40 transition-all transform hover:scale-105">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl grid place-items-center group-hover:bg-green-500/30 transition-colors">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <svg class="w-5 h-5 text-zinc-600 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Cobros de Caja</h3>
                    <p class="text-sm text-zinc-400">Transacciones y métodos de pago</p>
                </a>

                {{-- Reporte de Arqueos --}}
                <a href="{{ route('reportes.exportar.arqueos.form') }}" class="dashboard-card group hover:border-amber-500/40 transition-all transform hover:scale-105">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl grid place-items-center group-hover:bg-blue-500/30 transition-colors">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <svg class="w-5 h-5 text-zinc-600 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Arqueos / Cierres</h3>
                    <p class="text-sm text-zinc-400">Cierres de caja y diferencias</p>
                </a>

                {{-- Reporte de Pedidos --}}
                <a href="{{ route('reportes.exportar.pedidos.form') }}" class="dashboard-card group hover:border-amber-500/40 transition-all transform hover:scale-105">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center group-hover:bg-amber-500/30 transition-colors">
                            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <svg class="w-5 h-5 text-zinc-600 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Pedidos</h3>
                    <p class="text-sm text-zinc-400">Historial de pedidos y estados</p>
                </a>

                {{-- Reporte de Promociones --}}
                <a href="{{ route('reportes.exportar.promociones.form') }}" class="dashboard-card group hover:border-amber-500/40 transition-all transform hover:scale-105">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-red-500/20 rounded-xl grid place-items-center group-hover:bg-red-500/30 transition-colors">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <svg class="w-5 h-5 text-zinc-600 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Promociones</h3>
                    <p class="text-sm text-zinc-400">Promociones activas y vencidas</p>
                </a>

            </div>

        </div>
    </div>
</x-layouts.app>
