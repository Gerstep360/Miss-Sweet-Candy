<x-layouts.guest title="Monitor | Miss Sweet Candy">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&family=Inter:wght@300;400;600;900&display=swap');
        
        [x-cloak] { display: none !important; }
        
        /* Ocultar scrollbars */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Ticker Suave */
        .marquee-container { overflow: hidden; white-space: nowrap; position: relative; mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent); }
        .marquee-content { display: inline-block; animation: marquee 250s linear infinite; } 
        
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        /* Efectos de Fondo y Vidrio */
        .bg-glass-header { background: rgba(10, 10, 12, 0.6); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .bg-glass-card { background: linear-gradient(145deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%); border: 1px solid rgba(255, 255, 255, 0.05); }
        
        /* Resplandores Específicos */
        .glow-ready { box-shadow: 0 0 60px -20px rgba(34, 197, 94, 0.3); }
        .glow-text-ready { text-shadow: 0 0 30px rgba(34, 197, 94, 0.5); }
        
        /* Fondo de malla sutil para la columna Listo */
        .bg-grid-pattern {
            background-image: radial-gradient(rgba(34, 197, 94, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('monitorSystem', () => ({
                itemsPerPage: 4,     
                rotationSpeed: 10000, 
                
                currentTime: new Date(),
                connectionStatus: 'disconnected', 
                flashMessage: null, 
                flashToken: null,

                orders: { pending: [], preparing: [], ready: [] },
                pages: { pending: 0, preparing: 0, ready: 0 },
                progress: { pending: 0, preparing: 0, ready: 0 },

                init() {
                    console.log('🚀 Monitor UI Pro Loaded');
                    this.startClock();
                    this.initWebSockets(); 
                    this.fetchInitialData();
                    this.startRotationLoops();

                    // Audio Unlocker
                    document.addEventListener('click', () => {
                        this.$refs.ding.play().then(() => {
                            this.$refs.ding.pause();
                            this.$refs.ding.currentTime = 0;
                        }).catch(e => {});
                    }, { once: true });
                },

                startClock() {
                    setInterval(() => { this.currentTime = new Date(); }, 1000);
                },

                startRotationLoops() {
                    const tickRate = 100;
                    const step = 100 / (this.rotationSpeed / tickRate);

                    setInterval(() => {
                        ['pending', 'preparing', 'ready'].forEach(type => {
                            if (this.orders[type].length > this.itemsPerPage) {
                                this.progress[type] += step;
                                if (this.progress[type] >= 100) {
                                    this.progress[type] = 0;
                                    this.nextPage(type);
                                }
                            } else {
                                this.progress[type] = 0;
                            }
                        });
                    }, tickRate);
                },

                nextPage(type) {
                    const max = Math.ceil(this.orders[type].length / this.itemsPerPage);
                    this.pages[type] = (this.pages[type] + 1) % max;
                },

                async fetchInitialData() {
                    try {
                        const res = await fetch('/turnos/feed');
                        const data = await res.json();
                        this.orders.pending = data.pendiente || [];
                        this.orders.preparing = data.preparando || [];
                        this.orders.ready = data.preparado || [];
                    } catch (e) { console.error("Error feed", e); }
                },

                initWebSockets() {
                    if (!window.Echo) { setTimeout(() => this.initWebSockets(), 500); return; }
                    this.connectionStatus = 'connecting';
                    
                    window.Echo.channel('turnero')
                        .listen('.pedido.actualizado', (e) => {
                            const p = e.pedido || e; 
                            if(p && p.id) this.processOrderEvent(p);
                        })
                        .subscribed(() => this.connectionStatus = 'connected')
                        .error(() => this.connectionStatus = 'disconnected');
                },

                processOrderEvent(order) {
                    ['pending', 'preparing', 'ready'].forEach(k => {
                        this.orders[k] = this.orders[k].filter(o => o.id !== order.id);
                    });

                    if (['pendiente', 'confirmado'].includes(order.estado)) {
                        this.orders.pending.push(order);
                        this.orders.pending.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    } else if (order.estado === 'en_preparacion') {
                        this.orders.preparing.push(order);
                        this.orders.preparing.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
                    } else if (['preparado', 'listo'].includes(order.estado)) {
                        this.orders.ready.unshift(order);
                        this.triggerNotification(order);
                    }
                },

                triggerNotification(order) {
                    if(this.$refs.ding) {
                        this.$refs.ding.currentTime = 0;
                        this.$refs.ding.play().catch(e => {});
                    }
                    this.flashMessage = 'LISTO PARA RETIRAR';
                    this.flashToken = order.token;
                    
                    setTimeout(() => {
                        this.flashMessage = null;
                        this.flashToken = null;
                    }, 5000);
                    
                    this.pages.ready = 0;
                    this.progress.ready = 0;
                },

                getVisible(type) {
                    const start = this.pages[type] * this.itemsPerPage;
                    return this.orders[type].slice(start, start + this.itemsPerPage);
                },

                calcTimeAgo(dateStr) {
                    if (!dateStr) return '';
                    const min = Math.floor((this.currentTime - new Date(dateStr)) / 60000);
                    return min < 1 ? 'Ahora' : `${min} min`;
                }
            }));
        });
    </script>

    <div x-data="monitorSystem" x-cloak class="h-screen w-screen bg-[#050505] text-zinc-100 flex flex-col overflow-hidden font-sans select-none">
        
        <audio x-ref="ding" src="{{ asset('sounds/ding.mp3') }}" preload="auto"></audio>

        <div x-show="flashMessage" 
             class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-black/90 backdrop-blur-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-105"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
             
             <div class="absolute inset-0 bg-green-500/10 animate-pulse"></div>
             
             <div class="relative z-10 text-center space-y-4">
                 <div class="text-green-400 font-bold tracking-[0.3em] text-2xl animate-bounce">ATENCIÓN CLIENTE</div>
                 <div class="text-[12rem] font-black font-mono leading-none text-white drop-shadow-[0_0_80px_rgba(34,197,94,0.8)]" x-text="flashToken"></div>
                 <div class="text-5xl font-bold text-white uppercase tracking-widest border-t-4 border-green-500 pt-6 mt-4" x-text="flashMessage"></div>
             </div>
        </div>

        <header class="h-24 px-8 flex items-center justify-between bg-glass-header shrink-0 z-20 relative">
            <div class="flex items-center gap-6">
                <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-amber-600 rounded-2xl flex items-center justify-center shadow-[0_0_30px_rgba(245,158,11,0.3)] ring-1 ring-white/20">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white uppercase italic drop-shadow-md font-sans">Miss Sweet Candy</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10">
                            <div class="w-2 h-2 rounded-full transition-colors duration-500" :class="connectionStatus === 'connected' ? 'bg-green-500 shadow-[0_0_10px_#22c55e]' : 'bg-red-500'"></div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400" x-text="connectionStatus === 'connected' ? 'SISTEMA ONLINE' : 'OFFLINE'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-end">
                <div class="text-6xl font-mono font-bold tracking-tighter tabular-nums text-white drop-shadow-lg" 
                     x-text="currentTime.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})">
                </div>
                <div class="text-zinc-500 text-xs font-bold uppercase tracking-[0.2em] -mt-1">Hora Actual</div>
            </div>
        </header>

        <main class="flex-1 grid grid-cols-10 gap-0 min-h-0 relative z-10">
            
            <section class="col-span-3 bg-[#0a0a0c] border-r border-white/5 flex flex-col relative">
                <div class="p-6 border-b border-white/5 bg-white/[0.02] flex justify-between items-center">
                    <h2 class="text-zinc-500 font-black uppercase tracking-[0.2em] text-sm flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-zinc-600"></span> En Cola
                    </h2>
                    <span class="bg-zinc-800 text-zinc-400 text-xs font-bold px-2 py-1 rounded border border-white/5" x-text="orders.pending.length"></span>
                </div>
                
                <div class="flex-1 p-4 space-y-3 flex flex-col justify-start">
                    <template x-for="item in getVisible('pending')" :key="item.id">
                        <div class="bg-glass-card rounded-xl p-4 flex justify-between items-center h-[23%] group relative overflow-hidden"
                             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-zinc-700"></div>
                            
                            <div class="pl-3">
                                <div class="text-4xl font-mono font-bold text-zinc-500 group-hover:text-zinc-300 transition-colors" x-text="item.token"></div>
                                <div class="text-[10px] uppercase font-bold text-zinc-600 tracking-wider mt-1" x-text="item.tipo"></div>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] uppercase font-bold text-zinc-600 block">Espera</span>
                                <span class="text-lg font-bold text-zinc-500 tabular-nums" x-text="calcTimeAgo(item.created_at)"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="orders.pending.length === 0" class="h-full flex items-center justify-center text-zinc-800 font-bold uppercase tracking-widest text-lg">Sin Cola</div>
                </div>
                <div class="h-1 bg-zinc-800 w-full"><div class="h-full bg-zinc-500 transition-all duration-200 ease-linear" :style="`width: ${progress.pending}%`"></div></div>
            </section>

            <section class="col-span-3 bg-[#0a0a0c] border-r border-white/5 flex flex-col relative">
                <div class="p-6 border-b border-white/5 bg-blue-500/[0.02] flex justify-between items-center">
                    <h2 class="text-blue-500 font-black uppercase tracking-[0.2em] text-sm flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span> Preparando
                    </h2>
                    <span class="bg-blue-900/20 text-blue-400 text-xs font-bold px-2 py-1 rounded border border-blue-500/20" x-text="orders.preparing.length"></span>
                </div>

                <div class="flex-1 p-4 space-y-3 flex flex-col justify-start">
                    <template x-for="item in getVisible('preparing')" :key="item.id">
                        <div class="bg-gradient-to-r from-blue-900/10 to-transparent border border-blue-500/10 rounded-xl p-4 flex justify-between items-center h-[23%] relative overflow-hidden"
                             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                             
                             <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 shadow-[0_0_10px_#3b82f6]"></div>

                            <div class="pl-3 relative z-10">
                                <div class="text-5xl font-mono font-bold text-white drop-shadow-md" x-text="item.token"></div>
                                <div class="text-[10px] uppercase font-bold text-blue-400 tracking-wider mt-1" x-text="item.tipo"></div>
                            </div>
                            <div class="text-right relative z-10">
                                <span class="text-[9px] uppercase font-bold text-blue-400/60 block">Estimado</span>
                                <span class="text-2xl font-bold text-blue-300 tabular-nums" x-text="item.eta > 0 ? item.eta + ' min' : '...'"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="orders.preparing.length === 0" class="h-full flex items-center justify-center text-zinc-800 font-bold uppercase tracking-widest text-lg">Cocina Libre</div>
                </div>
                <div class="h-1 bg-blue-900/20 w-full"><div class="h-full bg-blue-500 transition-all duration-200 ease-linear" :style="`width: ${progress.preparing}%`"></div></div>
            </section>

            <section class="col-span-4 bg-grid-pattern relative flex flex-col overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-green-900/10 via-transparent to-green-900/5 pointer-events-none"></div>
                
                <div class="p-6 border-b border-green-500/20 bg-green-900/10 backdrop-blur-md flex justify-between items-center relative z-10">
                    <h2 class="text-green-400 font-black uppercase tracking-[0.2em] text-xl flex items-center gap-3">
                        <span class="w-3 h-3 rounded bg-green-400 shadow-[0_0_15px_#4ade80] animate-pulse"></span> Listo
                    </h2>
                    <span class="bg-green-500 text-black text-sm font-black px-3 py-1 rounded shadow-lg shadow-green-500/20" x-text="orders.ready.length"></span>
                </div>

                <div class="flex-1 p-6 space-y-4 flex flex-col justify-start relative z-10">
                    <template x-for="item in getVisible('ready')" :key="item.id">
                        <div class="bg-black/40 border border-green-500/30 rounded-2xl flex justify-between items-center h-[23%] relative overflow-hidden group shadow-lg transition-transform duration-500 hover:scale-[1.02]"
                             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-700" 
                             x-transition:enter-start="opacity-0 translate-x-20 scale-90" 
                             x-transition:enter-end="opacity-100 translate-x-0 scale-100">
                             
                             <div class="absolute inset-0 bg-gradient-to-r from-green-500/10 to-transparent opacity-100"></div>
                             <div class="absolute left-0 top-0 bottom-0 w-2 bg-green-500 shadow-[0_0_30px_#22c55e]"></div>
                             
                             <div class="pl-8 relative z-10">
                                 <div class="text-7xl font-mono font-black text-white tracking-tighter glow-text-ready" x-text="item.token"></div>
                                 <div class="flex items-center gap-2 mt-1">
                                     <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                     <div class="text-xs font-bold text-green-400 uppercase tracking-[0.2em]">Retirar en Barra</div>
                                 </div>
                             </div>

                             <div class="pr-8 relative z-10">
                                 <div class="w-16 h-16 rounded-full bg-green-500/10 border border-green-500/30 flex items-center justify-center text-green-400 shadow-[0_0_20px_rgba(34,197,94,0.15)] group-hover:scale-110 transition-transform duration-300">
                                     <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                 </div>
                             </div>
                        </div>
                    </template>
                    <div x-show="orders.ready.length === 0" class="h-full flex flex-col items-center justify-center text-zinc-700 opacity-60">
                        <span class="text-2xl font-black uppercase tracking-widest mb-2">Esperando Pedidos</span>
                        <div class="flex gap-2">
                            <span class="w-2 h-2 rounded-full bg-zinc-700 animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-2 h-2 rounded-full bg-zinc-700 animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-2 h-2 rounded-full bg-zinc-700 animate-bounce" style="animation-delay: 300ms"></span>
                        </div>
                    </div>
                </div>
                <div class="h-1.5 bg-green-900/30 w-full"><div class="h-full bg-green-500 transition-all duration-200 ease-linear" :style="`width: ${progress.ready}%`"></div></div>
            </section>
        </main>

        <footer class="h-14 bg-black border-t border-white/10 flex items-center shrink-0 z-20">
            <div class="bg-amber-500 text-black font-black text-xs uppercase px-8 h-full flex items-center shrink-0 z-10 tracking-[0.1em]">
                Últimos Listos
            </div>
            <div class="flex-1 marquee-container h-full flex items-center bg-[#08080a] relative">
                <div class="absolute left-0 top-0 bottom-0 w-12 bg-gradient-to-r from-black to-transparent z-10"></div>
                <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-black to-transparent z-10"></div>
                
                <div class="marquee-content flex items-center gap-16 pl-4" x-show="orders.ready.length > 0">
                    <template x-for="loop in 2">
                        <div class="flex gap-16">
                            <template x-for="item in orders.ready" :key="item.id">
                                <div class="flex items-center gap-4 opacity-50 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                                    <span class="text-2xl font-mono font-bold text-white" x-text="item.token"></span>
                                    <span class="text-[9px] font-bold text-black bg-zinc-500 px-1.5 py-0.5 rounded uppercase" x-text="item.tipo"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </footer>
    </div>
</x-layouts.guest>