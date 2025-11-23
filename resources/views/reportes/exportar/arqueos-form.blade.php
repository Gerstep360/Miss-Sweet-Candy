{{-- filepath: resources/views/reportes/exportar/arqueos-form.blade.php --}}
<x-layouts.app :title="__('Exportar Reporte de Arqueos')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-6 sm:py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="dashboard-card mb-6">
                <div class="flex items-center gap-4 mb-4">
                    <a href="{{ route('reportes.exportar.index') }}" class="text-zinc-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-white flex items-center gap-2">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Reporte de Arqueos / Cierres
                        </h1>
                        <p class="text-zinc-400">Configura y exporta el reporte de cierres de caja</p>
                    </div>
                </div>
            </div>

            <form id="export-form" method="POST" class="space-y-6">
                @csrf
                
                <div class="dashboard-card">
                    <h3 class="text-xl font-bold text-white mb-4">Filtros</h3>
                    
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
                                            <p class="text-xs text-zinc-400">Último mes</p>
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

                    <div id="custom-filters" class="space-y-4 hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-2">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-2">Fecha Fin</label>
                                <input type="date" name="fecha_fin" class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Cajero</label>
                            <select name="cajero_id" class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                <option value="">Todos</option>
                                @foreach($cajeros as $cajero)
                                    <option value="{{ $cajero->id }}">{{ $cajero->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-zinc-300 cursor-pointer">
                                <input type="checkbox" name="con_diferencias" value="1" class="w-4 h-4 text-amber-500 bg-zinc-800 border-zinc-700 rounded focus:ring-amber-500">
                                <span class="text-sm font-medium">Solo cierres con diferencias</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Formato de Exportación</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button type="submit" data-action="{{ route('reportes.exportar.arqueos.pdf') }}" class="export-btn px-6 py-4 bg-red-500 hover:bg-red-400 text-white font-semibold rounded-lg transition-all flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Exportar PDF
                        </button>
                        <button type="submit" data-action="{{ route('reportes.exportar.arqueos.excel') }}" class="export-btn px-6 py-4 bg-green-500 hover:bg-green-400 text-white font-semibold rounded-lg transition-all flex items-center justify-center gap-2">
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

        document.querySelectorAll('.export-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = document.getElementById('export-form');
                const action = this.getAttribute('data-action');
                form.action = action;
            });
        });
    </script>
</x-layouts.app>
