{{-- resources/views/partials/sidebar.blade.php --}}

@php
    $unread = $unread ?? 0;
@endphp

{{-- 
    IMPORTANTE: 
    Quitamos <flux:sidebar>, 'fixed', 'w-XX', 'transform', 'x-bind'.
    Usamos un div simple con h-full flex-col para llenar el contenedor padre (#main-sidebar).
--}}
<div class="relative flex h-full flex-col justify-between bg-transparent">

    {{-- BOTÓN CERRAR (Solo móvil) --}}
    {{-- Mantenemos este botón para UX en móvil, oculto en desktop --}}
    <div class="absolute right-3 top-3 z-50 lg:hidden">
        <button type="button" @click="close()" class="p-2 rounded-lg text-zinc-400 hover:text-white hover:bg-white/10 transition-colors" aria-label="Cerrar menú">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- 1. ZONA SUPERIOR (Logo + Notificaciones) --}}
    <div class="shrink-0 border-b border-zinc-800 px-4 pt-6 pb-4">
        {{-- Logo --}}
        <div class="flex items-center justify-between gap-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group" wire:navigate>
                <div class="w-10 h-10 bg-amber-500 rounded-xl grid place-items-center shadow-lg shadow-amber-500/30 group-hover:shadow-amber-500/50 transition">
                    <svg class="w-6 h-6 text-black" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <span class="text-lg font-bold block text-white group-hover:text-amber-400 transition-colors">Miss Sweet Candy</span>
                    <span class="text-[11px] text-zinc-400 font-medium uppercase tracking-wider">Sistema de Gestión</span>
                </div>
            </a>
        </div>

        {{-- Botón Notificaciones --}}
        <div class="mt-5">
            <button type="button" @click="$dispatch('open-notifications')"  class="relative inline-flex items-center justify-center w-full py-2.5 rounded-lg bg-zinc-950/50 hover:bg-zinc-800 border border-zinc-800 hover:border-amber-500/30 transition-all group">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 {{ $unread > 0 ? 'text-amber-400' : 'text-zinc-400' }} group-hover:text-amber-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="text-xs text-zinc-300 font-medium">Notificaciones</span>
                </div>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 flex h-5 min-w-[1.25rem] px-1.5 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white shadow-sm animate-pulse"
                      x-show="unread > 0"
                      x-text="unread > 9 ? '9+' : unread"></span>
            </button>
        </div>
    </div>

    {{-- 2. MENÚ NAVEGACIÓN (Zona Central Scrollable) --}}
    {{-- pb-24 asegura que el último item no quede tapado por el footer --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar px-3 pt-2 pb-24 space-y-6">
        <flux:navlist variant="outline">
            
            {{-- Dashboard --}}
            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="nav-item-single font-semibold">
                Dashboard
            </flux:navlist.item>

            {{-- Botón Búsqueda --}}
            @can('consultar-perfil-cliente')
                <button type="button" @click="$dispatch('open-search-client')" class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-lg text-zinc-400 hover:text-white hover:bg-white/5 transition-colors group mt-1">
                    <div class="p-1.5 rounded-md bg-zinc-800 group-hover:bg-amber-500/20 text-zinc-500 group-hover:text-amber-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Buscar Cliente...</span>
                    <div class="ml-auto text-[10px] border border-zinc-700 rounded px-1.5 py-0.5 text-zinc-500 hidden lg:block">Ctrl K</div>
                </button>
            @endcan
        </flux:navlist>

        <flux:navlist variant="outline">
            {{-- 1. PAQUETE USUARIOS --}}
            @canany(['ver-usuarios', 'ver-roles', 'gestionar-permisos'])
                <flux:navlist.group expandable heading="Paquete Usuarios" icon="users" class="group-wrapper">
                    @can('ver-usuarios')
                        <flux:navlist.item :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate class="nav-item-child">Usuarios</flux:navlist.item>
                    @endcan
                    @can('ver-roles')
                        <flux:navlist.item :href="route('roles.index')" :current="request()->routeIs('roles.*')" wire:navigate class="nav-item-child">Roles</flux:navlist.item>
                    @endcan
                    @can('gestionar-permisos')
                        <flux:navlist.item :href="route('permissions.index')" :current="request()->routeIs('permissions.*')" wire:navigate class="nav-item-child">Permisos</flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            {{-- 2. PAQUETE INVENTARIO --}}
            @canany(['ver-productos', 'ver-categorias', 'ver-alergenos', 'ver-inventario'])
                <flux:navlist.group expandable heading="Paquete Inventario" icon="archive-box" class="group-wrapper">
                    @can('ver-productos')
                        <flux:navlist.item :href="route('productos.index')" :current="request()->routeIs('productos.*')" wire:navigate class="nav-item-child">Productos</flux:navlist.item>
                    @endcan
                    @can('ver-categorias')
                        <flux:navlist.item :href="route('categorias.index')" :current="request()->routeIs('categorias.*')" wire:navigate class="nav-item-child">Categorías</flux:navlist.item>
                    @endcan
                    @can('ver-inventario')
                        <flux:navlist.item :href="route('inventario.index')" :current="request()->routeIs('inventario.*')" wire:navigate class="nav-item-child">Stock & Kardex</flux:navlist.item>
                    @endcan
                    @can('ver-alergenos')
                        <flux:navlist.item :href="route('alergenos.index')" :current="request()->routeIs('alergenos.*')" wire:navigate class="nav-item-child">Alérgenos</flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            {{-- 3. PAQUETE PROMOCIONES --}}
            @canany(['ver-promociones', 'ver-especiales'])
                <flux:navlist.group expandable heading="Paquete Promociones" icon="sparkles" class="group-wrapper">
                    @can('ver-promociones')
                        <flux:navlist.item :href="route('promociones.index')" :current="request()->routeIs('promociones.*')" wire:navigate class="nav-item-child">Promociones</flux:navlist.item>
                    @endcan
                    @can('ver-especiales')
                        <flux:navlist.item :href="route('especial_dia.index')" :current="request()->routeIs('especial_dia.*')" wire:navigate class="nav-item-child">Especiales del Día</flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            {{-- 4. PAQUETE VENTAS --}}
            @canany(['ver-pedidos', 'ver-cobros', 'iniciar-turno', 'cerrar-caja', 'ver-reporte-caja'])
                <flux:navlist.group expandable heading="Paquete Ventas y Caja" icon="banknotes" class="group-wrapper">
                    @can('ver-pedidos')
                        <flux:navlist.item :href="route('pedidos.index')" :current="request()->routeIs('pedidos.*')" wire:navigate class="nav-item-child">Gestión Pedidos</flux:navlist.item>
                    @endcan
                    @can('ver-cobros')
                        <flux:navlist.item :href="route('cobro_caja.index')" :current="request()->routeIs('cobro_caja.index')" wire:navigate class="nav-item-child">Terminal de Cobro</flux:navlist.item>
                    @endcan
                    @can('iniciar-turno')
                        <flux:navlist.item :href="route('turnos_caja.index')" :current="request()->routeIs('turnos_caja.*')" wire:navigate class="nav-item-child">Turnos</flux:navlist.item>
                    @endcan
                    @can('cerrar-caja')
                        <flux:navlist.item :href="route('cierres_caja.index')" :current="request()->routeIs('cierres_caja.*')" wire:navigate class="nav-item-child">Cierres de Caja</flux:navlist.item>
                    @endcan
                    @can('ver-reporte-caja')
                        <flux:navlist.item :href="route('cobro_caja.reporte_diario')" :current="request()->routeIs('cobro_caja.reporte_diario')" wire:navigate class="nav-item-child">Reporte Diario</flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            {{-- 5. PAQUETE REPORTES --}}
            @canany(['ver-reportes', 'ver-bitacora'])
                <flux:navlist.group expandable heading="Paquete Reportes" icon="chart-pie" class="group-wrapper">
                    @can('ver-reportes')
                        <flux:navlist.item :href="route('reportes.ventas.index')" :current="request()->routeIs('reportes.ventas.*')" wire:navigate class="nav-item-child">Ventas Globales</flux:navlist.item>
                        <flux:navlist.item :href="route('reportes.exportar.index')" :current="request()->routeIs('reportes.exportar.*')" wire:navigate class="nav-item-child">Exportar Datos</flux:navlist.item>
                    @endcan
                    @can('ver-bitacora')
                        <flux:navlist.item :href="route('bitacora.index')" :current="request()->routeIs('bitacora.*')" wire:navigate class="nav-item-child">Bitácora Sistema</flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            {{-- 6. PAQUETE PERSONAL --}}
            @canany(['gestionar-pedidos-barista', 'ver-horarios'])
                <flux:navlist.group expandable heading="Paquete Personal" icon="briefcase" class="group-wrapper">
                    @can('gestionar-pedidos-barista')
                        <flux:navlist.item :href="route('barista.pedidos.index')" :current="request()->routeIs('barista.pedidos.*')" wire:navigate class="nav-item-child">Pantalla Barista</flux:navlist.item>
                    @endcan
                    @can('ver-horarios')
                        <flux:navlist.item :href="route('horarios.index')" :current="request()->routeIs('horarios.*')" wire:navigate class="nav-item-child">Horarios</flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            {{-- 7. PAQUETE CLIENTES --}}
            @canany(['consultar-perfil-cliente', 'ver-mesas', 'ver-mis-feedbacks', 'ver-cola-turnero'])
                <flux:navlist.group expandable heading="Paquete Clientes" icon="face-smile" class="group-wrapper">
                    @can('consultar-perfil-cliente')
                        <flux:navlist.item :href="route('perfil.consultar.todos')" :current="request()->routeIs('perfil.consultar.todos')" wire:navigate class="nav-item-child">Directorio Clientes</flux:navlist.item>
                    @endcan
                    @can('ver-mesas')
                        <flux:navlist.item :href="route('mesas.index')" :current="request()->routeIs('mesas.*')" wire:navigate class="nav-item-child">Gestión Mesas</flux:navlist.item>
                    @endcan
                    @can('ver-mis-feedbacks')
                        <flux:navlist.item :href="route('feedback.index')" :current="request()->routeIs('feedback.index')" wire:navigate class="nav-item-child">Feedback</flux:navlist.item>
                    @endcan
                    @can('ver-cola-turnero')
                        <flux:navlist.item :href="route('turnos.turnero.index')" :current="request()->routeIs('turnos.turnero.index')" wire:navigate class="nav-item-child">Cola de Turnos</flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            <div class="h-px bg-zinc-800 my-4"></div>

            {{-- CONFIGURACIÓN --}}
            <flux:navlist.group expandable heading="Configuración" icon="cog-6-tooth" class="group-wrapper">
                <flux:navlist.item :href="route('perfil.show')" wire:navigate class="nav-item-child">Mi Perfil</flux:navlist.item>
                <flux:navlist.item :href="route('settings.profile')" wire:navigate class="nav-item-child">Cuenta</flux:navlist.item>

                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </flux:navlist.group>
        </flux:navlist>
    </div>

    {{-- 3. FOOTER USUARIO (Zona Inferior Fija) --}}
    <div class="absolute bottom-0 left-0 w-full px-3 py-4 border-t border-zinc-800 bg-zinc-900/95 backdrop-blur-md z-20">
        <div class="bg-zinc-950 rounded-xl p-3 hover:bg-black border border-zinc-800 hover:border-amber-500/30 transition cursor-pointer group flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 bg-amber-500 rounded-lg grid place-items-center text-zinc-950 font-bold text-sm shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform">
                {{ auth()->user()->initials() }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold truncate text-zinc-200 group-hover:text-white">{{ auth()->user()->name }}</div>
                <div class="text-[10px] text-zinc-500 truncate group-hover:text-zinc-400 uppercase font-medium tracking-wide">{{ auth()->user()->roles->pluck('name')->first() ?? 'Usuario' }}</div>
            </div>
            <svg class="w-4 h-4 text-zinc-600 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

</div>