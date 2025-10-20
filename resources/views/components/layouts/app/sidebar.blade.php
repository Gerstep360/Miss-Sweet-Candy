{{-- filepath: resources/views/components/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
  @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-950 text-white">
  {{-- Overlay móvil para cerrar al tocar fuera --}}
  <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] hidden lg:hidden"></div>

  @php
    $unread = \App\Models\Notificacion::where('usuario_destino_id', auth()->id())
      ->where('leido', false)->count();
    $lastNotifs = \App\Models\Notificacion::where('usuario_destino_id', auth()->id())
      ->latest('id')->limit(5)->get();
  @endphp

  {{-- ===== Sidebar ===== --}}
  <flux:sidebar sticky stashable
    class="relative z-[70] border-e border-zinc-800 bg-zinc-900/95 backdrop-blur shadow-[0_0_40px_-12px_rgba(245,158,11,.15)]"
    style="width: 280px"
    data-flux-sidebar
  >
    {{-- Toggle close (solo móvil) --}}
    <flux:sidebar.toggle class="lg:hidden absolute right-2 top-2" icon="x-mark" />

    {{-- Marca + Notificaciones (arriba) --}}
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

        {{-- Campana arriba (visible, UX) --}}
        <a href="{{ route('notificaciones.index') }}"
           class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-zinc-800 transition"
           wire:navigate aria-label="Notificaciones">
          <svg class="w-5 h-5 {{ $unread ? 'text-amber-400' : 'text-zinc-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          @if($unread)
            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold">
              {{ $unread > 9 ? '9+' : $unread }}
            </span>
          @endif
        </a>
      </div>

      {{-- Últimas 3 notifs (compacto) --}}
      @if($lastNotifs->count())
        <div class="mt-3 space-y-1.5">
          @foreach($lastNotifs->take(3) as $n)
            <a href="{{ route('notificaciones.show', $n->id) }}" wire:navigate
               class="block bg-zinc-900/60 rounded-lg px-3 py-2 hover:bg-zinc-900/90 transition">
              <p class="text-[12px] text-zinc-300 line-clamp-2 {{ !$n->leido ? 'font-semibold' : '' }}">
                {{ $n->mensaje }}
              </p>
            </a>
          @endforeach
          @if($lastNotifs->count() > 3)
            <a href="{{ route('notificaciones.index') }}" wire:navigate
               class="block text-[11px] text-amber-400 hover:text-amber-300 text-right">Ver todas →</a>
          @endif
        </div>
      @endif
    </div>

    {{-- Contenido con scroll --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar px-3 space-y-2" id="sidebar-scroll">
      {{-- Dashboard --}}
      <flux:navlist variant="outline">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="nav-item-single">
          Dashboard
        </flux:navlist.item>
      </flux:navlist>

      {{-- ===== Grupos ===== --}}
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

      @canany(['ver-productos','ver-categorias','ver-horarios'])
      <x-sidebar.group id="cafeteria" icon="building-storefront" text="Cafetería">
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
      </x-sidebar.group>
      @endcanany

      @canany(['ver-mesas'])
      <x-sidebar.group id="operaciones" icon="rectangle-stack" text="Operaciones">
        @can('ver-mesas')
        <flux:navlist.item icon="table-cells" :href="route('mesas.index')" :current="request()->routeIs('mesas.*')" wire:navigate class="nav-item-child">
          Mesas
        </flux:navlist.item>
        @endcan
      </x-sidebar.group>
      @endcanany

      @canany(['ver-cobros','ver-reporte-caja','ver-pedidos'])
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
      </x-sidebar.group>
      @endcanany

      @canany(['ver-bitacora'])
      <x-sidebar.group id="bitacora" icon="clipboard-document-list" text="Bitácora">
        <flux:navlist.item icon="clipboard-document-list" :href="route('bitacora.index')" :current="request()->routeIs('bitacora.*')" wire:navigate class="nav-item-child">
          Bitácora
        </flux:navlist.item>
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
        <flux:navlist.item icon="user" :href="route('settings.profile')" :current="request()->routeIs('settings.profile')" wire:navigate class="nav-item-child">
          Mi Perfil
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

  {{-- ===== Header SOLO MÓVIL (lg:hidden) ===== --}}
  <flux:header class="lg:hidden sticky top-0 z-[65] bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
    <div class="w-full flex items-center gap-3 py-2">
      {{-- Botón menú --}}
      <flux:sidebar.toggle class="lg:hidden flex-shrink-0" icon="bars-2" inset="left" />
      
      {{-- Logo y nombre --}}
      <div class="flex items-center gap-2 flex-1 min-w-0">
        <div class="w-9 h-9 bg-amber-500 rounded-lg grid place-items-center shadow-lg flex-shrink-0">
          <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
          </svg>
        </div>
        <span class="text-sm font-bold truncate">Miss Sweet Candy</span>
      </div>
      
      {{-- Notificaciones Móvil --}}
      <a href="{{ route('notificaciones.index') }}" class="relative flex items-center justify-center w-10 h-10 hover:bg-zinc-800 rounded-lg transition flex-shrink-0" wire:navigate>
        <svg class="w-5 h-5 {{ $unread > 0 ? 'text-amber-400' : 'text-zinc-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unread > 0)
          <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold text-white">
            {{ $unread > 9 ? '9+' : $unread }}
          </span>
        @endif
      </a>
      
      {{-- Avatar usuario --}}
      <div class="w-9 h-9 bg-amber-500 rounded-lg grid place-items-center text-black font-bold text-xs shadow-lg flex-shrink-0">
        {{ auth()->user()->initials() }}
      </div>
    </div>
  </flux:header>

  {{-- ===== Contenido (sin contenedor extra en desktop) ===== --}}
  {{ $slot }}

  {{-- ===== Estilos ===== --}}
  <style>
    .custom-scrollbar{scrollbar-width:thin;scrollbar-color:rgb(82,82,91) transparent}
    .custom-scrollbar::-webkit-scrollbar{width:7px}
    .custom-scrollbar::-webkit-scrollbar-track{background:transparent;border-radius:10px;margin:4px 0}
    .custom-scrollbar::-webkit-scrollbar-thumb{background:linear-gradient(180deg,rgb(82,82,91),rgb(63,63,70));border-radius:10px;border:2px solid transparent;background-clip:content-box;transition:all .3s}
    .custom-scrollbar::-webkit-scrollbar-thumb:hover{background:linear-gradient(180deg,rgb(113,113,122),rgb(82,82,91));background-clip:content-box}

    .nav-item-single{padding:.625rem 1rem; border-radius:.75rem; color:#d4d4d8}
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
    .nav-group-header{
      width:100%; display:flex; align-items:center; justify-content:space-between;
      padding:.6rem 1rem; border-radius:.75rem; color:rgb(251,191,36); font-weight:600; font-size:.9rem;
      background:transparent; transition:.2s; user-select:none
    }
    .nav-group-header:hover{color:#fde68a;background:rgba(39,39,42,.5); transform:translateX(2px)}
    .nav-group-icon{color:rgb(161,161,170); transition:transform .35s, color .2s}
    .nav-group-header:hover .nav-group-icon{color:rgb(245,158,11)}
    .nav-group-icon.rotate{transform:rotate(180deg); color:rgb(245,158,11)}

    .nav-group-content{max-height:0; overflow:hidden; opacity:0; margin-top:0; transition:max-height .35s, opacity .25s, margin-top .25s}
    .nav-group-content.open{max-height:700px; opacity:1; margin-top:.25rem}
    .nav-group-content.open>*{animation:fadeIn .25s ease both}
    @keyframes fadeIn{from{opacity:0; transform:translateY(-4px)} to{opacity:1; transform:translateY(0)}}

    /* Alturas/espacios responsive del header */
    @media (max-width: 640px){
      .h-14{height:3.25rem}
    }
  </style>

  {{-- ===== Scripts ===== --}}
  <script>
    // Acordeones con persistencia
    function toggleSection(id){
      const sec=document.getElementById(`${id}-section`);
      const icn=document.getElementById(`${id}-icon`);
      if(!sec||!icn) return;
      const isOpen=sec.classList.toggle('open');
      sec.hidden=!isOpen; icn.classList.toggle('rotate', isOpen);
      const header=document.querySelector(`[data-group="${id}"]`);
      header && header.setAttribute('aria-expanded', String(isOpen));
      localStorage.setItem(`sidebar-group-${id}`, isOpen?'1':'0');
    }
    document.addEventListener('DOMContentLoaded',()=>{
      const groups=['admin','cafeteria','operaciones','ventas','bitacora','cuenta'];
      groups.forEach(id=>{
        const sec=document.getElementById(`${id}-section`);
        const icn=document.getElementById(`${id}-icon`);
        if(!sec) return;
        const saved=localStorage.getItem(`sidebar-group-${id}`)==='1';
        const active=!!sec.querySelector('[data-current="true"]');
        if(saved||active){ sec.classList.add('open'); sec.hidden=false; icn?.classList.add('rotate'); }
      });
      // Scroll suave
      const sc=document.getElementById('sidebar-scroll'); if(sc) sc.style.scrollBehavior='smooth';
    });

    // Overlay móvil al abrir/cerrar
    const overlay=document.getElementById('sidebar-overlay');
    function showOverlay(){ overlay.classList.remove('hidden'); }
    function hideOverlay(){ overlay.classList.add('hidden'); }
    document.addEventListener('click',(e)=>{
      const t=e.target.closest('[data-flux-sidebar-toggle]');
      if(!t) return;
      setTimeout(()=>{ // esperar a que flux actualice estado
        const openNow=document.querySelector('[data-flux-sidebar]')?.getAttribute('data-open')==='true';
        openNow?showOverlay():hideOverlay();
      },80);
    });
    overlay.addEventListener('click',()=>{
      document.querySelector('[data-flux-sidebar] [data-flux-sidebar-toggle]')?.click();
      hideOverlay();
    });

    // Tecla B abre/cierra
    document.addEventListener('keydown',(e)=>{
      if(e.key?.toLowerCase()==='b' && !e.metaKey && !e.ctrlKey && !e.altKey){
        document.querySelector('[data-flux-sidebar-toggle]')?.click(); e.preventDefault();
      }
    });

    // Resizer desktop
    (function(){
      const handle=document.getElementById('sidebar-resizer');
      const sidebar=document.querySelector('[data-flux-sidebar]');
      if(!handle||!sidebar) return;
      const savedW=localStorage.getItem('sidebar-width');
      if(savedW){ sidebar.style.width = `${Math.min(420, Math.max(220, parseInt(savedW)||280))}px`; }
      let dragging=false, startX=0, startW=0;
      handle.addEventListener('mousedown',(e)=>{ dragging=true; startX=e.clientX; startW=sidebar.getBoundingClientRect().width; document.body.style.userSelect='none'; });
      window.addEventListener('mousemove',(e)=>{ if(!dragging) return; const w=Math.min(420, Math.max(220, Math.round(startW+(e.clientX-startX)))); sidebar.style.width=w+'px'; });
      window.addEventListener('mouseup',()=>{ if(!dragging) return; dragging=false; document.body.style.userSelect=''; const w=Math.round(sidebar.getBoundingClientRect().width); localStorage.setItem('sidebar-width', w); });
    })();
  </script>

  {{-- Componente auxiliar para grupos (para no repetir markup) --}}
  <template id="sidebar-group-template"></template>
  @once
    @push('components')
      @php /* Blade inline component for group */ @endphp
    @endpush
  @endonce

  @vite(['resources/js/app.js'])
  @fluxScripts
</body>
</html>
