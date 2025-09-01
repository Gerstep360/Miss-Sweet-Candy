{{-- resources/views/dashboard.blade.php --}}
<x-layouts.app :title="__('Dashboard - Miss Sweet Candy')">
  <div class="min-h-screen bg-zinc-950">
    <!-- Header del Dashboard - MEJORADO -->
    <div class="bg-gradient-to-r from-zinc-800 to-zinc-900 border-b border-zinc-700 px-6 py-6">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white">
              ¡Bienvenido de vuelta, {{ auth()->user()->name }}!
            </h1>
            <p class="text-zinc-400 mt-1 text-sm sm:text-base">Panel de control de Miss Sweet Candy</p>
          </div>
          <div class="flex items-center gap-3 bg-zinc-800/50 rounded-lg px-4 py-2 border border-zinc-700">
            <div class="w-3 h-3 rounded-full {{ $isOpen ? 'bg-green-400 animate-pulse' : 'bg-red-400' }}"></div>
            <div class="text-white">
              <span class="font-semibold">{{ $isOpen ? 'Abierto' : 'Cerrado' }}</span>
              <span class="text-zinc-400 text-sm ml-2">{{ $time }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
      <!-- KPIs Grid -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Ventas del día -->
        <div class="bg-zinc-900 rounded-xl p-4 sm:p-6 border border-zinc-800">
          <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="p-2 sm:p-3 bg-green-500/10 rounded-lg">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 10v1"/>
              </svg>
            </div>
            <span class="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded-full hidden sm:inline">Hoy</span>
          </div>
          <div>
            <h3 class="text-lg sm:text-2xl font-bold text-white">${{ number_format($metrics['sales'], 0) }}</h3>
            <p class="text-zinc-400 text-xs sm:text-sm">Ventas del día</p>
          </div>
        </div>

        <!-- Órdenes -->
        <div class="bg-zinc-900 rounded-xl p-4 sm:p-6 border border-zinc-800">
          <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="p-2 sm:p-3 bg-blue-500/10 rounded-lg">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
            </div>
            <span class="text-xs bg-blue-500/20 text-blue-400 px-2 py-1 rounded-full hidden sm:inline">Hoy</span>
          </div>
          <div>
            <h3 class="text-lg sm:text-2xl font-bold text-white">{{ $metrics['orders'] }}</h3>
            <p class="text-zinc-400 text-xs sm:text-sm">Órdenes hoy</p>
          </div>
        </div>

        <!-- Clientes -->
        <div class="bg-zinc-900 rounded-xl p-4 sm:p-6 border border-zinc-800">
          <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="p-2 sm:p-3 bg-purple-500/10 rounded-lg">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
            </div>
            <span class="text-xs bg-purple-500/20 text-purple-400 px-2 py-1 rounded-full hidden sm:inline">Únicos</span>
          </div>
          <div>
            <h3 class="text-lg sm:text-2xl font-bold text-white">{{ $metrics['clients'] }}</h3>
            <p class="text-zinc-400 text-xs sm:text-sm">Clientes únicos</p>
          </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-zinc-900 rounded-xl p-4 sm:p-6 border border-zinc-800">
          <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="p-2 sm:p-3 bg-orange-500/10 rounded-lg">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
            </div>
            @if($metrics['lowStock'] > 0)
              <span class="text-xs bg-red-500/20 text-red-400 px-2 py-1 rounded-full animate-pulse">⚠️</span>
            @else
              <span class="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded-full">✅</span>
            @endif
          </div>
          <div>
            <h3 class="text-lg sm:text-2xl font-bold text-white">{{ $metrics['lowStock'] }}</h3>
            <p class="text-zinc-400 text-xs sm:text-sm">Productos bajos</p>
          </div>
        </div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 items-stretch gap-6 sm:gap-8 mb-6 sm:mb-8">

        {{-- Especial del día --}}
        <section class="lg:col-span-2 bg-zinc-900 rounded-xl border border-zinc-800 h-full">
          <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 sm:mb-6">
              <h2 class="text-lg sm:text-xl font-bold text-white">Especial del Día</h2>

              @can('editar-productos')
              <button
                onclick="openProductModal()"
                class="w-full sm:w-auto px-4 py-2 bg-amber-500 hover:bg-amber-400 text-black font-medium rounded-lg transition-colors text-center">
                Cambiar Producto
              </button>
              @endcan
            </div>

            @if(isset($especialHoy))
              <div class="rounded-xl border border-amber-500/20 bg-gradient-to-r from-amber-500/10 to-orange-500/10 p-4 sm:p-5">
                <div class="sm:grid sm:grid-cols-12 sm:items-center sm:gap-5">
                  <div class="sm:col-span-3">
                    <div class="w-24 h-24 lg:w-28 lg:h-28 bg-amber-500/20 rounded-xl ring-1 ring-amber-400/20 overflow-hidden mx-auto sm:mx-0">
                      @if($especialHoy->producto->imagen_url)
                        <img src="{{ $especialHoy->producto->imagen_url }}"
                            alt="{{ $especialHoy->producto->nombre }}"
                            class="w-full h-full object-cover">
                      @else
                        <div class="w-full h-full grid place-items-center">
                          <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                          </svg>
                        </div>
                      @endif
                    </div>
                  </div>

                  <div class="sm:col-span-9 mt-4 sm:mt-0 text-center sm:text-left">
                    <h3 class="text-lg sm:text-xl font-bold text-white mb-1 truncate">
                      {{ $especialHoy->producto->nombre }}
                    </h3>

                    {{-- Precios --}}
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 sm:gap-3 mb-2">
                      @if($especialHoy->tieneDescuento())
                        <p class="text-base sm:text-lg text-zinc-400 line-through">
                          ${{ number_format($especialHoy->producto->precio, 2) }}
                        </p>
                        <p class="text-xl sm:text-2xl font-bold text-amber-400">
                          ${{ number_format($especialHoy->getPrecioFinal(), 2) }}
                        </p>
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                          -${{ number_format($especialHoy->getDescuentoMonto(), 2) }}
                        </span>
                      @else
                        <p class="text-xl sm:text-2xl font-bold text-amber-400">
                          ${{ number_format($especialHoy->producto->precio, 2) }}
                        </p>
                      @endif
                    </div>

                    {{-- Badge + descripción compacta --}}
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                      <span class="px-3 py-1 bg-zinc-800 text-zinc-300 text-xs rounded-full">
                        {{ $especialHoy->producto->categoria->nombre ?? 'Sin categoría' }}
                      </span>
                    </div>

                    <p class="text-zinc-300 text-sm mt-2 max-w-[65ch] mx-auto sm:mx-0 overflow-hidden text-ellipsis">
                      {{ $especialHoy->getDescripcionCompleta() }}
                    </p>
                  </div>
                </div>
              </div>
            @else
              <div class="text-center py-10">
                <div class="w-14 h-14 bg-zinc-800 rounded-full grid place-items-center mx-auto mb-4">
                  <svg class="w-7 h-7 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                  </svg>
                </div>
                <h3 class="text-white font-medium mb-1">Sin especial configurado</h3>
                <p class="text-zinc-400 text-sm">Selecciona un producto como especial del día</p>
              </div>
            @endif
          </div>
        </section>

        {{-- Acciones rápidas --}}
        <aside class="lg:col-span-1 bg-zinc-900 rounded-xl border border-zinc-800 h-full">
          <div class="p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold text-white mb-4 sm:mb-6">Acciones Rápidas</h2>
            <div class="space-y-3">
              @can('crear-ordenes')
              <button onclick="openOrderModal()"
                      class="w-full p-3 sm:p-4 bg-amber-500 hover:bg-amber-400 text-black font-medium rounded-lg transition-colors flex items-center justify-center gap-3">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                </svg>
                <span class="text-sm sm:text-base">Nueva Orden</span>
              </button>
              @endcan

              @can('ver-productos')
              <a href="{{ route('productos.index') }}"
                class="w-full p-3 sm:p-4 bg-zinc-800 hover:bg-zinc-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-3">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="text-sm sm:text-base">Ver Inventario</span>
              </a>
              @endcan

              @can('ver-reportes-ventas')
              <a href="{{ route('cobro_caja.index') }}"
                class="w-full p-3 sm:p-4 bg-zinc-800 hover:bg-zinc-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-3">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-sm sm:text-base">Reportes</span>
              </a>
              @endcan

              @can('administrar')
              <a href="{{ route('especiales-del-dia.index') }}"
                class="w-full p-3 sm:p-4 bg-zinc-800 hover:bg-zinc-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-3">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <span class="text-sm sm:text-base">Gestionar Especiales</span>
              </a>
              @endcan

              <a href="{{ route('home') }}" target="_blank"
                class="w-full p-3 sm:p-4 bg-zinc-800 hover:bg-zinc-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-3">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span class="text-sm sm:text-base">Ver Sitio Web</span>
              </a>
            </div>
          </div>
        </aside>
      </div>

      <!-- Segunda fila -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Ventas de la Semana -->
        <div class="bg-zinc-900 rounded-xl border border-zinc-800">
          <div class="p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold text-white mb-4 sm:mb-6">Ventas de la Semana</h2>
            <div class="space-y-4">
              @php
                $maxSales = collect($ventasSemana)->max('sales');
              @endphp

              @foreach($ventasSemana as $data)
              @php
                $percentage = $maxSales > 0 ? ($data['sales'] / $maxSales) * 100 : 0;
                $isToday = $data['fecha'] === now('America/La_Paz')->toDateString();
              @endphp
              <div class="space-y-2">
                <div class="flex justify-between items-center">
                  <span class="text-white font-medium text-sm sm:text-base {{ $isToday ? 'text-amber-400' : '' }}">
                    {{ $data['day'] }} {{ $isToday ? '(Hoy)' : '' }}
                  </span>
                  <div class="text-right">
                    <span class="text-white font-bold text-sm sm:text-base">${{ number_format($data['sales']) }}</span>
                    <span class="text-zinc-400 text-xs sm:text-sm ml-2">{{ $data['orders'] }} órdenes</span>
                  </div>
                </div>
                <div class="w-full bg-zinc-800 rounded-full h-2">
                  <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-2 rounded-full transition-all duration-1000 {{ $isToday ? 'animate-pulse' : '' }}" 
                       style="width: {{ $percentage }}%"></div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Órdenes Recientes -->
        <div class="bg-zinc-900 rounded-xl border border-zinc-800">
          <div class="p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold text-white mb-4 sm:mb-6">Órdenes Recientes</h2>
            
            <div class="space-y-3 max-h-80 overflow-y-auto">
              @forelse($orders as $order)
              <div class="flex items-center justify-between p-3 sm:p-4 bg-zinc-800 rounded-lg hover:bg-zinc-700 transition-colors">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 sm:w-10 sm:h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                    <span class="text-amber-400 font-bold text-xs sm:text-sm">#{{ $order->id }}</span>
                  </div>
                  <div>
                    <p class="text-white font-medium text-sm sm:text-base">
                      {{ $order->type === 'takeaway' ? '🥤' : '🍽️' }} {{ $order->title }}
                    </p>
                    <p class="text-zinc-400 text-xs sm:text-sm">
                      {{ $order->type === 'takeaway' ? 'Para llevar' : 'Mesa' }} • 
                      {{ $order->created_at->format('H:i') }}
                    </p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-amber-400 font-bold text-sm sm:text-base">${{ number_format($order->total) }}</p>
                  <p class="text-green-400 text-xs">✓ Pagado</p>
                </div>
              </div>
              @empty
              <div class="text-center py-6 sm:py-8">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-6 h-6 sm:w-8 sm:h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                </div>
                <p class="text-zinc-400 text-sm">Sin órdenes recientes</p>
              </div>
              @endforelse
            </div>

            @can('ver-ventas')
            <div class="mt-4 pt-4 border-t border-zinc-800">
              <a href="{{ route('cobro_caja.index') }}" class="w-full p-3 text-center text-amber-400 hover:text-amber-300 border border-amber-500/30 rounded-lg hover:bg-amber-500/10 transition-colors block text-sm sm:text-base">
                Ver todas las órdenes
              </a>
            </div>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Nueva Orden -->
  <div id="orderModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="bg-zinc-900 rounded-xl p-6 w-full max-w-md border border-zinc-800">
        <h3 class="text-xl font-bold text-white mb-4">Nueva Orden</h3>
        <p class="text-zinc-400 mb-6">¿Dónde será la orden?</p>
        <div class="space-y-3">
          @can('crear-ordenes')
          <a href="{{ route('pedido-mesas.create') }}" class="block w-full p-4 bg-amber-500 hover:bg-amber-400 text-black font-medium rounded-lg transition-colors text-center">
            🍽️ Para Mesa
          </a>
          <a href="{{ route('pedido_mostrador.create') }}" class="block w-full p-4 bg-zinc-800 hover:bg-zinc-700 text-white font-medium rounded-lg transition-colors text-center">
            🥤 Para Llevar
          </a>
          @endcan
        </div>
        <button onclick="closeOrderModal()" class="w-full mt-4 p-2 text-zinc-400 hover:text-white transition-colors">
          Cancelar
        </button>
      </div>
    </div>
  </div>

  <!-- Modal Seleccionar Producto -->
  <div id="productModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="bg-zinc-900 rounded-xl p-6 w-full max-w-2xl border border-zinc-800 max-h-[80vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-white mb-4">Seleccionar Especial del Día</h3>
        
        <div id="productsList" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto">
          <!-- Los productos se cargarán aquí -->
        </div>

        <div class="flex gap-3 mt-6">
          <button onclick="closeProductModal()" class="flex-1 p-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors">
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function openOrderModal() {
      document.getElementById('orderModal').classList.remove('hidden');
    }

    function closeOrderModal() {
      document.getElementById('orderModal').classList.add('hidden');
    }

    function openProductModal() {
      document.getElementById('productModal').classList.remove('hidden');
      loadProducts();
    }

    function closeProductModal() {
      document.getElementById('productModal').classList.add('hidden');
    }

    function loadProducts() {
      fetch('/api/productos')
        .then(response => response.json())
        .then(products => {
          const container = document.getElementById('productsList');
          container.innerHTML = '';
          
          products.forEach(product => {
            const productElement = document.createElement('div');
            productElement.className = 'p-4 bg-zinc-800 rounded-lg hover:bg-zinc-700 transition-colors cursor-pointer';
            productElement.onclick = () => selectProduct(product.id);
            
            productElement.innerHTML = `
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center">
                  ${product.imagen_url ? 
                    `<img src="${product.imagen_url}" alt="${product.nombre}" class="w-full h-full object-cover rounded-lg">` :
                    `<svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>`
                  }
                </div>
                <div class="flex-1">
                  <h4 class="text-white font-medium">${product.nombre}</h4>
                  <p class="text-amber-400 font-bold">$${parseFloat(product.precio).toFixed(2)}</p>
                  <p class="text-zinc-400 text-xs">${product.categoria?.nombre || 'Sin categoría'}</p>
                </div>
              </div>
            `;
            
            container.appendChild(productElement);
          });
        })
        .catch(error => {
          console.error('Error loading products:', error);
        });
    }

    function selectProduct(productId) {
      fetch(`/dashboard/set-special-product/${productId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Mostrar mensaje de éxito
          const toast = document.createElement('div');
          toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg z-50';
          toast.textContent = data.message || 'Especial actualizado correctamente';
          document.body.appendChild(toast);
          
          setTimeout(() => {
            toast.remove();
            location.reload();
          }, 2000);
        }
      })
      .catch(error => {
        console.error('Error setting special product:', error);
      });
    }
  </script>
</x-layouts.app>