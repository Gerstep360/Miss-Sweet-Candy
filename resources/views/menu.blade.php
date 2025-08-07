<x-layouts.guest :title="__('Menú Completo - Miss Sweet Candy')">
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
                        <a href="{{ route('home') }}" class="text-zinc-300 hover:text-white transition-colors">Inicio</a>
                        <a href="#categorias" class="text-zinc-300 hover:text-white transition-colors">Categorías</a>
                        <a href="{{ route('home') }}#ubicacion" class="text-zinc-300 hover:text-white transition-colors">Ubicación</a>
                        <a href="{{ route('home') }}#contacto" class="text-zinc-300 hover:text-white transition-colors">Contacto</a>
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

                    <!-- Botón menú móvil -->
                    <button class="md:hidden mobile-menu-btn text-white p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                <!-- Menú móvil -->
                <div class="md:hidden mobile-menu hidden bg-zinc-900 border-t border-zinc-800 py-4">
                    <div class="flex flex-col space-y-3">
                        <a href="{{ route('home') }}" class="text-zinc-300 hover:text-white px-4 py-2 transition-colors">Inicio</a>
                        <a href="#categorias" class="text-zinc-300 hover:text-white px-4 py-2 transition-colors">Categorías</a>
                        <a href="{{ route('home') }}#ubicacion" class="text-zinc-300 hover:text-white px-4 py-2 transition-colors">Ubicación</a>
                        <a href="{{ route('home') }}#contacto" class="text-zinc-300 hover:text-white px-4 py-2 transition-colors">Contacto</a>
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

        <!-- Hero Section para el Menú -->
        <section class="welcome-hero pt-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center py-16">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-6">
                        <span class="w-2 h-2 bg-amber-400 rounded-full mr-2"></span>
                        Menú Completo
                    </div>
                    
                    <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight text-white">
                        Nuestro <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">Menú</span> Completo
                    </h1>
                    
                    <p class="text-xl text-zinc-300 mb-8 leading-relaxed max-w-3xl mx-auto">
                        Descubre todos nuestros productos artesanales preparados con los mejores ingredientes y años de experiencia
                    </p>

                    <!-- Contador de productos -->
                    <div class="flex justify-center items-center gap-8 mb-8">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-400">{{ $categorias->count() }}</div>
                            <div class="text-sm text-zinc-400">Categorías</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-400">{{ $categorias->sum(function($cat) { return $cat->productos->count(); }) }}</div>
                            <div class="text-sm text-zinc-400">Productos</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Categorías y Productos -->
        <section id="categorias" class="py-16 lg:py-24 bg-zinc-900/30">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                @forelse($categorias as $categoria)
                    <div class="mb-16 last:mb-0">
                        <!-- Header de Categoría -->
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl lg:text-3xl font-bold text-amber-400">{{ $categoria->nombre }}</h2>
                                <div class="w-full h-px bg-gradient-to-r from-amber-400/60 to-transparent mt-2"></div>
                            </div>
                            <div class="text-sm text-zinc-400 bg-zinc-800/50 px-3 py-1 rounded-full">
                                {{ $categoria->productos->count() }} {{ $categoria->productos->count() == 1 ? 'producto' : 'productos' }}
                            </div>
                        </div>

                        <!-- Grid de Productos -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @forelse($categoria->productos as $producto)
                                <div class="group product-card hover:scale-105 transition-all duration-300">
                                    <div class="bg-zinc-900/80 backdrop-blur border border-zinc-800 rounded-2xl p-5 h-full flex flex-col hover:border-amber-400/40 hover:shadow-lg hover:shadow-amber-400/10">
                                        <!-- Imagen del producto -->
                                        <div class="aspect-square bg-zinc-800 rounded-xl mb-4 overflow-hidden relative">
                                                <img
                                                    src="{{ $producto->imagen ? asset('storage/'.$producto->imagen) : asset('storage/img/none/none.png') }}"
                                                    alt="{{ $producto->nombre }}"
                                                    class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300"
                                                    onerror="this.onerror=null;this.src='{{ asset('storage/img/none/none.png') }}';"
                                                />
                                            <div class="absolute top-3 right-3 bg-black/50 backdrop-blur rounded-full px-2 py-1">
                                                <span class="text-amber-400 text-xs font-bold">${{ number_format($producto->precio, 0) }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Información del producto -->
                                        <div class="flex-1 flex flex-col">
                                            <h3 class="text-lg font-bold mb-2 text-white group-hover:text-amber-400 transition-colors">
                                                {{ $producto->nombre }}
                                            </h3>
                                            
                                            <p class="text-zinc-400 text-sm mb-4 flex-1 leading-relaxed">
                                                {{ $producto->descripcion ?? 'Delicioso producto preparado con ingredientes frescos y de la mejor calidad.' }}
                                            </p>
                                            
                                            <!-- Footer del producto -->
                                            <div class="flex items-center justify-between mt-auto pt-3 border-t border-zinc-800">
                                                <span class="text-amber-400 font-bold text-xl">
                                                    ${{ number_format($producto->precio, 2) }}
                                                </span>
                                                <span class="bg-amber-400/10 text-amber-400 text-xs px-3 py-1 rounded-full">
                                                    {{ $categoria->nombre }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <p class="text-zinc-400 text-lg">No hay productos en esta categoría.</p>
                                    <p class="text-zinc-500 text-sm mt-1">¡Pronto habrá nuevos productos!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="text-center py-24">
                        <div class="w-24 h-24 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold mb-4 text-white">Próximamente</h3>
                        <p class="text-zinc-400 text-xl mb-8">Estamos preparando nuestro delicioso menú</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            Volver al Inicio
                        </a>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 lg:py-24 bg-gradient-to-r from-amber-500 to-orange-500">
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-black mb-6">
                    ¿Te gustó nuestro menú?
                </h2>
                <p class="text-lg sm:text-xl text-black/80 mb-8">
                    Ven y disfruta de estos productos en nuestro acogedor ambiente
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('home') }}#ubicacion" 
                       class="bg-black text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:bg-zinc-800 transition-colors inline-flex items-center justify-center gap-2 text-sm sm:text-base">
                        Ver Ubicación
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
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
                        El mejor café artesanal desde 1995
                    </p>
                    <div class="flex justify-center space-x-6 text-sm text-zinc-400">
                        <span>&copy; 2025 Miss Sweet Candy. Todos los derechos reservados.</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
</x-layouts.guest>