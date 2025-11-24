{{-- resources/views/admin/controlAcceso/backup-codes.blade.php --}}
<x-layouts.app :title="__('Códigos de Respaldo - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Códigos de Respaldo</h1>
                        <p class="text-zinc-300">Usa estos códigos si no puedes acceder a tu correo electrónico</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('control-acceso.two-factor') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="text-green-400 text-sm">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            <div class="dashboard-card mb-8">
                <!-- Información Importante -->
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4 mb-6">
                    <h4 class="text-amber-400 font-semibold mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Información Importante
                    </h4>
                    <ul class="text-amber-300 text-sm space-y-1">
                        <li>• Guarda estos códigos en un lugar seguro</li>
                        <li>• Cada código solo se puede usar una vez</li>
                        <li>• Si pierdes todos los códigos, contacta al administrador</li>
                        <li>• Puedes generar nuevos códigos en cualquier momento</li>
                    </ul>
                </div>

                <!-- Lista de Códigos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    @foreach($backupCodes as $index => $code)
                    <div class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4 text-center">
                        <div class="font-mono text-lg text-white mb-1">{{ $code['code'] }}</div>
                        <div class="text-zinc-400 text-xs">
                            @if($code['used'])
                            <span class="text-red-400">❌ Usado</span>
                            @else
                            <span class="text-green-400">✅ Disponible</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Acciones -->
                <div class="flex flex-wrap gap-3 pt-6 border-t border-zinc-700">
                    <form action="{{ route('control-acceso.generate-backup-codes') }}" method="POST" class="flex items-center">
                        @csrf
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-400 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2"
                                onclick="return confirm('¿Estás seguro de generar nuevos códigos? Los códigos actuales dejarán de funcionar.')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Generar Nuevos Códigos
                        </button>
                    </form>

                    <button onclick="window.print()" class="bg-green-500 hover:bg-green-400 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimir Códigos
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>