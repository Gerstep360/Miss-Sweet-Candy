{{-- resources/views/admin/controlAcceso/import-ips.blade.php --}}
<x-layouts.app :title="__('Importar IPs - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Importar Lista de IPs</h1>
                        <p class="text-zinc-300">Agrega múltiples IPs a la lista blanca</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('control-acceso.config-ips') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <!-- Instrucciones -->
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 mb-6">
                    <h4 class="text-blue-400 font-semibold mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Instrucciones
                    </h4>
                    <ul class="text-blue-300 text-sm space-y-2">
                        <li>• Ingresa una dirección IP por línea</li>
                        <li>• Formatos aceptados: IPv4 e IPv6</li>
                        <li>• Ejemplos válidos: 192.168.1.1, 10.0.0.1, 2001:db8::1</li>
                        <li>• También puedes usar formato CIDR: 192.168.1.0/24</li>
                        <li>• Las IPs duplicadas serán ignoradas</li>
                    </ul>
                </div>

                <!-- Formulario -->
                <form action="{{ route('control-acceso.import-ips') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label for="ips" class="block text-white font-medium mb-3">
                            Lista de IPs
                        </label>
                        <textarea 
                            name="ips" 
                            id="ips" 
                            rows="12" 
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white font-mono text-sm placeholder-zinc-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Ejemplo:
192.168.1.1
10.0.0.1
172.16.0.1
2001:db8::1
192.168.1.0/24"
                            required
                        >{{ old('ips') }}</textarea>
                        @error('ips')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-400 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                            Importar IPs
                        </button>
                        
                        <button type="button" onclick="document.getElementById('ips').value = ''" class="bg-zinc-600 hover:bg-zinc-500 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Limpiar
                        </button>
                    </div>
                </form>

                <!-- Ejemplo -->
                <div class="mt-6 pt-6 border-t border-zinc-700">
                    <h4 class="text-amber-400 font-semibold mb-3">Ejemplo de formato:</h4>
                    <div class="bg-zinc-800/50 rounded-lg p-4">
                        <pre class="text-zinc-300 text-sm font-mono">192.168.1.1
10.0.0.1
172.16.0.1
2001:db8::1
192.168.1.0/24</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>