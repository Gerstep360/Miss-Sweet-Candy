{{-- filepath: resources/views/admin/auditoria/backups.blade.php --}}
<x-layouts.app :title="__('Gestión de Backups')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('auditoria.index') }}" 
                           class="inline-flex items-center gap-2 text-zinc-300 hover:text-white px-2 py-1 rounded-lg hover:bg-zinc-800 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Volver
                        </a>
                        <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Gestión de Backups</h1>
                            <p class="text-sm text-zinc-400">Crea, restaura y gestiona respaldos del sistema</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Crear backup manual -->
            <div class="dashboard-card mb-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Crear Backup Manual
                </h2>
                <form method="POST" action="{{ route('auditoria.backup.crear') }}" class="space-y-4">
                    @csrf
                    <div class="grid sm:grid-cols-3 gap-4">
                        <label class="flex items-center gap-3 bg-zinc-900 border border-zinc-700 rounded-lg p-4 cursor-pointer hover:border-amber-500/50 transition">
                            <input type="radio" name="tipo" value="completo" checked class="text-amber-500">
                            <div>
                                <div class="font-medium text-white">Completo</div>
                                <div class="text-xs text-zinc-400">Base de datos + Archivos</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 bg-zinc-900 border border-zinc-700 rounded-lg p-4 cursor-pointer hover:border-amber-500/50 transition">
                            <input type="radio" name="tipo" value="base_datos" class="text-amber-500">
                            <div>
                                <div class="font-medium text-white">Solo Base de Datos</div>
                                <div class="text-xs text-zinc-400">Dump SQL comprimido</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 bg-zinc-900 border border-zinc-700 rounded-lg p-4 cursor-pointer hover:border-amber-500/50 transition">
                            <input type="radio" name="tipo" value="archivos" class="text-amber-500">
                            <div>
                                <div class="font-medium text-white">Solo Archivos</div>
                                <div class="text-xs text-zinc-400">Archivos críticos</div>
                            </div>
                        </label>
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white py-2.5 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-amber-500/30 hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Crear Backup Ahora
                    </button>
                </form>
            </div>

            <!-- Configuración de backups automáticos -->
            <div class="dashboard-card mb-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configuración de Backups Automáticos
                </h2>
                <form method="POST" action="{{ route('auditoria.backup.configurar') }}" class="space-y-4">
                    @csrf
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-zinc-300 text-sm font-medium mb-2">Frecuencia</label>
                            <select name="frecuencia" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                                <option value="diaria" {{ ($configuracion['frecuencia'] ?? '') == 'diaria' ? 'selected' : '' }}>Diaria</option>
                                <option value="semanal" {{ ($configuracion['frecuencia'] ?? '') == 'semanal' ? 'selected' : '' }}>Semanal</option>
                                <option value="mensual" {{ ($configuracion['frecuencia'] ?? '') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-zinc-300 text-sm font-medium mb-2">Hora</label>
                            <input type="time" name="hora" value="{{ $configuracion['hora'] ?? '02:00' }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-zinc-300 text-sm font-medium mb-2">Tipo</label>
                            <select name="tipo" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                                <option value="completo" {{ ($configuracion['tipo'] ?? '') == 'completo' ? 'selected' : '' }}>Completo</option>
                                <option value="base_datos" {{ ($configuracion['tipo'] ?? '') == 'base_datos' ? 'selected' : '' }}>Base de Datos</option>
                                <option value="archivos" {{ ($configuracion['tipo'] ?? '') == 'archivos' ? 'selected' : '' }}>Archivos</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-zinc-300 text-sm font-medium mb-2">Retención (días)</label>
                            <input type="number" name="retencion" value="{{ $configuracion['retencion'] ?? 30 }}" min="1" max="365" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="notificar_email" id="notificar_email" {{ ($configuracion['notificar_email'] ?? false) ? 'checked' : '' }} class="text-amber-500 rounded">
                        <label for="notificar_email" class="text-sm text-zinc-300">Notificar por email al finalizar</label>
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white py-2.5 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Configuración
                    </button>
                </form>
            </div>

            <!-- Lista de backups -->
            <div class="dashboard-card">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Backups Existentes
                </h2>
                @forelse($backups as $backup)
                    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 mb-3 hover:border-amber-500/30 transition-colors">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-sm font-medium text-white">{{ $backup['archivo'] }}</span>
                                    <span class="text-xs px-2 py-1 bg-zinc-800 rounded text-zinc-300">{{ $backup['tipo'] }}</span>
                                </div>
                                <div class="text-xs text-zinc-400 flex items-center gap-4">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($backup['fecha'])->format('d/m/Y H:i:s') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                        </svg>
                                        {{ number_format($backup['tamaño'] / 1024 / 1024, 2) }} MB
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('auditoria.backup.descargar', $backup['archivo']) }}" 
                                   class="bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Descargar
                                </a>
                                <form method="POST" action="{{ route('auditoria.backup.eliminar', $backup['archivo']) }}" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este backup?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-zinc-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <p>No hay backups disponibles</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
