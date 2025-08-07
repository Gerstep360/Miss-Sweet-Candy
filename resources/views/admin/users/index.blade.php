{{-- filepath: resources/views/admin/users/index.blade.php --}}
<x-layouts.app :title="__('Gestión de Usuarios - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Gestión de Usuarios</h1>
                        <p class="text-zinc-300">Administra usuarios y sus roles en el sistema</p>
                    </div>
                    <a href="{{ route('users.create') }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nuevo Usuario
                    </a>
                </div>
            </div>

            <!-- Filtros y búsqueda -->
            <div class="dashboard-card mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Búsqueda -->
                    <div class="md:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Buscar por nombre o email..." 
                               class="w-full px-4 py-2 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors">
                    </div>
                    
                    <!-- Filtro por rol -->
                    <div class="flex gap-2">
                        <select name="role" class="flex-1 px-4 py-2 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        
                        <button type="submit" class="bg-zinc-700 hover:bg-zinc-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                        
                        @if(request('search') || request('role'))
                        <a href="{{ route('users.index') }}" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Lista de usuarios -->
            <div class="grid gap-6">
                @forelse($users as $user)
                <div class="dashboard-card">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                <span class="text-amber-400 font-medium text-lg">{{ $user->initials() }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">{{ $user->name }}</h3>
                                <p class="text-zinc-400">{{ $user->email }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    @foreach($user->roles as $role)
                                    <span class="bg-amber-500/20 text-amber-400 text-xs px-2 py-1 rounded capitalize">{{ $role->name }}</span>
                                    @endforeach
                                    
                                    @if($user->needsPasswordSetup())
                                    <span class="bg-orange-500/20 text-orange-400 text-xs px-2 py-1 rounded">Pendiente activación</span>
                                    @elseif($user->isFullyActive())
                                    <span class="bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded">Activo</span>
                                    @else
                                    <span class="bg-blue-500/20 text-blue-400 text-xs px-2 py-1 rounded">Registrado</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('users.show', $user) }}" class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-3 rounded-lg transition-colors" title="Ver detalles">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('users.edit', $user) }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-3 rounded-lg transition-colors" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-500 text-white py-2 px-3 rounded-lg transition-colors" onclick="return confirm('¿Estás seguro?')" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="dashboard-card text-center py-12">
                    <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-medium mb-2">No se encontraron usuarios</h3>
                    <p class="text-zinc-400 mb-4">{{ request('search') || request('role') ? 'No hay usuarios que coincidan con tu búsqueda.' : 'Aún no hay usuarios creados.' }}</p>
                </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if($users->hasPages())
            <div class="mt-8">
                {{ $users->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>