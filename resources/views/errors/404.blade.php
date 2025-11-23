{{-- filepath: resources/views/errors/404.blade.php --}}
<x-layouts.guest :title="__('404 — Página no encontrada | Miss Sweet Candy')">
    <div class="min-h-screen bg-zinc-950 text-white flex flex-col">
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 select-none">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg grid place-items-center">
                            <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
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
                            <span class="w-2 h-2 bg-amber-400 rounded-full mr-2"></span>
                            Error 404 — Página no encontrada 🔍
                        </div>

                        <h1 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight">
                            Se nos perdió la <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">galleta</span> 🍪
                        </h1>

                        <p class="text-lg text-zinc-300 mb-6">
                            La URL que buscas no existe o cambió de lugar. Prueba regresar al inicio o abre el tablero.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ url('/') }}" class="bg-amber-500 text-black px-6 py-3 rounded-lg font-semibold hover:bg-amber-400 transition-colors inline-flex items-center justify-center gap-2">
                                Ir al inicio
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                            @auth
                                <a href="{{ route('dashboard') }}" class="border-2 border-amber-500 text-amber-500 px-6 py-3 rounded-lg font-semibold hover:bg-amber-500/10 transition-colors">
                                    Ir al Dashboard
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Tarjeta lateral -->
                    <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6 sm:p-8 shadow-xl">
                        <h3 class="font-semibold text-white text-base sm:text-lg mb-2">Pista del día</h3>
                        <p class="text-zinc-300">
                            Revisa que la dirección esté bien escrita o usa el menú para encontrar lo que buscas.
                        </p>
                        <div class="mt-4">
                            <form action="{{ url('/') }}" class="flex gap-2">
                                <input type="text" placeholder="Buscar en el sitio..." class="flex-1 bg-zinc-800/70 border border-zinc-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/40" />
                                <button type="submit" class="bg-amber-500 text-black px-4 py-2 rounded-lg font-semibold hover:bg-amber-400 transition-colors">
                                    Buscar
                                </button>
                            </form>
                        </div>
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
                            <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-semibold">Miss Sweet Candy</span>
                    </div>
                    <p class="text-zinc-400">El mejor café desde 2024</p>
                    <div class="mt-2 text-sm text-zinc-500">&copy; {{ now()->year }} Miss Sweet Candy. Todos los derechos reservados.</div>
                </div>
            </div>
        </footer>
    </div>

    <style>
        /* Efecto “linterna”: sigue el puntero con un degradado suave */
        .spotlight {
            position: fixed; inset: 0; pointer-events: none; z-index: 40;
            background: radial-gradient(200px 200px at var(--x, 50%) var(--y, 50%), rgba(255,255,255,0.06), rgba(0,0,0,0));
            transition: background-position .05s linear;
        }
    </style>

    <div id="spot" class="spotlight"></div>

    <script>
        // Menú móvil
        (function () {
            const btn = document.querySelector('.mobile-menu-btn');
            const menu = document.querySelector('.mobile-menu');
            if (btn && menu) btn.addEventListener('click', () => menu.classList.toggle('hidden'));
        })();

        // Spotlight follow
        (function () {
            const spot = document.getElementById('spot');
            if (!spot) return;
            addEventListener('pointermove', (e) => {
                spot.style.setProperty('--x', e.clientX + 'px');
                spot.style.setProperty('--y', e.clientY + 'px');
            }, { passive: true });
        })();
    </script>
</x-layouts.guest>
