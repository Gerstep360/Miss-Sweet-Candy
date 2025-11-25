<x-layouts.app title="Tu turno | Miss Sweet Candy">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('trackerApp', () => ({
                orderId: {{ $pedido->id }},
                token: '{{ $pedido->token }}',
                status: '{{ $pedido->estado }}', 
                eta: {{ $pedido->eta_minutes ?? 0 }},
                type: '{{ $pedido->tipo }}',
                connected: false,
                clock: '',

                init() {
                    this.startClock();
                    this.connectSocket();
                },

                startClock() {
                    setInterval(() => {
                        this.clock = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    }, 1000);
                },

                connectSocket() {
                    if (!window.Echo) { setTimeout(() => this.connectSocket(), 500); return; }

                    window.Echo.channel('turnero')
                        .listen('.pedido.actualizado', (e) => {
                            const p = e.pedido || e;
                            if (p.id == this.orderId) {
                                this.status = p.estado;
                                this.eta = p.eta;
                                // Vibración sutil solo si está listo
                                if (['preparado', 'listo'].includes(this.status) && navigator.vibrate) {
                                    navigator.vibrate([200]);
                                }
                            }
                        })
                        .subscribed(() => this.connected = true);
                },

                get stepIndex() {
                    if (['pendiente', 'confirmado'].includes(this.status)) return 0;
                    if (this.status === 'en_preparacion') return 1;
                    if (['preparado', 'listo', 'entregado'].includes(this.status)) return 2;
                    return 0;
                },

                get statusLabel() {
                    if (this.stepIndex === 0) return 'En Cola';
                    if (this.stepIndex === 1) return 'Preparando';
                    return '¡Listo para retirar!';
                },

                get statusColor() {
                    // Colores Pastel / Clásicos
                    if (this.stepIndex === 0) return 'text-amber-600 bg-amber-100 border-amber-200';
                    if (this.stepIndex === 1) return 'text-blue-600 bg-blue-100 border-blue-200';
                    return 'text-green-700 bg-green-100 border-green-200';
                }
            }));
        });
    </script>

    {{-- Contenedor Principal: Ajustado para no tapar sidebar --}}
    <div x-data="trackerApp" x-cloak class="w-full max-w-4xl mx-auto p-6">
        
        {{-- Header de navegación simple --}}
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('turnos.turnero.index') }}" 
               class="inline-flex items-center gap-2 text-zinc-500 hover:text-zinc-800 transition-colors font-medium text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver a mis pedidos
            </a>
            
            {{-- Indicador de estado discreto --}}
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-400">
                <span class="w-2 h-2 rounded-full" :class="connected ? 'bg-green-500' : 'bg-gray-300'"></span>
                <span x-text="connected ? 'Conectado' : 'Conectando...'"></span>
            </div>
        </div>

        {{-- TARJETA PRINCIPAL (Estilo Clásico) --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            
            {{-- Encabezado de la tarjeta --}}
            <div class="bg-zinc-50 dark:bg-zinc-900/50 px-6 py-4 border-b border-zinc-100 dark:border-zinc-700 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500 text-white rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-zinc-800 dark:text-zinc-100 text-lg leading-tight">Miss Sweet Candy</h1>
                        <p class="text-xs text-zinc-500">Ticket digital</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Hora Actual</p>
                    <p class="font-mono text-lg font-semibold text-zinc-700 dark:text-zinc-300" x-text="clock"></p>
                </div>
            </div>

            <div class="p-8">
                <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">
                    
                    {{-- SECCIÓN IZQUIERDA: EL TURNO --}}
                    <div class="flex-1 w-full text-center md:text-left">
                        <span class="inline-block py-1 px-3 rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-500 text-xs font-bold uppercase tracking-widest mb-3">
                            Número de Turno
                        </span>
                        
                        <div class="text-7xl font-black text-zinc-800 dark:text-white tracking-tight mb-4" x-text="token"></div>
                        
                        {{-- Badge de Estado Grande --}}
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-l-4 text-sm font-bold uppercase tracking-wide transition-colors duration-300"
                             :class="statusColor">
                            <span x-text="statusLabel"></span>
                        </div>

                        <div class="mt-6 flex gap-4 justify-center md:justify-start">
                            <div class="text-left">
                                <div class="text-[10px] uppercase text-zinc-400 font-bold">Tipo</div>
                                <div class="font-semibold text-zinc-700 dark:text-zinc-300 capitalize" x-text="type"></div>
                            </div>
                            <div class="w-px h-8 bg-zinc-200 dark:bg-zinc-700"></div>
                            <div class="text-left">
                                <div class="text-[10px] uppercase text-zinc-400 font-bold">Tiempo Aprox.</div>
                                <div class="font-semibold text-zinc-700 dark:text-zinc-300 tabular-nums" x-text="eta > 0 ? eta + ' min' : '—'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN DERECHA: PROGRESO VERTICAL (Más clásico) --}}
                    <div class="w-full md:w-1/2 bg-zinc-50 dark:bg-zinc-900/30 rounded-xl p-6 border border-zinc-100 dark:border-zinc-700/50">
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-200 mb-6 border-b border-zinc-200 dark:border-zinc-700 pb-2">
                            Estado del Pedido
                        </h3>
                        
                        <div class="relative space-y-8 pl-2">
                            {{-- Línea conectora --}}
                            <div class="absolute left-[15px] top-2 bottom-2 w-0.5 bg-zinc-200 dark:bg-zinc-700 -z-10"></div>
                            
                            {{-- Paso 1 --}}
                            <div class="flex items-center gap-4 transition-opacity duration-300" :class="stepIndex >= 0 ? 'opacity-100' : 'opacity-40'">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 z-10 bg-white dark:bg-zinc-800 transition-colors duration-300"
                                     :class="stepIndex >= 0 ? 'border-amber-500 text-amber-500' : 'border-zinc-300 text-zinc-300'">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold" :class="stepIndex >= 0 ? 'text-zinc-800 dark:text-white' : 'text-zinc-400'">En Cola</div>
                                    <div class="text-xs text-zinc-500">Recibimos tu pedido</div>
                                </div>
                            </div>

                            {{-- Paso 2 --}}
                            <div class="flex items-center gap-4 transition-opacity duration-300" :class="stepIndex >= 1 ? 'opacity-100' : 'opacity-40'">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 z-10 bg-white dark:bg-zinc-800 transition-colors duration-300"
                                     :class="stepIndex >= 1 ? 'border-blue-500 text-blue-500' : 'border-zinc-300 text-zinc-300'">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold" :class="stepIndex >= 1 ? 'text-zinc-800 dark:text-white' : 'text-zinc-400'">Preparando</div>
                                    <div class="text-xs text-zinc-500">Barista trabajando</div>
                                </div>
                            </div>

                            {{-- Paso 3 --}}
                            <div class="flex items-center gap-4 transition-opacity duration-300" :class="stepIndex >= 2 ? 'opacity-100' : 'opacity-40'">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 z-10 bg-white dark:bg-zinc-800 transition-colors duration-300"
                                     :class="stepIndex >= 2 ? 'border-green-500 text-green-500 bg-green-50' : 'border-zinc-300 text-zinc-300'">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold" :class="stepIndex >= 2 ? 'text-zinc-800 dark:text-white' : 'text-zinc-400'">Listo para retirar</div>
                                    <div class="text-xs text-zinc-500">Pasa por barra</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Footer de la tarjeta --}}
            <div class="px-6 py-3 bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-100 dark:border-zinc-700 text-center">
                <p class="text-xs text-zinc-400">Por favor, mantente atento a las pantallas.</p>
            </div>
        </div>
    </div>
</x-layouts.app>