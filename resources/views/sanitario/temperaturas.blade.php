<x-layouts.app>
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Registro de Temperaturas</h1>
            <a href="{{ route('sanitario.index') }}" class="text-zinc-400 hover:text-white">Volver</a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-500/10 border border-green-500/20 text-green-500 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Form -->
            <div class="bg-zinc-800 p-6 rounded-lg border border-zinc-700 h-fit">
                <h2 class="text-lg font-semibold text-white mb-4">Nuevo Registro</h2>
                <form action="{{ route('sanitario.temperaturas.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Equipo / Área</label>
                        <input type="text" name="equipo" required
                            class="w-full bg-zinc-900 border-zinc-700 rounded-lg text-white px-4 py-2 focus:border-amber-500 focus:ring-amber-500"
                            placeholder="Ej: Refrigerador Cocina">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Temperatura (°C)</label>
                        <input type="number" step="0.1" name="temperatura" required
                            class="w-full bg-zinc-900 border-zinc-700 rounded-lg text-white px-4 py-2 focus:border-amber-500 focus:ring-amber-500"
                            placeholder="Ej: 4.5">
                    </div>
                    <button type="submit"
                        class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg transition-colors">
                        Registrar
                    </button>
                </form>
            </div>

            <!-- History -->
            <div class="bg-zinc-800 p-6 rounded-lg border border-zinc-700">
                <h2 class="text-lg font-semibold text-white mb-4">Últimos Registros</h2>
                <div class="space-y-4">
                    @forelse($ultimas as $temp)
                        <div
                            class="flex justify-between items-center p-3 bg-zinc-900/50 rounded-lg border border-zinc-700/50">
                            <div>
                                <p class="text-white font-medium">{{ $temp->equipo }}</p>
                                <p class="text-xs text-zinc-500">{{ $temp->usuario->name ?? 'Usuario' }} •
                                    {{ $temp->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="text-lg font-bold {{ $temp->temperatura > 5 ? 'text-red-400' : 'text-blue-400' }}">
                                    {{ $temp->temperatura }}°C
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-zinc-500 text-center py-4">No hay registros recientes.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
