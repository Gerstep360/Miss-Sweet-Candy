{{-- filepath: resources/views/fidelidad/recompensas.blade.php --}}
<x-layouts.app :title="__('Recompensas y Canjes')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- HEADER -->
            <div class="dashboard-card mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                            <i class="fas fa-gift text-purple-400"></i>
                            Recompensas Disponibles
                        </h1>
                        <p class="text-zinc-300 flex items-center gap-2">
                            <i class="fas fa-star text-amber-400"></i>
                            Canjea tus puntos por recompensas exclusivas
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        @if(auth()->user()->hasRole('cliente'))
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg px-4 py-2 text-white">
                            <div class="text-sm">Tus Puntos</div>
                            <div class="text-xl font-bold">{{ number_format($puntosTotales ?? $puntosCliente ?? 0) }}</div>
                        </div>
                        @endif
                        
                        <a href="{{ route('fidelidad.index') }}" 
                        class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i>
                            Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- RESUMEN DE PUNTOS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-arrow-up text-green-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Puntos Acumulados</h3>
                    <p class="text-2xl font-bold text-green-400">
                        {{ number_format(isset($movimientos) ? $movimientos->where('tipo', 'acumulo')->sum('puntos') : 0) }}
                    </p>
                </div>

                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-gift text-purple-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Puntos Canjeados</h3>
                    <p class="text-2xl font-bold text-purple-400">
                        {{ number_format(isset($movimientos) ? $movimientos->where('tipo', 'canje')->sum('puntos') : 0) }}
                    </p>
                </div>

                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-star text-amber-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Saldo Actual</h3>
                    <p class="text-2xl font-bold text-amber-400">
                        {{ number_format($puntosTotales ?? $puntosCliente ?? 0) }}
                    </p>
                </div>
            </div>

            <!-- LISTA DE RECOMPENSAS -->
            <div class="dashboard-card mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-gift text-purple-400"></i>
                    Recompensas Disponibles
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recompensas as $recompensa)
                    @if($recompensa['activo'])
                    <div class="dashboard-card hover:border-purple-500/30 transition-all duration-300">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-gift text-purple-400 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white">{{ $recompensa['nombre'] }}</h3>
                            <p class="text-zinc-300 mt-2">{{ $recompensa['descripcion'] }}</p>
                        </div>
                        
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-amber-400 font-bold text-lg">
                                {{ number_format($recompensa['puntos_requeridos']) }} pts
                            </span>
                            @if(auth()->user()->hasRole('cliente'))
                                @if(($puntosTotales ?? $puntosCliente ?? 0) >= $recompensa['puntos_requeridos'])
                                <form action="{{ route('fidelidad.canjear-recompensa', $recompensa['id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                                        <i class="fas fa-check"></i>
                                        Canjear
                                    </button>
                                </form>
                                @else
                                <span class="text-red-400 text-sm font-medium">
                                    Faltan {{ $recompensa['puntos_requeridos'] - ($puntosTotales ?? $puntosCliente ?? 0) }} pts
                                </span>
                                @endif
                            @endif
                        </div>
                        
                        @if($recompensa['tipo'] === 'descuento')
                        <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-3 text-center">
                            <span class="text-green-400 font-semibold">
                                {{ $recompensa['valor_descuento'] }}% de descuento
                            </span>
                        </div>
                        @elseif($recompensa['tipo'] === 'producto')
                        <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3 text-center">
                            <span class="text-blue-400 font-semibold">
                                Producto gratis
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            <!-- LISTA DE MOVIMIENTOS -->
            <div class="dashboard-card">
                <div class="flex items-center gap-2 mb-6">
                    <i class="fas fa-list text-amber-400"></i>
                    <h2 class="text-xl font-semibold text-white">Movimientos Recientes</h2>
                </div>
                
                <div class="space-y-4">
                    @if(isset($movimientos) && $movimientos->count() > 0)
                        @foreach($movimientos as $movimiento)
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
                                            {{ $movimiento->motivo }}
                                        </h3>
                                        <p class="text-zinc-400 text-sm">
                                            @if($movimiento->pedido)
                                                Pedido #{{ $movimiento->pedido->id }}
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
                        </div>
                        @endforeach
                    @else
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
                    </div>
                    @endif
                </div>
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