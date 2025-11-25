{{-- filepath: resources/views/fidelidad/historial.blade.php --}}
<x-layouts.app :title="__('Historial y Puntos')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- HEADER -->
            <div class="dashboard-card mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-blue-400"></i>
                            Historial y Puntos Detallados
                            @if($cliente->id != auth()->id())
                                <span class="text-zinc-300 text-lg">- {{ $cliente->name }}</span>
                            @endif
                        </h1>
                        <p class="text-zinc-300 flex items-center gap-2">
                            <i class="fas fa-star text-amber-400"></i>
                            Consulta tu saldo, movimientos y estadísticas detalladas
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg px-4 py-2 text-white">
                            <div class="text-sm">Puntos Actuales</div>
                            <div class="text-xl font-bold">{{ number_format($puntosTotales) }}</div>
                        </div>
                        
                        <a href="{{ route('fidelidad.index') }}" 
                        class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i>
                            Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: RESUMEN DETALLADO DE PUNTOS -->
            <div class="dashboard-card mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <i class="fas fa-chart-pie text-green-400"></i>
                    Resumen Detallado de Puntos
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <!-- Puntos Acumulados -->
                    <div class="dashboard-card text-center bg-green-500/10 border-green-500/20">
                        <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-arrow-up text-green-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Puntos Acumulados</h3>
                        <p class="text-2xl font-bold text-green-400">
                            {{ number_format($puntosAcumulados) }}
                        </p>
                        <p class="text-zinc-400 text-sm mt-1">Total ganado</p>
                    </div>

                    <!-- Puntos Canjeados -->
                    <div class="dashboard-card text-center bg-purple-500/10 border-purple-500/20">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-gift text-purple-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Puntos Canjeados</h3>
                        <p class="text-2xl font-bold text-purple-400">
                            {{ number_format($puntosCanjeados) }}
                        </p>
                        <p class="text-zinc-400 text-sm mt-1">Total utilizado</p>
                    </div>

                    <!-- Saldo Actual -->
                    <div class="dashboard-card text-center bg-amber-500/10 border-amber-500/20">
                        <div class="w-12 h-12 bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-star text-amber-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Saldo Actual</h3>
                        <p class="text-2xl font-bold text-amber-400">
                            {{ number_format($puntosTotales) }}
                        </p>
                        <p class="text-zinc-400 text-sm mt-1">Disponibles</p>
                    </div>

                    <!-- Eficiencia de Puntos -->
                    <div class="dashboard-card text-center bg-blue-500/10 border-blue-500/20">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-percentage text-blue-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Tasa de Uso</h3>
                        <p class="text-2xl font-bold text-blue-400">
                            @php
                                $tasaUso = $puntosAcumulados > 0 ? ($puntosCanjeados / $puntosAcumulados) * 100 : 0;
                            @endphp
                            {{ number_format($tasaUso, 1) }}%
                        </p>
                        <p class="text-zinc-400 text-sm mt-1">De puntos utilizados</p>
                    </div>
                </div>

                <!-- BARRA DE PROGRESO Y ESTADÍSTICAS -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Barra de progreso -->
                    <div>
                        <div class="flex justify-between text-sm text-zinc-300 mb-2">
                            <span>Progreso hacia recompensas premium</span>
                            <span>{{ number_format($puntosTotales) }} / 500 pts</span>
                        </div>
                        <div class="w-full bg-zinc-700 rounded-full h-3">
                            <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-3 rounded-full" 
                                 style="width: {{ min(($puntosTotales / 500) * 100, 100) }}%"></div>
                        </div>
                        <p class="text-zinc-400 text-xs mt-2">
                            @if($puntosTotales >= 500)
                                ✅ ¡Felicidades! Tienes puntos suficientes para las mejores recompensas
                            @else
                                🎯 Necesitas {{ 500 - $puntosTotales }} puntos más para recompensas premium
                            @endif
                        </p>
                    </div>

                    <!-- Estadísticas rápidas -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-lg font-bold text-amber-400">{{ $movimientos->count() }}</div>
                            <div class="text-xs text-zinc-400">Movimientos</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-green-400">
                                {{ $movimientos->where('tipo', 'acumulo')->count() }}
                            </div>
                            <div class="text-xs text-zinc-400">Acumulaciones</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-purple-400">
                                {{ $movimientos->where('tipo', 'canje')->count() }}
                            </div>
                            <div class="text-xs text-zinc-400">Canjes</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: GRÁFICO DE EVOLUCIÓN (Placeholder) -->
            <div class="dashboard-card mb-8">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-green-400"></i>
                    Evolución de Puntos
                </h3>
                <div class="bg-zinc-800/50 rounded-lg p-8 text-center border border-zinc-700">
                    <div class="w-16 h-16 bg-zinc-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-area text-zinc-500 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Gráfico de Evolución</h4>
                    <p class="text-zinc-400 text-sm mb-4">
                        Próximamente: Visualización de la evolución de tus puntos en el tiempo
                    </p>
                    <div class="text-xs text-zinc-500">
                        📈 Esta función mostrará tu progreso mensual y tendencias
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: MOVIMIENTOS RECIENTES -->
            <div class="dashboard-card">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-list text-amber-400"></i>
                        <h2 class="text-xl font-semibold text-white">Movimientos Recientes</h2>
                    </div>
                    
                    @if($movimientos->count() > 0)
                    <div class="text-sm text-zinc-400">
                        Total: {{ $movimientos->total() }} movimientos
                    </div>
                    @endif
                </div>
                
                <div class="space-y-4">
                    @forelse($movimientos as $movimiento)
                    <div class="dashboard-card hover:border-amber-500/20 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <!-- Icono según tipo -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $movimiento->tipo === 'acumulo' ? 'bg-green-500/20' : 'bg-purple-500/20' }}">
                                    <i class="fas {{ $movimiento->tipo === 'acumulo' ? 'fa-arrow-up text-green-400' : 'fa-gift text-purple-400' }}"></i>
                                </div>

                                <!-- Información del movimiento -->
                                <div>
                                    <h3 class="text-white font-semibold">
                                        {{ $movimiento->descripcion }}
                                    </h3>
                                    <p class="text-zinc-400 text-sm">
                                        @if($movimiento->origen && $movimiento->origen_type === 'App\\Models\\Pedido')
                                            Pedido #{{ $movimiento->origen->id }}
                                        @endif
                                        • {{ $movimiento->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Puntos -->
                            <div class="text-right">
                                <span class="text-lg font-bold 
                                    {{ $movimiento->tipo === 'acumulo' ? 'text-green-400' : 'text-purple-400' }}">
                                    {{ $movimiento->tipo === 'acumulo' ? '+' : '-' }}{{ number_format($movimiento->puntos) }}
                                </span>
                                <p class="text-zinc-400 text-sm">
                                    {{ $movimiento->tipo === 'acumulo' ? 'Acumulación' : 'Canje' }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Información adicional si es canje -->
                        @if($movimiento->tipo === 'canje')
                        <div class="mt-3 pt-3 border-t border-zinc-700">
                            <p class="text-sm text-zinc-300">
                                <i class="fas fa-gift text-purple-400 mr-1"></i>
                                Recompensa canjeada
                            </p>
                        </div>
                        @endif
                    </div>
                    @empty
                    <!-- Estado vacío -->
                    <div class="dashboard-card text-center py-16">
                        <div class="w-24 h-24 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-history text-zinc-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">No hay movimientos registrados</h3>
                        <p class="text-zinc-400 mb-6 max-w-md mx-auto">
                            @if(auth()->user()->hasRole('cliente'))
                                Realiza tu primer pedido para comenzar a acumular puntos.
                            @else
                                Este cliente aún no tiene movimientos de puntos.
                            @endif
                        </p>
                        @if(auth()->user()->hasRole('cliente'))
                        <a href="{{ route('pedidos.index') }}" 
                           class="bg-amber-600 hover:bg-amber-500 text-white py-3 px-6 rounded-lg transition-colors inline-flex items-center gap-2 font-medium">
                            <i class="fas fa-shopping-cart"></i> Realizar Pedido
                        </a>
                        @endif
                    </div>
                    @endforelse
                </div>

                <!-- PAGINACIÓN -->
                @if($movimientos->hasPages())
                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-zinc-400">
                            Mostrando {{ $movimientos->firstItem() }} - {{ $movimientos->lastItem() }} de {{ $movimientos->total() }} movimientos
                        </div>
                        {{ $movimientos->links() }}
                    </div>
                </div>
                @endif
            </div>

            <!-- INFORMACIÓN ADICIONAL -->
            <div class="dashboard-card mt-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-400"></i>
                    Información del Programa
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <h4 class="font-medium text-white mb-2">Tipos de Movimientos:</h4>
                        <ul class="space-y-2 text-zinc-300">
                            <li class="flex items-center gap-2">
                                <i class="fas fa-arrow-up text-green-400"></i>
                                <span class="font-medium text-green-400">Acumulación:</span> Puntos ganados por compras
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-gift text-purple-400"></i>
                                <span class="font-medium text-purple-400">Canje:</span> Puntos usados para recompensas
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-star text-amber-400"></i>
                                <span class="font-medium text-amber-400">Saldo:</span> Diferencia entre acumulación y canje
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-white mb-2">Beneficios y Notas:</h4>
                        <ul class="space-y-2 text-zinc-300">
                            <li class="flex items-center gap-2">
                                <i class="fas fa-clock text-amber-400 text-xs"></i>
                                Los puntos no tienen fecha de expiración
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-sync text-amber-400 text-xs"></i>
                                El historial se actualiza automáticamente
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-eye text-amber-400 text-xs"></i>
                                @if(auth()->user()->hasRole('cliente'))
                                    Solo puedes ver tu propio historial
                                @else
                                    Viendo historial de: {{ $cliente->name }}
                                @endif
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-trophy text-amber-400 text-xs"></i>
                                500 puntos para recompensas premium
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <a href="{{ route('fidelidad.recompensas') }}" 
                   class="dashboard-card text-center hover:border-purple-500/50 transition-all duration-300 group cursor-pointer">
                    <div class="p-6">
                        <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-gift text-purple-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Canjear Recompensas</h3>
                        <p class="text-zinc-300 mb-4">Usa tus puntos para obtener descuentos y productos</p>
                        <span class="text-purple-400 font-semibold group-hover:underline">Ver recompensas →</span>
                    </div>
                </a>

                <a href="{{ route('fidelidad.index') }}" 
                   class="dashboard-card text-center hover:border-amber-500/50 transition-all duration-300 group cursor-pointer">
                    <div class="p-6">
                        <div class="w-16 h-16 bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-home text-amber-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Dashboard Principal</h3>
                        <p class="text-zinc-300 mb-4">Volver al panel principal de fidelidad</p>
                        <span class="text-amber-400 font-semibold group-hover:underline">Ir al dashboard →</span>
                    </div>
                </a>

                @if(auth()->user()->hasRole('cliente'))
                <a href="{{ route('pedidos.index') }}" 
                   class="dashboard-card text-center hover:border-green-500/50 transition-all duration-300 group cursor-pointer">
                    <div class="p-6">
                        <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-shopping-cart text-green-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Realizar Pedido</h3>
                        <p class="text-zinc-300 mb-4">Haz un pedido para acumular más puntos</p>
                        <span class="text-green-400 font-semibold group-hover:underline">Hacer pedido →</span>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </div>

    <style>
        .dashboard-card {
            background: rgba(39, 39, 42, 0.7);
            border: 1px solid rgba(63, 63, 70, 0.5);
            border-radius: 0.75rem;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
        }
    </style>
</x-layouts.app>