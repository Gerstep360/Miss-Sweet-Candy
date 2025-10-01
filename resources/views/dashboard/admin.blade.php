{{-- filepath: resources/views/dashboard/admin.blade.php --}}
<x-layouts.app :title="__('Dashboard Administrador')">
  <div class="min-h-screen bg-zinc-950 text-white">
    <!-- Header (estilo 401: zinc + acentos ámbar) -->
    <header class="bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
      <div class="max-w-7xl mx-auto px-6 py-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 shrink-0 bg-amber-500 rounded-xl grid place-items-center">
              <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl sm:text-3xl font-bold leading-tight">Panel de Administrador</h1>
              <p class="text-zinc-300 text-sm">{{ auth()->user()->name }}</p>
              <div class="mt-3 inline-flex items-center gap-2 bg-amber-500/10 text-amber-300 border border-amber-500/20 rounded-full px-3 py-1">
                <span class="w-2.5 h-2.5 rounded-full {{ $hours->isOpenAt($now) ? 'bg-amber-400 animate-pulse' : 'bg-zinc-500' }}"></span>
                <span class="text-xs font-medium">
                  {{ $hours->isOpenAt($now) ? 'Abierto' : 'Cerrado' }} • {{ $now->format('H:i') }}
                </span>
              </div>
            </div>
          </div>

          <!-- CTA (opcional) -->
          <div class="grid grid-cols-2 sm:flex sm:items-center gap-3">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 border-2 border-amber-500 text-amber-500 rounded-xl px-4 py-2 font-semibold hover:bg-amber-500/10 transition">
              <span class="text-lg">🏠</span><span class="text-sm">Inicio</span>
            </a>
            <a href="{{ route('productos.index') }}"
               class="inline-flex items-center gap-2 bg-amber-500 text-black rounded-xl px-4 py-2 font-semibold hover:bg-amber-400 transition">
              <span class="text-lg">🍫</span><span class="text-sm">Productos</span>
            </a>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- KPIs (tarjetas zinc + acentos ámbar) -->
      <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center">
              <span class="text-2xl">💲</span>
            </div>
            <span class="text-xs bg-amber-500/15 text-amber-300 border border-amber-500/20 px-3 py-1 rounded-full font-medium">Hoy</span>
          </div>
          <h3 class="text-3xl font-extrabold tracking-tight">${{ number_format($ventasHoy, 2) }}</h3>
          <p class="text-zinc-400 text-sm">Ventas del día</p>
        </article>

        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center">
              <span class="text-2xl">🧾</span>
            </div>
            <span class="text-xs bg-amber-500/15 text-amber-300 border border-amber-500/20 px-3 py-1 rounded-full font-medium">Hoy</span>
          </div>
          <h3 class="text-3xl font-extrabold tracking-tight">{{ $ordenesHoy }}</h3>
          <p class="text-zinc-400 text-sm">Órdenes procesadas</p>
        </article>

        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center mb-4">
            <span class="text-2xl">👥</span>
          </div>
          <h3 class="text-3xl font-extrabold tracking-tight">{{ $clientesUnicos }}</h3>
          <p class="text-zinc-400 text-sm">Clientes únicos</p>
        </article>

        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center">
              <span class="text-2xl">📦</span>
            </div>
            @if($lowStock > 0)
              <span class="text-xs bg-red-500/20 text-red-400 px-3 py-1 rounded-full font-medium animate-pulse">⚠️</span>
            @endif
          </div>
          <h3 class="text-3xl font-extrabold tracking-tight">{{ $lowStock }}</h3>
          <p class="text-zinc-400 text-sm">Productos bajo stock</p>
        </article>
      </section>

      <!-- Gráficas y Estado de Mesas -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Ventas de la semana -->
        <div class="lg:col-span-2 bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">Ventas de la Semana</h2>
          <div class="space-y-4">
            @php $maxVenta = collect($ventasSemana)->max('ventas'); @endphp
            @foreach($ventasSemana as $dia)
              @php $porcentaje = $maxVenta > 0 ? ($dia['ventas'] / $maxVenta) * 100 : 0; @endphp
              <div>
                <div class="flex justify-between items-center mb-2">
                  <span class="font-medium {{ $dia['esHoy'] ? 'text-amber-400' : '' }}">
                    {{ $dia['dia'] }} {{ $dia['esHoy'] ? '(Hoy)' : '' }}
                  </span>
                  <div class="text-right">
                    <span class="font-bold">${{ number_format($dia['ventas'], 0) }}</span>
                    <span class="text-zinc-400 text-sm ml-2">{{ $dia['ordenes'] }} órdenes</span>
                  </div>
                </div>
                <div class="w-full bg-zinc-800 rounded-full h-3 overflow-hidden">
                  <div class="bg-amber-500 h-3 rounded-full transition-all duration-1000 {{ $dia['esHoy'] ? 'animate-pulse' : '' }}"
                       style="width: {{ $porcentaje }}%"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Estado de Mesas -->
        <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">Estado de Mesas</h2>
          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-zinc-900 border border-zinc-800 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500/20 rounded-lg grid place-items-center">
                  <span class="text-2xl">✅</span>
                </div>
                <div>
                  <p class="font-medium">Libres</p>
                  <p class="text-zinc-400 text-xs">Disponibles</p>
                </div>
              </div>
              <span class="text-xl font-bold text-emerald-400">{{ $estadoMesas['libres'] }}</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-zinc-900 border border-zinc-800 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-500/20 rounded-lg grid place-items-center">
                  <span class="text-2xl">🔴</span>
                </div>
                <div>
                  <p class="font-medium">Ocupadas</p>
                  <p class="text-zinc-400 text-xs">En uso</p>
                </div>
              </div>
              <span class="text-xl font-bold text-red-400">{{ $estadoMesas['ocupadas'] }}</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-zinc-900 border border-zinc-800 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center">
                  <span class="text-2xl">📅</span>
                </div>
                <div>
                  <p class="font-medium">Reservadas</p>
                  <p class="text-zinc-400 text-xs">Pendientes</p>
                </div>
              </div>
              <span class="text-xl font-bold text-amber-300">{{ $estadoMesas['reservadas'] }}</span>
            </div>

            <div class="pt-4 border-t border-zinc-800">
              <div class="flex justify-between items-center">
                <span class="text-zinc-400">Total de mesas</span>
                <span class="font-bold text-xl">{{ $estadoMesas['total'] }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Top Productos y Métodos de Pago -->
      <section class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Productos (Hoy) -->
        <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">Productos Más Vendidos (Hoy)</h2>
          <div class="space-y-3">
            @foreach($topProductos->take(5) as $index => $producto)
              <div class="flex items-center gap-4 p-4 bg-zinc-900 border border-zinc-800 rounded-xl hover:border-amber-500/30 transition-colors">
                <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center">
                  <span class="text-amber-400 font-bold">#{{ $index + 1 }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-medium truncate">{{ $producto->nombre }}</p>
                  <p class="text-zinc-400 text-sm">{{ $producto->total_vendido }} unidades</p>
                </div>
                <div class="text-right shrink-0">
                  <p class="text-amber-300 font-bold">${{ number_format($producto->ingresos, 2) }}</p>
                  <p class="text-zinc-500 text-xs">ingresos</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Métodos de Pago (Hoy) -->
        <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">Métodos de Pago (Hoy)</h2>
          <div class="space-y-4">
            @php $totalVentas = $ventasPorMetodo->sum('total'); @endphp
            @foreach($ventasPorMetodo as $metodo)
              @php $porcentaje = $totalVentas > 0 ? ($metodo['total'] / $totalVentas) * 100 : 0; @endphp
              <div>
                <div class="flex justify-between items-center mb-2">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center">
                      <span class="text-xl">
                        @if($metodo['metodo'] == 'Efectivo') 💵
                        @elseif($metodo['metodo'] == 'Tarjeta') 💳
                        @else 📱
                        @endif
                      </span>
                    </div>
                    <div>
                      <p class="font-medium">{{ $metodo['metodo'] }}</p>
                      <p class="text-zinc-400 text-xs">{{ $metodo['cantidad'] }} transacciones</p>
                    </div>
                  </div>
                  <span class="font-bold">${{ number_format($metodo['total'], 2) }}</span>
                </div>
                <div class="w-full bg-zinc-800 rounded-full h-2">
                  <div class="bg-amber-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $porcentaje }}%"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </section>

      <!-- Órdenes Recientes y Empleados -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Órdenes Recientes -->
        <div class="lg:col-span-2 bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">Órdenes Recientes</h2>
          <div class="space-y-3">
            @forelse($ordenesRecientes as $orden)
              @php
                $badge = $orden['estado'] === 'pagado'
                  ? 'bg-emerald-500/20 text-emerald-400'
                  : 'bg-amber-500/20 text-amber-300';
              @endphp
              <div class="flex items-center justify-between p-4 bg-zinc-900 border border-zinc-800 rounded-xl hover:border-amber-500/30 transition-colors">
                <div class="flex items-center gap-4 min-w-0">
                  <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center shrink-0">
                    <span class="text-amber-400 font-bold">#{{ $orden['id'] }}</span>
                  </div>
                  <div class="min-w-0">
                    <p class="font-medium truncate">{{ $orden['tipo'] }}</p>
                    <p class="text-zinc-400 text-sm truncate">
                      {{ $orden['cliente'] }} • {{ $orden['mesa'] }} • {{ $orden['items_count'] }} items
                    </p>
                    <p class="text-zinc-500 text-xs">{{ $orden['fecha'] }}</p>
                  </div>
                </div>
                <div class="text-right shrink-0">
                  <p class="font-bold">${{ number_format($orden['total'], 2) }}</p>
                  <span class="text-xs px-2 py-1 rounded-full {{ $badge }}">{{ $orden['estado'] }}</span>
                </div>
              </div>
            @empty
              <p class="text-zinc-400 text-center py-8">No hay órdenes recientes</p>
            @endforelse
          </div>
        </div>

        <!-- Empleados del Día -->
        <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">Empleados del Día</h2>
          <div class="space-y-3">
            @forelse($empleadosActivos as $index => $empleado)
              <div class="flex items-center gap-3 p-4 bg-zinc-900 border border-zinc-800 rounded-xl">
                <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center">
                  @if($index == 0)
                    <span class="text-xl">🏆</span>
                  @else
                    <span class="text-amber-400 font-bold">{{ $index + 1 }}</span>
                  @endif
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-medium truncate">{{ $empleado->name }}</p>
                  <p class="text-zinc-400 text-xs">{{ $empleado->cobros_realizados_count }} cobros</p>
                </div>
              </div>
            @empty
              <p class="text-zinc-400 text-center py-8 text-sm">Sin actividad hoy</p>
            @endforelse
          </div>
        </div>
      </section>

      <!-- Especial del Día -->
      @if($especialHoy)
      <section class="mt-8 bg-amber-500/10 border border-amber-500/20 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <span class="text-2xl">⭐</span>
            <h2 class="text-xl font-bold">Especial del Día</h2>
            <span class="ml-2 text-[10px] uppercase bg-amber-400/20 text-amber-300 border border-amber-300/30 px-2 py-0.5 rounded-full tracking-wide">Hoy</span>
          </div>
          @can('editar-productos')
          <button onclick="openProductModal()"
                  class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-black font-semibold rounded-lg transition-colors">
            Cambiar
          </button>
          @endcan
        </div>

        <div class="grid md:grid-cols-12 gap-6 items-center">
          <div class="md:col-span-3">
            <div class="w-full aspect-square bg-amber-500/20 rounded-2xl overflow-hidden">
              @if($especialHoy->producto->imagen_url)
                <img src="{{ $especialHoy->producto->imagen_url }}" alt="{{ $especialHoy->producto->nombre }}"
                     class="w-full h-full object-cover" loading="lazy">
              @else
                <div class="w-full h-full grid place-items-center"><span class="text-6xl">☕</span></div>
              @endif
            </div>
          </div>
          <div class="md:col-span-6">
            <h3 class="text-2xl font-bold mb-2">{{ $especialHoy->producto->nombre }}</h3>
            <p class="text-zinc-300 mb-4">{{ $especialHoy->getDescripcionCompleta() }}</p>
            <div class="flex items-center gap-4 flex-wrap">
              @if($especialHoy->tieneDescuento())
                <span class="text-zinc-400 line-through text-lg">${{ number_format($especialHoy->producto->precio, 2) }}</span>
                <span class="text-4xl font-extrabold text-amber-400">${{ number_format($especialHoy->getPrecioFinal(), 2) }}</span>
                <span class="bg-red-500 text-white text-xs px-3 py-1.5 rounded-full font-bold">
                  Ahorra ${{ number_format($especialHoy->getDescuentoMonto(), 2) }}
                </span>
              @else
                <span class="text-4xl font-extrabold text-amber-400">${{ number_format($especialHoy->producto->precio, 2) }}</span>
              @endif
            </div>
          </div>
          <div class="md:col-span-3 flex items-center md:justify-end">
            <a href="{{ route('productos.show', $especialHoy->producto->id) }}"
               class="w-full md:w-auto px-8 py-4 bg-amber-500 hover:bg-amber-400 text-black font-bold rounded-2xl transition-all transform hover:scale-[1.02] shadow-lg shadow-amber-500/40 text-center">
              Ver producto
            </a>
          </div>
        </div>
      </section>
      @endif
    </main>
  </div>

  <!-- Modal Cambiar Especial -->
  <div id="productModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="bg-zinc-900 rounded-2xl p-6 w-full max-w-2xl border border-zinc-800">
        <h3 class="text-xl font-bold mb-4">Seleccionar Especial del Día</h3>
        <div id="productsList" class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-96 overflow-y-auto"></div>
        <button onclick="closeProductModal()" class="w-full mt-6 p-3 bg-zinc-800 hover:bg-zinc-700 rounded-lg">
          Cancelar
        </button>
      </div>
    </div>
  </div>

  <script>
    function openProductModal() {
      document.getElementById('productModal').classList.remove('hidden');
      loadProducts();
    }
    function closeProductModal() {
      document.getElementById('productModal').classList.add('hidden');
    }
    function loadProducts() {
      fetch('/api/productos')
        .then(r => r.json())
        .then(products => {
          const container = document.getElementById('productsList');
          container.innerHTML = (products || []).map(p => `
            <button type="button" onclick="selectProduct(${p.id})"
                    class="w-full text-left p-4 bg-zinc-900 border border-zinc-800 rounded-xl hover:border-amber-500/30 transition-colors">
              <h4 class="font-medium mb-1 truncate">${p.nombre}</h4>
              <p class="text-amber-300 font-bold">$${Number(p.precio ?? 0).toFixed(2)}</p>
            </button>
          `).join('');
        })
        .catch(() => {
          document.getElementById('productsList').innerHTML =
            '<p class="text-zinc-400">No se pudieron cargar los productos.</p>';
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
      .then(r => r.json())
      .then(data => { if (data?.success) location.reload(); });
    }
  </script>
</x-layouts.app>
