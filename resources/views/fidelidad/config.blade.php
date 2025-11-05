{{-- filepath: resources/views/fidelidad/config.blade.php --}}
<x-layouts.app :title="__('Configuración de Fidelidad')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- HEADER -->
            <div class="dashboard-card mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                            <i class="fas fa-cog text-purple-400"></i>
                            Configuración de Fidelidad
                        </h1>
                        <p class="text-zinc-300 flex items-center gap-2">
                            <i class="fas fa-sliders-h text-purple-400"></i>
                            Configura los criterios para acumulación de puntos
                        </p>
                    </div>
                    
                    <a href="{{ route('fidelidad.index') }}" 
                    class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </div>

            <!-- FORMULARIO DE CONFIGURACIÓN -->
            <div class="dashboard-card">
                <form method="POST" action="{{ route('fidelidad.update-config') }}">
                    @csrf
                    
                    <div class="space-y-8">
                        <!-- Puntos por Dólar -->
                        <div>
                            <label class="block text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <i class="fas fa-dollar-sign text-green-400"></i>
                                Puntos por Monto Gastado
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">
                                        Puntos por cada $1 gastado
                                    </label>
                                    <input type="number" name="puntos_por_dolar" 
                                           value="{{ $config['puntos_por_dolar'] ?? 10 }}" 
                                           step="1" min="0" max="100"
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                           required>
                                    <p class="text-zinc-400 text-xs mt-2">
                                        <i class="fas fa-info-circle text-amber-400"></i>
                                        Ejemplo: Con valor 10, un pedido de $20 genera 200 puntos
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Puntos por Antigüedad -->
                        <div>
                            <label class="block text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-blue-400"></i>
                                Puntos por Antigüedad
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">
                                        Meses para bonificación
                                    </label>
                                    <input type="number" name="puntos_por_antiguedad_meses" 
                                           value="{{ $config['puntos_por_antiguedad_meses'] ?? 12 }}" 
                                           min="0" max="60"
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                    <p class="text-zinc-400 text-xs mt-2">
                                        Ejemplo: 12 = cada 12 meses
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">
                                        Puntos base por antigüedad
                                    </label>
                                    <input type="number" name="puntos_antiguedad_base" 
                                           value="{{ $config['puntos_antiguedad_base'] ?? 100 }}" 
                                           min="0" max="1000"
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                    <p class="text-zinc-400 text-xs mt-2">
                                        Puntos a otorgar por cada período
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Multiplicadores -->
                        <div>
                            <label class="block text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <i class="fas fa-chart-line text-amber-400"></i>
                                Multiplicadores Especiales
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">
                                        Multiplicador fin de semana
                                    </label>
                                    <input type="number" name="multiplicador_fin_semana" 
                                           value="{{ $config['multiplicador_fin_semana'] ?? 1.5 }}" 
                                           step="0.1" min="1" max="3"
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                    <p class="text-zinc-400 text-xs mt-2">
                                        Ejemplo: 1.5 = 50% más puntos los fines de semana
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Recompensas -->
                        <div>
                            <label class="block text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <i class="fas fa-gift text-purple-400"></i>
                                Puntos para Recompensas
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">
                                        10% descuento
                                    </label>
                                    <input type="number" name="recompensa_descuento_10_puntos" 
                                           value="{{ $config['recompensa_descuento_10_puntos'] ?? 100 }}" 
                                           min="0" max="1000"
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">
                                        20% descuento
                                    </label>
                                    <input type="number" name="recompensa_descuento_20_puntos" 
                                           value="{{ $config['recompensa_descuento_20_puntos'] ?? 200 }}" 
                                           min="0" max="1000"
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">
                                        Producto gratis
                                    </label>
                                    <input type="number" name="recompensa_producto_gratis_puntos" 
                                           value="{{ $config['recompensa_producto_gratis_puntos'] ?? 150 }}" 
                                           min="0" max="1000"
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                </div>
                            </div>
                            <p class="text-zinc-400 text-xs mt-3">
                                <i class="fas fa-info-circle text-amber-400"></i>
                                Puntos requeridos para canjear cada tipo de recompensa
                            </p>
                        </div>

                        <!-- Estado del Programa - VERSIÓN CORREGIDA -->
                        <div>
                            <label class="block text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                <i class="fas fa-power-off text-red-400"></i>
                                Estado del Programa
                            </label>
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" name="activo" value="1" 
                                               {{ ($config['activo'] ?? true) ? 'checked' : '' }} 
                                               class="sr-only peer">
                                        <div class="w-14 h-8 bg-zinc-700 peer-checked:bg-green-500 rounded-full transition-colors duration-200"></div>
                                        <div class="absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-200 peer-checked:translate-x-6"></div>
                                    </div>
                                    <span class="text-white font-medium peer-checked:text-green-400 transition-colors">
                                        {{ ($config['activo'] ?? true) ? 'Programa Activo' : 'Programa Inactivo' }}
                                    </span>
                                </label>
                            </div>
                            <p class="text-zinc-400 text-sm mt-2">
                                Cuando el programa está inactivo, los clientes no acumularán puntos en nuevos pedidos.
                            </p>
                        </div>
                    </div>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="flex gap-3 pt-6 mt-8 border-t border-zinc-700">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white font-medium py-3 px-8 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Guardar Configuración
                        </button>
                        <a href="{{ route('fidelidad.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                        <a href="{{ route('fidelidad.recompensas') }}" class="bg-purple-600 hover:bg-purple-500 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-eye"></i>
                            Ver Recompensas
                        </a>
                    </div>
                </form>
            </div>

            <!-- INFORMACIÓN DEL SISTEMA -->
            <div class="dashboard-card mt-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-400"></i>
                    Información del Sistema
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <h4 class="font-medium text-white mb-3">Estructura Actual:</h4>
                        <ul class="space-y-2 text-zinc-300">
                            <li class="flex items-center gap-2">
                                <i class="fas fa-database text-green-400 text-xs"></i>
                                <span class="font-mono">fidelidad_config</span> - Configuración persistente
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-exchange-alt text-blue-400 text-xs"></i>
                                <span class="font-mono">fidelidad_movimientos</span> - Acumulación y canjes
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-users text-amber-400 text-xs"></i>
                                <span class="font-mono">users</span> - Clientes del sistema
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-receipt text-purple-400 text-xs"></i>
                                <span class="font-mono">pedidos</span> - Pedidos realizados
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-white mb-3">Ejemplo de Cálculo:</h4>
                        <div class="bg-zinc-800/50 rounded-lg p-4 text-zinc-300">
                            <p class="mb-2 font-medium">Pedido de <span class="text-green-400">$50</span> en fin de semana:</p>
                            <div class="space-y-1 text-xs">
                                <p>• Puntos base: 50 × {{ $config['puntos_por_dolar'] ?? 10 }} = <span class="text-amber-400">{{ 50 * ($config['puntos_por_dolar'] ?? 10) }} pts</span></p>
                                <p>• Multiplicador fin de semana: ×{{ $config['multiplicador_fin_semana'] ?? 1.5 }}</p>
                                <p class="pt-2 font-semibold text-white">Total: <span class="text-green-400">{{ number_format(50 * ($config['puntos_por_dolar'] ?? 10) * ($config['multiplicador_fin_semana'] ?? 1.5)) }} pts</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado de la Configuración -->
                <div class="mt-6 pt-6 border-t border-zinc-700">
                    <h4 class="font-medium text-white mb-3">Estado Actual:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div class="bg-zinc-800/50 rounded-lg p-3 text-center">
                            <div class="text-amber-400 font-bold text-lg">{{ $config['puntos_por_dolar'] ?? 10 }}</div>
                            <div class="text-zinc-400 text-xs">Pts/$1</div>
                        </div>
                        <div class="bg-zinc-800/50 rounded-lg p-3 text-center">
                            <div class="text-blue-400 font-bold text-lg">{{ $config['multiplicador_fin_semana'] ?? 1.5 }}x</div>
                            <div class="text-zinc-400 text-xs">Fin semana</div>
                        </div>
                        <div class="bg-zinc-800/50 rounded-lg p-3 text-center">
                            <div class="text-green-400 font-bold text-lg">{{ $config['recompensa_descuento_10_puntos'] ?? 100 }}</div>
                            <div class="text-zinc-400 text-xs">10% desc</div>
                        </div>
                        <div class="bg-zinc-800/50 rounded-lg p-3 text-center">
                            <div class="{{ ($config['activo'] ?? true) ? 'text-green-400' : 'text-red-400' }} font-bold text-lg">
                                {{ ($config['activo'] ?? true) ? 'ACTIVO' : 'INACTIVO' }}
                            </div>
                            <div class="text-zinc-400 text-xs">Programa</div>
                        </div>
                    </div>
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
        
        /* Estilos específicos para el toggle */
        .peer:checked + div {
            background-color: #10B981 !important;
        }
        .peer:checked + div + div {
            transform: translateX(1.5rem);
        }
    </style>
</x-layouts.app>