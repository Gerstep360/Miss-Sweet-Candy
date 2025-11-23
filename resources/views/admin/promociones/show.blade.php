{{-- resources/views/admin/promociones/show.blade.php --}}
<x-layouts.app :title="__('Detalle de Promoción - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-4 sm:py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Header con botón volver -->
            <div class="flex items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                <a href="{{ route('promociones.index') }}" 
                   class="bg-zinc-800 hover:bg-zinc-700 active:bg-zinc-600 text-white p-2 sm:p-2.5 rounded-lg transition-all min-h-[44px] min-w-[44px] flex items-center justify-center touch-manipulation">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="flex-1">
                    <h1 class="text-xl sm:text-2xl font-bold text-white">Detalle de Promoción</h1>
                    <p class="text-sm text-zinc-400">Información completa de la promoción</p>
                </div>
            </div>

            <!-- Card Principal -->
            <div class="dashboard-card mb-4 sm:mb-6">
                <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-6">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-amber-500/30 to-amber-600/20 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-xl">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-2">
                            <h2 class="text-xl sm:text-2xl font-bold text-white flex-1">{{ $promocion->nombre }}</h2>
                            @if($promocion->esta_vigente)
                                <span class="flex-shrink-0 w-3 h-3 bg-green-400 rounded-full animate-pulse"></span>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="bg-gradient-to-r from-amber-500/20 to-amber-600/10 text-amber-400 text-xs sm:text-sm px-3 py-1.5 rounded-full capitalize font-medium border border-amber-500/30">
                                {{ $promocion->tipo }}
                            </span>
                            <span class="bg-gradient-to-r from-blue-500/20 to-blue-600/10 text-blue-400 text-xs sm:text-sm px-3 py-1.5 rounded-full border border-blue-500/30">
                                {{ $promocion->aplica_sobre }}
                            </span>
                            <span class="bg-gradient-to-r from-green-500/20 to-green-600/10 text-green-400 text-xs sm:text-sm px-3 py-1.5 rounded-full font-bold border border-green-500/30">
                                @if($promocion->tipo == 'porcentaje')
                                    -{{ $promocion->valor }}% OFF
                                @elseif($promocion->tipo == 'monto_fijo')
                                    -${{ number_format($promocion->valor, 2) }}
                                @else
                                    {{ ucfirst($promocion->tipo) }}
                                @endif
                            </span>
                            @if($promocion->esta_vigente)
                                <span class="bg-gradient-to-r from-green-500/20 to-green-600/10 text-green-400 text-xs sm:text-sm px-3 py-1.5 rounded-full border border-green-500/30 animate-pulse">
                                    ✓ Vigente Ahora
                                </span>
                            @else
                                <span class="bg-gradient-to-r from-red-500/20 to-red-600/10 text-red-400 text-xs sm:text-sm px-3 py-1.5 rounded-full border border-red-500/30">
                                    ✕ No Vigente
                                </span>
                            @endif
                            @if($promocion->activo)
                                <span class="bg-gradient-to-r from-emerald-500/20 to-emerald-600/10 text-emerald-400 text-xs sm:text-sm px-3 py-1.5 rounded-full border border-emerald-500/30">
                                    🟢 Activa
                                </span>
                            @else
                                <span class="bg-gradient-to-r from-gray-500/20 to-gray-600/10 text-gray-400 text-xs sm:text-sm px-3 py-1.5 rounded-full border border-gray-500/30">
                                    ⚫ Inactiva
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Vigencia -->
            <div class="dashboard-card mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Vigencia
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-zinc-800/50 p-4 rounded-lg border border-zinc-700/50">
                        <p class="text-zinc-400 text-sm mb-1">Período</p>
                        <p class="text-white font-medium">
                            {{ $promocion->fecha_inicio ? $promocion->fecha_inicio->format('d/m/Y') : 'Sin inicio' }} 
                            → 
                            {{ $promocion->fecha_fin ? $promocion->fecha_fin->format('d/m/Y') : 'Sin fin' }}
                        </p>
                    </div>
                    <div class="bg-zinc-800/50 p-4 rounded-lg border border-zinc-700/50">
                        <p class="text-zinc-400 text-sm mb-1">Horario</p>
                        <p class="text-white font-medium">
                            {{ $promocion->hora_inicio ?? '00:00' }} - {{ $promocion->hora_fin ?? '23:59' }}
                        </p>
                    </div>
                </div>
                @if($promocion->dias_semana && count($promocion->dias_semana) > 0)
                <div class="mt-4">
                    <p class="text-zinc-400 text-sm mb-2">Días de la semana:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['lun' => 'Lunes', 'mar' => 'Martes', 'mie' => 'Miércoles', 'jue' => 'Jueves', 'vie' => 'Viernes', 'sab' => 'Sábado', 'dom' => 'Domingo'] as $valor => $dia)
                            <span class="px-3 py-1.5 rounded-lg text-sm {{ in_array($valor, $promocion->dias_semana) ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30 font-medium' : 'bg-zinc-800 text-zinc-500 border border-zinc-700' }}">
                                {{ $dia }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Detalles de la Promoción -->
            <div class="dashboard-card mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Detalles
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-zinc-800/50 p-4 rounded-lg border border-zinc-700/50">
                        <p class="text-zinc-400 text-sm mb-1">Valor</p>
                        <p class="text-2xl font-bold text-white">
                            @if($promocion->tipo == 'porcentaje')
                                {{ $promocion->valor }}%
                            @elseif($promocion->tipo == 'monto_fijo')
                                ${{ number_format($promocion->valor, 2) }}
                            @else
                                {{ ucfirst($promocion->tipo) }}
                            @endif
                        </p>
                    </div>
                    <div class="bg-zinc-800/50 p-4 rounded-lg border border-zinc-700/50">
                        <p class="text-zinc-400 text-sm mb-1">Prioridad</p>
                        <p class="text-2xl font-bold text-white">{{ $promocion->prioridad }}</p>
                    </div>
                    @if($promocion->tope_descuento)
                    <div class="bg-zinc-800/50 p-4 rounded-lg border border-zinc-700/50">
                        <p class="text-zinc-400 text-sm mb-1">Tope de descuento</p>
                        <p class="text-2xl font-bold text-white">${{ number_format($promocion->tope_descuento, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Productos Aplicables -->
            @if($promocion->productos->count() > 0)
            <div class="dashboard-card mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Productos Aplicables ({{ $promocion->productos->count() }})
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($promocion->productos as $producto)
                    <div class="bg-zinc-800/50 p-3 rounded-lg border border-zinc-700/50 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-white text-sm">{{ $producto->nombre }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Categorías Aplicables -->
            @if($promocion->categorias->count() > 0)
            <div class="dashboard-card mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Categorías Aplicables ({{ $promocion->categorias->count() }})
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($promocion->categorias as $categoria)
                    <div class="bg-zinc-800/50 p-3 rounded-lg border border-zinc-700/50 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-white text-sm">{{ $categoria->nombre }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Requisitos para Aplicar (NUEVO) -->
            <div class="bg-gradient-to-br from-blue-900/30 to-blue-800/20 border-2 border-blue-500/50 rounded-xl p-6 mb-4 sm:mb-6 shadow-xl">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    ✅ Requisitos para Aplicar
                </h3>

                <div class="space-y-4">
                    <!-- Tipo y Valor -->
                    <div class="bg-zinc-900/60 rounded-lg p-4 border border-zinc-700">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-2xl">
                                @if($promocion->tipo == 'porcentaje')
                                    📊
                                @elseif($promocion->tipo == 'monto_fijo')
                                    💵
                                @elseif($promocion->tipo == '2x1')
                                    🎁
                                @else
                                    🍰
                                @endif
                            </span>
                            <span class="text-white font-bold text-lg">Descuento:</span>
                        </div>
                        <p class="text-blue-300 text-lg font-semibold ml-8">
                            @if($promocion->tipo == 'porcentaje')
                                {{ $promocion->valor }}% de descuento
                                @if($promocion->tope_descuento)
                                    (máximo ${{ number_format($promocion->tope_descuento, 2) }})
                                @endif
                            @elseif($promocion->tipo == 'monto_fijo')
                                ${{ number_format($promocion->valor, 2) }} de descuento
                            @elseif($promocion->tipo == '2x1')
                                2x1 - El producto más barato sale GRATIS (50% de descuento automático)
                            @else
                                Combo especial
                            @endif
                        </p>
                    </div>

                    <!-- Dónde Aplica -->
                    <div class="bg-zinc-900/60 rounded-lg p-4 border border-zinc-700">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-2xl">{{ $promocion->aplica_sobre == 'pedido' ? '🛒' : '🎯' }}</span>
                            <span class="text-white font-bold text-lg">Aplica sobre:</span>
                        </div>
                        <p class="text-blue-300 font-semibold ml-8">
                            @if($promocion->aplica_sobre == 'pedido')
                                Todo el pedido completo
                            @else
                                Solo productos/categorías específicos
                            @endif
                        </p>
                        
                        @if($promocion->aplica_sobre == 'item')
                            @if($promocion->productos->count() > 0)
                            <div class="ml-8 mt-3">
                                <p class="text-zinc-400 text-sm mb-2">✓ Productos válidos:</p>
                                <ul class="list-disc list-inside text-blue-200 text-sm space-y-1">
                                    @foreach($promocion->productos as $producto)
                                        <li>{{ $producto->nombre }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if($promocion->categorias->count() > 0)
                            <div class="ml-8 mt-3">
                                <p class="text-zinc-400 text-sm mb-2">✓ Categorías válidas:</p>
                                <ul class="list-disc list-inside text-blue-200 text-sm space-y-1">
                                    @foreach($promocion->categorias as $categoria)
                                        <li>Todos los productos de: {{ $categoria->nombre }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        @endif
                    </div>

                    <!-- Horario -->
                    <div class="bg-zinc-900/60 rounded-lg p-4 border border-zinc-700">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-2xl">⏰</span>
                            <span class="text-white font-bold text-lg">Horario:</span>
                        </div>
                        <p class="text-blue-300 font-semibold ml-8">
                            @if($promocion->hora_inicio && $promocion->hora_fin)
                                Válida de {{ \Carbon\Carbon::parse($promocion->hora_inicio)->format('H:i') }} a {{ \Carbon\Carbon::parse($promocion->hora_fin)->format('H:i') }}
                            @else
                                ✅ Todo el día - Sin restricción de horario
                            @endif
                        </p>
                    </div>

                    <!-- Días de la Semana -->
                    <div class="bg-zinc-900/60 rounded-lg p-4 border border-zinc-700">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-2xl">📅</span>
                            <span class="text-white font-bold text-lg">Días válidos:</span>
                        </div>
                        @php
                            $diasMapping = ['lun' => 'Lunes', 'mar' => 'Martes', 'mie' => 'Miércoles', 'jue' => 'Jueves', 'vie' => 'Viernes', 'sab' => 'Sábado', 'dom' => 'Domingo'];
                        @endphp
                        @if($promocion->dias_semana && count($promocion->dias_semana) > 0)
                            <p class="text-blue-300 font-semibold ml-8">
                                @foreach($promocion->dias_semana as $dia)
                                    {{ $diasMapping[$dia] ?? $dia }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </p>
                        @else
                            <p class="text-blue-300 font-semibold ml-8">✅ Todos los días de la semana</p>
                        @endif
                    </div>

                    <!-- Periodo de Validez -->
                    <div class="bg-zinc-900/60 rounded-lg p-4 border border-zinc-700">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-2xl">📆</span>
                            <span class="text-white font-bold text-lg">Periodo de validez:</span>
                        </div>
                        <p class="text-blue-300 font-semibold ml-8">
                            @if($promocion->fecha_inicio && $promocion->fecha_fin)
                                Desde {{ \Carbon\Carbon::parse($promocion->fecha_inicio)->format('d/m/Y') }} hasta {{ \Carbon\Carbon::parse($promocion->fecha_fin)->format('d/m/Y') }}
                            @elseif($promocion->fecha_inicio)
                                Desde {{ \Carbon\Carbon::parse($promocion->fecha_inicio)->format('d/m/Y') }} - Sin fecha límite
                            @elseif($promocion->fecha_fin)
                                Hasta {{ \Carbon\Carbon::parse($promocion->fecha_fin)->format('d/m/Y') }}
                            @else
                                ✅ Sin fecha límite - Válida indefinidamente
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Nota Final -->
                <div class="mt-6 bg-gradient-to-r from-green-900/40 to-green-800/30 border-2 border-green-500/50 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl flex-shrink-0">🎉</span>
                        <div>
                            <p class="text-green-300 font-semibold text-sm">
                                @if($promocion->activo)
                                    ¡Buenas noticias! Esta promoción ya está activa y disponible. Se aplicará automáticamente cuando se cumplan todas las condiciones anteriores.
                                @else
                                    Esta promoción está configurada pero inactiva. Actívala para que los clientes puedan usarla.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('promociones.index') }}" 
                   class="flex-1 sm:flex-none bg-zinc-700 hover:bg-zinc-600 active:bg-zinc-500 text-white font-medium px-6 py-3 sm:py-2 rounded-lg transition-all min-h-[44px] flex items-center justify-center gap-2 touch-manipulation">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>
                @can('editar-promociones')
                <a href="{{ route('promociones.edit', $promocion) }}" 
                   class="flex-1 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-semibold px-6 py-3 sm:py-2 rounded-lg transition-all min-h-[44px] flex items-center justify-center gap-2 touch-manipulation shadow-lg shadow-amber-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar Promoción
                </a>
                @endcan
            </div>
        </div>
    </div>
</x-layouts.app>
