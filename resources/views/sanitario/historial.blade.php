<x-layouts.app>
    <div class="max-w-6xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Historial: {{ $lista->nombre }}</h1>
                <p class="text-zinc-400">Registros anteriores</p>
            </div>
            <a href="{{ route('sanitario.index') }}" class="text-zinc-400 hover:text-white">Volver</a>
        </div>

        <div class="bg-zinc-800 rounded-lg border border-zinc-700 overflow-hidden">
            <table class="w-full text-left text-sm text-zinc-400">
                <thead class="bg-zinc-900/50 text-zinc-200 uppercase font-medium">
                    <tr>
                        <th class="px-6 py-3">Fecha y Hora</th>
                        <th class="px-6 py-3">Usuario</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    @forelse($registros as $registro)
                        <tr class="hover:bg-zinc-700/30 transition-colors">
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($registro->fecha_hora)->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">{{ $registro->usuario->name ?? 'Usuario Eliminado' }}</td>
                            <td class="px-6 py-4 text-right">
                                {{-- Here we could add a button to see details of this specific submission --}}
                                <span class="text-xs bg-green-500/10 text-green-500 px-2 py-1 rounded">Completado</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-zinc-500">
                                No hay registros históricos para esta lista.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
