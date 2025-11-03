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
                    
                    <div class="space-y-6">
                        <!-- Puntos por Dólar -->
                        <div>
                            <label class="block text-lg font-semibold text-white mb-3 flex items-center gap-2">
                                <i class="fas fa-dollar-sign text-green-400"></i>
                                Puntos por Monto Gastado
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">Puntos por cada $1 gastado</label>
                                    <input type="number" name="puntos_por_dolar" value="{{ $config['puntos_por_dolar'] }}" step="0.1" min="0" 
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                           required>
                                    <p class="text-zinc-400 text-xs mt-1">Ejemplo: 10 puntos = 10 puntos por cada $1 gastado</p>
                                </div>
                            </div>
                        </div>

                        <!-- Puntos por Antigüedad -->
                        <div>
                            <label class="block text-lg font-semibold text-white mb-3 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-blue-400"></i>
                                Puntos por Antigüedad
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">Meses para bonificación</label>
                                    <input type="number" name="puntos_por_antiguedad_meses" value="{{ $config['puntos_por_antiguedad_meses'] }}" min="0" 
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                    <p class="text-zinc-400 text-xs mt-1">Ejemplo: 12 = cada 12 meses</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">Puntos base por antigüedad</label>
                                    <input type="number" name="puntos_antiguedad_base" value="{{ $config['puntos_antiguedad_base'] }}" min="0" 
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                    <p class="text-zinc-400 text-xs mt-1">Puntos a otorgar por cada período</p>
                                </div>
                            </div>
                        </div>

                        <!-- Multiplicadores -->
                        <div>
                            <label class="block text-lg font-semibold text-white mb-3 flex items-center gap-2">
                                <i class="fas fa-chart-line text-amber-400"></i>
                                Multiplicadores Especiales
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">Multiplicador fin de semana</label>
                                    <input type="number" name="multiplicador_fin_semana" value="{{ $config['multiplicador_fin_semana'] }}" step="0.1" min="1" 
                                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                    <p class="text-zinc-400 text-xs mt-1">Ejemplo: 1.5 = 50% más puntos los fines de semana</p>
                                </div>
                            </div>
                        </div>

                        <!-- Estado del Programa -->
                        <div>
                            <label class="block text-lg font-semibold text-white mb-3 flex items-center gap-2">
                                <i class="fas fa-power-off text-red-400"></i>
                                Estado del Programa
                            </label>
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" name="activo" value="1" {{ $config['activo'] ? 'checked' : '' }} class="sr-only">
                                        <div class="block bg-zinc-700 w-14 h-8 rounded-full transition-colors duration-200 {{ $config['activo'] ? 'bg-green-500' : '' }}"></div>
                                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-200 {{ $config['activo'] ? 'transform translate-x-6' : '' }}"></div>
                                    </div>
                                    <span class="text-white font-medium">{{ $config['activo'] ? 'Programa Activo' : 'Programa Inactivo' }}</span>
                                </label>
                            </div>
                            <p class="text-zinc-400 text-sm mt-2">
                                Cuando el programa está inactivo, los clientes no acumularán puntos en nuevos pedidos.
                            </p>
                        </div>
                    </div>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="flex gap-3 pt-6 mt-6 border-t border-zinc-700">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white font-medium py-3 px-8 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Guardar Configuración
                        </button>
                        <a href="{{ route('fidelidad.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

            <!-- INFORMACIÓN ADICIONAL -->
            <div class="dashboard-card mt-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-400"></i>
                    Información del Programa
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <h4 class="font-medium text-white mb-2">Cómo funcionan los puntos:</h4>
                        <ul class="space-y-2 text-zinc-300">
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check text-green-400 text-xs"></i>
                                Los puntos se acumulan automáticamente al completar pedidos
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check text-green-400 text-xs"></i>
                                Solo clientes registrados acumulan puntos
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check text-green-400 text-xs"></i>
                                Los puntos no expiran
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-white mb-2">Ejemplo de cálculo:</h4>
                        <div class="bg-zinc-800/50 rounded-lg p-3 text-zinc-300">
                            <p class="mb-1">Pedido de $50 con configuración actual:</p>
                            <p class="text-xs">• Puntos base: 50 × {{ $config['puntos_por_dolar'] }} = {{ 50 * $config['puntos_por_dolar'] }} pts</p>
                            <p class="text-xs">• Fin de semana: ×{{ $config['multiplicador_fin_semana'] }}</p>
                            <p class="text-xs font-semibold mt-1">Total: {{ number_format(50 * $config['puntos_por_dolar'] * $config['multiplicador_fin_semana']) }} pts</p>
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
    </style>
</x-layouts.app>