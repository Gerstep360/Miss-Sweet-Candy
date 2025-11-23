{{-- resources/views/admin/productos/edit.blade.php --}}
<x-layouts.app :title="__('Editar Producto - Café Aroma')">
    <div class="max-w-2xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-6">Editar Producto</h1>
        <div class="bg-zinc-900 rounded-lg p-6 shadow mb-8 flex flex-col items-center">
            <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="w-48 h-48 object-cover rounded-xl border border-zinc-800 bg-zinc-900 mb-4">
            <h2 class="text-xl font-semibold text-white mb-2">{{ $producto->nombre }}</h2>
            <div class="flex gap-2 mb-2">
                <span class="bg-amber-500/20 text-amber-400 text-xs px-2 py-1 rounded capitalize">{{ $producto->categoria->nombre }}</span>
                @if($producto->unidad)
                    <span class="bg-zinc-800/50 text-zinc-300 text-xs px-2 py-1 rounded">{{ $producto->unidad }}</span>
                @endif
                <span class="bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded font-bold">${{ number_format($producto->precio, 2) }}</span>
            </div>
            @if($producto->descripcion)
                <p class="text-zinc-300 text-sm mt-2">{{ $producto->descripcion }}</p>
            @endif
        </div>
        <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data" class="bg-zinc-900 rounded-lg p-6 shadow">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nombre" class="block text-zinc-300 font-medium mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $producto->nombre) }}" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required maxlength="100">
            </div>

            <div class="mb-4">
                <label for="categoria_id" class="block text-zinc-300 font-medium mb-2">Categoría</label>
                <select name="categoria_id" id="categoria_id" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" @if($categoria->id == $producto->categoria_id) selected @endif>{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="unidad" class="block text-zinc-300 font-medium mb-2">Unidad</label>
                <input type="text" name="unidad" id="unidad" value="{{ old('unidad', $producto->unidad) }}" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" maxlength="30">
            </div>

            <div class="mb-4">
                <label for="precio" class="block text-zinc-300 font-medium mb-2">Precio</label>
                <input type="number" name="precio" id="precio" value="{{ old('precio', $producto->precio) }}" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required min="0" step="0.01">
            </div>

            <div class="mb-4">
                <label for="imagen" class="block text-zinc-300 font-medium mb-2">Imagen (opcional)</label>
                <input type="file" name="imagen" id="imagen" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500">
                <p class="text-xs text-zinc-400 mt-1">Si seleccionas una nueva imagen, reemplazará la actual.</p>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('productos.index') }}" class="text-zinc-400 hover:text-white">Cancelar</a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-semibold px-6 py-2 rounded-lg transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>