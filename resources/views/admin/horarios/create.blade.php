{{--resources/views/admin/horarios/create.blade.php --}}
<x-layouts.app :title="__('Nuevo Horario - Café Aroma')">
    <div class="max-w-xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-6">Nuevo Horario</h1>
        <form action="{{ route('horarios.store') }}" method="POST" class="bg-zinc-900 rounded-lg p-6 shadow">
            @csrf

            <div class="mb-4">
                <label for="dia" class="block text-zinc-300 font-medium mb-2">Día</label>
                <input type="text" name="dia" id="dia" value="{{ old('dia') }}" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required maxlength="10" placeholder="Ejemplo: lunes">
            </div>

            <div class="mb-4">
                <label for="abre" class="block text-zinc-300 font-medium mb-2">Hora de apertura</label>
                <input type="time" name="abre" id="abre" value="{{ old('abre') }}" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required>
            </div>

            <div class="mb-4">
                <label for="cierra" class="block text-zinc-300 font-medium mb-2">Hora de cierre</label>
                <input type="time" name="cierra" id="cierra" value="{{ old('cierra') }}" class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-amber-500" required>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('horarios.index') }}" class="text-zinc-400 hover:text-white">Cancelar</a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-semibold px-6 py-2 rounded-lg transition-colors">
                    Crear Horario
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>