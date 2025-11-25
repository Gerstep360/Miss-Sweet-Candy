{{-- filepath: resources/views/admin/auditoria/intentos-login.blade.php --}}
<x-layouts.app :title="__('Intentos de Login')">
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
                        <div class="w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Intentos de Login</h1>
                            <p class="text-sm text-zinc-400">Registro de accesos exitosos y fallidos</p>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <form method="GET" action="{{ route('auditoria.intentos-login') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Email</label>
                        <input type="email" name="email" value="{{ request('email') }}" placeholder="usuario@ejemplo.com" 
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">IP</label>
                        <input type="text" name="ip" value="{{ request('ip') }}" placeholder="192.168.1.1" 
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Resultado</label>
                        <select name="exitoso" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                            <option value="">Todos</option>
                            <option value="1" {{ request('exitoso') === '1' ? 'selected' : '' }}>Exitosos</option>
                            <option value="0" {{ request('exitoso') === '0' ? 'selected' : '' }}>Fallidos</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Desde</label>
                        <input type="date" name="desde" value="{{ request('desde') }}" 
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-4 flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filtrar
                        </button>
                        <a href="{{ route('auditoria.intentos-login') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Lista de intentos -->
            <div class="dashboard-card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Registro de Intentos
                    </h2>
                    <div class="text-zinc-400 text-sm">
                        Mostrando <span class="text-white font-semibold">{{ $intentos->count() }}</span> de
                        <span class="text-white font-semibold">{{ $intentos->total() }}</span> intentos
                    </div>
                </div>

                @forelse($intentos as $intento)
                    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 mb-3 hover:border-amber-500/30 transition-colors">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $intento->exitoso ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current inline-block mr-1.5"></span>
                                        {{ $intento->exitoso ? 'Exitoso' : 'Fallido' }}
                                    </span>
                                    <span class="font-medium text-white">{{ $intento->email }}</span>
                                </div>
                                <div class="text-xs text-zinc-400 flex items-center gap-4">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 12l4-4m-4 4l4 4"/>
                                        </svg>
                                        IP: <span class="font-mono text-zinc-300">{{ $intento->ip ?? '—' }}</span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $intento->created_at_formatted ?? '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-zinc-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p>No se encontraron intentos de login</p>
                    </div>
                @endforelse

                @if($intentos->hasPages())
                    <div class="mt-6">
                        {{ $intentos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>