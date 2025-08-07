<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-700 bg-zinc-900/95 backdrop-blur">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <!-- Logo personalizado -->
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4" wire:navigate>
                    <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-semibold text-white">Miss Sweet Candy</span>
                </a>
            </div>

            <!-- Dashboard -->
            <flux:navlist variant="outline" class="mb-4">
                <flux:navlist.item 
                    icon="home" 
                    :href="route('dashboard')" 
                    :current="request()->routeIs('dashboard')" 
                    wire:navigate
                    class="text-zinc-300 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors"
                >
                    {{ __('Dashboard') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Sección Administración -->
            <flux:navlist variant="outline" class="mb-4">
                <flux:navlist.group 
                    :heading="__('Administración')" 
                    class="text-amber-500 font-semibold text-xs uppercase tracking-wider"
                >
                    <flux:navlist.item 
                        icon="users" 
                        :href="route('users.index')" 
                        :current="request()->routeIs('users.*')" 
                        wire:navigate
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Usuarios
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="shield-check" 
                        :href="route('roles.index')" 
                        :current="request()->routeIs('roles.*')" 
                        wire:navigate
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Roles
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="key" 
                        :href="route('permissions.index')" 
                        :current="request()->routeIs('permissions.*')" 
                        wire:navigate
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Permisos
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <!-- Sección Cafetería -->
            <flux:navlist variant="outline" class="mb-4">
                <flux:navlist.group 
                    :heading="__('Cafetería')" 
                    class="text-amber-500 font-semibold text-xs uppercase tracking-wider"
                >
                    <flux:navlist.item 
                        icon="cube" 
                        href="#" 
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Productos
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="tag" 
                        href="#" 
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Categorías
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="clipboard-document-list" 
                        href="#" 
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Inventario
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <!-- Sección Ventas -->
            <flux:navlist variant="outline" class="mb-4">
                <flux:navlist.group 
                    :heading="__('Ventas')" 
                    class="text-amber-500 font-semibold text-xs uppercase tracking-wider"
                >
                    <flux:navlist.item 
                        icon="shopping-cart" 
                        href="#" 
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Nueva Venta
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="document-text" 
                        href="#" 
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Historial
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="chart-bar" 
                        href="#" 
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Reportes
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <!-- Enlaces externos -->
            <flux:navlist variant="outline" class="mb-4">
                <flux:navlist.item 
                    icon="arrow-top-right-on-square" 
                    :href="route('home')" 
                    target="_blank"
                    class="text-zinc-300 hover:text-amber-400 hover:bg-zinc-800 rounded-lg transition-colors"
                >
                    Ver Sitio Web
                </flux:navlist.item>
            </flux:navlist>

            <!-- Sección Usuario/Cuenta -->
            <flux:navlist variant="outline" class="mb-4">
                <flux:navlist.group 
                    :heading="__('Mi Cuenta')" 
                    class="text-amber-500 font-semibold text-xs uppercase tracking-wider"
                >
                    <flux:navlist.item 
                        icon="user" 
                        :href="route('settings.profile')" 
                        :current="request()->routeIs('settings.profile')" 
                        wire:navigate
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Mi Perfil
                    </flux:navlist.item>
                    
                    <flux:navlist.item 
                        icon="cog" 
                        :href="route('settings.profile')" 
                        :current="request()->routeIs('settings.*')" 
                        wire:navigate
                        class="text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors text-sm"
                    >
                        Configuración
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <!-- Cerrar Sesión -->
            <flux:navlist variant="outline" class="mb-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:navlist.item 
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle" 
                        class="w-full text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors text-sm"
                    >
                        Cerrar Sesión
                    </flux:navlist.item>
                </form>
            </flux:navlist>

            <!-- Información del usuario (solo mostrar) -->
            <div class="px-4 py-3 bg-zinc-800/30 rounded-lg mx-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-black font-semibold text-sm">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-zinc-400 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
            </div>
        </flux:sidebar>

        <!-- Mobile Header simplificado -->
        <flux:header class="lg:hidden bg-zinc-900/95 backdrop-blur border-b border-zinc-800">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <!-- Logo móvil -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
                <span class="text-lg font-semibold text-white">Café Aroma</span>
            </div>

            <flux:spacer />

            <!-- Solo avatar en móvil -->
            <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-black font-semibold text-sm">
                {{ auth()->user()->initials() }}
            </div>
        </flux:header>

        {{ $slot }}
        
        <!-- CSS personalizado para el tema de cafetería -->
        <style>
        /* Personalización del sidebar */
        [data-flux-sidebar] {
            background: rgba(24, 24, 27, 0.95) !important;
            backdrop-filter: blur(12px);
            border-color: rgb(63, 63, 70) !important;
        }

        /* Personalización de navlist items */
        [data-flux-navlist-item] {
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        [data-flux-navlist-item]:hover {
            background-color: rgb(39, 39, 42) !important;
            color: white !important;
        }

        [data-flux-navlist-item][data-current="true"] {
            background-color: rgb(39, 39, 42) !important;
            color: white !important;
            border-left: 3px solid rgb(245, 158, 11);
        }

        /* Estilo para grupos de navegación */
        [data-flux-navlist-group] h3 {
            color: rgb(245, 158, 11) !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            margin-bottom: 0.5rem !important;
        }

        /* Botón de cerrar sesión personalizado */
        [data-flux-navlist-item][type="submit"] {
            border: none;
            background: none;
            text-align: left;
            justify-content: flex-start;
        }

        [data-flux-navlist-item][type="submit"]:hover {
            background-color: rgba(239, 68, 68, 0.1) !important;
            color: rgb(252, 165, 165) !important;
        }
        </style>

        @vite(['resources/js/app.js'])
        @fluxScripts
    </body>
</html>