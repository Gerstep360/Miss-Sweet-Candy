{{-- filepath: resources/views/dashboard/cajero.blade.php --}}
<x-layouts.app :title="__('Dashboard Cajero')">
  <div class="min-h-screen bg-zinc-950 text-white">
    <!-- Header Cajero (estilo 401: zinc + acentos ámbar) -->
    <header class="bg-zinc-950/80 backdrop-blur border-b border-zinc-800">
      <div class="max-w-7xl mx-auto px-6 py-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 shrink-0 bg-amber-500 rounded-xl grid place-items-center">
              <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl sm:text-3xl font-bold leading-tight">Panel de Cajero</h1>
              <p class="text-zinc-300 text-sm">{{ auth()->user()->name }}</p>
              <div class="mt-3 inline-flex items-center gap-2 bg-amber-500/10 text-amber-300 border border-amber-500/20 rounded-full px-3 py-1">
                <span class="w-2.5 h-2.5 rounded-full {{ $hours->isOpenAt($now) ? 'bg-amber-400 animate-pulse' : 'bg-zinc-500' }}"></span>
                <span class="text-xs font-medium">
                  {{ $hours->isOpenAt($now) ? 'Abierto' : 'Cerrado' }} • {{ $now->format('H:i') }}
                </span>
              </div>
            </div>
          </div>

          <!-- Acciones rápidas en header (primarias/outline ámbar) -->
          <div class="grid grid-cols-2 sm:flex sm:items-center gap-3">
            <a href="{{ route('pedidos.mostrador.create') }}"
               class="inline-flex items-center gap-2 bg-amber-500 text-black rounded-xl px-4 py-2 font-semibold hover:bg-amber-400 transition">
              <span class="text-lg">🥤</span><span class="text-sm">Nueva Orden</span>
            </a>
            <a href="{{ route('cobro_caja.index') }}"
               class="inline-flex items-center gap-2 border-2 border-amber-500 text-amber-500 rounded-xl px-4 py-2 font-semibold hover:bg-amber-500/10 transition">
              <span class="text-lg">💰</span><span class="text-sm">Ver Cobros</span>
            </a>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Métricas del Cajero (tarjetas zinc + borde) -->
      <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <h3 class="text-3xl font-extrabold tracking-tight">{{ $misCobrosHoy }}</h3>
          <p class="text-zinc-400 text-sm">Mis Cobros Hoy</p>
        </article>

        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center mb-4">
            <span class="text-2xl">💲</span>
          </div>
          <h3 class="text-3xl font-extrabold tracking-tight">${{ number_format($misVentasHoy, 2) }}</h3>
          <p class="text-zinc-400 text-sm">Mis Ventas Hoy</p>
        </article>

        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center mb-4">
            <span class="text-2xl">💵</span>
          </div>
          <h3 class="text-3xl font-extrabold tracking-tight">${{ number_format($efectivoRecaudado, 2) }}</h3>
          <p class="text-zinc-400 text-sm">Efectivo Recaudado</p>
        </article>

        <article class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <div class="w-12 h-12 bg-amber-500/20 rounded-xl grid place-items-center mb-4">
            <span class="text-2xl">💳</span>
          </div>
          <h3 class="text-3xl font-extrabold tracking-tight">${{ number_format($tarjetasRecaudadas, 2) }}</h3>
          <p class="text-zinc-400 text-sm">Tarjetas Recaudadas</p>
        </article>
      </section>

      <!-- Acciones Rápidas (cuerpo) -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('pedidos.mostrador.create') }}"
           class="flex items-center gap-4 p-6 bg-amber-500 text-black rounded-2xl hover:bg-amber-400 transition-all transform hover:scale-[1.02]">
          <div class="w-14 h-14 bg-black/20 rounded-xl grid place-items-center">
            <span class="text-3xl">🥤</span>
          </div>
          <div>
            <h3 class="font-bold text-lg">Nueva Orden</h3>
            <p class="text-black/80 text-sm">Para llevar</p>
          </div>
        </a>

        <a href="{{ route('pedidos.mesa.create') }}"
           class="flex items-center gap-4 p-6 bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl hover:border-amber-500/30 transition-all">
          <div class="w-14 h-14 bg-amber-500/20 rounded-xl grid place-items-center">
            <span class="text-3xl">🍽️</span>
          </div>
          <div>
            <h3 class="font-bold text-lg">Orden Mesa</h3>
            <p class="text-zinc-300 text-sm">Para mesa</p>
          </div>
        </a>

        <a href="{{ route('cobro_caja.index') }}"
           class="flex items-center gap-4 p-6 bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl hover:border-amber-500/30 transition-all">
          <div class="w-14 h-14 bg-amber-500/20 rounded-xl grid place-items-center">
            <span class="text-3xl">💰</span>
          </div>
          <div>
            <h3 class="font-bold text-lg">Ver Cobros</h3>
            <p class="text-zinc-300 text-sm">Historial</p>
          </div>
        </a>
      </section>

      <!-- Mis Cobros y Pedidos Pendientes -->
      <section class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Mis Cobros Recientes -->
        <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">Mis Cobros Recientes</h2>
          <div class="space-y-3 max-h-96 overflow-y-auto">
            @forelse($misCobros as $cobro)
              <div class="flex items-center justify-between p-4 bg-zinc-900/60 border border-zinc-800 rounded-xl hover:border-amber-500/30 transition-colors">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center">
                    <span class="text-amber-400 font-bold text-sm">#{{ $cobro['pedido_id'] }}</span>
                  </div>
                  <div>
                    <p class="font-medium">{{ $cobro['mesa'] }}</p>
                    <p class="text-zinc-400 text-xs">{{ $cobro['metodo'] }} • {{ $cobro['fecha'] }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-amber-300 font-bold">${{ number_format($cobro['importe'], 2) }}</p>
                  <span class="text-xs text-green-400">✓ Cobrado</span>
                </div>
              </div>
            @empty
              <p class="text-zinc-400 text-center py-8">No hay cobros registrados</p>
            @endforelse
          </div>
        </div>

        <!-- Pedidos Pendientes de Cobro -->
        <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">⏳ Pendientes de Cobro</h2>
          <div class="space-y-3 max-h-96 overflow-y-auto">
            @forelse($pedidosPendientes as $pedido)
              <div class="flex items-center justify-between p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center">
                    <span class="text-amber-400 font-bold text-sm">#{{ $pedido['id'] }}</span>
                  </div>
                  <div>
                    <p class="font-medium">{{ $pedido['tipo'] }}</p>
                    <p class="text-zinc-400 text-xs">{{ $pedido['mesa'] }} • {{ $pedido['items_count'] }} items</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="font-bold">${{ number_format($pedido['total'], 2) }}</p>
                  <!-- FIX: la ruta requiere {pedido}, no 'pedido_id' -->
                  <a href="{{ route('cobro_caja.create', ['pedido' => $pedido['id']]) }}"
                     class="text-xs text-amber-300 hover:text-amber-200 font-medium">
                    Cobrar →
                  </a>
                </div>
              </div>
            @empty
              <p class="text-zinc-400 text-center py-8">✅ No hay pedidos pendientes</p>
            @endforelse
          </div>
        </div>
      </section>

      <!-- Ventas por Método y Hora -->
      <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Mis Ventas por Método -->
        <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">Mis Ventas por Método (Hoy)</h2>
          <div class="space-y-4">
            @php $totalMetodo = $misVentasPorMetodo->sum('total'); @endphp
            @forelse($misVentasPorMetodo as $metodo)
              @php
                $porcentaje = $totalMetodo > 0 ? ($metodo->total / $totalMetodo) * 100 : 0;
              @endphp
              <div>
                <div class="flex justify-between items-center mb-2">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center">
                      <span class="text-xl">
                        @if($metodo->metodo == 'efectivo') 💵
                        @elseif($metodo->metodo == 'pos') 💳
                        @else 📱
                        @endif
                      </span>
                    </div>
                    <div>
                      <p class="font-medium">
                        {{ $metodo->metodo == 'efectivo' ? 'Efectivo' : ($metodo->metodo == 'pos' ? 'Tarjeta' : 'QR/Transfer') }}
                      </p>
                      <p class="text-zinc-400 text-xs">{{ $metodo->cantidad }} transacciones</p>
                    </div>
                  </div>
                  <span class="font-bold">${{ number_format($metodo->total, 2) }}</span>
                </div>
                <div class="w-full bg-zinc-800 rounded-full h-2">
                  <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                </div>
              </div>
            @empty
              <p class="text-zinc-400 text-center py-8">No hay ventas registradas</p>
            @endforelse
          </div>
        </div>

        <!-- Mis Ventas por Hora -->
        <div class="bg-zinc-900/60 backdrop-blur border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold mb-6">Mis Ventas por Hora (Hoy)</h2>
          <div class="space-y-3">
            @forelse($ventasPorHora as $hora)
              <div class="flex items-center justify-between p-3 bg-zinc-900/60 border border-zinc-800 rounded-xl">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-amber-500/20 rounded-lg grid place-items-center">
                    <span class="text-amber-400 font-bold text-sm">{{ sprintf('%02d', $hora->hora) }}h</span>
                  </div>
                  <p class="text-zinc-400 text-sm">{{ $hora->cantidad }} cobros</p>
                </div>
                <span class="font-bold">${{ number_format($hora->total, 2) }}</span>
              </div>
            @empty
              <p class="text-zinc-400 text-center py-8">No hay actividad por hora</p>
            @endforelse
          </div>
        </div>
      </section>

      <!-- Especial del Día -->
      @if($especialHoy)
      <section class="mt-8 bg-amber-500/10 border border-amber-500/20 rounded-2xl p-6">
        <div class="flex items-center gap-2 mb-4">
          <span class="text-2xl">⭐</span>
          <h2 class="text-xl font-bold">Especial del Día</h2>
          <span class="ml-2 text-[10px] uppercase bg-amber-400/20 text-amber-300 border border-amber-300/30 px-2 py-0.5 rounded-full tracking-wide">Hoy</span>
        </div>
        <div class="grid md:grid-cols-12 gap-6 items-center">
          <div class="md:col-span-3">
            <div class="w-full aspect-square bg-amber-500/20 rounded-2xl overflow-hidden">
              @if($especialHoy->producto->imagen_url)
                <img src="{{ $especialHoy->producto->imagen_url }}" alt="{{ $especialHoy->producto->nombre }}" class="w-full h-full object-cover" loading="lazy">
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
                <span class="bg-red-500 text-white text-xs px-3 py-1.5 rounded-full font-bold">Ahorra ${{ number_format($especialHoy->getDescuentoMonto(), 2) }}</span>
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
    </main>
  </div>
</x-layouts.app>
