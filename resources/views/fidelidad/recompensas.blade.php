{{-- filepath: resources/views/fidelidad/recompensas.blade.php --}}
<x-layouts.app :title="__('Recompensas Disponibles')">
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
                            Catálogo de recompensas para canjear con puntos
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- Mostrar puntos del cliente si está logueado como cliente -->
                        @if(auth()->user()->hasRole('cliente') && isset($puntosCliente))
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg px-4 py-2 text-white">
                            <div class="text-sm">Tus Puntos</div>
                            <div class="text-xl font-bold">{{ number_format($puntosCliente) }}</div>
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

            <!-- ALERTA SI EL PROGRAMA NO ESTÁ ACTIVO -->
            @if(!$programaActivo)
            <div class="dashboard-card mb-6 bg-red-500/10 border-red-500/30">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                    <div>
                        <h3 class="text-red-400 font-semibold">Programa de Fidelidad Inactivo</h3>
                        <p class="text-zinc-300 text-sm mt-1">
                            El programa de fidelidad no está activo actualmente. Las recompensas no están disponibles para canje.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- INFORMACIÓN PARA CLIENTES -->
            @if(auth()->user()->hasRole('cliente'))
            <div class="dashboard-card mb-6 bg-amber-500/10 border-amber-500/30">
                <div class="flex items-center gap-3">
                    <i class="fas fa-info-circle text-amber-400 text-xl"></i>
                    <div>
                        <h3 class="text-amber-400 font-semibold">¿Cómo canjear recompensas?</h3>
                        <p class="text-zinc-300 text-sm mt-1">
                            Acércate al mostrador y menciona al cajero qué recompensa deseas canjear. 
                            Ellos verificarán tus puntos y procesarán tu canje.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- LISTA DE RECOMPENSAS -->
            <div class="dashboard-card">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-gift text-purple-400"></i>
                    Catálogo de Recompensas
                </h2>
                
                @if(count($recompensas) > 0 && $programaActivo)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recompensas as $recompensa)
                    <div class="dashboard-card hover:border-purple-500/30 transition-all duration-300 group">
                        <!-- Badge de tipo -->
                        <div class="flex justify-between items-start mb-4">
                            @if($recompensa['tipo'] === 'descuento')
                            <span class="bg-green-500/20 text-green-400 text-xs font-medium px-3 py-1 rounded-full">
                                <i class="fas fa-percentage mr-1"></i>
                                Descuento
                            </span>
                            @elseif($recompensa['tipo'] === 'producto')
                            <span class="bg-blue-500/20 text-blue-400 text-xs font-medium px-3 py-1 rounded-full">
                                <i class="fas fa-utensils mr-1"></i>
                                Producto Gratis
                            </span>
                            @endif
                            
                            <!-- Estado activo -->
                            <span class="bg-emerald-500/20 text-emerald-400 text-xs font-medium px-3 py-1 rounded-full">
                                <i class="fas fa-check mr-1"></i>
                                Disponible
                            </span>
                        </div>
                        
                        <!-- Icono y nombre -->
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-purple-500/30 transition-colors">
                                <i class="fas fa-gift text-purple-400 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white">{{ $recompensa['nombre'] }}</h3>
                            <p class="text-zinc-300 mt-2 text-sm">{{ $recompensa['descripcion'] }}</p>
                        </div>
                        
                        <!-- Puntos requeridos -->
                        <div class="text-center mb-4">
                            <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/20 rounded-full px-4 py-2">
                                <i class="fas fa-star text-amber-400"></i>
                                <span class="text-amber-400 font-bold text-lg">
                                    {{ number_format($recompensa['puntos_requeridos']) }} puntos
                                </span>
                            </div>
                        </div>
                        
                        <!-- Detalles específicos -->
                        <div class="text-center">
                            @if($recompensa['tipo'] === 'descuento')
                            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-3">
                                <span class="text-green-400 font-semibold text-lg">
                                    {{ str_replace(['10% de ', '20% de ', 'descuento'], '', $recompensa['nombre']) }}
                                </span>
                                <p class="text-zinc-300 text-sm mt-1">En tu próxima compra</p>
                            </div>
                            @elseif($recompensa['tipo'] === 'producto')
                            <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3">
                                <span class="text-blue-400 font-semibold text-lg">
                                    PRODUCTO GRATIS
                                </span>
                                <p class="text-zinc-300 text-sm mt-1">Del menú disponible</p>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Información para canje -->
                        <div class="mt-4 pt-4 border-t border-zinc-700/50">
                            <div class="flex items-center gap-2 text-zinc-400 text-sm">
                                <i class="fas fa-store"></i>
                                <span>Acércate al mostrador para canjear</span>
                            </div>
                            
                            <!-- Mostrar si el cliente tiene puntos suficientes -->
                            @if(auth()->user()->hasRole('cliente') && isset($puntosCliente))
                                @if($puntosCliente >= $recompensa['puntos_requeridos'])
                                <div class="mt-2 flex items-center gap-2 text-emerald-400 text-sm">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Tienes puntos suficientes</span>
                                </div>
                                @else
                                <div class="mt-2 flex items-center gap-2 text-amber-400 text-sm">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Te faltan {{ number_format($recompensa['puntos_requeridos'] - $puntosCliente) }} puntos</span>
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @elseif(!$programaActivo)
                <!-- Programa inactivo -->
                <div class="dashboard-card text-center py-16">
                    <div class="w-24 h-24 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-pause-circle text-zinc-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Programa de Fidelidad Inactivo</h3>
                    <p class="text-zinc-400 mb-6 max-w-md mx-auto">
                        El programa de fidelidad no está activo actualmente. 
                        Vuelve pronto cuando el programa esté disponible.
                    </p>
                </div>
                
                @else
                <!-- Estado vacío -->
                <div class="dashboard-card text-center py-16">
                    <div class="w-24 h-24 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-gift text-zinc-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">No hay recompensas disponibles</h3>
                    <p class="text-zinc-400 mb-6 max-w-md mx-auto">
                        Actualmente no hay recompensas disponibles para canjear. 
                        Vuelve pronto para ver nuevas promociones.
                    </p>
                </div>
                @endif
            </div>

            <!-- INFORMACIÓN ADICIONAL -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                <div class="dashboard-card">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-question-circle text-blue-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">¿Cómo funcionan los puntos?</h3>
                    </div>
                    <ul class="text-zinc-300 space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-400"></i>
                            Gana puntos con cada compra realizada
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-400"></i>
                            Los puntos no expiran
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-400"></i>
                            Canjea cuando tengas suficientes puntos
                        </li>
                        @if(auth()->user()->hasRole('cliente') && isset($puntosCliente))
                        <li class="flex items-center gap-2 mt-3 pt-3 border-t border-zinc-700">
                            <i class="fas fa-star text-amber-400"></i>
                            <span class="font-semibold text-amber-400">Tus puntos actuales: {{ number_format($puntosCliente) }}</span>
                        </li>
                        @endif
                    </ul>
                </div>

                <div class="dashboard-card">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-500/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-store text-purple-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Proceso de Canje</h3>
                    </div>
                    <ul class="text-zinc-300 space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-1 text-amber-400"></i>
                            Elige la recompensa que deseas
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-2 text-amber-400"></i>
                            Acércate al mostrador
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-3 text-amber-400"></i>
                            Menciona al cajero tu elección
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-4 text-amber-400"></i>
                            El cajero procesará tu canje
                        </li>
                    </ul>
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