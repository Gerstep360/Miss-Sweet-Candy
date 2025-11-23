<x-layouts.app :title="__('Comprobante de Pago')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header con acciones mejorado -->
            <div class="dashboard-card mb-6 print:hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('cobro_caja.index') }}" 
                           class="w-10 h-10 bg-zinc-700 hover:bg-zinc-600 rounded-lg flex items-center justify-center transition-all hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center ring-2 ring-green-500/30">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Comprobante de Pago</h1>
                            <p class="text-sm text-zinc-400">{{ $cobro->comprobante }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="window.print()" 
                                class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center gap-2 hover:scale-105 active:scale-95 shadow-lg shadow-green-500/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Imprimir
                        </button>
                        <a href="{{ route('cobro_caja.comprobante.pdf', $cobro) }}" 
                           class="bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center gap-2 hover:scale-105 active:scale-95 shadow-lg shadow-red-500/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Comprobante rediseñado -->
            <div id="comprobante" class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Encabezado elegante -->
                <div class="relative bg-gradient-to-br from-amber-600 via-amber-500 to-orange-500 px-8 py-8">
                    <!-- Patrón decorativo -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    </div>
                    
                    <div class="relative flex items-start justify-between flex-wrap gap-4">
                        <!-- Logo y nombre -->
                        <div class="text-white">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center ring-4 ring-white/30">
                                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M2,21H20V19H2M20,8H18V5H20M20,3H4V13A4,4 0 0,0 8,17H14A4,4 0 0,0 18,13V10H20A2,2 0 0,0 22,8V5C22,3.89 21.1,3 20,3Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-4xl font-black mb-1 tracking-tight text-white">CAFETERÍA</h1>
                                    <p class="text-amber-100 text-lg font-medium">Café de especialidad</p>
                                </div>
                            </div>
                            <div class="mt-4 space-y-1.5 text-amber-50">
                                <p class="flex items-center gap-2 text-white">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Av. Principal #123, La Paz, Bolivia
                                </p>
                                <p class="flex items-center gap-2 text-white">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    (591) 123-4567
                                </p>
                                <p class="flex items-center gap-2 text-white">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    info@cafeteria.com
                                </p>
                            </div>
                        </div>
                        
                        <!-- Comprobante badge -->
                        <div class="bg-white rounded-xl px-6 py-4 shadow-2xl ring-4 ring-white/20">
                            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Comprobante</p>
                            <p class="text-2xl font-black text-gray-900">{{ $cobro->comprobante }}</p>
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <p class="text-xs text-gray-600">{{ $cobro->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-600">{{ $cobro->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del cliente y pedido -->
                <div class="px-8 py-6 bg-gradient-to-br from-gray-50 to-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cliente -->
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Cliente</h3>
                            </div>
                            @if($cobro->pedido->cliente)
                                <p class="text-lg font-bold text-gray-900 mb-1">{{ $cobro->pedido->cliente->name }}</p>
                                @if($cobro->pedido->cliente->email)
                                    <p class="text-sm text-gray-600 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $cobro->pedido->cliente->email }}
                                    </p>
                                @endif
                            @else
                                <p class="text-lg font-bold text-gray-900">Cliente General</p>
                            @endif
                            @if($cobro->pedido->telefono_contacto)
                                <p class="text-sm text-gray-600 flex items-center gap-2 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $cobro->pedido->telefono_contacto }}
                                </p>
                            @endif
                        </div>
                        
                        <!-- Pedido -->
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Detalles del Pedido</h3>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">N° Pedido:</span>
                                    <span class="text-sm font-bold text-gray-900">#{{ $cobro->pedido->id }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Tipo:</span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cobro->pedido->tipo === 'mesa' ? 'bg-blue-100 text-blue-700' : ($cobro->pedido->tipo === 'mostrador' ? 'bg-amber-100 text-amber-700' : 'bg-purple-100 text-purple-700') }}">
                                        {{ ucfirst($cobro->pedido->tipo) }}
                                    </span>
                                </div>
                                @if($cobro->pedido->mesa)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Mesa:</span>
                                        <span class="text-sm font-bold text-gray-900">{{ $cobro->pedido->mesa->nombre }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Fecha:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $cobro->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalle de productos elegante -->
                <div class="px-8 py-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Detalle de Productos</h3>
                    </div>
                    
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Producto</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Cant.</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">P. Unit.</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cobro->pedido->items as $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-gray-900">{{ $item->producto->nombre }}</div>
                                            @if($item->notas)
                                                <div class="text-xs text-gray-500 italic mt-1 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                                    </svg>
                                                    {{ $item->notas }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-900 font-bold text-sm">
                                                {{ $item->cantidad }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right text-gray-700 font-medium">Bs {{ number_format($item->precio_unitario, 2) }}</td>
                                        <td class="px-4 py-4 text-right text-gray-900 font-bold">Bs {{ number_format($item->subtotal_item, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totales destacados -->
                <div class="px-8 py-6 bg-gradient-to-br from-gray-50 to-gray-100">
                    <div class="max-w-md ml-auto">
                        <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-gray-200">
                            <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-200">
                                <span class="text-gray-600 font-medium">Subtotal:</span>
                                <span class="text-lg font-bold text-gray-900">Bs {{ number_format($cobro->importe, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg px-4 py-4">
                                <span class="text-lg font-bold text-white uppercase tracking-wide">Total:</span>
                                <span class="text-3xl font-black text-white">Bs {{ number_format($cobro->importe, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de pago mejorada -->
                <div class="px-8 py-6 border-t border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Información de Pago</h3>
                    </div>
                    
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Método de pago</span>
                                <span class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    @if($cobro->metodo === 'efectivo')
                                        <span class="text-2xl">💵</span> Efectivo
                                    @elseif($cobro->metodo === 'pos')
                                        <span class="text-2xl">💳</span> Tarjeta/POS
                                    @else
                                        <span class="text-2xl">📱</span> QR/Transferencia
                                    @endif
                                </span>
                            </div>
                            
                            @if($cobro->cajero)
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Atendido por</span>
                                    <span class="text-base font-bold text-gray-900">{{ $cobro->cajero->name }}</span>
                                </div>
                            @endif
                            
                            @if($cobro->esQr())
                                <div class="bg-white rounded-lg p-4 shadow-sm md:col-span-2">
                                    <span class="text-xs text-gray-500 uppercase font-semibold block mb-2">Detalles QR</span>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Proveedor:</span>
                                            <span class="font-semibold text-gray-900 capitalize">{{ $cobro->qr_proveedor }}</span>
                                        </div>
                                        @if($cobro->qr_tx_id)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">ID Transacción:</span>
                                                <span class="font-mono text-xs text-gray-900">{{ $cobro->qr_tx_id }}</span>
                                            </div>
                                        @endif
                                        @if($cobro->qr_referencia)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Referencia:</span>
                                                <span class="text-gray-900">{{ $cobro->qr_referencia }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer elegante -->
                <div class="relative bg-gradient-to-br from-amber-600 via-amber-500 to-orange-500 px-8 py-8 text-center overflow-hidden">
                    <!-- Patrón decorativo -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    </div>
                    
                    <div class="relative">
                        <div class="w-16 h-16 mx-auto mb-4 bg-white/20 backdrop-blur rounded-full flex items-center justify-center ring-4 ring-white/30">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold mb-2 text-white">¡Gracias por su preferencia!</p>
                        <p class="text-amber-100 mb-6">Esperamos verle pronto nuevamente</p>
                        <div class="pt-6 border-t border-white/30 space-y-1">
                            <p class="text-xs text-amber-100 font-medium">Este documento es un comprobante válido de pago</p>
                            <p class="text-xs text-amber-100">Generado: {{ now()->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones adicionales (solo pantalla) -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3 print:hidden">
                <a href="{{ route('cobro_caja.show', $cobro->pedido) }}" 
                   class="flex-1 bg-zinc-700 hover:bg-zinc-600 text-white py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver Detalle del Pedido
                </a>
                <a href="{{ route('cobro_caja.index') }}" 
                   class="flex-1 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 active:scale-95 shadow-lg shadow-green-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Volver a Cobros
                </a>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white !important;
            }
            
            .print\:hidden,
            nav,
            header,
            footer,
            .dashboard-card {
                display: none !important;
            }
            
            #comprobante {
                box-shadow: none !important;
                margin: 0 !important;
                border-radius: 0 !important;
                max-width: 100% !important;
            }
            
            .min-h-screen {
                min-height: auto !important;
                padding: 0 !important;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</x-layouts.app>