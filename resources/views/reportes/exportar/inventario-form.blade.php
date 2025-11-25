{{-- filepath: resources/views/reportes/exportar/inventario-form.blade.php --}}
<x-layouts.app :title="__('Exportar Reporte de Inventario')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-6 sm:py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="dashboard-card mb-6">
                <div class="flex items-center gap-4 mb-4">
                    <a href="{{ route('reportes.exportar.index') }}" class="text-zinc-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-white flex items-center gap-2">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Reporte de Inventario
                        </h1>
                        <p class="text-zinc-400">Configura y exporta el reporte de inventario</p>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <form id="export-form" method="POST" class="space-y-6">
                @csrf
                
                <div class="dashboard-card">
                    <h3 class="text-xl font-bold text-white mb-4">Filtros</h3>
                    
                    {{-- Tipo de reporte --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-zinc-300 mb-3">Tipo de Reporte</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="relative flex items-center p-4 bg-zinc-800/50 rounded-lg border-2 border-zinc-700 cursor-pointer hover:border-amber-500/50 transition-all">
                                <input type="radio" name="tipo" value="todo" checked class="hidden peer">
                                <div class="peer-checked:border-amber-500 peer-checked:bg-amber-500/10 w-full border-2 border-transparent rounded-lg p-3 transition-all">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-white">Todo</p>
                                            <p class="text-xs text-zinc-400">Todos los productos</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-4 bg-zinc-800/50 rounded-lg border-2 border-zinc-700 cursor-pointer hover:border-amber-500/50 transition-all">
                                <input type="radio" name="tipo" value="personalizado" class="hidden peer">
                                <div class="peer-checked:border-amber-500 peer-checked:bg-amber-500/10 w-full border-2 border-transparent rounded-lg p-3 transition-all">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-white">Personalizado</p>
                                            <p class="text-xs text-zinc-400">Con filtros específicos</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Filtros personalizados --}}
                    <div id="custom-filters" class="space-y-4 hidden">
                        {{-- Estado del stock --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Estado del Stock</label>
                            <select name="estado_stock" class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                <option value="">Todos</option>
                                <option value="OK">OK</option>
                                <option value="BAJO">Bajo</option>
                                <option value="CRÍTICO">Crítico</option>
                            </select>
                        </div>

                        {{-- Categoría --}}
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Categoría</label>
                            <select name="categoria_id" class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Botones de exportación --}}
                <div class="dashboard-card p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Formato de Exportación</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button type="submit" 
                                data-action="{{ route('reportes.exportar.inventario.pdf') }}" 
                                class="export-btn px-6 py-4 bg-red-500 hover:bg-red-400 text-white font-semibold rounded-lg transition-all flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Exportar PDF
                        </button>

                        <button type="submit" 
                                data-action="{{ route('reportes.exportar.inventario.excel') }}" 
                                class="export-btn px-6 py-4 bg-green-500 hover:bg-green-400 text-white font-semibold rounded-lg transition-all flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exportar Excel
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        // Toggle filtros personalizados
        document.querySelectorAll('input[name="tipo"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const customFilters = document.getElementById('custom-filters');
                if (this.value === 'personalizado') {
                    customFilters.classList.remove('hidden');
                } else {
                    customFilters.classList.add('hidden');
                }
            });
        });

        // Manejar click en botones de exportación
        document.querySelectorAll('.export-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = document.getElementById('export-form');
                const action = this.getAttribute('data-action');
                form.action = action;
            });
        });
    </script>
</x-layouts.app>
