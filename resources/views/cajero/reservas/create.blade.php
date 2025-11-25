<x-layouts.app title="Nueva Reserva | Miss Sweet Candy">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <div class="min-h-screen bg-zinc-950 p-4 md:p-8" 
         x-data="reservationForm()">
        
        {{-- Header --}}
        <div class="max-w-5xl mx-auto mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/20 text-lg">📅</span>
                    Nueva Reserva
                </h1>
                <p class="text-zinc-400 mt-1 ml-14 text-sm">Programa tu visita a Miss Sweet Candy</p>
            </div>
            
            <a href="{{ url()->previous() }}" class="group flex items-center gap-2 text-zinc-500 hover:text-zinc-200 transition-colors text-sm font-medium">
                <div class="w-8 h-8 rounded-full border border-zinc-800 flex items-center justify-center group-hover:bg-zinc-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </div>
                Cancelar y volver
            </a>
        </div>

        <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- COLUMNA IZQUIERDA: FORMULARIO --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Tarjeta de Configuración --}}
                <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-3xl p-6 backdrop-blur-sm">
                    <form id="mainForm" action="{{ route('reservas.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="mesa_id" :value="selectedTable">
                        <input type="hidden" name="numero_personas" :value="people">

                        {{-- Fecha --}}
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-amber-500 uppercase tracking-wider mb-2">Fecha</label>
                            <input type="date" name="fecha" x-model="date" min="{{ date('Y-m-d') }}"
                                   class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none transition-all">
                        </div>

                        {{-- Hora --}}
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-amber-500 uppercase tracking-wider mb-2">Hora</label>
                            <input type="time" name="hora" x-model="time"
                                   class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none transition-all">
                        </div>

                        {{-- Contador Personas --}}
                        <div class="mb-8">
                            <label class="block text-xs font-bold text-amber-500 uppercase tracking-wider mb-2">Personas</label>
                            <div class="flex items-center justify-between bg-zinc-950 border border-zinc-700 rounded-xl p-2">
                                <button type="button" @click="people > 1 ? people-- : null" 
                                        class="w-10 h-10 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-white flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <span class="text-xl font-bold text-white tabular-nums" x-text="people"></span>
                                <button type="button" @click="people < 20 ? people++ : null"
                                        class="w-10 h-10 rounded-lg bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center transition-colors shadow-lg shadow-amber-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Botón Buscar --}}
                        <button type="button" @click="checkAvailability()"
                                :disabled="loading || !date || !time"
                                class="w-full py-4 rounded-xl font-bold text-white flex items-center justify-center gap-2 transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="loading ? 'bg-zinc-700' : 'bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg shadow-amber-500/20 hover:shadow-amber-500/40'">
                            
                            <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            
                            <span x-text="loading ? 'Buscando...' : 'Buscar Mesas'"></span>
                        </button>
                    </form>
                </div>

                {{-- Info Card --}}
                <div class="bg-blue-500/5 border border-blue-500/20 rounded-2xl p-5">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-blue-500/10 rounded-lg text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-sm text-blue-200/80 space-y-1">
                            <p class="font-semibold text-blue-300">Política de Reservas</p>
                            <p>• Tolerancia de 15 minutos.</p>
                            <p>• Cancelación gratuita 2hs antes.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: RESULTADOS --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Estado Inicial --}}
                <div x-show="!hasSearched" class="h-full min-h-[400px] flex flex-col items-center justify-center text-zinc-600 border-2 border-dashed border-zinc-800 rounded-3xl bg-zinc-900/20">
                    <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <p class="text-lg font-medium">Selecciona fecha y hora para ver mesas</p>
                </div>

                {{-- Resultados --}}
                <div x-show="hasSearched" x-transition.opacity>
                    
                    {{-- Header Resultados --}}
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-white">Mesas Disponibles</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-500/10 text-green-400 border border-green-500/20" x-show="tables.length > 0">
                            <span x-text="tables.length"></span> opciones
                        </span>
                    </div>

                    {{-- Grid de Mesas --}}
                    <div x-show="tables.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <template x-for="table in tables" :key="table.id">
                            <div @click="selectTable(table.id)"
                                 class="group relative cursor-pointer rounded-2xl border-2 p-5 transition-all duration-300 hover:scale-[1.02]"
                                 :class="selectedTable === table.id 
                                    ? 'bg-amber-500/10 border-amber-500 shadow-lg shadow-amber-500/10' 
                                    : 'bg-zinc-900 border-zinc-800 hover:border-zinc-600 hover:bg-zinc-800'">
                                
                                {{-- Check icon --}}
                                <div x-show="selectedTable === table.id" class="absolute top-3 right-3 w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center text-white shadow-lg scale-100 transition-transform">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>

                                <div class="flex flex-col items-center text-center">
                                    <div class="text-4xl mb-3 transition-transform group-hover:scale-110">🪑</div>
                                    <div class="font-bold text-white text-lg" x-text="table.nombre"></div>
                                    <div class="text-xs font-medium mt-1" 
                                         :class="selectedTable === table.id ? 'text-amber-200' : 'text-zinc-500'"
                                         x-text="'Capacidad: ' + table.capacidad + ' pers.'"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Sin Resultados --}}
                    <div x-show="tables.length === 0 && !loading" class="bg-red-500/10 border border-red-500/20 rounded-2xl p-8 text-center">
                        <div class="text-3xl mb-2">😔</div>
                        <h3 class="text-red-400 font-bold text-lg">No hay mesas disponibles</h3>
                        <p class="text-red-200/60 text-sm mt-1">Intenta con otra hora o fecha.</p>
                    </div>

                    {{-- Área de Confirmación --}}
                    <div x-show="selectedTable" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-8 bg-zinc-900 rounded-2xl p-6 border-t-4 border-amber-500 shadow-2xl">
                        
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Observaciones (Opcional)</label>
                            <textarea name="observaciones" form="mainForm" rows="2" 
                                      class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 outline-none resize-none text-sm"
                                      placeholder="Ej: Es un cumpleaños, necesitamos silla de bebé..."></textarea>
                        </div>

                        <button type="button" @click="submitForm()"
                                class="w-full py-4 bg-green-500 hover:bg-green-400 text-black font-black uppercase tracking-widest rounded-xl transition-all hover:scale-[1.01] shadow-lg shadow-green-500/20 flex items-center justify-center gap-2">
                            <span>Confirmar Reserva</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reservationForm', () => ({
                date: '',
                time: '12:00',
                people: 2,
                loading: false,
                hasSearched: false,
                tables: [],
                selectedTable: null,

                async checkAvailability() {
                    this.loading = true;
                    this.selectedTable = null;
                    
                    try {
                        const response = await fetch('{{ route("reservas.verificar-disponibilidad") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                fecha: this.date,
                                hora: this.time,
                                numero_personas: this.people
                            })
                        });

                        const data = await response.json();
                        this.tables = data.mesas || [];
                        this.hasSearched = true;
                        
                    } catch (error) {
                        console.error(error);
                        alert('Error al verificar disponibilidad');
                    } finally {
                        this.loading = false;
                    }
                },

                selectTable(id) {
                    this.selectedTable = id;
                    // Vibración suave si es móvil
                    if (navigator.vibrate) navigator.vibrate(50);
                },

                submitForm() {
                    document.getElementById('mainForm').submit();
                }
            }));
        });
    </script>
</x-layouts.app>