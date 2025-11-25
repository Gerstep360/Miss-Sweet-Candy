<x-layouts.app>
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">{{ $lista->nombre }}</h1>
            <a href="{{ route('sanitario.index') }}" class="text-zinc-400 hover:text-white">Volver</a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 text-red-500 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('sanitario.store', $lista) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            @foreach ($lista->items as $item)
                <div class="p-6 bg-zinc-800 rounded-lg border border-zinc-700">
                    <label class="block text-lg font-medium text-white mb-4">{{ $item->texto }}</label>

                    @if ($item->tipo === 'check')
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="respuestas[{{ $item->id }}][check]"
                                class="w-6 h-6 rounded border-zinc-600 bg-zinc-700 text-amber-500 focus:ring-amber-500">
                            <span class="text-zinc-300">Cumple</span>
                        </div>
                    @elseif($item->tipo === 'numero')
                        <input type="number" step="0.01" name="respuestas[{{ $item->id }}][numero]"
                            class="w-full bg-zinc-900 border-zinc-700 rounded-lg text-white px-4 py-2 focus:border-amber-500 focus:ring-amber-500"
                            placeholder="Ingrese valor numérico">
                    @elseif($item->tipo === 'texto')
                        <textarea name="respuestas[{{ $item->id }}][texto]" rows="3"
                            class="w-full bg-zinc-900 border-zinc-700 rounded-lg text-white px-4 py-2 focus:border-amber-500 focus:ring-amber-500"
                            placeholder="Ingrese observaciones"></textarea>
                    @elseif($item->tipo === 'foto')
                        <input type="file" name="respuestas[{{ $item->id }}][foto]" accept="image/*"
                            class="block w-full text-sm text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-500 file:text-white hover:file:bg-amber-600">
                    @endif

                    <!-- Hidden input to ensure item_id is sent even if unchecked/empty -->
                    <input type="hidden" name="respuestas[{{ $item->id }}][item_id]" value="{{ $item->id }}">
                </div>
            @endforeach

            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg transition-colors">
                    Guardar Registro
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
