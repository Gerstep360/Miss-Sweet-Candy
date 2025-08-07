{{-- filepath: resources/views/cajero/mesas/edit.blade.php --}}
<x-layouts.app :title="__('Editar Mesa')">
    <div class="max-w-xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-6">Editar Mesa</h1>
        <form action="{{ route('mesas.update', $mesa) }}" method="POST" class="bg-zinc-900 rounded-lg p-6 shadow">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nombre" class="block text-zinc-300 font-medium mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $mesa->nombre) }}" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required maxlength="30">
            </div>

            <div class="mb-4">
                <label for="estado" class="block text-zinc-300 font-medium mb-2">Estado</label>
                <select name="estado" id="estado" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required>
                    <option value="libre" {{ old('estado', $mesa->estado) == 'libre' ? 'selected' : '' }}>Libre</option>
                    <option value="ocupada" {{ old('estado', $mesa->estado) == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                    <option value="reservada" {{ old('estado', $mesa->estado) == 'reservada' ? 'selected' : '' }}>Reservada</option>
                    <option value="fusionada" {{ old('estado', $mesa->estado) == 'fusionada' ? 'selected' : '' }}>Fusionada</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="capacidad" class="block text-zinc-300 font-medium mb-2">Capacidad</label>
                <input type="number" name="capacidad" id="capacidad" value="{{ old('capacidad', $mesa->capacidad) }}" min="1" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required>
            </div>

            <div class="mb-4">
                <label for="fusion_id" class="block text-zinc-300 font-medium mb-2">Fusionar con Mesa</label>
                @can('fusionar-mesas')
                    <select name="fusion_id" id="fusion_id" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500">
                        <option value="">Sin fusión</option>
                        @foreach($mesas as $fusionable)
                            <option value="{{ $fusionable->id }}" {{ old('fusion_id', $mesa->fusion_id) == $fusionable->id ? 'selected' : '' }}>{{ $fusionable->nombre }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="hidden" name="fusion_id" value="{{ $mesa->fusion_id }}">
                    <div class="text-zinc-400 text-sm">No tienes permiso para fusionar mesas.</div>
                @endcan
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('mesas.index') }}" class="text-zinc-400 hover:text-white">Cancelar</a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-semibold px-6 py-2 rounded-lg transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>