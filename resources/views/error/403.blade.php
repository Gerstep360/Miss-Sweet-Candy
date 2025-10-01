<x-layouts.guest :title="__('401 — No autorizado | Miss Sweet Candy')">
    <div class="min-h-screen bg-zinc-950 text-white flex flex-col">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 select-none">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg grid place-items-center">
                            <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>
                        </div>
                        <span class="text-xl font-semibold">Miss Sweet Candy</span>
                    </a>

                    <nav class="hidden md:flex items-center space-x-6">
                        <a href="{{ url('/') }}#productos" class="text-zinc-300 hover:text-white transition-colors">Productos</a>
                        <a href="{{ url('/') }}#ubicacion" class="text-zinc-300 hover:text-white transition-colors">Ubicación</a>
                        <a href="{{ url('/') }}#contacto" class="text-zinc-300 hover:text-white transition-colors">Contacto</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="bg-amber-500 text-black px-4 py-2 rounded-lg font-medium hover:bg-amber-400 transition-colors">Dashboard</a>
                            @else
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('login') }}" class="text-zinc-300 hover:text-white px-3 py-2 rounded-lg hover:bg-zinc-800 transition-colors">Iniciar Sesión</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="bg-amber-500 text-black px-4 py-2 rounded-lg font-medium hover:bg-amber-400 transition-colors">Registrarse</a>
                                    @endif
                                </div>
                            @endauth
                        @endif
                    </nav>

                    <button class="md:hidden mobile-menu-btn text-white p-2" aria-label="Abrir menú">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                <!-- Mobile menu -->
                <div class="md:hidden mobile-menu hidden bg-zinc-900 border-t border-zinc-800 py-4">
                    <div class="flex flex-col space-y-3">
                        <a href="{{ url('/') }}#productos" class="text-zinc-300 hover:text-white px-4 py-2 transition-colors">Productos</a>
                        <a href="{{ url('/') }}#ubicacion" class="text-zinc-300 hover:text-white px-4 py-2 transition-colors">Ubicación</a>
                        <a href="{{ url('/') }}#contacto" class="text-zinc-300 hover:text-white px-4 py-2 transition-colors">Contacto</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="bg-amber-500 text-black py-2 rounded-lg font-medium hover:bg-amber-400 transition-colors mx-4 text-center">Dashboard</a>
                            @else
                                <div class="px-4 space-y-2">
                                    <a href="{{ route('login') }}" class="block w-full text-center bg-zinc-800 text-white py-2 rounded-lg hover:bg-zinc-700 transition-colors">Iniciar Sesión</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="block w-full text-center bg-amber-500 text-black py-2 rounded-lg font-medium hover:bg-amber-400 transition-colors">Registrarse</a>
                                    @endif
                                </div>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <main class="flex-1 pt-10 sm:pt-12">
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-10 items-center">
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-6">
                            <span class="w-2 h-2 bg-amber-400 rounded-full mr-2" id="status-dot"></span>
                            Error 401 — No autorizado
                        </div>

                        <h1 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight">
                            No puedes pasar… <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">por ahora</span> ☕
                        </h1>

                        <p class="text-lg text-zinc-300 mb-6">
                            No tienes permiso para seguir :(
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ url('/') }}" class="bg-amber-500 text-black px-6 py-3 rounded-lg font-semibold hover:bg-amber-400 transition-colors inline-flex items-center justify-center gap-2">
                                Ir al inicio
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="border-2 border-amber-500 text-amber-500 px-6 py-3 rounded-lg font-semibold hover:bg-amber-500/10 transition-colors">
                                    Iniciar sesión
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6 sm:p-8 shadow-xl">
                        <h3 class="font-semibold text-white text-base sm:text-lg mb-2">Mensaje de la Casa</h3>
                        <p class="text-zinc-300">Si la vida te cerró una puerta... Pide un café en ventanilla. :)</p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-zinc-950 border-t border-zinc-800 py-10 mt-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="flex items-center justify-center space-x-3 mb-3">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg grid place-items-center">
                            <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>
                        </div>
                        <span class="text-xl font-semibold">Miss Sweet Candy</span>
                    </div>
                    <p class="text-zinc-400">El mejor café desde 2024</p>
                    <div class="mt-2 text-sm text-zinc-500">&copy; {{ now()->year }} Miss Sweet Candy. Todos los derechos reservados.</div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Audio local -->
    <audio id="taco-audio" src="{{ asset('storage/assets/error/401/tacos.mp3') }}" preload="auto"></audio>

    <!-- Overlay global lluvia + FAB + Panel -->
    <div id="taco-rain-root" class="pointer-events-none fixed inset-0 z-[10000]"></div>

    <!-- FAB Taco -->
    <button id="taco-fab"
            class="hidden fixed z-[10060] bottom-5 right-5 h-12 w-12 rounded-full bg-amber-500 text-black text-2xl shadow-lg border border-amber-400
                   grid place-items-center hover:bg-amber-400 active:scale-95 transition"
            aria-label="It’s Raining Tacos">🌮</button>

    <!-- Panel flotante (draggable) -->
    <div id="taco-panel" class="hidden fixed z-[10055] bottom-24 right-5 w-[min(92vw,320px)] select-none">
        <div id="taco-drag" class="cursor-grab active:cursor-grabbing rounded-t-xl bg-amber-500 text-black px-4 py-2 flex items-center justify-between" style="touch-action:none">
            <strong class="text-sm uppercase tracking-wide">It’s Raining Tacos</strong>
            <button id="taco-close" class="h-6 w-6 rounded bg-black/20 hover:bg-black/30 grid place-items-center" aria-label="Cerrar">×</button>
        </div>
        <div class="rounded-b-xl bg-zinc-900/95 backdrop-blur border border-zinc-800 p-3">
            <div class="grid grid-cols-2 gap-2">
                <button id="egg-play"  class="px-3 py-2 rounded bg-amber-500 text-black font-semibold hover:bg-amber-400">Reproducir</button>
                <button id="egg-stop"  class="px-3 py-2 rounded border border-zinc-700 hover:bg-zinc-800 text-white">Detener</button>
            </div>
        </div>
    </div>

    <style>
        @keyframes fall-emoji {
            0%   { transform: translateY(-10vh) rotate(0deg); opacity: 0; }
            10%  { opacity: 1; }
            100% { transform: translateY(110vh) rotate(360deg); opacity: 0; }
        }
        .taco-drop {
            position: fixed; top: -2rem;
            font-size: clamp(20px, 3.6vw, 32px);
            animation: fall-emoji linear forwards;
            will-change: transform, opacity;
            filter: drop-shadow(0 2px 2px rgba(0,0,0,.25));
            user-select: none;
            pointer-events: none;
        }
    </style>

    <script>
        // Menú móvil
        (function () {
            const btn = document.querySelector('.mobile-menu-btn');
            const menu = document.querySelector('.mobile-menu');
            if (btn && menu) btn.addEventListener('click', () => menu.classList.toggle('hidden'));
        })();

        // ====== EASTER EGG: UX minimal ======
        (function () {
            const isCoarse = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;
            const dot      = document.getElementById('status-dot');
            const fab      = document.getElementById('taco-fab');
            const panel    = document.getElementById('taco-panel');
            const drag     = document.getElementById('taco-drag');
            const btnClose = document.getElementById('taco-close');
            const btnPlay  = document.getElementById('egg-play');
            const btnStop  = document.getElementById('egg-stop');
            const audio    = document.getElementById('taco-audio');
            const rainRoot = document.getElementById('taco-rain-root');

            let unlocked = false;
            let spawnTimer = null;

            // Helpers
            function showFab() {
                fab.classList.remove('hidden');
                try { navigator.vibrate && navigator.vibrate([10,20,10]); } catch {}
            }
            function openPanel() { panel.classList.remove('hidden'); }
            function closePanel(){ panel.classList.add('hidden'); }

            // Drag panel (pointer events)
            (function enableDrag() {
                let startX=0, startY=0, ox=0, oy=0, dragging=false;
                const clamp = (v, min, max) => Math.min(Math.max(v, min), max);
                const onDown = (e) => {
                    dragging = true; try { drag.setPointerCapture(e.pointerId); } catch {}
                    const rect = panel.getBoundingClientRect();
                    ox = rect.left; oy = rect.top;
                    startX = e.clientX; startY = e.clientY;
                };
                const onMove = (e) => {
                    if (!dragging) return;
                    const dx = e.clientX - startX, dy = e.clientY - startY;
                    const x = clamp(ox + dx, 8, innerWidth - panel.offsetWidth - 8);
                    const y = clamp(oy + dy, 8, innerHeight - panel.offsetHeight - 8);
                    panel.style.left = x + 'px'; panel.style.top = y + 'px';
                    panel.style.right = 'auto'; panel.style.bottom = 'auto';
                };
                const onUp = (e) => { dragging = false; try { drag.releasePointerCapture(e.pointerId); } catch {} };
                drag.addEventListener('pointerdown', onDown);
                addEventListener('pointermove', onMove);
                addEventListener('pointerup', onUp);
            })();

            // Lluvia (suave, fija)
            function spawnOne() {
                const e = document.createElement('span');
                e.className = 'taco-drop';
                e.textContent = '🌮';
                e.style.left = (Math.random() * 100) + 'vw';
                e.style.animationDuration = (7 + Math.random()*5) + 's';
                e.style.animationDelay = (Math.random()*0.6) + 's';
                e.style.transform = `translateX(${(Math.random()*2-1)*8}vw)`;
                rainRoot.appendChild(e);
                setTimeout(() => e.remove(), 14000);
            }
            function startRain() {
                stopRain(); // seguridad
                // ráfaga inicial (poca, bonita)
                for (let i = 0; i < 24; i++) spawnOne();
                // goteo lento
                spawnTimer = setInterval(() => { for (let i = 0; i < 2; i++) spawnOne(); }, 900);
            }
            function stopRain() {
                clearInterval(spawnTimer);
                spawnTimer = null;
                rainRoot.innerHTML = '';
            }

            // Audio
            function playAudio() {
                try { audio.currentTime = 0; audio.play(); } catch {}
            }
            function stopAudio()  {
                try { audio.pause(); audio.currentTime = 0; } catch {}
            }

            // Egg ON/OFF
            function startEgg() { startRain(); playAudio(); document.title = "🌮 It’s Raining Tacos — 401"; }
            function stopEgg()  { stopRain();  stopAudio(); }

            // FAB comportamiento
            // Tap: inicia egg; Long-press (~500ms): abre panel
            let pressTimer = null;
            function armPressTimer(){ pressTimer = setTimeout(openPanel, 500); }
            function clearPressTimer(){ if (pressTimer) { clearTimeout(pressTimer); pressTimer = null; } }

            fab.addEventListener('click', () => startEgg());
            fab.addEventListener('touchstart', armPressTimer, {passive:true});
            fab.addEventListener('touchend', clearPressTimer);
            fab.addEventListener('mousedown', armPressTimer);
            fab.addEventListener('mouseup', clearPressTimer);
            fab.addEventListener('mouseleave', clearPressTimer);

            // Panel botones
            btnPlay.addEventListener('click', startEgg);
            btnStop.addEventListener('click', stopEgg);
            btnClose.addEventListener('click', closePanel);

            // Desbloqueo
            function unlock() { if (unlocked) return; unlocked = true; showFab(); }
            if (!isCoarse) {
                const buf = [];
                addEventListener('keydown', (e) => {
                    const k = (e.key || '').toUpperCase();
                    if (!k) return;
                    buf.push(k); if (buf.length > 5) buf.shift();
                    if (buf.join('') === 'TACOS') unlock();
                });
            } else if (dot) {
                let taps = 0, timer = null;
                const tap = () => {
                    taps++; clearTimeout(timer);
                    timer = setTimeout(() => taps = 0, 1200);
                    if (taps >= 5) { taps = 0; unlock(); }
                };
                dot.addEventListener('touchstart', tap, {passive:true});
                dot.addEventListener('click', tap, {passive:true});
            }
        })();
        // ====== FIN EASTER EGG ======
    </script>
</x-layouts.guest>
