{{-- filepath: resources/views/cajero/reservas/index.blade.php --}}
<x-layouts.app :title="__('Gestión de Reservas')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Gestión de Reservas</h1>
                            <p class="text-sm text-zinc-400">Administra las reservas de los clientes</p>
                        </div>
                    </div>
                    <a href="{{ route('cajero.reservas.create') }}" 
                       class="bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-purple-500/30 hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="font-semibold">Nueva Reserva</span>
                    </a>
                </div>

                @if(session('success'))
                    <div class="mt-4 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mt-4 bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Estadísticas -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                    <div class="bg-purple-500/10 border border-purple-500/20 rounded-xl p-4">
                        <div class="text-sm text-purple-400 mb-1">Total Hoy</div>
                        <div class="text-2xl font-bold text-white">{{ $stats['total_hoy'] }}</div>
                    </div>
                    <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-4">
                        <div class="text-sm text-green-400 mb-1">Confirmadas</div>
                        <div class="text-2xl font-bold text-white">{{ $stats['confirmadas'] }}</div>
                    </div>
                    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-4">
                        <div class="text-sm text-yellow-400 mb-1">Pendientes</div>
                        <div class="text-2xl font-bold text-white">{{ $stats['pendientes'] }}</div>
                    </div>
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
                        <div class="text-sm text-blue-400 mb-1">Cumplidas</div>
                        <div class="text-2xl font-bold text-white">{{ $stats['cumplidas'] }}</div>
                    </div>
                </div>

                <!-- Filtros -->
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mt-6">
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Fecha</label>
                        <input type="date" name="fecha" value="{{ request('fecha', date('Y-m-d')) }}" 
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Estado</label>
                        <select name="estado" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 text-sm">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="confirmada" {{ request('estado') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="cumplida" {{ request('estado') == 'cumplida' ? 'selected' : '' }}>Cumplida</option>
                            <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 text-sm font-medium mb-2">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, email, mesa..." 
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 text-sm">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-500 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filtrar
                        </button>
                        <a href="{{ route('cajero.reservas.index') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg transition-colors">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Lista de reservas -->
            <div class="grid grid-cols-1 gap-4">
                @forelse($reservas as $reserva)
                    @php
                        $estadoColors = [
                            'pendiente' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                            'confirmada' => 'bg-green-500/20 text-green-400 border-green-500/30',
                            'cumplida' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                            'cancelada' => 'bg-red-500/20 text-red-400 border-red-500/30',
                        ];
                        $color = $estadoColors[$reserva->estado] ?? $estadoColors['pendiente'];
                    @endphp

                    <div class="dashboard-card border-l-4 {{ $color }}">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $color }}">
                                        {{ strtoupper($reserva->estado) }}
                                    </span>
                                    <span class="text-sm text-zinc-400">{{ $reserva->cliente->name }}</span>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $reserva->fecha->format('d/m/Y') }}
                                    </div>
                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($reserva->hora)->format('H:i') }}
                                    </div>
                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        {{ $reserva->numero_personas }} personas
                                    </div>
                                    <div class="flex items-center gap-2 text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                        </svg>
                                        {{ $reserva->mesa->nombre }}
                                    </div>
                                </div>

                                @if($reserva->observaciones)
                                    <div class="mt-2 text-xs text-zinc-500">
                                        📝 {{ $reserva->observaciones }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('cajero.reservas.show', $reserva) }}" 
                                   class="bg-blue-600 hover:bg-blue-500 text-white p-2 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                @if($reserva->estado === 'pendiente')
                                    <form method="POST" action="{{ route('cajero.reservas.confirmar', $reserva) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-500 text-white p-2 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                                @if($reserva->estado === 'confirmada')
                                    <form method="POST" action="{{ route('cajero.reservas.confirmar-llegada', $reserva) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white px-3 py-2 rounded-lg transition-colors text-sm font-medium">
                                            Confirmar Llegada
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($reserva->estado, ['pendiente', 'confirmada']))
                                    <form method="POST" action="{{ route('cajero.reservas.cancelar', $reserva) }}" class="inline" onsubmit="return confirm('¿Cancelar esta reserva?')">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-500 text-white p-2 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="dashboard-card text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-zinc-700/30 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-lg text-zinc-400 font-medium mb-2">No hay reservas</p>
                        <p class="text-sm text-zinc-500">No se encontraron reservas con los filtros aplicados.</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if($reservas->hasPages())
                <div class="mt-6">
                    {{ $reservas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
