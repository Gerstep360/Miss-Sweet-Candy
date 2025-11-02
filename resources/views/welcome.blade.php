<x-layouts.guest :title="$meta['title'] ?? __('Miss Sweet Candy - La Mejor Experiencia de Café')">
    <div class="welcome-page min-h-screen bg-zinc-950 text-zinc-100">

        <!-- Header (SSR, sin JS) -->
        <header class="welcome-header sticky top-0 z-40 {{ $ui['header_classes'] ?? 'backdrop-blur bg-zinc-950/80' }} border-b border-zinc-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">

                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <span class="w-8 h-8 bg-amber-500 rounded-lg grid place-items-center ring-1 ring-black/10">
                            <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a2 2 0 01-2-2v-4zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </span>
                        <span class="text-xl font-semibold">Miss Sweet Candy</span>
                    </a>

                    <!-- Nav desktop -->
                    <nav class="hidden md:flex items-center gap-6">
                        <a href="#productos" class="text-zinc-300 hover:text-white transition-colors">Productos</a>
                        <a href="#ubicacion" class="text-zinc-300 hover:text-white transition-colors">Ubicación</a>
                        <a href="#contacto" class="text-zinc-300 hover:text-white transition-colors">Contacto</a>

                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                            @else
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('login') }}" class="text-zinc-300 hover:text-white px-3 py-2 rounded-lg hover:bg-zinc-800 transition-colors">Iniciar Sesión</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
                                    @endif
                                </div>
                            @endauth
                        @endif
                    </nav>

                    <!-- Nav móvil (sin JS, con <details>) -->
                    <details class="md:hidden relative">
                        <summary class="list-none cursor-pointer rounded-lg p-2 text-white hover:bg-zinc-800/70 inline-flex items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </summary>
                        <div class="absolute right-0 mt-2 w-56 bg-zinc-900/95 backdrop-blur border border-zinc-800 rounded-xl shadow-xl p-2">
                            <a href="#productos" class="block px-4 py-2 rounded-lg text-zinc-300 hover:text-white hover:bg-zinc-800">Productos</a>
                            <a href="#ubicacion" class="block px-4 py-2 rounded-lg text-zinc-300 hover:text-white hover:bg-zinc-800">Ubicación</a>
                            <a href="#contacto" class="block px-4 py-2 rounded-lg text-zinc-300 hover:text-white hover:bg-zinc-800">Contacto</a>

                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="block px-4 py-2 rounded-lg text-amber-400 hover:bg-zinc-800">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="block px-4 py-2 rounded-lg text-zinc-300 hover:text-white hover:bg-zinc-800">Iniciar Sesión</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="block px-4 py-2 rounded-lg text-amber-400 hover:bg-zinc-800">Registrarse</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </details>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <section class="welcome-hero pt-10 sm:pt-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Texto -->
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-6">
                            <span class="w-2 h-2 bg-amber-400 rounded-full mr-2"></span>
                            Desde 2024
                        </div>

                        <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                            El Mejor
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">Café</span>
                            de la Ciudad
                        </h1>

                        <p class="text-xl text-zinc-300 mb-8 leading-relaxed">
                            Disfruta de granos selectos, ambiente acogedor y la tradición de más de 25 años sirviendo el mejor café artesanal.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a href="{{ route('menu.publico') }}" class="btn btn-primary px-8 py-4 text-lg inline-flex items-center justify-center">
                                Ver Menú
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                            <a href="#ubicacion" class="btn btn-outline px-8 py-4 text-lg">Cómo Llegar</a>
                        </div>
                    </div>

                    <!-- “Especial del Día” (SSR) -->
                    <div class="relative">
                        <div class="bg-gradient-to-br from-zinc-900/70 to-zinc-900/40 backdrop-blur border border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-2xl ring-1 ring-black/5">
                            <div class="bg-zinc-950/90 rounded-xl p-4 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center px-2.5 py-1 text-[12px] font-medium rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                            {{ $especialHoy['existe'] ? 'Especial del Día' : 'Café del Día' }}
                                        </span>
                                        @if($especialHoy['existe'] && $especialHoy['porcentaje'] > 0)
                                            <span class="px-2 py-0.5 text-[11px] rounded-full bg-green-500/15 text-green-300 border border-green-500/30">
                                                -{{ $especialHoy['porcentaje'] }}%
                                            </span>
                                        @endif
                                    </div>
                                    <div class="w-2.5 h-2.5 rounded-full {{ $estadoLocal['abierto'] ? 'bg-green-400' : 'bg-red-400' }}" aria-label="{{ $estadoLocal['texto'] }}"></div>
                                </div>

                                @if($especialHoy['existe'])
                                    <div class="flex items-start gap-4">
                                        @if(!empty($especialHoy['imagen_url']))
                                            <div class="relative">
                                                <img src="{{ $especialHoy['imagen_url'] }}" alt="{{ $especialHoy['nombre'] }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-lg object-cover border border-zinc-800 shadow">
                                                <div class="absolute inset-0 rounded-lg ring-1 ring-white/5"></div>
                                            </div>
                                        @else
                                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-lg bg-amber-500/10 border border-zinc-800 grid place-items-center">
                                                <svg class="w-8 h-8 text-amber-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                    <path d="M3 6h18v2H3zM4 10h10v8H4zM16 10h4v8h-4z"/>
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            <h4 class="text-white font-semibold text-xl leading-tight mb-1">{{ $especialHoy['nombre'] }}</h4>

                                            @if(!empty($especialHoy['descripcion']))
                                                <p class="text-zinc-400 text-sm mb-3">{{ $especialHoy['descripcion'] }}</p>
                                            @endif

                                            <div class="flex items-end gap-3 mb-2">
                                                <span class="text-zinc-500 line-through text-sm">${{ $especialHoy['precio_original'] }}</span>
                                                <span class="text-amber-300 font-extrabold text-2xl tracking-tight">${{ $especialHoy['precio_final'] }}</span>
                                                @if((float)$especialHoy['ahorro'] > 0)
                                                    <span class="text-xs text-green-400">Ahorras ${{ $especialHoy['ahorro'] }}</span>
                                                @endif
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-[11px] px-2 py-1 rounded bg-zinc-900 border border-zinc-800 text-zinc-400">
                                                    {{ $especialHoy['tipo'] === 'precio_fijo' ? 'Precio especial' : 'Descuento porcentual' }}
                                                </span>
                                                @if(!empty($especialHoy['categoria']))
                                                    <span class="text-[11px] px-2 py-1 rounded bg-zinc-900 border border-zinc-800 text-zinc-400">
                                                        {{ $especialHoy['categoria'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Fallback SSR -->
                                    <div class="space-y-3 sm:space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-zinc-400 text-sm sm:text-base">Espresso</span>
                                            <span class="text-amber-300 font-bold text-sm sm:text-base">$25.00</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-zinc-400 text-sm sm:text-base">Cappuccino</span>
                                            <span class="text-amber-300 font-bold text-sm sm:text-base">$35.00</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-zinc-400 text-sm sm:text-base">Latte</span>
                                            <span class="text-amber-300 font-bold text-sm sm:text-base">$40.00</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Estado del local -->
                                <div class="mt-6 text-center">
                                    <div class="text-xl sm:text-2xl font-bold {{ $estadoLocal['abierto'] ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $estadoLocal['texto'] }}
                                    </div>
                                    @if(!empty($estadoLocal['rango']))
                                        <div class="text-xs text-zinc-400">{{ $estadoLocal['rango'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Productos destacados -->
        <section id="productos" class="py-16 lg:py-24 bg-zinc-900/30">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <h2 class="text-3xl lg:text-4xl font-bold mb-3">Nuestros Productos</h2>
                    <p class="text-lg sm:text-xl text-zinc-300 max-w-3xl mx-auto">
                        Desde espressos perfectos hasta postres artesanales, cada producto es preparado con amor y dedicación
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @forelse($destacados as $p)
                        <article class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4 hover:border-zinc-700 transition">
                            <div class="w-full h-40 rounded-lg mb-4 overflow-hidden bg-zinc-900 border border-zinc-800 grid place-items-center">
                                @if(!empty($p->imagen_url))
                                    <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-12 h-12 text-amber-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold mb-2">{{ $p->nombre }}</h3>

                            @if($p->tiene_oferta)
                            <div class="flex items-end gap-2">
                                <span class="text-zinc-500 line-through text-sm">${{ number_format($p->precio, 2, '.', '') }}</span>
                                <span class="text-amber-300 font-extrabold text-xl">${{ number_format($p->precio_vigente, 2, '.', '') }}</span>
                                <span class="text-[11px] px-2 py-0.5 rounded bg-green-500/15 text-green-300 border border-green-500/30">
                                -{{ $p->porcentaje_oferta }}%
                                </span>
                            </div>
                            @else
                            <div class="text-amber-400 font-bold text-lg">
                                ${{ number_format($p->precio_vigente, 2, '.', '') }}
                            </div>
                            @endif
                    
                        </article>
                    @empty
                        <!-- Tres tarjetas de ejemplo -->
                        <div class="product-card">
                            <div class="w-full h-40 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-lg mb-4 grid place-items-center">
                                <svg class="w-12 h-12 sm:w-16 sm:h-16 text-amber-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold mb-2">Cafés Especiales</h3>
                            <p class="text-zinc-300 mb-3 text-sm sm:text-base">Espresso, Cappuccino, Latte, Americano y más.</p>
                            <span class="text-amber-400 font-bold text-lg">Desde $25</span>
                        </div>

                        <div class="product-card">
                            <div class="w-full h-40 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-lg mb-4 grid place-items-center">
                                <svg class="w-12 h-12 sm:w-16 sm:h-16 text-amber-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l-1.07-3.292z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold mb-2">Repostería Artesanal</h3>
                            <p class="text-zinc-300 mb-3 text-sm sm:text-base">Croissants, muffins y cheesecakes frescos.</p>
                            <span class="text-amber-400 font-bold text-lg">Desde $15</span>
                        </div>

                        <div class="product-card">
                            <div class="w-full h-40 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-lg mb-4 grid place-items-center">
                                <svg class="w-12 h-12 sm:w-16 sm:h-16 text-amber-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a2 2 0 01-2-2v-4zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-semibold mb-2">Desayunos & Snacks</h3>
                            <p class="text-zinc-300 mb-3 text-sm sm:text-base">Sandwiches y tostadas artesanales.</p>
                            <span class="text-amber-400 font-bold text-lg">Desde $30</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Ubicación -->
        <section id="ubicacion" class="py-16 lg:py-24">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4">Visítanos</h2>
                    <p class="text-xl text-zinc-300">Ubicados en el corazón de la ciudad, te esperamos con el mejor ambiente</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-start">
                    <!-- Mapa (SSR, sin lazy JS) -->
                    <div class="w-full h-64 sm:h-80 lg:h-96 bg-zinc-800 rounded-2xl overflow-hidden">
                        <iframe
                            src="{{ $ui['map_iframe_src'] }}"
                            width="100%" height="100%" style="border:0;"
                            allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="w-full h-full" aria-label="Mapa de ubicación">
                        </iframe>
                    </div>

                    <!-- Info -->
                    <div class="bg-zinc-950/80 border border-zinc-800 rounded-2xl p-6">
                        <h3 class="text-xl sm:text-2xl font-semibold mb-6">Información de Contacto</h3>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center text-amber-500 flex-shrink-0">📍</div>
                                <div>
                                    <p class="text-white font-medium">Dirección</p>
                                    <p class="text-zinc-400 text-sm">Av. Principal 123, Centro de la Ciudad</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center text-amber-500 flex-shrink-0">📞</div>
                                <div>
                                    <p class="text-white font-medium">Teléfono</p>
                                    <p class="text-zinc-400 text-sm">(555) 123-CAFÉ</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center text-amber-500 flex-shrink-0">🕐</div>
                                <div>
                                    <p class="text-white font-medium">Horarios</p>
                                    <ul class="text-zinc-400 text-sm">
                                        @foreach($horarios as $horario)
                                            <li>
                                                {{ ucfirst($horario->dia) }}:
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $horario->abre)->format('g:i A') }}
                                                -
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $horario->cierra)->format('g:i A') }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center text-amber-500 flex-shrink-0">📧</div>
                                <div>
                                    <p class="text-white font-medium">Email</p>
                                    <p class="text-zinc-400 text-sm">hola@cafearoma.com</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 space-y-3">
                            <a href="{{ $ui['map_dir_href'] }}" target="_blank"
                               class="w-full bg-amber-500 text-black py-3 px-4 rounded-lg font-medium hover:bg-amber-400 transition-colors grid place-items-center text-sm sm:text-base">
                                🧭 Obtener Direcciones
                            </a>
                            <a href="tel:+59177016469"
                               class="w-full bg-zinc-700 text-white py-3 px-4 rounded-lg font-medium hover:bg-zinc-600 transition-colors grid place-items-center text-sm sm:text-base">
                                📞 Llamar Ahora
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section id="contacto" class="py-16 lg:py-24 bg-gradient-to-r from-amber-500 to-orange-500">
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-black mb-6">¿Listo para la mejor experiencia de café?</h2>
                <p class="text-lg sm:text-xl text-black/80 mb-8">Ven y disfruta de nuestro ambiente acogedor y productos artesanales</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ $ui['map_dir_href'] }}" target="_blank"
                       class="bg-black text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:bg-zinc-800 transition-colors inline-flex items-center justify-center gap-2 text-sm sm:text-base">
                        Visítanos Ahora
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="tel:+555123CAFE"
                       class="border-2 border-black text-black px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:bg-black hover:text-white transition-colors text-sm sm:text-base">
                        Llamar para Reservar
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-zinc-950 border-t border-zinc-800 py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg grid place-items-center">
                            <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a2 2 0 01-2-2v-4zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-semibold">Miss Sweet Candy</span>
                    </div>
                    <p class="text-zinc-400 mb-6">El mejor café desde 2024</p>
                    <div class="text-sm text-zinc-400">&copy; 2025 Miss Sweet Candy. Todos los derechos reservados.</div>
                </div>
            </div>
        </footer>

    </div>
</x-layouts.guest>
