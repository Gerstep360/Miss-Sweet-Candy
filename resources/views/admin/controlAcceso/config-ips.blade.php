{{-- resources/views/admin/controlAcceso/config-ips.blade.php --}}
<x-layouts.app :title="__('Configuración de IPs - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Configuración de IPs</h1>
                        <p class="text-zinc-300">Gestiona las IPs autorizadas y bloqueadas en el sistema</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('control-acceso.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna 1: IPs Detectadas -->
                <div class="dashboard-card lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            IPs Detectadas en el Sistema
                        </h3>
                        <span class="bg-blue-500/20 text-blue-400 text-xs px-2 py-1 rounded-full">
                            {{ $detectedIps->count() }} IPs
                        </span>
                    </div>

                    <!-- Filtros -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <button class="px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            Todas
                        </button>
                        <button class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-500/20 text-zinc-400 border border-zinc-500/30 hover:bg-zinc-500/30">
                            Solo Whitelist
                        </button>
                        <button class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-500/20 text-zinc-400 border border-zinc-500/30 hover:bg-zinc-500/30">
                            Solo Blacklist
                        </button>
                        <button class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-500/20 text-zinc-400 border border-zinc-500/30 hover:bg-zinc-500/30">
                            Sin Clasificar
                        </button>
                    </div>

                    <!-- Lista de IPs Detectadas -->
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($detectedIps as $ip)
                        <div class="flex items-center justify-between p-3 bg-zinc-800/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full 
                                    @if($ip->in_whitelist) bg-green-400
                                    @elseif($ip->in_blacklist) bg-red-400
                                    @else bg-blue-400 @endif">
                                </div>
                                <div>
                                    <div class="text-white font-mono text-sm">{{ $ip->ip }}</div>
                                    <div class="text-zinc-400 text-xs">
                                        @if($ip->in_whitelist)
                                        <span class="text-green-400">✓ En lista blanca</span>
                                        @elseif($ip->in_blacklist)
                                        <span class="text-red-400">✗ En lista negra</span>
                                        @else
                                        <span class="text-blue-400">○ Sin clasificar</span>
                                        @endif
                                        • {{ $ip->access_count }} accesos
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(!$ip->in_whitelist && !$ip->in_blacklist)
                                <form action="{{ route('control-acceso.add-to-whitelist') }}" method="POST" class="flex items-center">
                                    @csrf
                                    <input type="hidden" name="ip" value="{{ $ip->ip }}">
                                    <button type="submit" class="bg-green-500 hover:bg-green-400 text-white text-xs font-medium py-1 px-2 rounded transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Whitelist
                                    </button>
                                </form>
                                <form action="{{ route('control-acceso.add-to-blacklist') }}" method="POST" class="flex items-center">
                                    @csrf
                                    <input type="hidden" name="ip" value="{{ $ip->ip }}">
                                    <button type="submit" class="bg-red-500 hover:bg-red-400 text-white text-xs font-medium py-1 px-2 rounded transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Blacklist
                                    </button>
                                </form>
                                @else
                                <span class="text-xs px-2 py-1 rounded-full 
                                    @if($ip->in_whitelist) bg-green-500/20 text-green-400
                                    @else bg-red-500/20 text-red-400 @endif">
                                    {{ $ip->in_whitelist ? 'Whitelist' : 'Blacklist' }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-zinc-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>No hay IPs detectadas</p>
                            <p class="text-sm mt-1">Las IPs aparecerán aquí cuando accedan al sistema</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Columna 2: Gestión Manual -->
                <div class="space-y-6">
                    <!-- Agregar IP Manualmente -->
                    <div class="dashboard-card">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Agregar IP Manualmente
                        </h3>

                        <form action="{{ route('control-acceso.add-ip-manual') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="ip_cidr" class="block text-white text-sm font-medium mb-2">
                                    Dirección IP
                                </label>
                                <input type="text" 
                                    id="ip_cidr"
                                    name="ip_cidr" 
                                    placeholder="192.168.1.1 o 192.168.1.0/24" 
                                    class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2 text-white placeholder-zinc-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    required
                                >
                                @error('ip_cidr')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-white text-sm font-medium mb-2">
                                    Tipo de Lista
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center p-3 bg-green-500/10 border border-green-500/20 rounded-lg cursor-pointer hover:bg-green-500/20 transition-colors">
                                        <input type="radio" name="list_type" value="whitelist" class="text-green-500 focus:ring-green-500" checked>
                                        <span class="text-green-400 text-sm font-medium ml-2">Lista Blanca</span>
                                    </label>
                                    <label class="flex items-center p-3 bg-red-500/10 border border-red-500/20 rounded-lg cursor-pointer hover:bg-red-500/20 transition-colors">
                                        <input type="radio" name="list_type" value="blacklist" class="text-red-500 focus:ring-red-500">
                                        <span class="text-red-400 text-sm font-medium ml-2">Lista Negra</span>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Agregar IP
                            </button>
                        </form>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="dashboard-card">
                        <h3 class="text-lg font-semibold text-white mb-4">Estadísticas</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-400">IPs en Whitelist:</span>
                                <span class="text-green-400 font-semibold">{{ $whitelistCount }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-400">IPs en Blacklist:</span>
                                <span class="text-red-400 font-semibold">{{ $blacklistCount }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-400">IPs Sin Clasificar:</span>
                                <span class="text-blue-400 font-semibold">{{ $unclassifiedCount }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-zinc-700">
                                <span class="text-zinc-400">Total Detectadas:</span>
                                <span class="text-white font-semibold">{{ $totalIps }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="dashboard-card">
                        <h3 class="text-lg font-semibold text-white mb-4">Acciones Rápidas</h3>
                        <div class="space-y-3">
                            <a href="{{ route('control-acceso.import-ips-form') }}" class="w-full bg-blue-500 hover:bg-blue-400 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 justify-center text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                                Importar IPs
                            </a>
                            <a href="{{ route('control-acceso.export-ips') }}" class="w-full bg-green-500 hover:bg-green-400 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 justify-center text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Exportar Listas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>