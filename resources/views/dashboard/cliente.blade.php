{{-- filepath: resources/views/dashboard/cliente.blade.php --}}
<x-layouts.app :title="__('Mi Panel')">
  <div class="min-h-screen bg-zinc-950 text-white">
    <!-- Header Cliente (estilo 401: zinc + acentos ámbar) -->
    <header class="bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
      <div class="max-w-7xl mx-auto px-6 py-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 shrink-0 bg-amber-500 rounded-xl grid place-items-center">
              <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl sm:text-3xl font-bold leading-tight">¡Hola, {{ auth()->user()->name }}!</h1>
              <p class="text-zinc-300 text-sm">Bienvenido a tu panel personal</p>

              <div class="mt-3 inline-flex items-center gap-2 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-full px-3 py-1">
                <span class="w-2.5 h-2.5 rounded-full {{ $hours->isOpenAt($now) ? 'bg-amber-400 animate-pulse' : 'bg-zinc-500' }}"></span>
                <span class="text-xs font-medium">
                  {{ $hours->isOpenAt($now) ? 'Abierto' : 'Cerrado' }} • {{ $now->format('H:i') }}
                </span>
              </div>
            </div>
          </div>

          <!-- Acciones rápidas (header) con acento ámbar -->
          <div class="grid grid-cols-2 sm:flex sm:items-center gap-3">
            <a href="{{ route('menu.publico') }}"
              class="inline-flex items-center gap-2 bg-amber-500 text-black rounded-xl px-4 py-2 font-semibold hover:bg-amber-400 transition">
              <span class="text-lg">🍽️</span><span class="text-sm">Ver Menú</span>
            </a>

            <a href="{{ route('settings.profile') }}"
              class="inline-flex items-center gap-2 border-2 border-amber-500 text-amber-500 rounded-xl px-4 py-2 font-semibold hover:bg-amber-500/10 transition">
              <span class="text-lg">⚙️</span><span class="text-sm">Mi Perfil</span>
            </a>

            @php
              // Evita acceder a propiedades inexistentes en stdClass
              $favId = (is_object($productoFavorito) && isset($productoFavorito->id)) ? $productoFavorito->id : null;
            @endphp

            @if($favId)
              <a href="{{ route('productos.show', $favId) }}"
                class="inline-flex items-center gap-2 bg-amber-500 text-black rounded-xl px-4 py-2 font-semibold hover:bg-amber-400 transition">
                <span class="text-lg">⭐</span><span class="text-sm">Reordenar favorito</span>
              </a>
            @elseif(!empty($productoFavorito?->nombre))
              <a href="{{ route('productos.index') }}"
                class="inline-flex items-center gap-2 bg-amber-500/20 text-amber-300 border border-amber-500/20 rounded-xl px-4 py-2 font-semibold hover:bg-amber-500/25 transition">
                <span class="text-lg">⭐</span><span class="text-sm">Elegir “{{ $productoFavorito->nombre }}”</span>
              </a>
            @endif
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Métricas (tarjetas estilo 401: zinc + borde) -->
      <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center mb-4"><span class="text-2xl">💰</span></div>
          <h3 class="text-3xl font-extrabold tracking-tight">${{ number_format($totalGastado, 2) }}</h3>
          <p class="text-zinc-400 text-sm">Total gastado</p>
        </article>

        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center mb-4"><span class="text-2xl">📦</span></div>
          <h3 class="text-3xl font-extrabold tracking-tight">{{ $totalPedidos }}</h3>
          <p class="text-zinc-400 text-sm">Pedidos realizados</p>
        </article>

        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center mb-4"><span class="text-2xl">⭐</span></div>
          <h3 class="text-lg font-bold">
            {{ $productoFavorito->nombre ?? 'Sin favorito aún' }}
          </h3>
          <p class="text-zinc-400 text-sm">
            @if($productoFavorito) {{ $productoFavorito->veces_pedido }} veces pedido
            @else Elige tu próximo favorito
            @endif
          </p>
        </article>

        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center mb-4"><span class="text-2xl">📅</span></div>
          <h3 class="text-lg font-bold">
            @if($ultimaVisita) {{ $ultimaVisita->diffForHumans() }} @else Primera vez @endif
          </h3>
          <p class="text-zinc-400 text-sm">Última visita</p>
        </article>
      </section>

      <!-- Pedidos Activos -->
      @if($pedidosActivos->count() > 0)
      <section class="mb-10">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-2xl font-bold">🔥 Pedidos Activos</h2>
          <span class="bg-amber-500/10 text-amber-300 px-4 py-1.5 rounded-full text-sm font-medium border border-amber-500/20">
            {{ $pedidosActivos->count() }} en proceso
          </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($pedidosActivos as $pedido)
            @php
              $estado = $pedido->estado;
              $step = match($estado) {
                'pendiente' => 0,
                'en_preparacion' => 1,
                default => 2,
              };
              $etiqueta = $pedido->estado_nombre;
            @endphp
            <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6 hover:border-amber-500/40 transition-colors">
              <header class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center">
                    <span class="text-amber-400 font-bold">#{{ $pedido->id }}</span>
                  </div>
                  <div>
                    <p class="font-semibold leading-tight">{{ $pedido->tipo_nombre }}</p>
                    <p class="text-zinc-400 text-xs">{{ $pedido->mesa->nombre ?? '-' }}</p>
                  </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-medium
                  @if($estado == 'pendiente') bg-yellow-500/20 text-yellow-400
                  @elseif($estado == 'en_preparacion') bg-blue-500/20 text-blue-400
                  @else bg-green-500/20 text-green-400 @endif">
                  {{ $etiqueta }}
                </span>
              </header>

              <ul class="space-y-2 mb-4">
                @foreach($pedido->items as $item)
                  <li class="flex justify-between items-center text-sm">
                    <span class="text-zinc-300">
                      <span class="text-amber-400 font-medium">{{ $item->cantidad }}x</span> {{ $item->producto->nombre }}
                    </span>
                    <span class="font-medium">${{ number_format($item->subtotal_item, 2) }}</span>
                  </li>
                @endforeach
              </ul>

              <div class="flex items-center justify-between py-3 border-t border-zinc-800">
                <span class="text-zinc-400 text-sm">Total</span>
                <span class="font-bold text-xl">${{ number_format($pedido->total, 2) }}</span>
              </div>

              <!-- Stepper amber -->
              <div class="mt-4">
                <ol class="grid grid-cols-3 gap-2 text-xs text-center">
                  <li class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full grid place-items-center
                                {{ $step>=0 ? 'bg-amber-500 text-black' : 'bg-zinc-800 text-zinc-400' }}">1</div>
                    <span class="mt-1 text-zinc-400">Pendiente</span>
                  </li>
                  <li class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full grid place-items-center
                                {{ $step>=1 ? 'bg-amber-500 text-black' : 'bg-zinc-800 text-zinc-400' }}">2</div>
                    <span class="mt-1 text-zinc-400">Preparación</span>
                  </li>
                  <li class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full grid place-items-center
                                {{ $step>=2 ? 'bg-amber-500 text-black' : 'bg-zinc-800 text-zinc-400' }}">3</div>
                    <span class="mt-1 text-zinc-400">Listo</span>
                  </li>
                </ol>
              </div>
            </article>
          @endforeach
        </div>
      </section>
      @endif

      <!-- Historial & Populares -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <!-- Historial -->
        <div class="lg:col-span-2 bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
            <h2 class="text-xl font-bold">📋 Mi Historial</h2>
            <div class="relative">
              <input id="history-search" type="search" placeholder="Buscar por #id, producto o mesa…"
                     class="w-full sm:w-72 bg-zinc-800 border border-zinc-700 rounded-xl px-10 py-2 text-sm placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-500">
              <svg class="w-5 h-5 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/>
              </svg>
            </div>
          </div>

          <div id="history-list" class="space-y-3">
            @forelse($misPedidos as $pedido)
              @php
                $estadoBadge = match($pedido['estado']) {
                  'pagado' => 'bg-green-500/20 text-green-400',
                  'cancelado' => 'bg-red-500/20 text-red-400',
                  default => 'bg-yellow-500/20 text-yellow-400'
                };
              @endphp
              <article class="history-item p-4 bg-zinc-900/60 border border-zinc-800 rounded-xl hover:border-amber-500/30 transition-colors"
                       data-search="{{ '#'.$pedido['id'].' '.$pedido['tipo'].' '.$pedido['mesa'].' '.collect($pedido['items'])->pluck('producto')->join(' ') }}">
                <button class="w-full text-left" type="button" onclick="toggleOrderDetails({{ $pedido['id'] }})"
                        aria-expanded="false" aria-controls="order-details-{{ $pedido['id'] }}">
                  <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-4 min-w-0">
                      <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center shrink-0">
                        <span class="text-amber-400 font-bold text-sm">#{{ $pedido['id'] }}</span>
                      </div>
                      <div class="min-w-0">
                        <p class="font-medium truncate">{{ $pedido['tipo'] }}</p>
                        <p class="text-zinc-400 text-xs truncate">{{ $pedido['fecha'] }} • {{ $pedido['mesa'] }}</p>
                      </div>
                    </div>
                    <div class="text-right shrink-0">
                      <p class="font-bold">${{ number_format($pedido['total'], 2) }}</p>
                      <span class="text-xs px-2 py-1 rounded-full {{ $estadoBadge }}">{{ $pedido['estado'] }}</span>
                    </div>
                  </div>
                </button>

                <div id="order-details-{{ $pedido['id'] }}" class="hidden mt-4 pt-4 border-t border-zinc-800">
                  <p class="text-zinc-400 text-sm mb-3 font-medium">Items del pedido</p>
                  <div class="space-y-2">
                    @foreach($pedido['items'] as $item)
                      <div class="flex justify-between items-center text-sm bg-zinc-950/60 border border-zinc-800 p-2 rounded-lg">
                        <span class="text-zinc-300">
                          <span class="text-amber-400 font-medium">{{ $item['cantidad'] }}x</span> {{ $item['producto'] }}
                        </span>
                        <span class="font-medium">${{ number_format($item['subtotal'], 2) }}</span>
                      </div>
                    @endforeach
                  </div>

                  @if($pedido['puede_cancelar'])
                    <button onclick="cancelOrder({{ $pedido['id'] }})"
                            class="w-full mt-4 px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-colors">
                      Cancelar pedido
                    </button>
                  @endif
                </div>
              </article>
            @empty
              <div class="text-center py-12">
                <span class="text-6xl mb-4 block">🍽️</span>
                <p class="text-zinc-400">Aún no has realizado ningún pedido</p>
                <a href="{{ route('menu.publico') }}"
                   class="inline-block mt-4 px-6 py-3 bg-amber-500 hover:bg-amber-400 text-black rounded-xl font-medium transition-colors">
                  Explorar Menú
                </a>
              </div>
            @endforelse
          </div>

          <div class="mt-4 text-right">
            <a href="{{ route('pedidos.index') }}" class="text-amber-300 hover:text-amber-200 text-sm font-medium">Ver todo →</a>
          </div>
        </div>

        <!-- Populares -->
        <aside class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">🔥 Populares</h2>
          <div class="space-y-4">
            @forelse($productosPopulares as $producto)
              <button type="button"
                      class="w-full text-left flex items-center gap-4 p-3 bg-zinc-900/60 border border-zinc-800 rounded-xl hover:border-amber-500/30 transition-colors"
                      onclick="window.location.href='{{ route('productos.show', $producto->id) }}'">
                <div class="w-16 h-16 bg-amber-500/20 rounded-xl overflow-hidden shrink-0">
                  @if($producto->imagen_url)
                    <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" loading="lazy" class="w-full h-full object-cover">
                  @else
                    <div class="w-full h-full grid place-items-center"><span class="text-2xl">☕</span></div>
                  @endif
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-sm truncate">{{ $producto->nombre }}</p>
                  <p class="text-amber-400 font-bold">${{ number_format($producto->precio, 2) }}</p>
                  <p class="text-zinc-500 text-xs">{{ $producto->pedido_items_count }} pedidos</p>
                </div>
                <span class="text-zinc-400 text-lg">→</span>
              </button>
            @empty
              <p class="text-zinc-400 text-center py-8 text-sm">No hay productos disponibles</p>
            @endforelse
          </div>

          <a href="{{ route('menu.publico') }}"
             class="block w-full mt-6 px-4 py-3 bg-amber-500 hover:bg-amber-400 text-black text-center rounded-xl font-medium transition-colors">
            Ver Menú Completo
          </a>
        </aside>
      </section>

      <!-- Especial del Día (ya está en ámbar) -->
      @if($especialHoy)
      <section class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-6">
        <div class="flex items-center gap-2 mb-4">
          <span class="text-2xl">⭐</span>
          <h2 class="text-xl font-bold">Especial del Día</h2>
          <span class="ml-2 text-[10px] uppercase bg-amber-400/20 text-amber-300 border border-amber-300/30 px-2 py-0.5 rounded-full tracking-wide">Hoy</span>
        </div>

        <div class="grid md:grid-cols-12 gap-6 items-center">
          <div class="md:col-span-3">
            <div class="w-full aspect-square bg-amber-500/20 rounded-2xl overflow-hidden">
              @if($especialHoy->producto->imagen_url)
                <img src="{{ $especialHoy->producto->imagen_url }}" alt="{{ $especialHoy->producto->nombre }}"
                     loading="lazy" class="w-full h-full object-cover">
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
                <span class="bg-red-500 text-white text-xs px-3 py-1.5 rounded-full font-bold animate-pulse">
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
              Ordenar ahora
            </a>
          </div>
        </div>
      </section>
      @endif

      <!-- Acciones Rápidas (footer) -->
      <section class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('menu.publico') }}"
           class="flex items-center gap-4 p-6 bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl hover:border-amber-500/30 transition-all">
          <div class="w-14 h-14 bg-amber-500/20 rounded-xl grid place-items-center"><span class="text-3xl">🍽️</span></div>
          <div>
            <h3 class="font-bold text-lg">Ver Menú</h3>
            <p class="text-zinc-300 text-sm">Explora nuestros productos</p>
          </div>
        </a>

        <a href="{{ route('pedidos.index') }}"
           class="flex items-center gap-4 p-6 bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl hover:border-amber-500/30 transition-all">
          <div class="w-14 h-14 bg-amber-500/20 rounded-xl grid place-items-center"><span class="text-3xl">📦</span></div>
          <div>
            <h3 class="font-bold text-lg">Mis Pedidos</h3>
            <p class="text-zinc-300 text-sm">Ver historial completo</p>
          </div>
        </a>

        <a href="{{ route('settings.profile') }}"
           class="flex items-center gap-4 p-6 bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl hover:border-amber-500/30 transition-all">
          <div class="w-14 h-14 bg-amber-500/20 rounded-xl grid place-items-center"><span class="text-3xl">⚙️</span></div>
          <div>
            <h3 class="font-bold text-lg">Mi Perfil</h3>
            <p class="text-zinc-300 text-sm">Configuración</p>
          </div>
        </a>
      </section>
    </main>
  </div>

  <script>
    // Colapsar/expandir detalles de pedido
    function toggleOrderDetails(orderId) {
      const el = document.getElementById(`order-details-${orderId}`);
      if (!el) return;
      const isHidden = el.classList.contains('hidden');
      el.classList.toggle('hidden');
      const btn = el.parentElement.querySelector('button[aria-controls="order-details-' + orderId + '"]');
      if (btn) btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
    }

    // Cancelar pedido (manteniendo rutas y CSRF)
    function cancelOrder(orderId) {
      if (!confirm('¿Estás seguro de que deseas cancelar este pedido?')) return;
      fetch(`/pedidos/${orderId}/cancel`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json'
        }
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert(data.message || 'No se pudo cancelar el pedido');
        }
      })
      .catch(err => {
        alert('Error al cancelar el pedido');
        console.error(err);
      });
    }

    // Búsqueda local en historial
    (function historySearch() {
      const input = document.getElementById('history-search');
      const items = document.querySelectorAll('#history-list .history-item');
      if (!input || items.length === 0) return;

      input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        items.forEach(it => {
          const haystack = (it.getAttribute('data-search') || '').toLowerCase();
          it.style.display = haystack.includes(q) ? '' : 'none';
        });
      });
    })();
  </script>
</x-layouts.app>
