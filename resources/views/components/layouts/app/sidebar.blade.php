{{-- filepath: resources/views/components/layouts/app/sidebar.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
  <head>
    @include('partials.head')
  </head>
  <body class="min-h-screen bg-zinc-950 text-white">
    {{-- ===== Overlay móvil para cerrar al tocar fuera ===== --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] hidden lg:hidden"></div>

    {{-- ===== Sidebar (estilo 401: zinc + ámbar) ===== --}}
    <flux:sidebar sticky stashable
      class="relative z-[70] border-e border-zinc-800 bg-zinc-900/95 backdrop-blur shadow-[0_0_40px_-12px_rgba(245,158,11,.15)]"
      style="width: 280px"
      data-sidebar
    >
      {{-- Toggle close (solo móvil) --}}
      <flux:sidebar.toggle class="lg:hidden absolute right-2 top-2" icon="x-mark" />

      {{-- Logo + marca (igual al 401) --}}
      <div class="mb-5 border-b border-zinc-800 pb-4 px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group" wire:navigate>
          <div class="w-9 h-9 bg-amber-500 rounded-lg grid place-items-center shadow-lg shadow-amber-500/30 group-hover:shadow-amber-500/50 transition">
            <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
            </svg>
          </div>
          <div>
            <span class="text-lg font-semibold block group-hover:text-amber-400 transition-colors">Miss Sweet Candy</span>
            <span class="text-[11px] text-zinc-400">Sistema de Gestión</span>
          </div>
        </a>
      </div>

      {{-- Contenido con scroll suave --}}
      <div class="flex-1 overflow-y-auto custom-scrollbar px-3 space-y-2" id="sidebar-scroll">
        {{-- Dashboard --}}
        <flux:navlist variant="outline">
          <flux:navlist.item
            icon="home"
            :href="route('dashboard')"
            :current="request()->routeIs('dashboard')"
            wire:navigate
            class="nav-item-single"
          >
            Dashboard
          </flux:navlist.item>
        </flux:navlist>

        {{-- ===== Administración ===== --}}
        @canany(['ver-usuarios', 'ver-roles', 'gestionar-permisos'])
        <div class="nav-group">
          <button class="nav-group-header"
                  onclick="toggleSection('admin')"
                  aria-controls="admin-section"
                  aria-expanded="false">
            <div class="flex items-center gap-2 flex-1">
              <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              <span>Administración</span>
            </div>
            <svg id="admin-icon" class="w-4 h-4 nav-group-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div id="admin-section" class="nav-group-content" hidden>
            <flux:navlist variant="outline" class="space-y-1 mt-2">
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
            </flux:navlist>
          </div>
        </div>
        @endcanany

        {{-- ===== Cafetería ===== --}}
        @canany(['ver-productos', 'ver-categorias', 'ver-horarios'])
        <div class="nav-group">
          <button class="nav-group-header"
                  onclick="toggleSection('cafeteria')"
                  aria-controls="cafeteria-section"
                  aria-expanded="false">
            <div class="flex items-center gap-2 flex-1">
              <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                <path d="M2,21H20V19H2M20,8H18V5H20M20,3H4V13A4,4 0 0,0 8,17H14A4,4 0 0,0 18,13V10H20A2,2 0 0,0 22,8V5C22,3.89 21.1,3 20,3Z"/>
              </svg>
              <span>Cafetería</span>
            </div>
            <svg id="cafeteria-icon" class="w-4 h-4 nav-group-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div id="cafeteria-section" class="nav-group-content" hidden>
            <flux:navlist variant="outline" class="space-y-1 mt-2">
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
            </flux:navlist>
          </div>
        </div>
        @endcanany

        {{-- ===== Operaciones ===== --}}
        @canany(['ver-mesas', 'ver-pedidos'])
        <div class="nav-group">
          <button class="nav-group-header"
                  onclick="toggleSection('operaciones')"
                  aria-controls="operaciones-section"
                  aria-expanded="false">
            <div class="flex items-center gap-2 flex-1">
              <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              <span>Operaciones</span>
            </div>
            <svg id="operaciones-icon" class="w-4 h-4 nav-group-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div id="operaciones-section" class="nav-group-content" hidden>
            <flux:navlist variant="outline" class="space-y-1 mt-2">
              @can('ver-mesas')
              <flux:navlist.item icon="table-cells" :href="route('mesas.index')" :current="request()->routeIs('mesas.*')" wire:navigate class="nav-item-child">
                Mesas
              </flux:navlist.item>
              @endcan
              @can('ver-pedidos')
              <flux:navlist.item icon="shopping-cart" :href="route('pedidos.index')" :current="request()->routeIs('pedidos.*')" wire:navigate class="nav-item-child">
                Pedidos
              </flux:navlist.item>
              @endcan
            </flux:navlist>
          </div>
        </div>
        @endcanany

        {{-- ===== Ventas & Caja ===== --}}
        @canany(['ver-cobros', 'ver-reporte-caja'])
        <div class="nav-group">
          <button class="nav-group-header"
                  onclick="toggleSection('ventas')"
                  aria-controls="ventas-section"
                  aria-expanded="false">
            <div class="flex items-center gap-2 flex-1">
              <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Ventas & Caja</span>
            </div>
            <svg id="ventas-icon" class="w-4 h-4 nav-group-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div id="ventas-section" class="nav-group-content" hidden>
            <flux:navlist variant="outline" class="space-y-1 mt-2">
              @can('ver-pedidos')
              <flux:navlist.item icon="plus-circle" :href="route('pedidos.index')" :current="request()->routeIs('pedidos.index')" wire:navigate class="nav-item-child">
                Ventas
              </flux:navlist.item>
              @endcan

              @can('ver-cobros')
              <flux:navlist.item icon="document-text" :href="route('cobro_caja.index')" :current="request()->routeIs('cobro_caja.*')" wire:navigate class="nav-item-child">
                Historial de Cobros
              </flux:navlist.item>
              @endcan

              @can('ver-reporte-caja')
              <flux:navlist.item icon="chart-bar" :href="route('cobro_caja.reporte_diario')" :current="request()->routeIs('cobro_caja.reporte_diario')" wire:navigate class="nav-item-child">
                Reporte Diario
              </flux:navlist.item>
              @endcan
            </flux:navlist>
          </div>
        </div>
        @endcanany

        <div class="border-t border-zinc-800 my-3"></div>

        {{-- Menú Público --}}
        @can('ver-menu-publico')
        <flux:navlist variant="outline">
          <flux:navlist.item icon="book-open" :href="route('menu.publico')" target="_blank" class="nav-item-single">
            Ver Menú Público
          </flux:navlist.item>
        </flux:navlist>
        @endcan

        {{-- Sitio Web --}}
        <flux:navlist variant="outline">
          <flux:navlist.item icon="arrow-top-right-on-square" :href="route('home')" target="_blank" class="nav-item-single">
            Ver Sitio Web
          </flux:navlist.item>
        </flux:navlist>

        <div class="border-t border-zinc-800 my-3"></div>

        {{-- Mi Cuenta --}}
        <div class="nav-group">
          <button class="nav-group-header"
                  onclick="toggleSection('cuenta')"
                  aria-controls="cuenta-section"
                  aria-expanded="false">
            <div class="flex items-center gap-2 flex-1">
              <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <span>Mi Cuenta</span>
            </div>
            <svg id="cuenta-icon" class="w-4 h-4 nav-group-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div id="cuenta-section" class="nav-group-content" hidden>
            <flux:navlist variant="outline" class="space-y-1 mt-2">
              <flux:navlist.item icon="user" :href="route('settings.profile')" :current="request()->routeIs('settings.profile')" wire:navigate class="nav-item-child">
                Mi Perfil
              </flux:navlist.item>
              <flux:navlist.item icon="lock-closed" :href="route('settings.password')" :current="request()->routeIs('settings.password')" wire:navigate class="nav-item-child">
                Cambiar Contraseña
              </flux:navlist.item>
              <flux:navlist.item icon="paint-brush" :href="route('settings.appearance')" :current="request()->routeIs('settings.appearance')" wire:navigate class="nav-item-child">
                Apariencia
              </flux:navlist.item>
            </flux:navlist>
          </div>
        </div>

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

      {{-- ===== Asa para “slider” (resize ancho en desktop) ===== --}}
      <div id="sidebar-resizer" class="hidden lg:block absolute top-0 -right-1 h-full w-2 cursor-col-resize bg-transparent"></div>
    </flux:sidebar>

    {{-- ===== Header móvil coherente con 401 (abre sidebar) ===== --}}
    <flux:header class="lg:hidden sticky top-0 z-[65] bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
      <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
      <div class="flex items-center space-x-2">
        <div class="w-9 h-9 bg-amber-500 rounded-lg grid place-items-center shadow-lg">
          <svg class="w-5 h-5 text-black" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
          </svg>
        </div>
        <span class="text-base font-bold">Miss Sweet Candy</span>
      </div>
      <flux:spacer />
      <div class="w-9 h-9 bg-amber-500 rounded-lg grid place-items-center text-black font-bold text-xs shadow-lg">
        {{ auth()->user()->initials() }}
      </div>
    </flux:header>

    {{-- Slot de la app --}}
    {{ $slot }}

    {{-- ====== ESTILOS ====== --}}
    <style>
      .custom-scrollbar{scrollbar-width:thin;scrollbar-color:rgb(82,82,91) transparent}
      .custom-scrollbar::-webkit-scrollbar{width:7px}
      .custom-scrollbar::-webkit-scrollbar-track{background:transparent;border-radius:10px;margin:4px 0}
      .custom-scrollbar::-webkit-scrollbar-thumb{background:linear-gradient(180deg,rgb(82,82,91),rgb(63,63,70));border-radius:10px;border:2px solid transparent;background-clip:content-box;transition:all .3s}
      .custom-scrollbar::-webkit-scrollbar-thumb:hover{background:linear-gradient(180deg,rgb(113,113,122),rgb(82,82,91));background-clip:content-box}

      /* Items */
      .nav-item-single{padding:.625rem 1rem; border-radius:.75rem; color:#d4d4d8}
      .nav-item-single:hover{color:white;background:rgba(39,39,42,.8);box-shadow:0 0 15px rgba(245,158,11,.08);transform:translateX(3px);transition:.2s}
      .nav-item-child{padding:.5rem 1rem;margin-left:1.5rem;border-radius:.5rem;color:#a1a1aa;font-size:.925rem}
      .nav-item-child:hover{color:white;background:rgba(39,39,42,.6);box-shadow:0 0 12px rgba(245,158,11,.08);transform:translateX(3px);transition:.2s}
      .nav-item-logout{padding:.625rem 1rem;border-radius:.75rem;color:rgb(248,113,113)}
      .nav-item-logout:hover{color:rgb(252,165,165);background:rgba(248,113,113,.08);transform:translateX(3px)}

      /* Item activo */
      [data-flux-navlist-item][data-current="true"]{
        background:linear-gradient(to right,rgba(245,158,11,.18),rgba(245,158,11,.06))!important;
        color:rgb(251,191,36)!important;border-left:3px solid rgb(245,158,11);
        box-shadow:0 0 20px rgba(245,158,11,.15)
      }

      /* Headers de grupos */
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

      /* Contenido de grupos (acordeón) */
      .nav-group-content{max-height:0; overflow:hidden; opacity:0; margin-top:0; transition:max-height .35s, opacity .25s, margin-top .25s}
      .nav-group-content.open{max-height:700px; opacity:1; margin-top:.25rem}
      .nav-group-content.open>*{animation:fadeIn .25s ease both}
      @keyframes fadeIn{from{opacity:0; transform:translateY(-4px)} to{opacity:1; transform:translateY(0)}}
    </style>

    {{-- ====== SCRIPTS ====== --}}
    <script>
      // --------- Abrir/cerrar secciones (con estado persistente) ----------
      function toggleSection(id){
        const sec = document.getElementById(`${id}-section`);
        const icn = document.getElementById(`${id}-icon`);
        if(!sec || !icn) return;

        const isOpen = sec.classList.toggle('open');
        sec.hidden = !isOpen;
        icn.classList.toggle('rotate', isOpen);

        // ARIA
        const header = document.querySelector(`[onclick="toggleSection('${id}')"]`);
        header && header.setAttribute('aria-expanded', String(isOpen));

        localStorage.setItem(`sidebar-group-${id}`, isOpen ? '1':'0');
      }

      // --------- Sidebar overlay (móvil) ----------
      const overlay = document.getElementById('sidebar-overlay');
      function showOverlay(){ overlay.classList.remove('hidden'); }
      function hideOverlay(){ overlay.classList.add('hidden'); }

      // Conectar a los toggles de flux para mostrar/ocultar overlay
      document.addEventListener('click', (e)=>{
        const t = e.target.closest('[data-flux-sidebar-toggle]');
        if(t){
          const isOpen = document.querySelector('[data-flux-sidebar]')?.getAttribute('data-open') === 'true';
          // Flux abre/cierras con el click, retrasamos para leer estado final
          setTimeout(()=>{
            const openNow = document.querySelector('[data-flux-sidebar]')?.getAttribute('data-open') === 'true';
            openNow ? showOverlay() : hideOverlay();
          }, 60);
        }
      });
      overlay.addEventListener('click', ()=>{
        // simular cerrar tocando toggle close si existe
        const closeBtn = document.querySelector('[data-flux-sidebar] [data-flux-sidebar-toggle]');
        closeBtn?.click();
        hideOverlay();
      });

      // --------- Restaurar estado de grupos y abrir si hay item activo ----------
      document.addEventListener('DOMContentLoaded', ()=>{
        const groups = ['admin','cafeteria','operaciones','ventas','cuenta'];
        groups.forEach(id=>{
          const sec = document.getElementById(`${id}-section`);
          const icon = document.getElementById(`${id}-icon`);
          if(!sec) return;
          const saved = localStorage.getItem(`sidebar-group-${id}`)==='1';
          const hasActive = !!sec.querySelector('[data-current="true"]');
          if(saved || hasActive){
            sec.classList.add('open'); sec.hidden=false;
            icon?.classList.add('rotate');
            const header = document.querySelector(`[onclick="toggleSection('${id}')"]`);
            header && header.setAttribute('aria-expanded','true');
          }
        });

        // Scroll suave en el contenedor
        const sc = document.getElementById('sidebar-scroll');
        if(sc) sc.style.scrollBehavior = 'smooth';
      });

      // --------- Teclado: presiona "b" para abrir/cerrar sidebar ----------
      document.addEventListener('keydown', (e)=>{
        if(e.key?.toLowerCase()==='b' && !e.metaKey && !e.ctrlKey && !e.altKey){
          const toggles = document.querySelectorAll('[data-flux-sidebar-toggle]');
          // Prioriza el toggle móvil si existe, si no, cualquiera
          (toggles[0] || null)?.click();
          e.preventDefault();
        }
      });

      // --------- “Slider” de ancho: arrastrar el asa en desktop ----------
      (function(){
        const handle = document.getElementById('sidebar-resizer');
        const sidebar = document.querySelector('[data-flux-sidebar]');
        if(!handle || !sidebar) return;

        // Restaurar ancho guardado
        const savedW = localStorage.getItem('sidebar-width');
        if(savedW){
          sidebar.style.width = `${Math.min(420, Math.max(220, parseInt(savedW,10)||280))}px`;
        }

        let dragging=false, startX=0, startW=0;
        const onDown = (e)=>{
          dragging=true; startX=e.clientX; startW = sidebar.getBoundingClientRect().width;
          document.body.style.userSelect='none';
        };
        const onMove = (e)=>{
          if(!dragging) return;
          const dx = e.clientX - startX;
          const w = Math.min(420, Math.max(220, Math.round(startW + dx)));
          sidebar.style.width = w + 'px';
        };
        const onUp = ()=>{
          if(!dragging) return;
          dragging=false; document.body.style.userSelect='';
          const w = Math.round(sidebar.getBoundingClientRect().width);
          localStorage.setItem('sidebar-width', w);
        };

        handle.addEventListener('mousedown', onDown);
        window.addEventListener('mousemove', onMove);
        window.addEventListener('mouseup', onUp);
      })();

      // --------- Swipe para abrir (móvil) ----------
      (function(){
        let tsX=null;
        window.addEventListener('touchstart', (e)=>{
          if(e.touches[0].clientX < 24){ tsX = e.touches[0].clientX; }
        }, {passive:true});
        window.addEventListener('touchend', (e)=>{
          if(tsX===null) return; tsX=null;
          // abre usando el toggle
          const openBtn = document.querySelector('flux-sidebar-toggle, [data-flux-sidebar-toggle]');
          openBtn?.click();
        }, {passive:true});
      })();
    </script>

    @vite(['resources/js/app.js'])
    @fluxScripts
  </body>
</html>
