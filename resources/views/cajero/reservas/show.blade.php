<x-layouts.app title="Detalle Reserva | Miss Sweet Candy">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <div class="min-h-screen bg-zinc-950 p-4 md:p-8 flex flex-col items-center justify-center" 
         x-data="reservationDetails()">

        {{-- Botón Volver Flotante --}}
        <a href="{{ route('reservas.index') }}" class="absolute top-6 left-6 md:top-8 md:left-8 group flex items-center gap-2 text-zinc-500 hover:text-zinc-200 transition-colors text-sm font-medium z-10">
            <div class="w-8 h-8 rounded-full border border-zinc-800 flex items-center justify-center group-hover:bg-zinc-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </div>
            <span class="hidden md:inline">Volver al listado</span>
        </a>

        <div class="w-full max-w-lg">
            
            {{-- Mensaje de Éxito (Flash) --}}
            @if (session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-400 flex items-center gap-3 animate-fade-in-down">
                    <div class="w-8 h-8 bg-green-500 text-black rounded-full flex items-center justify-center shadow-lg shadow-green-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm">¡Operación Exitosa!</p>
                        <p class="text-xs opacity-80">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- TICKET DIGITAL --}}
            <div class="relative bg-zinc-900 rounded-[2rem] overflow-hidden shadow-2xl border border-zinc-800">
                
                {{-- Decoración Superior --}}
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-500"></div>

                {{-- Header del Ticket --}}
                <div class="px-8 py-8 text-center border-b border-dashed border-zinc-800 relative">
                    {{-- Muecas del ticket --}}
                    <div class="absolute -bottom-3 -left-3 w-6 h-6 bg-zinc-950 rounded-full"></div>
                    <div class="absolute -bottom-3 -right-3 w-6 h-6 bg-zinc-950 rounded-full"></div>

                    <h2 class="text-zinc-500 uppercase tracking-[0.3em] text-xs font-bold mb-2">Reserva Confirmada</h2>
                    <h1 class="text-3xl font-black text-white tracking-tight">Miss Sweet Candy</h1>
                    
                    {{-- Código --}}
                    <div class="mt-6 bg-zinc-950/50 rounded-xl p-4 border border-zinc-800 inline-block">
                        <div class="text-[10px] text-zinc-500 uppercase tracking-widest mb-1">Código de Reserva</div>
                        <div class="text-3xl font-mono font-bold text-amber-500 tracking-wider">
                            {{ $reservaService->generarCodigoReserva($reserva) }}
                        </div>
                    </div>
                </div>

                {{-- Cuerpo del Ticket --}}
                <div class="px-8 py-8 bg-zinc-900/50">
                    
                    {{-- Estado --}}
                    <div class="flex justify-center mb-8">
                        @php
                            $statusClasses = match($reserva->estado) {
                                'pendiente' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                'confirmada' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                'cancelada' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                'cumplida' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                default => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
                            };
                            $statusLabel = ucfirst($reserva->estado);
                        @endphp
                        <span class="px-4 py-1.5 rounded-full border text-xs font-black uppercase tracking-widest {{ $statusClasses }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- Detalles Grid --}}
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                        
                        {{-- Fecha --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400 shrink-0">
                                📅
                            </div>
                            <div>
                                <div class="text-[10px] uppercase text-zinc-500 font-bold">Fecha</div>
                                <div class="text-white font-bold">{{ $reserva->fecha->format('d M, Y') }}</div>
                            </div>
                        </div>

                        {{-- Hora --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400 shrink-0">
                                ⏰
                            </div>
                            <div>
                                <div class="text-[10px] uppercase text-zinc-500 font-bold">Hora</div>
                                <div class="text-white font-bold">{{ \Carbon\Carbon::parse($reserva->hora)->format('h:i A') }}</div>
                            </div>
                        </div>

                        {{-- Personas --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400 shrink-0">
                                👥
                            </div>
                            <div>
                                <div class="text-[10px] uppercase text-zinc-500 font-bold">Personas</div>
                                <div class="text-white font-bold">{{ $reserva->numero_personas }} Comensales</div>
                            </div>
                        </div>

                        {{-- Mesa --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400 shrink-0">
                                🪑
                            </div>
                            <div>
                                <div class="text-[10px] uppercase text-zinc-500 font-bold">Mesa Asignada</div>
                                <div class="text-white font-bold">{{ $reserva->mesa->nombre }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    @if($reserva->observaciones)
                        <div class="mt-6 p-4 rounded-xl bg-zinc-950 border border-zinc-800/50">
                            <div class="text-[10px] uppercase text-zinc-500 font-bold mb-1">Observaciones</div>
                            <p class="text-sm text-zinc-300 italic">"{{ $reserva->observaciones }}"</p>
                        </div>
                    @endif

                    {{-- QR Simulado --}}
                    <div class="mt-8 flex justify-center">
                        <div class="p-2 bg-white rounded-xl shadow-lg">
                            {{-- Aquí podrías poner el QR real si tuvieras la librería, por ahora es un patrón CSS --}}
                            <div class="w-32 h-32 bg-zinc-900 rounded-lg flex items-center justify-center overflow-hidden relative">
                                <div class="absolute inset-0 opacity-80" 
                                     style="background-image:  radial-gradient(#000 35%, transparent 36%), radial-gradient(#000 35%, transparent 36%); background-color: #fff; background-position: 0 0, 10px 10px; background-size: 20px 20px;">
                                </div>
                                <div class="bg-white p-1 rounded z-10">
                                    <span class="text-2xl">🍬</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-center text-[10px] text-zinc-500 mt-2 uppercase tracking-widest">Escanea al llegar</p>

                </div>

                {{-- Footer Actions --}}
                <div class="p-6 bg-zinc-950 border-t border-zinc-800 flex flex-col gap-3">
                    
                    <button @click="addToCalendar()" 
                            class="w-full py-3.5 bg-white hover:bg-zinc-200 text-zinc-900 font-bold uppercase tracking-widest rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-white/5">
                        <span>📅 Agregar al Calendario</span>
                    </button>

                    <div class="grid grid-cols-2 gap-3">
                        @if($reserva->estaActiva())
                            <a href="{{ route('reservas.edit', $reserva) }}" 
                               class="py-3.5 bg-zinc-800 hover:bg-zinc-700 text-white font-bold uppercase tracking-widest text-xs rounded-xl transition-all text-center border border-zinc-700">
                                ✏️ Modificar
                            </a>
                            
                            <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" class="block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Seguro que deseas cancelar?')"
                                        class="w-full py-3.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 font-bold uppercase tracking-widest text-xs rounded-xl transition-all">
                                    ❌ Cancelar
                                </button>
                            </form>
                        @else
                            <button disabled class="col-span-2 w-full py-3 bg-zinc-900 text-zinc-600 font-bold uppercase tracking-widest text-xs rounded-xl border border-zinc-800 cursor-not-allowed">
                                Acciones no disponibles
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <p class="text-center text-zinc-600 text-xs mt-6">
                Te hemos enviado un correo con los detalles a <span class="text-zinc-400 font-semibold">{{ auth()->user()->email }}</span>
            </p>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reservationDetails', () => ({
                addToCalendar() {
                    const title = 'Reserva Miss Sweet Candy';
                    // Formato fechas para Google Calendar (YYYYMMDDTHHmmss)
                    // Aseguramos que los digitos tengan leading zeros
                    const fecha = '{{ $reserva->fecha->format("Ymd") }}';
                    const horaInicio = '{{ \Carbon\Carbon::parse($reserva->hora)->format("His") }}';
                    const horaFin = '{{ \Carbon\Carbon::parse($reserva->hora)->addHours(2)->format("His") }}';
                    
                    const startDate = `${fecha}T${horaInicio}`;
                    const endDate = `${fecha}T${horaFin}`;
                    
                    const location = 'Miss Sweet Candy';
                    const details = `Reserva para {{ $reserva->numero_personas }} personas. Código: {{ $reservaService->generarCodigoReserva($reserva) }}`;

                    const googleUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${startDate}/${endDate}&details=${encodeURIComponent(details)}&location=${encodeURIComponent(location)}`;
                    
                    window.open(googleUrl, '_blank');
                }
            }));
        });
    </script>
</x-layouts.app>