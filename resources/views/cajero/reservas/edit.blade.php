<x-layouts.app title="Modificar Reserva | Miss Sweet Candy">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <div class="min-h-screen bg-zinc-950 p-4 md:p-8" 
         x-data="editReservationForm()">
        
        {{-- Header --}}
        <div class="max-w-5xl mx-auto mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center border border-zinc-700 text-lg">✏️</span>
                    Modificar Reserva
                </h1>
                <p class="text-zinc-400 mt-1 ml-14 text-sm">
                    Reserva <span class="font-mono text-amber-500 font-bold">#{{ $reserva->id }}</span>
                </p>
            </div>
            
            <a href="{{ route('reservas.show', $reserva) }}" class="group flex items-center gap-2 text-zinc-500 hover:text-zinc-200 transition-colors text-sm font-medium">
                <div class="w-8 h-8 rounded-full border border-zinc-800 flex items-center justify-center group-hover:bg-zinc-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </div>
                Cancelar cambios
            </a>
        </div>

        {{-- Mensajes Flash --}}
        <div class="max-w-5xl mx-auto mb-6">
            @if (session('error'))
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- COLUMNA IZQUIERDA: FORMULARIO --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Tarjeta de Edición --}}
                <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-3xl p-6 backdrop-blur-sm relative overflow-hidden">
                    
                    <form id="editForm" action="{{ route('reservas.update', $reserva) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="numero_personas" :value="people">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Fecha --}}
                            <div>
                                <label class="block text-xs font-bold text-amber-500 uppercase tracking-wider mb-2">Nueva Fecha</label>
                                <input type="date" name="fecha" x-model="date" min="{{ date('Y-m-d') }}" @change="resetVerification()"
                                       class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none transition-all">
                            </div>

                            {{-- Hora --}}
                            <div>
                                <label class="block text-xs font-bold text-amber-500 uppercase tracking-wider mb-2">Nueva Hora</label>
                                <input type="time" name="hora" x-model="time" @change="resetVerification()"
                                       class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none transition-all">
                            </div>
                        </div>

                        {{-- Personas --}}
                        <div class="mb-8">
                            <label class="block text-xs font-bold text-amber-500 uppercase tracking-wider mb-2">Personas</label>
                            <div class="flex items-center justify-between bg-zinc-950 border border-zinc-700 rounded-xl p-2 max-w-xs">
                                <button type="button" @click="decrementPeople()" 
                                        class="w-10 h-10 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-white flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <span class="text-xl font-bold text-white tabular-nums" x-text="people"></span>
                                <button type="button" @click="incrementPeople()"
                                        class="w-10 h-10 rounded-lg bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center transition-colors shadow-lg shadow-amber-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Observaciones --}}
                        <div class="mb-8">
                            <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Observaciones</label>
                            <textarea name="observaciones" rows="3"
                                      class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:border-amber-500 outline-none resize-none text-sm placeholder-zinc-600"
                                      placeholder="Ej: Alérgicos, silla de bebé...">{{ $reserva->observaciones }}</textarea>
                        </div>

                        {{-- Estado de Verificación --}}
                        <div x-show="verificationStatus === 'success'" 
                             class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl flex items-center gap-3"
                             x-transition>
                            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-black">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="text-green-400 font-bold text-sm">¡Disponible!</p>
                                <p class="text-green-400/70 text-xs">Puedes guardar los cambios.</p>
                            </div>
                        </div>

                        <div x-show="verificationStatus === 'error'" 
                             class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl flex items-center gap-3"
                             x-transition>
                            <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <p class="text-red-400 font-bold text-sm">No disponible</p>
                                <p class="text-red-400/70 text-xs">Intenta con otra hora o fecha.</p>
                            </div>
                        </div>

                        {{-- Botonera --}}
                        <div class="flex gap-3">
                            {{-- Botón Verificar --}}
                            <button type="button" @click="checkAvailability()" x-show="!verified"
                                    :disabled="loading"
                                    class="flex-1 py-4 rounded-xl font-bold text-zinc-900 flex items-center justify-center gap-2 transition-all transform active:scale-95 disabled:opacity-50"
                                    :class="loading ? 'bg-zinc-600' : 'bg-white hover:bg-zinc-200'">
                                <span x-show="loading" class="animate-spin w-4 h-4 border-2 border-zinc-900 border-t-transparent rounded-full"></span>
                                <span x-text="loading ? 'Verificando...' : '🔍 Verificar Disponibilidad'"></span>
                            </button>

                            {{-- Botón Guardar (Solo visible si verificado) --}}
                            <button type="button" @click="submitForm()" x-show="verified"
                                    class="flex-1 py-4 bg-gradient-to-r from-amber-500 to-orange-600 hover:shadow-amber-500/40 text-white font-black uppercase tracking-widest rounded-xl transition-all hover:scale-[1.01] shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                                <span>Guardar Cambios</span>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- COLUMNA DERECHA: INFO ACTUAL --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Datos Originales --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6">
                    <h3 class="text-zinc-500 text-xs font-black uppercase tracking-widest mb-4">Reserva Actual</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-400">
                                🪑
                            </div>
                            <div>
                                <div class="text-zinc-500 text-xs font-bold uppercase">Mesa</div>
                                <div class="text-white font-bold text-lg">{{ $reserva->mesa->nombre }}</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-400">
                                📅
                            </div>
                            <div>
                                <div class="text-zinc-500 text-xs font-bold uppercase">Fecha</div>
                                <div class="text-white font-bold">{{ $reserva->fecha->format('d/m/Y') }}</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-400">
                                ⏰
                            </div>
                            <div>
                                <div class="text-zinc-500 text-xs font-bold uppercase">Hora</div>
                                <div class="text-white font-bold">{{ \Carbon\Carbon::parse($reserva->hora)->format('h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="p-5 rounded-2xl border border-blue-500/20 bg-blue-500/5 text-blue-300 text-sm leading-relaxed">
                    <p class="font-bold mb-1">ℹ️ Importante</p>
                    Si cambias la fecha o la cantidad de personas, es posible que debamos asignarte una mesa diferente.
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('editReservationForm', () => ({
                // Inicializar con datos de Blade
                date: '{{ $reserva->fecha->format('Y-m-d') }}',
                time: '{{ \Carbon\Carbon::parse($reserva->hora)->format('H:i') }}',
                people: {{ $reserva->numero_personas }},
                
                loading: false,
                verified: false,
                verificationStatus: null, // null, 'success', 'error'

                incrementPeople() {
                    if (this.people < 20) {
                        this.people++;
                        this.resetVerification();
                    }
                },

                decrementPeople() {
                    if (this.people > 1) {
                        this.people--;
                        this.resetVerification();
                    }
                },

                resetVerification() {
                    this.verified = false;
                    this.verificationStatus = null;
                },

                async checkAvailability() {
                    this.loading = true;
                    this.verificationStatus = null;
                    
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
                                numero_personas: this.people,
                                reserva_actual_id: {{ $reserva->id }} // Importante para excluir la actual
                            })
                        });

                        const data = await response.json();
                        
                        if (data.disponible) {
                            this.verified = true;
                            this.verificationStatus = 'success';
                        } else {
                            this.verified = false;
                            this.verificationStatus = 'error';
                        }
                        
                    } catch (error) {
                        console.error(error);
                        alert('Error al verificar disponibilidad');
                    } finally {
                        this.loading = false;
                    }
                },

                submitForm() {
                    if (this.verified) {
                        document.getElementById('editForm').submit();
                    }
                }
            }));
        });
    </script>
</x-layouts.app>