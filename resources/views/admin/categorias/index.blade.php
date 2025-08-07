{{-- filepath: resources/views/admin/categorias/index.blade.php --}}
<x-layouts.app :title="__('Gestión de Categorías - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Gestión de Categorías</h1>
                        <p class="text-zinc-300">Administra las categorías de productos del café</p>
                    </div>
                    <a href="{{ route('categorias.create') }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nueva Categoría
                    </a>
                </div>
            </div>

            <!-- Lista de categorías -->
            <div class="grid gap-6">
                @forelse($categorias as $categoria)
                <div class="dashboard-card flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ $categoria->nombre }}</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('categorias.edit', $categoria) }}" class="bg-zinc-700 hover:bg-zinc-600 text-white py-2 px-3 rounded-lg transition-colors" title="Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-500 text-white py-2 px-3 rounded-lg transition-colors" onclick="return confirm('¿Estás seguro?')" title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="dashboard-card text-center py-12">
                    <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-medium mb-2">No se encontraron categorías</h3>
                    <p class="text-zinc-400 mb-4">Aún no hay categorías creadas.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>