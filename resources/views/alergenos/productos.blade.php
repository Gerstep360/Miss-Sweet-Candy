{{-- resources/views/alergenos/productos.blade.php --}}
<x-layouts.app :title="__('Productos con ' . $alergeno->nombre . ' - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('alergenos.index') }}" class="inline-flex items-center gap-2 text-zinc-400 hover:text-zinc-300 transition-colors mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Alérgenos
                </a>
                
                <h1 class="text-3xl font-bold text-zinc-100 flex items-center gap-3">
                    <span class="text-4xl">{{ $alergeno->icono ?? '⚠️' }}</span>
                    Productos con: {{ $alergeno->nombre }}
                </h1>
                <p class="text-zinc-400 mt-2">{{ $productos->total() }} productos contienen este alérgeno</p>
            </div>

            {{-- Tabla de productos --}}
            <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-zinc-800/50 border-b border-zinc-700">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-300 uppercase">Producto</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-300 uppercase">Categoría</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-300 uppercase">Precio</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-300 uppercase">Nivel de Presencia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse ($productos as $producto)
                                <tr class="hover:bg-zinc-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-zinc-100">{{ $producto->nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-zinc-400">
                                        {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-zinc-100 font-medium">
                                        ${{ number_format($producto->precio, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $nivelPresencia = $producto->alergenos->find($alergeno->id)->pivot->nivel_presencia ?? 'contiene';
                                            $badges = [
                                                'contiene' => ['bg' => 'bg-red-500/20', 'text' => 'text-red-300', 'border' => 'border-red-500/50', 'label' => 'Contiene'],
                                                'puede_contener' => ['bg' => 'bg-orange-500/20', 'text' => 'text-orange-300', 'border' => 'border-orange-500/50', 'label' => 'Puede contener'],
                                                'trazas' => ['bg' => 'bg-yellow-500/20', 'text' => 'text-yellow-300', 'border' => 'border-yellow-500/50', 'label' => 'Trazas'],
                                            ];
                                            $badge = $badges[$nivelPresencia] ?? $badges['contiene'];
                                        @endphp
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-zinc-400">
                                        No hay productos asociados a este alérgeno
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if ($productos->hasPages())
                    <div class="px-6 py-4 border-t border-zinc-800">
                        {{ $productos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
