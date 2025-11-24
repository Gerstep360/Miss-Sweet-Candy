{{-- filepath: resources/views/admin/auditoria/index.blade.php --}}
<x-layouts.app :title="__('Auditoría del Sistema')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">Auditoría del Sistema</h1>
                                <p class="text-sm text-zinc-400">Historial completo de actividades y eventos</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <a href="{{ route('auditoria.backups') }}" 
                           class="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span class="font-semibold">Backups</span>
                        </a>
                        <a href="{{ route('auditoria.anomalias') }}" 
                           class="bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-red-500/30 hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="font-semibold">Anomalías</span>
                        </a>
                        <a href="{{ route('auditoria.intentos-login') }}" 
                           class="bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-purple-500/30 hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span class="font-semibold">Login</span>
                        </a>
                    </div>
                </div>

                @if(session('warning'))
                    <div class="mb-4 bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 px-4 py-3 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            {{ session('warning') }}
                        </div>
                    </div>
                @endif

                <!-- Filtros -->
                <form method="GET" action="{{ route('auditoria.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Usuario</label>
                        <select name="usuario_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                            <option value="">Todos los usuarios</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" {{ request('usuario_id') == $usuario->id ? 'selected' : '' }}>
                                    {{ $usuario->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Acción</label>
                        <select name="accion" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                            <option value="">Todas las acciones</option>
                            @foreach($acciones as $accion)
                                <option value="{{ $accion }}" {{ request('accion') == $accion ? 'selected' : '' }}>
                                    {{ ucfirst($accion) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Entidad</label>
                        <select name="entidad" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                            <option value="">Todas las entidades</option>
                            @foreach($entidades as $entidad)
                                <option value="{{ $entidad }}" {{ request('entidad') == $entidad ? 'selected' : '' }}>
                                    {{ $entidad }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">IP</label>
                        <input type="text" name="ip" value="{{ request('ip') }}" placeholder="192.168.1.1" 
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Desde</label>
                        <input type="date" name="desde" value="{{ request('desde') }}" 
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Hasta</label>
                        <input type="date" name="hasta" value="{{ request('hasta') }}" 
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-2 flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-500 text-white py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filtrar
                        </button>
                        <a href="{{ route('auditoria.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors">
                            Limpiar
                        </a>
                        <a href="{{ route('auditoria.exportar.excel') }}?{{ http_build_query(request()->all()) }}" 
                           class="bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exportar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Lista de auditorías -->
            <div class="grid grid-cols-1 gap-4 sm:gap-6">
                @forelse ($auditorias as $log)
                    @php
                        $accion = strtolower($log->accion ?? '');
                        $badge = 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40';
                        if (str_contains($accion, 'elimin')) {
                            $badge = 'bg-red-500/20 text-red-400 ring-1 ring-red-500/30';
                        } elseif (str_contains($accion, 'crea') || $accion === 'login') {
                            $badge = 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30';
                        } elseif (str_contains($accion, 'actual') || str_contains($accion, 'modif') || str_contains($accion, 'update')) {
                            $badge = 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30';
                        }
                    @endphp

                    <div class="dashboard-card hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 group">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                            <div class="flex-1 space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $badge }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        {{ strtoupper($log->accion) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-zinc-800/80 text-zinc-300 ring-1 ring-zinc-700">
                                        {{ $log->entidad ?? 'Entidad' }} @if($log->entidad_id) #{{ $log->entidad_id }} @endif
                                    </span>
                                    <span class="text-zinc-500 text-xs font-mono">ID: {{ $log->id }}</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>
                                            {{ optional($log->usuario)->name ?? 'Sistema' }}
                                            @if(optional($log->usuario)->email)
                                                <span class="text-zinc-500">· {{ $log->usuario->email }}</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $log->created_at_formatted ?? '—' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-zinc-400">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 12l4-4m-4 4l4 4"/>
                                        </svg>
                                        <span class="font-mono text-xs">{{ $log->ip ?? '—' }}</span>
                                    </div>
                                    <div class="text-zinc-400 truncate text-xs">
                                        {{ Str::limit($log->user_agent ?? '—', 60) }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 lg:min-w-[180px] lg:justify-end">
                                <a href="{{ route('auditoria.show', $log->id) }}" 
                                   class="bg-amber-600 hover:bg-amber-500 text-white p-2 rounded-lg transition-all duration-200 hover:scale-110 active:scale-95"
                                   title="Ver detalle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="dashboard-card text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-zinc-700/30 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-lg text-zinc-400 font-medium mb-2">No se encontraron registros</p>
                        <p class="text-sm text-zinc-500">Intenta ajustar los filtros de búsqueda</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if($auditorias->hasPages())
                <div class="mt-6">
                    {{ $auditorias->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
