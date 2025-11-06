{{-- filepath: resources/views/components/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
  @include('partials.head')
  {{-- Importante: evita <link rel="preload"> manuales de app-*.css aquí. Deja que @vite inyecte CSS/JS --}}
  @livewireStyles
</head>
<body class="min-h-screen bg-zinc-950 text-white">

  {{-- ===== Overlay móvil para cerrar al tocar fuera (sidebar) ===== --}}
  <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] hidden lg:hidden"></div>

  @php
    $unread = \App\Models\Notificacion::where('usuario_destino_id', auth()->id())
      ->where('leido', false)->count();
    $lastNotifs = \App\Models\Notificacion::where('usuario_destino_id', auth()->id())
      ->where('leido', false)->latest('id')->limit(10)->get();
  @endphp

  {{-- ===== Sidebar (drawer en móvil, rail en desktop) ===== --}}
  <flux:sidebar sticky stashable
    class="relative z-[70] border-e border-zinc-800 bg-zinc-900/95 backdrop-blur shadow-[0_0_40px_-12px_rgba(245,158,11,.15)]"
    style="width: 280px"
    data-flux-sidebar
  >
    {{-- Toggle close (solo móvil) --}}
    <flux:sidebar.toggle class="lg:hidden absolute right-2 top-2" icon="x-mark" />

    {{-- Marca + Campana (SOLO trigger) --}}
    <div class="mb-4 border-b border-zinc-800 pb-4 px-4">
      <div class="flex items-center justify-between gap-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group" wire:navigate>
          <div class="w-9 h-9 bg-amber-500 rounded-lg grid place-items-center shadow-lg shadow-amber-500/30 group-hover:shadow-amber-500/50 transition">
            <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
            </svg>
          </div>
          <div class="min-w-0">
            <span class="text-lg font-semibold block group-hover:text-amber-400 transition-colors">Miss Sweet Candy</span>
            <span class="text-[11px] text-zinc-400">Sistema de Gestión</span>
          </div>
        </a>

        {{-- Campana (abre sheet en móvil / panel derecho en desktop) --}}
        <button type="button"
                class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-zinc-800 transition"
                id="btn-open-notifs"
                aria-haspopup="dialog"
                aria-label="Notificaciones">
          <svg class="w-5 h-5 {{ $unread ? 'text-amber-400' : 'text-zinc-300' }}"
               data-notif-icon fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold {{ $unread ? '' : 'hidden' }}"
                data-notif-badge>
            {{ $unread > 9 ? '9+' : $unread }}
          </span>
        </button>
      </div>
    </div>

    {{-- ===== Navegación con scroll ===== --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar px-3 space-y-2" id="sidebar-scroll">
      <flux:navlist variant="outline">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="nav-item-single">
          Dashboard
        </flux:navlist.item>
      </flux:navlist>

      @canany(['ver-usuarios','ver-roles','gestionar-permisos'])
      <x-sidebar.group id="admin" icon="users" text="Administración">
        @can('ver-usuarios')
        <flux:navlist.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate class="nav-item-child">
          Usuarios
        </flux:navlist.item>
        @endcan
        @can('ver-roles')
        <flux:navlist.item icon="shield-check" :href="route('roles.index')" :current="request()->routeIs('roles.*')" wire:navigate class="nav-item-child">
          Roles
        </flux:navlist.item>
        @endcan
        @can('gestionar-permisos')
        <flux:navlist.item icon="key" :href="route('permissions.index')" :current="request()->routeIs('permissions.*')" wire:navigate class="nav-item-child">
          Permisos
        </flux:navlist.item>
        @endcan
      </x-sidebar.group>
      @endcanany

      @canany(['ver-productos','ver-categorias','ver-horarios','ver-alergenos'])
      <x:sidebar.group id="cafeteria" icon="building-storefront" text="Cafetería">
        @can('ver-productos')
        <flux:navlist.item icon="cube" :href="route('productos.index')" :current="request()->routeIs('productos.*')" wire:navigate class="nav-item-child">
          Productos
        </flux:navlist.item>
        @endcan
        @can('ver-categorias')
        <flux:navlist.item icon="tag" :href="route('categorias.index')" :current="request()->routeIs('categorias.*')" wire:navigate class="nav-item-child">
          Categorías
        </flux:navlist.item>
        @endcan
        @can('ver-horarios')
        <flux:navlist.item icon="clock" :href="route('horarios.index')" :current="request()->routeIs('horarios.*')" wire:navigate class="nav-item-child">
          Horarios
        </flux:navlist.item>
        @endcan
        @can('ver-alergenos')
        <flux:navlist.item icon="shield-exclamation" :href="route('alergenos.index')" :current="request()->routeIs('alergenos.*')" wire:navigate class="nav-item-child">
          Alérgenos
        </flux:navlist.item>
        @endcan
      </x-sidebar.group>
      @endcanany

      @canany(['ver-mesas','ver-promociones','gestionar-pedidos-barista','consultar-perfil-cliente'])
      <x-sidebar.group id="operaciones" icon="operaciones" text="Operaciones">
        @can('ver-mesas')
        <flux:navlist.item icon="table-cells" :href="route('mesas.index')" :current="request()->routeIs('mesas.*')" wire:navigate class="nav-item-child">
          Mesas
        </flux:navlist.item>
        @endcan
        @can('ver-promociones')
        <flux:navlist.item icon="gift" :href="route('promociones.index')" :current="request()->routeIs('promociones.*')" wire:navigate class="nav-item-child">
          Promociones
        </flux:navlist.item>
        @endcan
        @can('gestionar-pedidos-barista')
        <flux:navlist.item icon="beaker" :href="route('barista.pedidos.index')" :current="request()->routeIs('barista.pedidos.*')" wire:navigate class="nav-item-child">
          Pedidos Barista
        </flux:navlist.item>
        @endcan
        @can('consultar-perfil-cliente')
        <flux:navlist.item icon="users" :href="route('perfil.consultar.todos')" :current="request()->routeIs('perfil.consultar.todos')" wire:navigate class="nav-item-child">
          Consultar Perfiles
        </flux:navlist.item>
        @endcan
      </x-sidebar.group>
      @endcanany

      @canany(['ver-cobros','ver-reporte-caja','ver-pedidos','iniciar-turno','cerrar-caja'])
      <x-sidebar.group id="ventas" icon="banknotes" text="Ventas & Caja">
        @can('ver-pedidos')
        <flux:navlist.item icon="plus-circle" :href="route('pedidos.index')" :current="request()->routeIs('pedidos.index')" wire:navigate class="nav-item-child">
          Pedidos
        </flux:navlist.item>
        @endcan
        @can('ver-cobros')
        <flux:navlist.item icon="document-text" :href="route('cobro_caja.index')" :current="request()->routeIs('cobro_caja.*')" wire:navigate class="nav-item-child">
          Cobrar
        </flux:navlist.item>
        @endcan
        @can('ver-reporte-caja')
        <flux:navlist.item icon="chart-bar" :href="route('cobro_caja.reporte_diario')" :current="request()->routeIs('cobro_caja.reporte_diario')" wire:navigate class="nav-item-child">
          Reporte Diario
        </flux:navlist.item>
        @endcan
        @can('iniciar-turno')
        <flux:navlist.item icon="clock" :href="route('turnos_caja.index')" :current="request()->routeIs('turnos_caja.*')" wire:navigate class="nav-item-child">
          Turnos de Caja
        </flux:navlist.item>
        @endcan
        @can('cerrar-caja')
        <flux:navlist.item icon="calculator" :href="route('cierres_caja.index')" :current="request()->routeIs('cierres_caja.*')" wire:navigate class="nav-item-child">
          Cierres de Caja
        </flux:navlist.item>
        @endcan
      </x-sidebar.group>
      @endcanany

      @canany(['ver-reportes'])
      <x-sidebar.group id="reportes" icon="chart-pie" text="Reportes">
        @can('ver-reportes')
        <flux:navlist.item icon="currency-dollar" :href="route('reportes.ventas.index')" :current="request()->routeIs('reportes.ventas.*')" wire:navigate class="nav-item-child">
          Reportes de Ventas
        </flux:navlist.item>
        @endcan
      </x-sidebar.group>
      @endcanany

      @canany(['ver-especiales'])
      <x-sidebar.group id="especiales" icon="star" text="Especiales del Día">
        @can('ver-especiales')
        <flux:navlist.item icon="sparkles" :href="route('especial_dia.index')" :current="request()->routeIs('especial_dia.*')" wire:navigate class="nav-item-child">
          Gestionar Especiales
        </flux:navlist.item>
        @endcan
      </x-sidebar.group>
      @endcanany

      @canany(['ver-inventario'])
      <x-sidebar.group id="inventario" icon="cube-transparent" text="Inventario">
        @can('ver-inventario')
        <flux:navlist.item icon="archive-box" :href="route('inventario.index')" :current="request()->routeIs('inventario.*')" wire:navigate class="nav-item-child">
          Inventario Productos
        </flux:navlist.item>
        @endcan
      </x-sidebar.group>
      @endcanany

      @canany(['ver-bitacora'])
      <x-sidebar.group id="bitacora" icon="clipboard-document-list" text="Bitácora">
        <flux:navlist.item icon="clipboard-document-list" :href="route('bitacora.index')" :current="request()->routeIs('bitacora.*')" wire:navigate class="nav-item-child">
          Bitácora
        </flux:navlist.item>
      </x-sidebar.group>
      @endcanany

      @canany(['crear-feedback','ver-mis-feedbacks','ver-estadisticas-feedback'])
      <x-sidebar.group id="feedback" icon="chat-bubble-left-right" text="Feedbacks">
        @can('ver-mis-feedbacks')
        <flux:navlist.item icon="rectangle-stack" :href="route('feedback.mis-feedbacks')" :current="request()->routeIs('feedback.mis-feedbacks')" wire:navigate class="nav-item-child">
          Mis Feedbacks
        </flux:navlist.item>
        @endcan
        @can('crear-feedback')
        <flux:navlist.item icon="pencil-square" :href="route('feedback.create')" :current="request()->routeIs('feedback.create')" wire:navigate class="nav-item-child">
          Crear Feedback
        </flux:navlist.item>
        @endcan
        @can('ver-estadisticas-feedback')
        <flux:navlist.item icon="chart-bar" :href="route('feedback.index')" :current="request()->routeIs('feedback.index')" wire:navigate class="nav-item-child">
          Ver Todos
        </flux:navlist.item>
        <flux:navlist.item icon="chart-pie" :href="route('feedback.estadisticas')" :current="request()->routeIs('feedback.estadisticas')" wire:navigate class="nav-item-child">
          Estadísticas
        </flux:navlist.item>
        @endcan
      </x-sidebar.group>
      @endcanany

      <div class="border-t border-zinc-800 my-3"></div>

      @can('ver-menu-publico')
      <flux:navlist variant="outline">
        <flux:navlist.item icon="book-open" :href="route('menu.publico')" target="_blank" class="nav-item-single">
          Ver Menú Público
        </flux:navlist.item>
      </flux:navlist>
      @endcan

      <flux:navlist variant="outline">
        <flux:navlist.item icon="arrow-top-right-on-square" :href="route('home')" target="_blank" class="nav-item-single">
          Ver Sitio Web
        </flux:navlist.item>
      </flux:navlist>

      <div class="border-t border-zinc-800 my-3"></div>

      {{-- Mi Cuenta --}}
      <x-sidebar.group id="cuenta" icon="user" text="Mi Cuenta">
        @can('ver-mi-perfil')
        <flux:navlist.item icon="identification" :href="route('perfil.show')" :current="request()->routeIs('perfil.*')" wire:navigate class="nav-item-child">
          Mi Perfil de Cliente
        </flux:navlist.item>
        @endcan
        <flux:navlist.item icon="user" :href="route('settings.profile')" :current="request()->routeIs('settings.profile')" wire:navigate class="nav-item-child">
          Configuración de Cuenta
        </flux:navlist.item>
        <flux:navlist.item icon="lock-closed" :href="route('settings.password')" :current="request()->routeIs('settings.password')" wire:navigate class="nav-item-child">
          Cambiar Contraseña
        </flux:navlist.item>
        <flux:navlist.item icon="paint-brush" :href="route('settings.appearance')" :current="request()->routeIs('settings.appearance')" wire:navigate class="nav-item-child">
          Apariencia
        </flux:navlist.item>
      </x-sidebar.group>

      {{-- Cerrar Sesión --}}
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <flux:navlist variant="outline">
          <flux:navlist.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="nav-item-logout">
            Cerrar Sesión
          </flux:navlist.item>
        </flux:navlist>
      </form>
    </div>

    {{-- Footer Usuario --}}
    <div class="px-3 py-4 border-t border-zinc-800">
      <div class="bg-zinc-800/50 rounded-xl p-3 hover:bg-zinc-800 transition cursor-pointer group">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-amber-500 rounded-xl grid place-items-center text-black font-bold text-sm shadow-lg shadow-amber-500/30">
            {{ auth()->user()->initials() }}
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold truncate">{{ auth()->user()->name }}</div>
            <div class="text-xs text-zinc-400 truncate">{{ auth()->user()->email }}</div>
          </div>
          <svg class="w-5 h-5 text-zinc-500 group-hover:text-amber-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
      </div>
    </div>

    {{-- Asa para resize (desktop) --}}
    <div id="sidebar-resizer" class="hidden lg:block absolute top-0 -right-1 h-full w-2 cursor-col-resize bg-transparent"></div>
  </flux:sidebar>

  {{-- ===== SIN HEADER en móvil ===== --}}
  <div class="fixed bottom-4 left-4 flex items-center gap-2 lg:hidden z-[65]">
    <flux:sidebar.toggle class="w-12 h-12 rounded-full bg-zinc-900/80 border border-zinc-800 grid place-items-center shadow-lg active:scale-95"
                         icon="bars-2" inset="left" aria-label="Abrir menú" />
    <button id="btn-open-notifs-floating"
            class="w-12 h-12 rounded-full bg-zinc-900/80 border border-zinc-800 grid place-items-center shadow-lg active:scale-95 relative"
            aria-label="Abrir notificaciones">
      <svg class="w-6 h-6 {{ $unread ? 'text-amber-400' : 'text-zinc-300' }}" data-notif-icon fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
      </svg>
      <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold {{ $unread ? '' : 'hidden' }}"
            data-notif-badge>
        {{ $unread > 9 ? '9+' : $unread }}
      </span>
    </button>
  </div>

  {{-- ===== Contenido ===== --}}
  {{ $slot }}

  {{-- ===== MODAL DE BÚSQUEDA DE CLIENTES (para cajeros) ===== --}}
  @can('consultar-perfil-cliente')
  <div id="modal-buscar-cliente" class="fixed inset-0 z-[90] hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="cerrarBuscarClienteModal()"></div>
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-800">
      <div class="p-6 border-b border-zinc-800">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-2xl font-bold text-white">Buscar Cliente</h2>
          <button onclick="cerrarBuscarClienteModal()" class="p-2 rounded-lg hover:bg-zinc-800 transition-colors">
            <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="relative">
          <input type="text" id="input-buscar-cliente" placeholder="Buscar por nombre o email..." 
                 class="w-full px-4 py-3 pl-12 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors"
                 oninput="buscarClientes(this.value)">
          <svg class="w-5 h-5 text-zinc-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
      </div>
      <div id="resultados-clientes" class="p-6 max-h-96 overflow-y-auto custom-scrollbar">
        <p class="text-center text-zinc-400 text-sm py-8">Escribe para buscar clientes...</p>
      </div>
    </div>
  </div>

  <script>
    function buscarClienteModal() {
      const modal = document.getElementById('modal-buscar-cliente');
      if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('input-buscar-cliente')?.focus(), 100);
      }
    }

    function cerrarBuscarClienteModal() {
      const modal = document.getElementById('modal-buscar-cliente');
      if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('input-buscar-cliente').value = '';
        document.getElementById('resultados-clientes').innerHTML = '<p class="text-center text-zinc-400 text-sm py-8">Escribe para buscar clientes...</p>';
      }
    }

    let timeoutBusqueda = null;
    async function buscarClientes(query) {
      clearTimeout(timeoutBusqueda);
      
      if (!query || query.length < 2) {
        document.getElementById('resultados-clientes').innerHTML = '<p class="text-center text-zinc-400 text-sm py-8">Escribe al menos 2 caracteres...</p>';
        return;
      }

      timeoutBusqueda = setTimeout(async () => {
        try {
          const meta = document.querySelector('meta[name="csrf-token"]');
          if (!meta) return;

          const response = await fetch(`/api/clientes/buscar?q=${encodeURIComponent(query)}`, {
            headers: {
              'X-CSRF-TOKEN': meta.content,
              'Accept': 'application/json'
            }
          });

          if (!response.ok) throw new Error('Error al buscar');

          const data = await response.json();
          mostrarResultados(data.data);
        } catch (error) {
          console.error('Error:', error);
          document.getElementById('resultados-clientes').innerHTML = '<p class="text-center text-red-400 text-sm py-8">Error al buscar clientes</p>';
        }
      }, 300);
    }

    function mostrarResultados(clientes) {
      const container = document.getElementById('resultados-clientes');
      
      if (!clientes || clientes.length === 0) {
        container.innerHTML = '<p class="text-center text-zinc-400 text-sm py-8">No se encontraron clientes</p>';
        return;
      }

      container.innerHTML = clientes.map(cliente => `
        <a href="/clientes/${cliente.id}/perfil" 
           class="block bg-zinc-800/30 hover:bg-zinc-800/50 border border-zinc-700 hover:border-amber-500/50 rounded-lg p-4 mb-3 transition-all duration-200 group">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-white font-bold text-lg">${cliente.name.substring(0, 2).toUpperCase()}</span>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <p class="text-white font-medium truncate">${cliente.name}</p>
                ${cliente.tiene_alergias_graves ? '<span class="bg-red-500/20 text-red-400 text-xs px-2 py-0.5 rounded-full font-bold animate-pulse">🚨 ALERGIAS GRAVES</span>' : ''}
                ${cliente.tiene_alergias && !cliente.tiene_alergias_graves ? '<span class="bg-orange-500/20 text-orange-400 text-xs px-2 py-0.5 rounded-full">⚠️ Alergias</span>' : ''}
              </div>
              <p class="text-zinc-400 text-sm truncate">${cliente.email}</p>
              ${cliente.telefono !== 'N/A' ? `<p class="text-zinc-500 text-xs mt-0.5">📞 ${cliente.telefono}</p>` : ''}
            </div>
            <svg class="w-5 h-5 text-zinc-500 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </a>
      `).join('');
    }

    // Cerrar con Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const modal = document.getElementById('modal-buscar-cliente');
        if (modal && !modal.classList.contains('hidden')) {
          cerrarBuscarClienteModal();
        }
      }
    });
  </script>
  @endcan

  {{-- ===== PANEL DERECHO (desktop) - Se cierra al hacer clic fuera ===== --}}
  <div id="notif-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[79] hidden"></div>
  <aside id="notif-drawer" role="dialog" aria-modal="true" aria-labelledby="notif-title-desktop"
         class="fixed inset-y-0 right-0 w-full max-w-md bg-zinc-900 border-l border-zinc-800 z-[80] translate-x-full transition-transform duration-300 ease-in-out shadow-2xl">
    <div class="h-full flex flex-col">
      <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="inline-flex w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 items-center justify-center">
            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          </span>
          <h2 id="notif-title-desktop" class="text-sm font-semibold">Notificaciones</h2>
        </div>
        <div class="flex items-center gap-2">
          <a href="{{ route('notificaciones.index') }}" wire:navigate class="text-xs text-amber-400 hover:text-amber-300 transition">Ver todas</a>
          <button class="p-2 rounded-lg hover:bg-zinc-800 transition-colors" id="btn-close-drawer" aria-label="Cerrar panel">
            <svg class="w-5 h-5 text-zinc-400 hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
      <div id="notif-list-desktop" class="flex-1 overflow-y-auto custom-scrollbar p-3">
        @forelse($lastNotifs as $n)
          <div class="notificacion-item mb-2" data-notif-id="{{ $n->id }}">
            <a href="#"
               onclick="event.preventDefault(); marcarYEliminar({{ $n->id }})"
               class="block rounded-xl border border-zinc-800/70 bg-zinc-900/50 hover:bg-zinc-900 transition">
              <div class="px-3 py-3 flex gap-3">
                <div class="pt-1">
                  <span class="inline-block w-2 h-2 rounded-full bg-amber-500"></span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-[13px] text-zinc-200 leading-snug line-clamp-3">{{ $n->mensaje }}</p>
                  @if(isset($n->created_at))
                    <span class="text-[11px] text-zinc-400">{{ $n->created_at?->diffForHumans() }}</span>
                  @endif
                </div>
                <button class="p-1.5 self-start rounded-md hover:bg-zinc-800" aria-label="Marcar como leída y ocultar"
                        onclick="event.preventDefault(); marcarYEliminar({{ $n->id }})">
                  <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </a>
          </div>
        @empty
          <p class="text-center text-zinc-400 text-sm py-6">Sin notificaciones nuevas.</p>
        @endforelse
      </div>
    </div>
  </aside>

  {{-- ===== SHEET (móvil) ===== --}}
  <div id="notif-sheet" role="dialog" aria-modal="true" aria-labelledby="notif-title"
       class="fixed inset-0 z-[80] hidden lg:hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-close-notif></div>
    <div class="absolute left-1/2 -translate-x-1/2 bottom-0 w-full max-w-md bg-zinc-900 border-t border-zinc-800 rounded-t-2xl shadow-2xl">
      <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
        <h2 id="notif-title" class="text-sm font-semibold">Notificaciones</h2>
        <button class="p-2 rounded-lg hover:bg-zinc-800" data-close-notif aria-label="Cerrar notificaciones">
          <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <div id="notif-list" class="max-h-[60vh] overflow-y-auto custom-scrollbar p-2">
        @forelse($lastNotifs as $n)
          <div class="notificacion-item" data-notif-id="{{ $n->id }}">
            <a href="#"
               onclick="event.preventDefault(); marcarYEliminar({{ $n->id }})"
               class="block bg-zinc-800/50 rounded-lg px-3 py-3 hover:bg-zinc-800 transition mb-2">
              <div class="flex items-start gap-2">
                <div class="w-2 h-2 rounded-full bg-amber-500 mt-1.5"></div>
                <p class="text-[13px] text-zinc-200 leading-snug flex-1">{{ $n->mensaje }}</p>
              </div>
            </a>
          </div>
        @empty
          <p class="text-center text-zinc-400 text-sm py-6">Sin notificaciones nuevas.</p>
        @endforelse
        <div class="text-right px-2 pb-3">
          <a href="{{ route('notificaciones.index') }}" wire:navigate class="text-amber-400 text-xs hover:text-amber-300">Ver todas →</a>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Estilos ===== --}}
  <style>
    .custom-scrollbar{scrollbar-width:thin;scrollbar-color:rgb(82,82,91) transparent}
    .custom-scrollbar::-webkit-scrollbar{width:7px}
    .custom-scrollbar::-webkit-scrollbar-track{background:transparent;border-radius:10px;margin:4px 0}
    .custom-scrollbar::-webkit-scrollbar-thumb{background:linear-gradient(180deg,rgb(82,82,91),rgb(63,63,70));border-radius:10px;border:2px solid transparent;background-clip:content-box;transition:all .3s}
    .custom-scrollbar::-webkit-scrollbar-thumb:hover{background:linear-gradient(180deg,rgb(113,113,122),rgb(82,82,91));background-clip:content-box}

    .nav-item-single{padding:.625rem 1rem;border-radius:.75rem;color:#d4d4d8}
    .nav-item-single:hover{color:white;background:rgba(39,39,42,.8);box-shadow:0 0 15px rgba(245,158,11,.08);transform:translateX(3px);transition:.2s}
    .nav-item-child{padding:.5rem 1rem;margin-left:1.5rem;border-radius:.5rem;color:#a1a1aa;font-size:.925rem}
    .nav-item-child:hover{color:white;background:rgba(39,39,42,.6);box-shadow:0 0 12px rgba(245,158,11,.08);transform:translateX(3px);transition:.2s}
    .nav-item-logout{padding:.625rem 1rem;border-radius:.75rem;color:rgb(248,113,113)}
    .nav-item-logout:hover{color:rgb(252,165,165);background:rgba(248,113,113,.08);transform:translateX(3px)}

    [data-flux-navlist-item][data-current="true"]{
      background:linear-gradient(to right,rgba(245,158,11,.18),rgba(245,158,11,.06))!important;
      color:rgb(251,191,36)!important;border-left:3px solid rgb(245,158,11);
      box-shadow:0 0 20px rgba(245,158,11,.15)
    }

    .nav-group{border-radius:.75rem}
    .nav-group-header{width:100%;display:flex;align-items:center;justify-content:space-between;padding:.6rem 1rem;border-radius:.75rem;color:rgb(251,191,36);font-weight:600;font-size:.9rem;background:transparent;transition:.2s;user-select:none}
    .nav-group-header:hover{color:#fde68a;background:rgba(39,39,42,.5);transform:translateX(2px)}
    .nav-group-icon{color:rgb(161,161,170);transition:transform .35s,color .2s}
    .nav-group-header:hover .nav-group-icon{color:rgb(245,158,11)}
    .nav-group-icon.rotate{transform:rotate(180deg);color:rgb(245,158,11)}
    .nav-group-content{max-height:0;overflow:hidden;opacity:0;margin-top:0;transition:max-height .35s,opacity .25s,margin-top .25s}
    .nav-group-content.open{max-height:700px;opacity:1;margin-top:.25rem}
    .nav-group-content.open>*{animation:fadeIn .25s ease both}
    @keyframes fadeIn{from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:translateY(0)}}

    /* Drawer (desktop) */
    #notif-drawer{transition:transform .3s cubic-bezier(0.4, 0, 0.2, 1);}
    #notif-drawer.open{transform:translateX(0)}
    #notif-overlay{transition:opacity .3s ease;}
    #notif-overlay.show{opacity:1;}

    @media (prefers-reduced-motion: reduce){
      *{animation:none !important;transition:none !important}
    }
  </style>

  {{-- Hook opcional para componentes --}}
  <template id="sidebar-group-template"></template>
  @once
    @push('components')
      @php /* Blade inline component for group */ @endphp
    @endpush
  @endonce

  {{-- Carga de assets --}}
  @vite(['resources/js/app.js'])
  @fluxScripts
  @livewireScripts

  {{-- ===== Scripts propios (seguros con wire:navigate) ===== --}}
  <script data-navigate-once>
  (() => {
    // Evitar doble init si Livewire reevalúa este <script>
    if (window.__ms_init) return;
    window.__ms_init = true;

    const $  = (s, c = document) => c.querySelector(s);
    const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

    // --- Notifs: marcar como leída y quitar (expuesto global porque se usa desde onclick="...")
    window.marcarYEliminar = async function(notifId) {
      try {
        const meta = $('meta[name="csrf-token"]');
        if (!meta) return;
        const resp = await fetch(`/notificaciones/${notifId}/marcar-leida`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': meta.content,
            'Accept': 'application/json'
          }
        });
        if (!resp.ok) return;

        // Quitar en ambas vistas si existen
        [
          `[data-notif-id="${notifId}"]`,
          `#notif-sheet [data-notif-id="${notifId}"]`,
          `#notif-drawer [data-notif-id="${notifId}"]`
        ].forEach(sel => {
          $$(sel).forEach(item => {
            item.style.transition = 'all .25s ease';
            item.style.opacity    = '0';
            item.style.transform  = 'translateX(-6px)';
            setTimeout(() => item.remove(), 250);
          });
        });

        // Mensaje vacío si corresponde
        ['#notif-list', '#notif-list-desktop'].forEach(id => {
          const el = $(id);
          if (el && !el.querySelector('.notificacion-item')) {
            el.insertAdjacentHTML('afterbegin', `<p class="text-center text-zinc-400 text-sm py-6">Sin notificaciones nuevas.</p>`);
          }
        });

        actualizarContadorNotificaciones();
      } catch (e) { console.error(e); }
    };

    // --- Contador notifs (poll ligero)
    async function actualizarContadorNotificaciones() {
      try {
        const meta = $('meta[name="csrf-token"]');
        if (!meta) return;
        const resp = await fetch('/api/notificaciones/count', {
          headers: { 'X-CSRF-TOKEN': meta.content, 'Accept': 'application/json' }
        });
        if (!resp.ok) return;
        const data = await resp.json();

        $$('[data-notif-badge]').forEach(b => {
          if (data.count > 0){ b.textContent = data.count > 9 ? '9+' : data.count; b.classList.remove('hidden'); }
          else { b.classList.add('hidden'); }
        });
        $$('[data-notif-icon]').forEach(i => {
          i.classList.toggle('text-amber-400', data.count > 0);
          i.classList.toggle('text-zinc-300', !(data.count > 0));
        });
      } catch (e) { console.error(e); }
    }

    // --- Acordeones con persistencia (por si tu componente los usa)
    window.toggleSection = function(id){
      const sec = document.getElementById(`${id}-section`);
      const icn = document.getElementById(`${id}-icon`);
      if(!sec) return;
      const isOpen = sec.classList.toggle('open');
      sec.hidden = !isOpen; icn?.classList.toggle('rotate', isOpen);
      document.querySelector(`[data-group="${id}"]`)?.setAttribute('aria-expanded', String(isOpen));
      localStorage.setItem(`sidebar-group-${id}`, isOpen ? '1' : '0');
    };

    function initAccordions(){
      const groups = ['admin','cafeteria','operaciones','ventas','reportes','especiales','inventario','bitacora','feedback','cuenta'];
      groups.forEach(id => {
        const sec = document.getElementById(`${id}-section`);
        const icn = document.getElementById(`${id}-icon`);
        if (!sec) return;
        const saved  = localStorage.getItem(`sidebar-group-${id}`) === '1';
        const active = !!sec.querySelector('[data-current="true"]');
        if (saved || active) { sec.classList.add('open'); sec.hidden = false; icn?.classList.add('rotate'); }
      });
      const sc = $('#sidebar-scroll'); if (sc) sc.style.scrollBehavior = 'smooth';
    }

    function initSidebar(){
      const sidebarEl = $('[data-flux-sidebar]');
      const overlayEl = $('#sidebar-overlay');

      // Toggle overlay al abrir/cerrar el sidebar
      document.addEventListener('click', (e) => {
        const t = e.target.closest('[data-flux-sidebar-toggle]');
        if (!t) return;
        setTimeout(() => {
          const openNow = sidebarEl?.getAttribute('data-open') === 'true';
          if (overlayEl) overlayEl.classList.toggle('hidden', !openNow);
        }, 80);
      }, { passive: true });

      // Cerrar tocando el overlay (móvil)
      if (overlayEl && !overlayEl.dataset.bound) {
        overlayEl.addEventListener('click', () => {
          $('[data-flux-sidebar] [data-flux-sidebar-toggle]')?.click();
          overlayEl.classList.add('hidden');
        }, { passive: true });
        overlayEl.dataset.bound = '1';
      }

      // Tecla B abre/cierra
      if (!window.__ms_keybind_b) {
        document.addEventListener('keydown', (e) => {
          if(e.key?.toLowerCase() === 'b' && !e.metaKey && !e.ctrlKey && !e.altKey){
            document.querySelector('[data-flux-sidebar-toggle]')?.click(); e.preventDefault();
          }
        });
        window.__ms_keybind_b = 1;
      }

      // Resizer desktop
      const handle  = $('#sidebar-resizer');
      if (handle && sidebarEl && !handle.dataset.bound) {
        const savedW = localStorage.getItem('sidebar-width');
        if (savedW) {
          const w = Math.min(420, Math.max(220, parseInt(savedW) || 280));
          sidebarEl.style.width = `${w}px`;
        }
        let dragging = false, startX = 0, startW = 0;
        handle.addEventListener('mousedown', (e) => {
          dragging = true; startX = e.clientX;
          startW = sidebarEl.getBoundingClientRect().width;
          document.body.style.userSelect = 'none';
        });
        window.addEventListener('mousemove', (e) => {
          if (!dragging) return;
          const w = Math.min(420, Math.max(220, Math.round(startW + (e.clientX - startX))));
          sidebarEl.style.width = w + 'px';
        });
        window.addEventListener('mouseup', () => {
          if (!dragging) return;
          dragging = false; document.body.style.userSelect = '';
          const w = Math.round(sidebarEl.getBoundingClientRect().width);
          localStorage.setItem('sidebar-width', w);
        });
        handle.dataset.bound = '1';
      }
    }

    function initNotifications(){
      const sheet         = $('#notif-sheet');     // móvil
      const drawer        = $('#notif-drawer');    // desktop
      const drawerOverlay = $('#notif-overlay');   // overlay desktop
      const btnOpenA      = $('#btn-open-notifs');
      const btnOpenB      = $('#btn-open-notifs-floating');
      const btnClose      = $('#btn-close-drawer');

      const openSheet  = () => { if (!sheet) return; sheet.classList.remove('hidden'); document.body.style.overflow = 'hidden'; };
      const closeSheet = () => { if (!sheet) return; sheet.classList.add('hidden');  document.body.style.overflow = ''; };

      const openDrawer = () => {
        if (!drawer || !drawerOverlay) return;
        drawerOverlay.classList.remove('hidden');
        drawer.classList.remove('translate-x-full');
        requestAnimationFrame(() => { drawerOverlay.classList.add('show'); drawer.classList.add('open'); });
        document.body.style.overflow = 'hidden';
      };
      const closeDrawer = () => {
        if (!drawer || !drawerOverlay) return;
        drawer.classList.remove('open');
        drawerOverlay.classList.remove('show');
        setTimeout(() => {
          drawer.classList.add('translate-x-full');
          drawerOverlay.classList.add('hidden');
          document.body.style.overflow = '';
        }, 300);
      };

      const openNotifs = () => {
        if (window.matchMedia('(min-width:1024px)').matches) openDrawer();
        else openSheet();
      };

      // Abrir
      [btnOpenA, btnOpenB].forEach(btn => {
        if (btn && !btn.dataset.bound) {
          btn.addEventListener('click', openNotifs, { passive: true });
          btn.dataset.bound = '1';
        }
      });
      // Cerrar (desktop)
      if (drawerOverlay && !drawerOverlay.dataset.bound) {
        drawerOverlay.addEventListener('click', closeDrawer, { passive: true });
        drawerOverlay.dataset.bound = '1';
      }
      if (btnClose && !btnClose.dataset.bound) {
        btnClose.addEventListener('click', closeDrawer);
        btnClose.dataset.bound = '1';
      }
      // Cerrar (móvil)
      if (sheet && !sheet.dataset.bound) {
        sheet.querySelectorAll('[data-close-notif]')?.forEach(el => {
          if (!el.dataset.bound) {
            el.addEventListener('click', closeSheet);
            el.dataset.bound = '1';
          }
        });
        sheet.dataset.bound = '1';
      }

      // Escape (una sola vez por navegación)
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          if (sheet && !sheet.classList.contains('hidden')) closeSheet();
          if (drawer && drawer.classList.contains('open')) closeDrawer();
        }
      }, { once: true });
    }

    function boot(){
      initSidebar();
      initAccordions();
      initNotifications();

      // Poll notifs una sola vez
      if (!window.__notif_interval) {
        window.__notif_interval = setInterval(actualizarContadorNotificaciones, 45000);
      }
      actualizarContadorNotificaciones();
    }

    document.addEventListener('DOMContentLoaded', boot, { once: true });
    document.addEventListener('livewire:navigated', boot);
  })();
  </script>
</body>
</html>
