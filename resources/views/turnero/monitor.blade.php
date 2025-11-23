<x-layouts.guest title="Turnero | Miss Sweet Candy">
    <div class="min-h-screen bg-zinc-950 text-zinc-100 overflow-hidden relative flex flex-col">

        {{-- ===== Background subtle grid + glow ===== --}}
        <div class="pointer-events-none absolute inset-0 opacity-30"
             style="background:
                radial-gradient(800px circle at 15% -10%, rgba(245,158,11,.18), transparent 40%),
                radial-gradient(900px circle at 85% 0%, rgba(34,197,94,.10), transparent 45%),
                linear-gradient(to bottom, rgba(255,255,255,.02), transparent 30%),
                repeating-linear-gradient(90deg, rgba(255,255,255,.03) 0, rgba(255,255,255,.03) 1px, transparent 1px, transparent 40px),
                repeating-linear-gradient(0deg, rgba(255,255,255,.03) 0, rgba(255,255,255,.03) 1px, transparent 1px, transparent 40px);
             ">
        </div>

        {{-- ====== TOP BAR ====== --}}
        <header class="sticky top-0 z-40 backdrop-blur bg-zinc-950/80 border-b border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 bg-amber-500 rounded-xl grid place-items-center ring-1 ring-black/10 shadow">
                        <svg class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a2 2 0 01-2-2v-4zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </span>
                    <div>
                        <div class="text-xl sm:text-2xl font-semibold tracking-tight text-zinc-50">
                            Miss Sweet Candy
                        </div>
                        <div class="text-xs text-zinc-300/80 -mt-0.5">
                            Retira tu pedido cuando aparezca en
                            <span class="text-amber-300 font-semibold">LISTO</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center gap-2 text-sm text-zinc-100 bg-zinc-900/60 border border-zinc-800 rounded-xl px-3 py-1.5">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        Sistema en vivo
                    </div>
                    <div id="clock" class="text-sm md:text-base font-mono text-zinc-50 bg-zinc-900/60 border border-zinc-800 rounded-xl px-3 py-1.5"></div>
                </div>
            </div>
        </header>

        {{-- ====== NOW CALLING / HERO ====== --}}
        <section class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
            <div class="hero-card relative overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-950 p-4 sm:p-5 shadow-xl">
                <div class="hero-shimmer absolute inset-0"></div>

                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="text-xs md:text-sm px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-200 border border-amber-500/30">
                            Ahora llamando
                        </span>
                        <div id="now-calling"
                             class="text-4xl sm:text-5xl md:text-6xl font-black tracking-widest text-white drop-shadow-[0_2px_12px_rgba(0,0,0,.9)]">
                            —
                        </div>
                    </div>

                    <div class="flex items-center gap-3 self-end sm:self-auto">
                        <div class="eta-pill">
                            <div class="eta-label">ETA</div>
                            <div id="now-eta" class="eta-value">—</div>
                        </div>
                        <div class="text-xs md:text-sm text-zinc-300/80">
                            Tiempo estimado
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ====== 3 COLUMN BOARD ====== --}}
        {{-- flex-1 min-h-0 = ocupa alto restante sin romper scroll interno --}}
        <main class="max-w-7xl mx-auto w-full flex-1 min-h-0 grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 px-4 sm:px-6 lg:px-8 pt-4 pb-20">
            {{-- EN COLA --}}
            <section class="board-col">
                <div class="board-head">
                    <div class="flex items-center gap-2">
                        <span class="dot dot-gray"></span>
                        <h2 class="board-title">EN COLA</h2>
                    </div>
                    <div id="count-pendiente" class="board-count">0</div>
                </div>

                <ul id="col-pendiente" class="board-body"></ul>

                <div class="board-empty hidden" data-empty="pendiente">
                    No hay pedidos en cola ✨
                </div>
            </section>

            {{-- PREPARANDO --}}
            <section class="board-col">
                <div class="board-head">
                    <div class="flex items-center gap-2">
                        <span class="dot dot-amber"></span>
                        <h2 class="board-title">PREPARANDO</h2>
                    </div>
                    <div id="count-preparando" class="board-count">0</div>
                </div>

                <ul id="col-preparando" class="board-body"></ul>

                <div class="board-empty hidden" data-empty="preparando">
                    Nadie preparando por ahora ☕
                </div>
            </section>

            {{-- LISTO --}}
            <section class="board-col board-col-ready">
                <div class="board-head">
                    <div class="flex items-center gap-2">
                        <span class="dot dot-green"></span>
                        <h2 class="board-title">LISTO PARA RETIRAR</h2>
                    </div>
                    <div id="count-preparado" class="board-count">0</div>
                </div>

                <ul id="col-preparado" class="board-body"></ul>

                <div class="board-empty hidden" data-empty="preparado">
                    Aún no hay pedidos listos 🌟
                </div>
            </section>
        </main>

        {{-- ====== BOTTOM TICKER ====== --}}
        <footer class="fixed bottom-0 left-0 right-0 border-t border-zinc-800 bg-zinc-950/90 backdrop-blur">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-3">
                <span class="text-xs md:text-sm px-2.5 py-1 rounded-full bg-green-500/10 text-green-200 border border-green-500/30">
                    Últimos listos
                </span>
                <div class="relative overflow-hidden flex-1 h-6">
                    <div id="ticker" class="absolute whitespace-nowrap ticker-move text-sm text-zinc-100/90">
                        —
                    </div>
                </div>
            </div>
        </footer>

        {{-- Ding --}}
   <audio id="ding" preload="auto">
          <source src="{{ asset('sounds/ding.mp3') }}" type="audio/mpeg">
      </audio>
  </div>

  @vite([
    'resources/css/turnero/monitor.css',
    'resources/js/turnero/monitor.js'
  ])
</x-layouts.guest>