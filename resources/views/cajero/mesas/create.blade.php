{{-- resources/views/cajero/mesas/create.blade.php --}}
<x-layouts.app :title="__('Nueva Mesa')">
    <div class="max-w-xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-6">Nueva Mesa</h1>
        <form action="{{ route('mesas.store') }}" method="POST" class="bg-zinc-900 rounded-lg p-6 shadow">
            @csrf

            <div class="mb-4">
                <label for="nombre" class="block text-zinc-300 font-medium mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required maxlength="30" placeholder="Ejemplo: Mesa 1">
            </div>

            <div class="mb-4">
                <label for="estado" class="block text-zinc-300 font-medium mb-2">Estado</label>
                <select name="estado" id="estado" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required>
                    <option value="libre" {{ old('estado') == 'libre' ? 'selected' : '' }}>Libre</option>
                    <option value="ocupada" {{ old('estado') == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                    <option value="reservada" {{ old('estado') == 'reservada' ? 'selected' : '' }}>Reservada</option>
                    <option value="fusionada" {{ old('estado') == 'fusionada' ? 'selected' : '' }}>Fusionada</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="capacidad" class="block text-zinc-300 font-medium mb-2">Capacidad</label>
                <input type="number" name="capacidad" id="capacidad" value="{{ old('capacidad', 4) }}" min="1" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required>
            </div>

            <div class="mb-4">
                <label for="fusion_id" class="block text-zinc-300 font-medium mb-2">Fusionar con Mesa</label>
                <select name="fusion_id" id="fusion_id" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500">
                    <option value="">Sin fusión</option>
                    @foreach($mesas as $mesa)
                        <option value="{{ $mesa->id }}" {{ old('fusion_id') == $mesa->id ? 'selected' : '' }}>{{ $mesa->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('mesas.index') }}" class="text-zinc-400 hover:text-white">Cancelar</a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-semibold px-6 py-2 rounded-lg transition-colors">
                    Crear Mesa
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>