{{-- resources/views/admin/categorias/edit.blade.php --}}
<x-layouts.app :title="__('Editar Categoría - Café Aroma')">
    <div class="max-w-xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-6">Editar Categoría</h1>
        <form action="{{ route('categorias.update', $categoria) }}" method="POST" class="bg-zinc-900 rounded-lg p-6 shadow">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nombre" class="block text-zinc-300 font-medium mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $categoria->nombre) }}" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required maxlength="50">
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('categorias.index') }}" class="text-zinc-400 hover:text-white">Cancelar</a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-semibold px-6 py-2 rounded-lg transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>