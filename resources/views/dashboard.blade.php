<x-layouts.app :title="__('Dashboard - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <!-- Contenido principal con padding responsive -->
        <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
            <!-- Saludo y resumen -->
            <div class="mb-6 sm:mb-8">
                <div class="dashboard-card">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold text-white mb-2">¡Bienvenido de vuelta!</h2>
                            <p class="text-sm sm:text-base text-zinc-300">Aquí tienes un resumen de tu cafetería hoy</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="status-indicator status-open"></div>
                            <span class="text-sm text-green-400 font-medium">Abierto</span>
                        </div>
                    </div>
                    
                    <!-- Estado en tiempo real - Responsive grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        <div class="dashboard-stat">
                            <div class="text-lg sm:text-2xl font-bold text-green-400">¡Abierto!</div>
                            <div class="text-xs text-zinc-400">6:00 AM - 10:00 PM</div>
                        </div>
                        <div class="dashboard-stat">
                            <div class="text-lg sm:text-xl font-bold text-amber-400">{{ date('d/m/Y') }}</div>
                            <div class="text-xs text-zinc-400">Fecha actual</div>
                        </div>
                        <div class="dashboard-stat sm:col-span-2 lg:col-span-1">
                            <div class="text-lg sm:text-xl font-bold text-blue-400">{{ date('H:i') }}</div>
                            <div class="text-xs text-zinc-400">Hora actual</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPIs Principales - Mejor responsive -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
                <!-- Ventas del día -->
                <div class="dashboard-card p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0 mb-3 sm:mb-4">
                        <div class="w-8 h-8 sm:w-12 sm:h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <span class="text-xs text-green-400 bg-green-400/10 px-2 py-1 rounded-full self-start sm:self-auto">+12%</span>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-bold text-white mb-1">$1,247</h3>
                    <p class="text-xs sm:text-sm text-zinc-400">Ventas del día</p>
                </div>

                <!-- Órdenes -->
                <div class="dashboard-card p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0 mb-3 sm:mb-4">
                        <div class="w-8 h-8 sm:w-12 sm:h-12 bg-amber-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="text-xs text-amber-400 bg-amber-400/10 px-2 py-1 rounded-full self-start sm:self-auto">+8%</span>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-bold text-white mb-1">47</h3>
                    <p class="text-xs sm:text-sm text-zinc-400">Órdenes hoy</p>
                </div>

                <!-- Clientes -->
                <div class="dashboard-card p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0 mb-3 sm:mb-4">
                        <div class="w-8 h-8 sm:w-12 sm:h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs text-blue-400 bg-blue-400/10 px-2 py-1 rounded-full self-start sm:self-auto">+5%</span>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-bold text-white mb-1">34</h3>
                    <p class="text-xs sm:text-sm text-zinc-400">Clientes únicos</p>
                </div>

                <!-- Inventario -->
                <div class="dashboard-card p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0 mb-3 sm:mb-4">
                        <div class="w-8 h-8 sm:w-12 sm:h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="text-xs text-red-400 bg-red-400/10 px-2 py-1 rounded-full self-start sm:self-auto">-3%</span>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-bold text-white mb-1">12</h3>
                    <p class="text-xs sm:text-sm text-zinc-400">Productos bajos</p>
                </div>
            </div>

            <!-- Sección de contenido principal - Layout adaptativo -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 sm:gap-8 mb-6 sm:mb-8">
                <!-- Menú del día - Toma más espacio en desktop -->
                <div class="xl:col-span-2">
                    <div class="dashboard-card h-full">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                            <h3 class="text-lg font-semibold text-white">Café del Día</h3>
                            <button class="text-amber-400 hover:text-amber-300 text-sm self-start sm:self-auto">Cambiar</button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-zinc-800/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center text-lg">
                                        ☕
                                    </div>
                                    <div>
                                        <p class="font-medium text-white">Cappuccino Especial</p>
                                        <p class="text-sm text-zinc-400">Especial del día</p>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="font-bold text-amber-400 text-lg">$35</p>
                                    <p class="text-xs text-zinc-400">12 vendidos</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-3 sm:gap-4">
                                <div class="bg-zinc-800/30 rounded-lg p-3 text-center">
                                    <p class="text-base sm:text-lg font-bold text-white">23</p>
                                    <p class="text-xs text-zinc-400">Espressos</p>
                                </div>
                                <div class="bg-zinc-800/30 rounded-lg p-3 text-center">
                                    <p class="text-base sm:text-lg font-bold text-white">15</p>
                                    <p class="text-xs text-zinc-400">Lattes</p>
                                </div>
                                <div class="bg-zinc-800/30 rounded-lg p-3 text-center">
                                    <p class="text-base sm:text-lg font-bold text-white">9</p>
                                    <p class="text-xs text-zinc-400">Americanos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="dashboard-card">
                    <h3 class="text-lg font-semibold text-white mb-6">Acciones Rápidas</h3>
                    <div class="space-y-3">
                        <button class="dashboard-button dashboard-button-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span class="hidden sm:inline">Nueva Orden</span>
                            <span class="sm:hidden">Orden</span>
                        </button>
                        
                        <button class="dashboard-button dashboard-button-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <span class="hidden sm:inline">Ver Inventario</span>
                            <span class="sm:hidden">Inventario</span>
                        </button>
                        
                        <button class="dashboard-button dashboard-button-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"/>
                            </svg>
                            <span class="hidden sm:inline">Reportes</span>
                            <span class="sm:hidden">Reportes</span>
                        </button>
                        
                        <a href="{{ route('home') }}" target="_blank" class="dashboard-button dashboard-button-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <span class="hidden sm:inline">Ver Sitio Web</span>
                            <span class="sm:hidden">Sitio Web</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Gráfico de ventas y órdenes recientes - Stack en móvil -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                <!-- Gráfico de ventas -->
                <div class="dashboard-card">
                    <h3 class="text-lg font-semibold text-white mb-6">Ventas de la Semana</h3>
                    <div class="h-48 sm:h-64 bg-zinc-800/30 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-12 h-12 sm:w-16 sm:h-16 text-amber-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"/>
                            </svg>
                            <p class="text-sm sm:text-base text-zinc-400">Gráfico de ventas aquí</p>
                        </div>
                    </div>
                </div>

                <!-- Órdenes recientes -->
                <div class="dashboard-card">
                    <h3 class="text-lg font-semibold text-white mb-6">Órdenes Recientes</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-zinc-800/30 rounded-lg">
                            <div class="flex items-center space-x-3 min-w-0 flex-1">
                                <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-green-400">#47</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-white truncate">Cappuccino + Croissant</p>
                                    <p class="text-xs text-zinc-400">Mesa 5 - {{ date('H:i') }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-amber-400 ml-2">$52</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-zinc-800/30 rounded-lg">
                            <div class="flex items-center space-x-3 min-w-0 flex-1">
                                <div class="w-8 h-8 bg-amber-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-amber-400">#46</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-white truncate">Latte Artesanal</p>
                                    <p class="text-xs text-zinc-400">Para llevar - {{ date('H:i', strtotime('-15 minutes')) }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-amber-400 ml-2">$40</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-zinc-800/30 rounded-lg">
                            <div class="flex items-center space-x-3 min-w-0 flex-1">
                                <div class="w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-blue-400">#45</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-white truncate">Americano Doble + Muffin</p>
                                    <p class="text-xs text-zinc-400">Mesa 2 - {{ date('H:i', strtotime('-30 minutes')) }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-amber-400 ml-2">$38</span>
                        </div>
                    </div>
                    
                    <button class="w-full mt-4 text-amber-400 hover:text-amber-300 text-sm py-2 border border-amber-500/20 rounded-lg hover:bg-amber-500/10 transition-colors">
                        Ver todas las órdenes
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>