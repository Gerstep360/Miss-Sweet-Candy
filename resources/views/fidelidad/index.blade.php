{{-- filepath: resources/views/fidelidad/index.blade.php --}}
<x-layouts.app :title="__('Sistema de Fidelidad')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- HEADER PRINCIPAL MEJORADO -->
            <div class="text-center mb-12">
                <div class="flex justify-center mb-4">
                    <div class="relative">
                        <div class="w-20 h-20 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-amber-500/30">
                            <i class="fas fa-crown text-white text-3xl"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-white text-xs"></i>
                        </div>
                    </div>
                </div>
                <h1 class="text-4xl font-bold text-white mb-4 bg-gradient-to-r from-amber-400 to-amber-200 bg-clip-text text-transparent">
                    Programa de Fidelidad
                </h1>
                <p class="text-zinc-300 text-lg max-w-2xl mx-auto">
                    @if(auth()->user()->hasRole('cliente'))
                        Descubre un mundo de recompensas exclusivas. Acumula puntos con tus compras y disfruta de beneficios especiales.
                    @elseif(auth()->user()->hasRole('cajero'))
                        Sistema de gestión de puntos de fidelidad para clientes
                    @else
                        Panel de administración del programa de fidelidad
                    @endif
                </p>
            </div>

            <!-- SECCIÓN CLIENTE - RESUMEN RÁPIDO -->
            @if(auth()->user()->hasRole('cliente'))
            <div class="dashboard-card mb-8 text-center">
                <div class="flex items-center justify-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-amber-500/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-amber-400 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">¡Hola, {{ auth()->user()->name }}!</h2>
                        <p class="text-zinc-300">Tu programa de fidelidad personal</p>
                    </div>
                </div>
                
                <!-- Tarjeta de puntos principal -->
                <div class="max-w-md mx-auto mb-8">
                    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-2xl shadow-amber-500/30">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-amber-100 text-sm">Tus Puntos</p>
                                @php
                                    $misPuntos = 0;
                                    foreach($puntosClientes as $puntosCliente) {
                                        if($puntosCliente->cliente_id == auth()->id()) {
                                            $misPuntos = $puntosCliente->puntos_actuales;
                                            break;
                                        }
                                    }
                                @endphp
                                <p class="text-4xl font-bold">{{ number_format($misPuntos) }}</p>
                            </div>
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-gem text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- GRID PRINCIPAL DE ACCIONES -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <!-- TARJETA 1: VER RECOMPENSAS -->
                <a href="{{ route('fidelidad.recompensas') }}" 
                   class="group relative overflow-hidden">
                    <div class="dashboard-card h-full bg-gradient-to-br from-purple-500/10 to-purple-600/10 border-purple-500/30 hover:border-purple-400/50 transition-all duration-500 transform hover:scale-105">
                        <div class="relative z-10">
                            <!-- Icono y badge -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 bg-purple-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-gift text-purple-400 text-2xl"></i>
                                </div>
                                <span class="bg-purple-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    @if(auth()->user()->hasRole('cliente'))
                                        6 Disponibles
                                    @else
                                        Ver Catálogo
                                    @endif
                                </span>
                            </div>
                            
                            <!-- Contenido -->
                            <h3 class="text-2xl font-bold text-white mb-3">
                                @if(auth()->user()->hasRole('cliente'))
                                    Explorar Recompensas
                                @else
                                    Ver Recompensas
                                @endif
                            </h3>
                            <p class="text-zinc-300 mb-6 leading-relaxed">
                                @if(auth()->user()->hasRole('cliente'))
                                    Descubre todos los premios exclusivos que puedes canjear con tus puntos. Desde descuentos hasta productos gratis.
                                @else
                                    Consulta el catálogo completo de recompensas disponibles para los clientes.
                                @endif
                            </p>
                            
                            <!-- Botón de acción -->
                            <div class="flex items-center text-purple-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                <span>
                                    @if(auth()->user()->hasRole('cliente'))
                                        Ver recompensas
                                    @else
                                        Ver catálogo
                                    @endif
                                </span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- TARJETA 2: VER PUNTOS -->
                @if(auth()->user()->hasRole('cliente'))
                <a href="{{ route('fidelidad.historial') }}" 
                   class="group relative overflow-hidden">
                    <div class="dashboard-card h-full bg-gradient-to-br from-green-500/10 to-green-600/10 border-green-500/30 hover:border-green-400/50 transition-all duration-500 transform hover:scale-105">
                        <div class="relative z-10">
                            <!-- Icono y badge -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 bg-green-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-chart-bar text-green-400 text-2xl"></i>
                                </div>
                                <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    Detallado
                                </span>
                            </div>
                            
                            <!-- Contenido -->
                            <h3 class="text-2xl font-bold text-white mb-3">Ver Puntos y Historial</h3>
                            <p class="text-zinc-300 mb-6 leading-relaxed">
                                Consulta el desglose completo de tus puntos acumulados, canjeados y revisa todos tus movimientos recientes.
                            </p>
                            
                            <!-- Botón de acción -->
                            <div class="flex items-center text-green-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                <span>Ver detalles</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </div>
                        </div>
                    </div>
                </a>
                @else
                <!-- TARJETA PARA CAJEROS/ADMIN: GESTIÓN DE CLIENTES -->
                <a href="{{ route('fidelidad.puntos-cajero') }}?vista=clientes" 
                   class="group relative overflow-hidden">
                    <div class="dashboard-card h-full bg-gradient-to-br from-blue-500/10 to-blue-600/10 border-blue-500/30 hover:border-blue-400/50 transition-all duration-500 transform hover:scale-105">
                        <div class="relative z-10">
                            <!-- Icono y badge -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 bg-blue-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-users text-blue-400 text-2xl"></i>
                                </div>
                                <span class="bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    Gestión
                                </span>
                            </div>
                            
                            <!-- Contenido -->
                            <h3 class="text-2xl font-bold text-white mb-3">Clientes y Puntos</h3>
                            <p class="text-zinc-300 mb-6 leading-relaxed">
                                Consulta y gestiona los puntos de fidelidad de todos los clientes registrados en el sistema.
                            </p>
                            
                            <!-- Botón de acción -->
                            <div class="flex items-center text-blue-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                <span>Gestionar clientes</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </div>
                        </div>
                    </div>
                </a>
                @endif

                <!-- TARJETA 3: CONFIGURACIÓN O INFORMACIÓN -->
                @can('gestionar-fidelidad')
                <a href="{{ route('fidelidad.config') }}" 
                   class="group relative overflow-hidden">
                    <div class="dashboard-card h-full bg-gradient-to-br from-indigo-500/10 to-indigo-600/10 border-indigo-500/30 hover:border-indigo-400/50 transition-all duration-500 transform hover:scale-105">
                        <div class="relative z-10">
                            <!-- Icono y badge -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 bg-indigo-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-cog text-indigo-400 text-2xl"></i>
                                </div>
                                <span class="bg-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    Admin
                                </span>
                            </div>
                            
                            <!-- Contenido -->
                            <h3 class="text-2xl font-bold text-white mb-3">Configurar Criterios</h3>
                            <p class="text-zinc-300 mb-6 leading-relaxed">
                                Gestiona las reglas del programa, ajusta puntos por compra y configura las recompensas disponibles.
                            </p>
                            
                            <!-- Botón de acción -->
                            <div class="flex items-center text-indigo-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                <span>Gestionar</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </div>
                        </div>
                    </div>
                </a>
                @else
                <!-- TARJETA INFORMATIVA PARA CAJEROS -->
                <div class="group relative overflow-hidden">
                    <div class="dashboard-card h-full bg-gradient-to-br from-zinc-700/50 to-zinc-800/50 border-zinc-600/30">
                        <div class="relative z-10">
                            <!-- Icono -->
                            <div class="w-14 h-14 bg-zinc-600/50 rounded-xl flex items-center justify-center mb-6">
                                <i class="fas fa-info-circle text-zinc-400 text-2xl"></i>
                            </div>
                            
                            <!-- Contenido -->
                            <h3 class="text-2xl font-bold text-white mb-3">Información del Sistema</h3>
                            <p class="text-zinc-400 mb-6 leading-relaxed">
                                Sistema de fidelidad para gestión de puntos y recompensas de clientes.
                            </p>
                            
                            <!-- Estado -->
                            <div class="text-zinc-500 text-sm">
                                Rol: {{ auth()->user()->getRoleNames()->first() }}
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            </div>

            <!-- SECCIÓN DE CLIENTES PARA CAJEROS/ADMIN -->
            @if(!auth()->user()->hasRole('cliente') && $puntosClientes->count() > 0)
            <div class="dashboard-card mb-8">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-users text-blue-400"></i>
                    Resumen de Clientes
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-blue-500/10 rounded-lg border border-blue-500/20">
                        <div class="text-2xl font-bold text-blue-400">{{ $puntosClientes->count() }}</div>
                        <div class="text-sm text-zinc-300">Clientes Activos</div>
                    </div>
                    <div class="text-center p-4 bg-green-500/10 rounded-lg border border-green-500/20">
                        <div class="text-2xl font-bold text-green-400">
                            {{ number_format($puntosClientes->sum('puntos_actuales')) }}
                        </div>
                        <div class="text-sm text-zinc-300">Puntos Totales</div>
                    </div>
                    <div class="text-center p-4 bg-amber-500/10 rounded-lg border border-amber-500/20">
                        <div class="text-2xl font-bold text-amber-400">
                            {{ number_format($puntosClientes->sum('puntos_acumulados')) }}
                        </div>
                        <div class="text-sm text-zinc-300">Puntos Acumulados</div>
                    </div>
                    <div class="text-center p-4 bg-purple-500/10 rounded-lg border border-purple-500/20">
                        <div class="text-2xl font-bold text-purple-400">
                            {{ number_format($puntosClientes->sum('puntos_canjeados')) }}
                        </div>
                        <div class="text-sm text-zinc-300">Puntos Canjeados</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- FOOTER DECORATIVO -->
            <div class="text-center mt-12">
                <div class="w-16 h-1 bg-gradient-to-r from-amber-500 to-purple-500 rounded-full mx-auto mb-4"></div>
                <p class="text-zinc-500 text-sm">
                    Programa de fidelidad • 
                    @if(auth()->user()->hasRole('cliente'))
                        Cliente
                    @elseif(auth()->user()->hasRole('cajero'))
                        Cajero
                    @else
                        Administrador
                    @endif
                </p>
            </div>
        </div>
    </div>

    <style>
        .dashboard-card {
            background: rgba(39, 39, 42, 0.7);
            border: 1px solid rgba(63, 63, 70, 0.5);
            border-radius: 1rem;
            padding: 2rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .dashboard-card:hover {
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
        }
    </style>
</x-layouts.app>