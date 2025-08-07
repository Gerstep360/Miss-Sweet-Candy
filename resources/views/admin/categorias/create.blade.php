{{-- filepath: resources/views/admin/categorias/create.blade.php --}}
<x-layouts.app :title="__('Nueva Categoría - Café Aroma')">
    <div class="max-w-xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-6">Nueva Categoría</h1>
        <form action="{{ route('categorias.store') }}" method="POST" class="bg-zinc-900 rounded-lg p-6 shadow">
            @csrf

            <div class="mb-4">
                <label for="nombre" class="block text-zinc-300 font-medium mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required maxlength="50" placeholder="Ejemplo: Café">
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('categorias.index') }}" class="text-zinc-400 hover:text-white">Cancelar</a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-semibold px-6 py-2 rounded-lg transition-colors">
                    Crear Categoría
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>