{{-- filepath: resources/views/reportes/ventas/index.blade.php --}}
<x-layouts.app :title="__('Reporte de Ventas')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="dashboard-card mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-2">
                            <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Reporte de Ventas
                        </h1>
                        <p class="text-zinc-400">Análisis detallado de ventas por período</p>
                    </div>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="dashboard-card mb-6">
                <form action="{{ route('reportes.ventas.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        {{-- Fecha inicio --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Fecha Inicio</label>
                            <input type="date" 
                                   name="fecha_inicio" 
                                   value="{{ $fechaInicio }}"
                                   class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        </div>

                        {{-- Fecha fin --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Fecha Fin</label>
                            <input type="date" 
                                   name="fecha_fin" 
                                   value="{{ $fechaFin }}"
                                   class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        </div>

                        {{-- Tipo de venta --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Tipo de Venta</label>
                            <select name="tipo_venta" 
                                    class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                <option value="todos" {{ $tipoVenta === 'todos' ? 'selected' : '' }}>Todos</option>
                                <option value="mesa" {{ $tipoVenta === 'mesa' ? 'selected' : '' }}>Mesa</option>
                                <option value="mostrador" {{ $tipoVenta === 'mostrador' ? 'selected' : '' }}>Mostrador</option>
                                <option value="web" {{ $tipoVenta === 'web' ? 'selected' : '' }}>En Línea</option>
                            </select>
                        </div>

                        {{-- Método de pago --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Método de Pago</label>
                            <select name="metodo_pago" 
                                    class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                <option value="todos" {{ $metodoPago === 'todos' ? 'selected' : '' }}>Todos</option>
                                <option value="efectivo" {{ $metodoPago === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="pos" {{ $metodoPago === 'pos' ? 'selected' : '' }}>POS</option>
                                <option value="qr" {{ $metodoPago === 'qr' ? 'selected' : '' }}>QR</option>
                            </select>
                        </div>

                        {{-- Cajero --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Cajero</label>
                            <select name="cajero_id" 
                                    class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                <option value="todos" {{ $cajero === 'todos' ? 'selected' : '' }}>Todos</option>
                                @foreach($cajeros as $c)
                                    <option value="{{ $c->id }}" {{ $cajero == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" 
                                class="px-6 py-2 bg-amber-500 hover:bg-amber-400 text-black font-semibold rounded-lg transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filtrar
                        </button>

                        <a href="{{ route('reportes.ventas.exportar-pdf', request()->query()) }}" 
                           class="px-6 py-2 bg-red-500 hover:bg-red-400 text-white font-semibold rounded-lg transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Exportar PDF
                        </a>

                        <a href="{{ route('reportes.ventas.exportar-excel', request()->query()) }}" 
                           class="px-6 py-2 bg-green-500 hover:bg-green-400 text-white font-semibold rounded-lg transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exportar Excel
                        </a>
                    </div>
                </form>
            </div>

            {{-- Resumen --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Total ventas --}}
                <div class="dashboard-card bg-gradient-to-br from-green-500/10 to-green-600/5 border-green-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-300 mb-1">Total Ventas</p>
                            <p class="text-3xl font-bold text-green-400">Bs {{ number_format($resumen['total_ventas'], 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Cantidad de ventas --}}
                <div class="dashboard-card bg-gradient-to-br from-blue-500/10 to-blue-600/5 border-blue-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-300 mb-1">Cantidad de Ventas</p>
                            <p class="text-3xl font-bold text-blue-400">{{ $resumen['cantidad_ventas'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Promedio por venta --}}
                <div class="dashboard-card bg-gradient-to-br from-amber-500/10 to-amber-600/5 border-amber-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-amber-300 mb-1">Promedio por Venta</p>
                            <p class="text-3xl font-bold text-amber-400">Bs {{ number_format($resumen['promedio_venta'], 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total items --}}
                <div class="dashboard-card bg-gradient-to-br from-purple-500/10 to-purple-600/5 border-purple-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-purple-300 mb-1">Total Items Vendidos</p>
                            <p class="text-3xl font-bold text-purple-400">{{ $resumen['total_items'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl grid place-items-center">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Ventas por tipo --}}
                <div class="dashboard-card">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Ventas por Tipo
                    </h3>
                    <div class="space-y-3">
                        @foreach($ventasPorTipo as $tipo)
                            <div class="bg-zinc-800/50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-white font-medium">
                                        @if($tipo->tipo === 'mesa') 🍽️ Mesa
                                        @elseif($tipo->tipo === 'mostrador') 🏪 Mostrador
                                        @else 🌐 En Línea
                                        @endif
                                    </span>
                                    <span class="text-green-400 font-bold">Bs {{ number_format($tipo->total, 2) }}</span>
                                </div>
                                <div class="text-sm text-zinc-400">{{ $tipo->cantidad }} ventas</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Ventas por método --}}
                <div class="dashboard-card">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Ventas por Método
                    </h3>
                    <div class="space-y-3">
                        @foreach($ventasPorMetodo as $metodo)
                            <div class="bg-zinc-800/50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-white font-medium">
                                        @if($metodo->metodo === 'efectivo') 💵 Efectivo
                                        @elseif($metodo->metodo === 'pos') 💳 POS
                                        @else 📱 QR
                                        @endif
                                    </span>
                                    <span class="text-green-400 font-bold">Bs {{ number_format($metodo->total, 2) }}</span>
                                </div>
                                <div class="text-sm text-zinc-400">{{ $metodo->cantidad }} transacciones</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Top productos --}}
                <div class="dashboard-card">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Top 10 Productos
                    </h3>
                    <div class="space-y-2 max-h-96 overflow-y-auto custom-scrollbar">
                        @foreach($topProductos as $producto)
                            <div class="bg-zinc-800/50 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-white text-sm font-medium">{{ $producto->nombre }}</span>
                                    <span class="text-amber-400 font-bold text-sm">{{ $producto->total_vendido }}</span>
                                </div>
                                <div class="text-xs text-zinc-400">Bs {{ number_format($producto->total_ingresos, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tabla de ventas --}}
            <div class="dashboard-card">
                <h3 class="text-xl font-bold text-white mb-4">Detalle de Ventas</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-zinc-700">
                                <th class="text-left text-sm font-semibold text-zinc-300 py-3 px-4">ID</th>
                                <th class="text-left text-sm font-semibold text-zinc-300 py-3 px-4">Fecha/Hora</th>
                                <th class="text-left text-sm font-semibold text-zinc-300 py-3 px-4">Pedido</th>
                                <th class="text-left text-sm font-semibold text-zinc-300 py-3 px-4">Tipo</th>
                                <th class="text-left text-sm font-semibold text-zinc-300 py-3 px-4">Método</th>
                                <th class="text-left text-sm font-semibold text-zinc-300 py-3 px-4">Cajero</th>
                                <th class="text-right text-sm font-semibold text-zinc-300 py-3 px-4">Importe</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($ventas as $venta)
                                <tr class="hover:bg-zinc-800/50 transition-colors">
                                    <td class="py-3 px-4 text-sm text-zinc-400">#{{ $venta->id }}</td>
                                    <td class="py-3 px-4 text-sm text-white">
                                        {{ $venta->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-amber-400 font-medium">
                                        <a href="{{ route('pedidos.show', $venta->pedido_id) }}" class="hover:underline">
                                            #{{ $venta->pedido_id }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded
                                            @if($venta->pedido->tipo === 'mesa') bg-blue-500/20 text-blue-400
                                            @elseif($venta->pedido->tipo === 'mostrador') bg-purple-500/20 text-purple-400
                                            @else bg-green-500/20 text-green-400
                                            @endif">
                                            {{ ucfirst($venta->pedido->tipo) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded
                                            @if($venta->metodo === 'efectivo') bg-green-500/20 text-green-400
                                            @elseif($venta->metodo === 'pos') bg-blue-500/20 text-blue-400
                                            @else bg-purple-500/20 text-purple-400
                                            @endif">
                                            {{ ucfirst($venta->metodo) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-zinc-300">
                                        {{ $venta->cajero->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-right font-bold text-green-400">
                                        Bs {{ number_format($venta->importe, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <div class="text-zinc-500">
                                            <svg class="w-16 h-16 mx-auto mb-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-lg font-medium">No hay ventas en este período</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($ventas->hasPages())
                    <div class="mt-6">
                        {{ $ventas->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(39, 39, 42, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.5);
        }
    </style>
</x-layouts.app>
