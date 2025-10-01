<x-layouts.guest :title="__('Miss Sweet Candy - La Mejor Experiencia de Café')">
    <div class="welcome-page">
        <!-- Header fijo -->
        <header class="welcome-header">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-semibold text-white">Miss Sweet Candy</span>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-6">
                        <a href="#productos" class="text-zinc-300 hover:text-white transition-colors">Productos</a>
                        <a href="#ubicacion" class="text-zinc-300 hover:text-white transition-colors">Ubicación</a>
                        <a href="#contacto" class="text-zinc-300 hover:text-white transition-colors">Contacto</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                            @else
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('login') }}" class="text-zinc-300 hover:text-white px-3 py-2 rounded-lg hover:bg-zinc-800 transition-colors">
                                        Iniciar Sesión
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-primary">
                                            Registrarse
                                        </a>
                                    @endif
                                </div>
                            @endauth
                        @endif
                    </nav>

                    <!-- Mobile menu button -->
                    <button class="md:hidden mobile-menu-btn text-white p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                <!-- Mobile menu -->
                <div class="md:hidden mobile-menu hidden bg-zinc-900 border-t border-zinc-800 py-4">
                    <div class="flex flex-col space-y-3">
                        <a href="#productos" class="text-zinc-300 hover:text-white px-4 py-2 transition-colors">Productos</a>
                        <a href="#ubicacion" class="text-zinc-300 hover:text-white px-4 py-2 transition-colors">Ubicación</a>
                        <a href="#contacto" class="text-zinc-300 hover:text-white px-4 py-2 transition-colors">Contacto</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary mx-4">Dashboard</a>
                            @else
                                <div class="px-4 space-y-2">
                                    <a href="{{ route('login') }}" class="block w-full text-center bg-zinc-800 text-white py-2 rounded-lg hover:bg-zinc-700 transition-colors">
                                        Iniciar Sesión
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="block w-full text-center btn btn-primary">
                                            Registrarse
                                        </a>
                                    @endif
                                </div>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="welcome-hero pt-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Content -->
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-6">
                            <span class="w-2 h-2 bg-amber-400 rounded-full mr-2"></span>
                            Desde 2024
                        </div>
                        
                        <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight text-white">
                            El Mejor
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">
                                Café
                            </span>
                            de la Ciudad
                        </h1>
                        
                        <p class="text-xl text-zinc-300 mb-8 leading-relaxed">
                            Disfruta de granos selectos, ambiente acogedor y la tradición de más de 25 años sirviendo el mejor café artesanal.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('menu.publico') }}" class="btn btn-primary px-8 py-4 text-lg">
                                Ver Menú
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                            <a href="#ubicacion" class="btn btn-outline px-8 py-4 text-lg">
                                Cómo Llegar
                            </a>
                        </div>
                    </div>

                    <!-- Visual - Café del Día FUNCIONAL -->
                    <div class="relative">
                        <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6 sm:p-8 transform rotate-3 hover:rotate-0 transition-transform duration-500">
                            <div class="bg-zinc-950 rounded-lg p-4 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-semibold text-white text-sm sm:text-base cafe-del-dia-title">Café del Día</h3>
                                    <div class="cafe-status-indicator w-3 h-3 bg-green-400 rounded-full"></div>
                                </div>
                                <div class="space-y-3 sm:space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-zinc-400 text-sm sm:text-base">Espresso</span>
                                        <span class="text-amber-400 font-bold text-sm sm:text-base">$25</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-zinc-400 text-sm sm:text-base">Cappuccino</span>
                                        <span class="text-amber-400 font-bold text-sm sm:text-base">$35</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-zinc-400 text-sm sm:text-base">Latte</span>
                                        <span class="text-amber-400 font-bold text-sm sm:text-base">$40</span>
                                    </div>
                                </div>
                                <div class="precio-especial"></div>
                                @php
                                    $diaActual = strtolower(\Carbon\Carbon::now()->locale('es')->dayName);
                                    $horarioHoy = $horarios->firstWhere('dia', $diaActual);
                                @endphp

                                <div class="mt-6 text-center cafe-status">
                                    @if($horarioHoy)
                                        @php
                                            $horaActual = \Carbon\Carbon::now()->format('H:i:s');
                                            $abierto = $horaActual >= $horarioHoy->abre && $horaActual < $horarioHoy->cierra;
                                        @endphp
                                        <div class="cafe-status-text text-xl sm:text-2xl font-bold {{ $abierto ? 'text-green-400' : 'text-red-400' }}">
                                            {{ $abierto ? '¡Abierto!' : 'Cerrado' }}
                                        </div>
                                        <div class="text-xs text-zinc-400">
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $horarioHoy->abre)->format('g:i A') }}
                                            -
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $horarioHoy->cierra)->format('g:i A') }}
                                        </div>
                                    @else
                                        <div class="cafe-status-text text-xl sm:text-2xl font-bold text-red-400">
                                            Sin horario para hoy
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Productos Section -->
        <section id="productos" class="py-16 lg:py-24 bg-zinc-900/30">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4 text-white">
                        Nuestros Productos
                    </h2>
                    <p class="text-xl text-zinc-300 max-w-3xl mx-auto">
                        Desde espressos perfectos hasta postres artesanales, cada producto es preparado con amor y dedicación
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    <!-- Producto 1 -->
                    <div class="product-card">
                        <div class="w-full h-32 sm:h-40 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-lg mb-4 flex items-center justify-center">
                            <svg class="w-12 h-12 sm:w-16 sm:h-16 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-white">Cafés Especiales</h3>
                        <p class="text-zinc-300 mb-4 text-sm sm:text-base">Espresso, Cappuccino, Latte, Americano y más variedades preparadas por baristas expertos.</p>
                        <span class="text-amber-500 font-bold text-lg">Desde $25</span>
                    </div>

                    <!-- Producto 2 -->
                    <div class="product-card">
                        <div class="w-full h-32 sm:h-40 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-lg mb-4 flex items-center justify-center">
                            <svg class="w-12 h-12 sm:w-16 sm:h-16 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-white">Repostería Artesanal</h3>
                        <p class="text-zinc-300 mb-4 text-sm sm:text-base">Croissants, muffins, cheesecakes y postres horneados frescos todos los días.</p>
                        <span class="text-amber-500 font-bold text-lg">Desde $15</span>
                    </div>

                    <!-- Producto 3 -->
                    <div class="product-card">
                        <div class="w-full h-32 sm:h-40 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-lg mb-4 flex items-center justify-center">
                            <svg class="w-12 h-12 sm:w-16 sm:h-16 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-white">Desayunos & Snacks</h3>
                        <p class="text-zinc-300 mb-4 text-sm sm:text-base">Sandwiches gourmet, tostadas artesanales y opciones saludables para cualquier momento.</p>
                        <span class="text-amber-500 font-bold text-lg">Desde $30</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Ubicación y Mapa Section -->
        <section id="ubicacion" class="py-16 lg:py-24">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4 text-white">
                        Visítanos
                    </h2>
                    <p class="text-xl text-zinc-300">
                        Ubicados en el corazón de la ciudad, te esperamos con el mejor ambiente
                    </p>
                </div>

                <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-start">
                    <!-- Mapa - RESPONSIVO -->
                    <div class="map-container">
                        <div id="map-container" class="w-full h-64 sm:h-80 lg:h-96 bg-zinc-800 rounded-2xl flex items-center justify-center">
                            <div class="text-center">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="text-zinc-400 text-sm sm:text-base">Cargando mapa...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Información de contacto - RESPONSIVO -->
                    <div class="map-info-card">
                        <h3 class="text-xl sm:text-2xl font-semibold text-white mb-6">Información de Contacto</h3>
                        
                        <div class="space-y-4">
                            <div class="contact-item">
                                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center text-amber-500 flex-shrink-0">
                                    📍
                                </div>
                                <div>
                                    <p class="text-white font-medium">Dirección</p>
                                    <p class="text-zinc-400 text-sm">Av. Principal 123, Centro de la Ciudad</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center text-amber-500 flex-shrink-0">
                                    📞
                                </div>
                                <div>
                                    <p class="text-white font-medium">Teléfono</p>
                                    <p class="text-zinc-400 text-sm">(555) 123-CAFÉ</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center text-amber-500 flex-shrink-0">
                                    🕐
                                </div>
                                <div>
                                    <p class="text-white font-medium">Horarios</p>
                                    <ul class="text-zinc-400 text-sm">
                                        @foreach($horarios as $horario)
                                            <li>
                                                {{ ucfirst($horario->dia) }}: {{ \Carbon\Carbon::createFromFormat('H:i:s', $horario->abre)->format('g:i A') }}
                                                -
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $horario->cierra)->format('g:i A') }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center text-amber-500 flex-shrink-0">
                                    📧
                                </div>
                                <div>
                                    <p class="text-white font-medium">Email</p>
                                    <p class="text-zinc-400 text-sm">hola@cafearoma.com</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 space-y-3">
                            <a href="https://www.google.com/maps/dir//-17.7433967590332,-63.1631317138672" 
                               target="_blank"
                               class="w-full bg-amber-500 text-black py-3 px-4 rounded-lg font-medium hover:bg-amber-400 transition-colors flex items-center justify-center gap-2 text-sm sm:text-base">
                                🧭 Obtener Direcciones
                            </a>
                            
                            <a href="tel:+59177016469" 
                               class="w-full bg-zinc-700 text-white py-3 px-4 rounded-lg font-medium hover:bg-zinc-600 transition-colors flex items-center justify-center gap-2 text-sm sm:text-base">
                                📞 Llamar Ahora
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section id="contacto" class="py-16 lg:py-24 bg-gradient-to-r from-amber-500 to-orange-500">
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-black mb-6">
                    ¿Listo para la mejor experiencia de café?
                </h2>
                <p class="text-lg sm:text-xl text-black/80 mb-8">
                    Ven y disfruta de nuestro ambiente acogedor y productos artesanales
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="https://www.google.com/maps/dir//-17.7433967590332,-63.1631317138672" 
                       target="_blank"
                       class="bg-black text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:bg-zinc-800 transition-colors inline-flex items-center justify-center gap-2 text-sm sm:text-base">
                        Visítanos Ahora
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-semibold text-white">Miss Sweet Candy</span>
                    </div>
                    <p class="text-zinc-400 mb-6">
                        El mejor café desde 2024
                    </p>
                    <div class="flex justify-center space-x-6 text-sm text-zinc-400">
                        <span>&copy; 2025 Miss Sweet Candy. Todos los derechos reservados.</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</x-layouts.guest>