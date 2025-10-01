<x-layouts.app :title="__('Reporte Diario de Cobros')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('cobro_caja.index') }}" 
                           class="w-10 h-10 bg-zinc-700 hover:bg-zinc-600 rounded-lg flex items-center justify-center transition-all hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Reporte Diario de Cobros</h1>
                            <p class="text-sm text-zinc-400">Resumen de transacciones y estadísticas</p>
                        </div>
                    </div>
                    <button onclick="window.print()" 
                            class="bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center gap-2 hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimir
                    </button>
                </div>
            </div>

            <!-- Selector de fecha -->
            <div class="dashboard-card mb-6 print:hidden">
                <form method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-zinc-300 font-medium mb-2 text-sm">Seleccionar fecha</label>
                        <input type="date" 
                               name="fecha" 
                               value="{{ $fecha instanceof \Carbon\Carbon ? $fecha->format('Y-m-d') : $fecha }}"
                               max="{{ now()->format('Y-m-d') }}"
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-500 text-white py-2.5 px-6 rounded-lg transition-all duration-200 flex items-center gap-2 hover:scale-105 active:scale-95 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Buscar
                    </button>
                </form>
            </div>

            <!-- Fecha del reporte -->
            <div class="dashboard-card bg-gradient-to-r from-blue-500/20 to-blue-600/10 border-blue-500/30 mb-6">
                <div class="text-center">
                    <p class="text-blue-300 text-sm mb-1">Reporte del día</p>
                    <h2 class="text-3xl font-bold text-white">
                        {{ is_string($fecha) ? \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') : $fecha->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                    </h2>
                </div>
            </div>

            <!-- Resumen de totales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
                <!-- Total General -->
                <div class="dashboard-card bg-gradient-to-br from-green-500/20 to-green-600/10 border-green-500/30">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-zinc-400 text-sm mb-1">Total General</p>
                            <p class="text-2xl font-bold text-green-400">${{ number_format($totalGeneral, 2) }}</p>
                            <p class="text-zinc-500 text-xs mt-1">{{ $cobros->count() }} transacciones</p>
                        </div>
                    </div>
                </div>

                <!-- Efectivo -->
                <div class="dashboard-card bg-gradient-to-br from-amber-500/20 to-amber-600/10 border-amber-500/30">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-amber-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-zinc-400 text-sm mb-1">Efectivo</p>
                            <p class="text-2xl font-bold text-amber-400">${{ number_format($totalEfectivo, 2) }}</p>
                            <p class="text-zinc-500 text-xs mt-1">{{ $cantidadEfectivo }} pagos</p>
                        </div>
                    </div>
                </div>

                <!-- POS/Tarjeta -->
                <div class="dashboard-card bg-gradient-to-br from-blue-500/20 to-blue-600/10 border-blue-500/30">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-zinc-400 text-sm mb-1">POS/Tarjeta</p>
                            <p class="text-2xl font-bold text-blue-400">${{ number_format($totalPos, 2) }}</p>
                            <p class="text-zinc-500 text-xs mt-1">{{ $cantidadPos }} pagos</p>
                        </div>
                    </div>
                </div>

                <!-- QR/Transferencia -->
                <div class="dashboard-card bg-gradient-to-br from-purple-500/20 to-purple-600/10 border-purple-500/30">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-zinc-400 text-sm mb-1">QR/Transfer</p>
                            <p class="text-2xl font-bold text-purple-400">${{ number_format($totalQr, 2) }}</p>
                            <p class="text-zinc-500 text-xs mt-1">{{ $cantidadQr }} pagos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de distribución (visual con barras CSS) -->
            <div class="dashboard-card mb-6">
                <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                    Distribución por Método de Pago
                </h2>

                <div class="space-y-4">
                    @php
                        $maxTotal = max($totalEfectivo, $totalPos, $totalQr) ?: 1;
                    @endphp

                    <!-- Efectivo -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-zinc-300 font-medium">💵 Efectivo</span>
                            <span class="text-amber-400 font-bold">${{ number_format($totalEfectivo, 2) }}</span>
                        </div>
                        <div class="w-full bg-zinc-800 rounded-full h-4 overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-600 to-amber-400 h-full rounded-full transition-all duration-500" 
                                 style="width: {{ $totalGeneral > 0 ? ($totalEfectivo / $totalGeneral * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-zinc-500 text-xs mt-1">{{ $totalGeneral > 0 ? number_format($totalEfectivo / $totalGeneral * 100, 1) : 0 }}% del total</p>
                    </div>

                    <!-- POS -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-zinc-300 font-medium">💳 POS/Tarjeta</span>
                            <span class="text-blue-400 font-bold">${{ number_format($totalPos, 2) }}</span>
                        </div>
                        <div class="w-full bg-zinc-800 rounded-full h-4 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-400 h-full rounded-full transition-all duration-500" 
                                 style="width: {{ $totalGeneral > 0 ? ($totalPos / $totalGeneral * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-zinc-500 text-xs mt-1">{{ $totalGeneral > 0 ? number_format($totalPos / $totalGeneral * 100, 1) : 0 }}% del total</p>
                    </div>

                    <!-- QR -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-zinc-300 font-medium">📱 QR/Transferencia</span>
                            <span class="text-purple-400 font-bold">${{ number_format($totalQr, 2) }}</span>
                        </div>
                        <div class="w-full bg-zinc-800 rounded-full h-4 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-600 to-purple-400 h-full rounded-full transition-all duration-500" 
                                 style="width: {{ $totalGeneral > 0 ? ($totalQr / $totalGeneral * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-zinc-500 text-xs mt-1">{{ $totalGeneral > 0 ? number_format($totalQr / $totalGeneral * 100, 1) : 0 }}% del total</p>
                    </div>
                </div>
            </div>

            <!-- Detalle de transacciones -->
            <div class="dashboard-card">
                <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Detalle de Transacciones ({{ $cobros->count() }})
                </h2>

                @if($cobros->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-zinc-700">
                                    <th class="text-left py-3 px-4 text-zinc-400 font-semibold text-sm">Hora</th>
                                    <th class="text-left py-3 px-4 text-zinc-400 font-semibold text-sm">Comprobante</th>
                                    <th class="text-left py-3 px-4 text-zinc-400 font-semibold text-sm">Pedido</th>
                                    <th class="text-left py-3 px-4 text-zinc-400 font-semibold text-sm">Cliente</th>
                                    <th class="text-left py-3 px-4 text-zinc-400 font-semibold text-sm">Método</th>
                                    <th class="text-left py-3 px-4 text-zinc-400 font-semibold text-sm">Cajero</th>
                                    <th class="text-right py-3 px-4 text-zinc-400 font-semibold text-sm">Importe</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800">
                                @foreach($cobros as $cobro)
                                    <tr class="hover:bg-zinc-800/50 transition-colors">
                                        <td class="py-3 px-4 text-zinc-300 text-sm">
                                            {{ $cobro->created_at->format('H:i') }}
                                        </td>
                                        <td class="py-3 px-4 text-zinc-300 text-sm font-mono">
                                            {{ $cobro->comprobante }}
                                        </td>
                                        <td class="py-3 px-4 text-zinc-300 text-sm">
                                            <a href="{{ route('cobro_caja.show', $cobro->pedido) }}" 
                                               class="text-blue-400 hover:text-blue-300 transition-colors print:text-zinc-900">
                                                #{{ $cobro->pedido->id }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-zinc-300 text-sm">
                                            {{ $cobro->pedido->cliente->name ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            @if($cobro->metodo === 'efectivo')
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-500/20 text-amber-400 text-xs font-semibold">
                                                    💵 Efectivo
                                                </span>
                                            @elseif($cobro->metodo === 'pos')
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-500/20 text-blue-400 text-xs font-semibold">
                                                    💳 POS
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-purple-500/20 text-purple-400 text-xs font-semibold">
                                                    📱 QR
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-zinc-300 text-sm">
                                            {{ $cobro->cajero->name ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 text-right text-green-400 font-bold text-sm">
                                            ${{ number_format($cobro->importe, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-zinc-700">
                                <tr>
                                    <td colspan="6" class="py-4 px-4 text-right text-white font-bold text-lg">
                                        TOTAL:
                                    </td>
                                    <td class="py-4 px-4 text-right text-green-400 font-bold text-xl">
                                        ${{ number_format($totalGeneral, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-zinc-700/30 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-base text-zinc-400 font-medium mb-2">No hay transacciones registradas</p>
                        <p class="text-sm text-zinc-500">No se encontraron cobros para esta fecha</p>
                    </div>
                @endif
            </div>

            <!-- Información adicional (solo impresión) -->
            <div class="hidden print:block mt-8 pt-6 border-t border-zinc-700">
                <div class="text-center text-zinc-500 text-sm">
                    <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
                    <p class="mt-1">☕ CAFETERÍA - Sistema de Gestión</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white !important;
            }
            .dashboard-card {
                background: white !important;
                border: 1px solid #e5e7eb !important;
                box-shadow: none !important;
            }
            .text-white { color: #000 !important; }
            .text-zinc-300,
            .text-zinc-400 { color: #374151 !important; }
            .text-zinc-500 { color: #6b7280 !important; }
            .bg-zinc-800,
            .bg-zinc-700 { background: #f3f4f6 !important; }
            nav,
            button,
            a[href]:not([href*="show"]),
            .print\\:hidden {
                display: none !important;
            }
        }
    </style>
</x-layouts.app>