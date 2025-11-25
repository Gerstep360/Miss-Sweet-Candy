{{-- resources/views/components/layouts/app/sidebar.blade.php --}}
@props([
    'title' => null,
    'unread' => 0,
])

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark h-full">
<head>
    @include('partials.head')
    <title>{{ $title ? $title . ' - Miss Sweet Candy' : 'Miss Sweet Candy' }}</title>

    <style>[x-cloak] { display: none !important; }</style>
    @livewireStyles

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/layouts/app/sidebar.css'
    ])

    <script>
        document.addEventListener('alpine:init', () => {
            // Lógica del Layout Principal (Sidebar y demás)
            Alpine.data('mainLayout', (initialUnread) => ({
                mobileOpen: false,
                unread: parseInt(initialUnread) || 0,
                toggle() { this.mobileOpen = !this.mobileOpen; },
                close() { this.mobileOpen = false; }
            }));

            // Lógica del Buscador Global
            Alpine.data('searchClient', (endpointUrl) => ({
                isOpen: false,
                query: '',
                results: [],
                isLoading: false,
                searchUrl: endpointUrl,

                init() {
                    window.buscarClienteModal = () => this.open();
                    window.cerrarBuscarClienteModal = () => this.close();
                    window.addEventListener('open-search-client', () => this.open());
                },
                open() {
                    this.isOpen = true;
                    this.$nextTick(() => { if(this.$refs.searchInput) this.$refs.searchInput.focus(); });
                    document.body.style.overflow = 'hidden';
                },
                close() {
                    this.isOpen = false;
                    document.body.style.overflow = '';
                    setTimeout(() => { this.query = ''; this.results = []; }, 300);
                },
                async performSearch() {
                    if (this.query.length < 2) { this.results = []; return; }
                    this.isLoading = true;
                    try {
                        const url = new URL(this.searchUrl, window.location.origin);
                        url.searchParams.append('q', this.query);
                        const response = await fetch(url, {
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        const data = await response.json();
                        this.results = data.data; 
                    } catch (e) { console.error(e); this.results = []; } 
                    finally { this.isLoading = false; }
                },
                getInitials(name) { return name ? name.substring(0, 2).toUpperCase() : '??'; },
                hasAllergy(c) { return c.tiene_alergias_graves || c.tiene_alergias; }
            }));
        });
    </script>
</head>

<body class="min-h-screen bg-zinc-950 text-white antialiased selection:bg-amber-500 selection:text-black relative overflow-x-hidden"
      x-data="mainLayout({{ $unread }})"
      @resize.window="if(window.innerWidth >= 1024) close()"
      @keydown.escape.window="close()"
      :class="{ 'body-sidebar-open': mobileOpen, 'overflow-hidden': mobileOpen }">

    {{-- Fondo --}}
    <div class="fixed inset-0 z-[-1] pointer-events-none">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>
        <div class="absolute left-0 right-0 top-0 -z-10 m-auto h-[310px] w-[310px] rounded-full bg-amber-500 opacity-[0.03] blur-[100px]"></div>
    </div>

    {{-- Overlay Móvil --}}
    <div id="sidebar-overlay" x-cloak
         class="fixed inset-0 z-[9990] lg:hidden bg-black/60 backdrop-blur-sm transition-opacity duration-300 ease-linear"
         :class="mobileOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
         @click="close()"></div>

    {{-- Sidebar (Partial Contenido) --}}
    <aside id="main-sidebar">
        @include('partials.sidebar', ['unread' => $unread])
    </aside>

    {{-- Contenido Principal --}}
    <main id="main-content" class="relative w-full min-h-screen transition-all duration-300 ease-in-out lg:pl-[280px]">
        <div class="w-full px-4 py-6 sm:px-6 lg:px-8 pb-24">
            {{ $slot }}
        </div>
    </main>

    {{-- Botones Móvil (Flotantes) --}}
    <div id="mobile-controls" class="fixed bottom-6 left-6 flex items-center gap-3 lg:hidden z-[2]" x-cloak>
        
        {{-- Toggle Sidebar --}}
        <button type="button" @click="toggle()"
                class="group flex h-14 w-14 items-center justify-center rounded-full bg-zinc-900/90 border border-zinc-700 shadow-md backdrop-blur-md active:scale-95 transition-all">
            <svg class="w-7 h-7 text-zinc-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>
        
        {{-- 🔴 CORRECCIÓN AQUÍ: Toggle Notificaciones --}}
        <button id="btn-open-notifs-floating" 
                type="button"
                @click="$dispatch('open-notifications')" 
                class="group relative flex h-14 w-14 items-center justify-center rounded-full bg-zinc-900/90 border border-zinc-700 shadow-md backdrop-blur-md active:scale-95 transition-all">
            
            <svg class="w-7 h-7" :class="unread > 0 ? 'text-amber-400' : 'text-zinc-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            
            {{-- Badge Contador --}}
            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 border-2 border-zinc-950 text-[10px] font-bold text-white shadow-sm"
                  x-show="unread > 0" 
                  :class="unread > 0 ? 'animate-pulse' : ''" 
                  x-text="unread > 9 ? '9+' : unread"></span>
        </button>
    </div>

    @include('partials.modal-search-client')
    
    {{-- Componente de Notificaciones (Escuchando el evento) --}}
    <x-notifications />
    
    {{ $modals ?? '' }}

    @fluxScripts
    @livewireScripts
    {{ $footerScripts ?? '' }}
</body>
</html>