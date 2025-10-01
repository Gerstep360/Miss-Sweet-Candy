<x-layouts.app :title="__('Dashboard Cajero')">
  <div class="min-h-screen bg-zinc-950">
    <!-- Header Cajero -->
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 border-b border-blue-700 px-6 py-6">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <h1 class="text-2xl font-bold text-white">Panel de Cajero</h1>
                <p class="text-white/80 text-sm">{{ auth()->user()->name }}</p>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-3 bg-white/20 backdrop-blur rounded-xl px-4 py-3 border border-white/30">
            <div class="w-3 h-3 rounded-full {{ $hours->isOpenAt($now) ? 'bg-green-400 animate-pulse' : 'bg-red-400' }}"></div>
            <div class="text-white">
              <span class="font-semibold">{{ $hours->isOpenAt($now) ? 'Abierto' : 'Cerrado' }}</span>
              <span class="text-white/80 text-sm ml-2">{{ $now->format('H:i') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Métricas del Cajero -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500/10 to-cyan-500/10 border border-blue-500/20 rounded-2xl p-6">
          <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <h3 class="text-3xl font-bold text-white mb-1">{{ $misCobrosHoy }}</h3>
          <p class="text-zinc-400 text-sm">Mis Cobros Hoy</p>
        </div>

        <div class="bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/20 rounded-2xl p-6">
          <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 10v1"/>
            </svg>
          </div>
          <h3 class="text-3xl font-bold text-white mb-1">${{ number_format($misVentasHoy, 2) }}</h3>
          <p class="text-zinc-400 text-sm">Mis Ventas Hoy</p>
        </div>

        <div class="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 border border-yellow-500/20 rounded-2xl p-6">
          <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center mb-4">
            <span class="text-2xl">💵</span>
          </div>
          <h3 class="text-3xl font-bold text-white mb-1">${{ number_format($efectivoRecaudado, 2) }}</h3>
          <p class="text-zinc-400 text-sm">Efectivo Recaudado</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-500/20 rounded-2xl p-6">
          <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center mb-4">
            <span class="text-2xl">💳</span>
          </div>
          <h3 class="text-3xl font-bold text-white mb-1">${{ number_format($tarjetasRecaudadas, 2) }}</h3>
          <p class="text-zinc-400 text-sm">Tarjetas Recaudadas</p>
        </div>
      </div>

      <!-- Acciones Rápidas -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('pedido_mostrador.create') }}" class="flex items-center gap-4 p-6 bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl hover:from-amber-600 hover:to-orange-600 transition-all transform hover:scale-105">
          <div class="w-14 h-14 bg-black/20 rounded-xl flex items-center justify-center">
            <span class="text-3xl">🥤</span>
          </div>
          <div>
            <h3 class="text-white font-bold text-lg">Nueva Orden</h3>
            <p class="text-white/80 text-sm">Para llevar</p>
          </div>
        </a>

        <a href="{{ route('pedido-mesas.create') }}" class="flex items-center gap-4 p-6 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl hover:from-blue-600 hover:to-cyan-600 transition-all transform hover:scale-105">
          <div class="w-14 h-14 bg-black/20 rounded-xl flex items-center justify-center">
            <span class="text-3xl">🍽️</span>
          </div>
          <div>
            <h3 class="text-white font-bold text-lg">Orden Mesa</h3>
            <p class="text-white/80 text-sm">Para mesa</p>
          </div>
        </a>

        <a href="{{ route('cobro_caja.index') }}" class="flex items-center gap-4 p-6 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl hover:from-green-600 hover:to-emerald-600 transition-all transform hover:scale-105">
          <div class="w-14 h-14 bg-black/20 rounded-xl flex items-center justify-center">
            <span class="text-3xl">💰</span>
          </div>
          <div>
            <h3 class="text-white font-bold text-lg">Ver Cobros</h3>
            <p class="text-white/80 text-sm">Historial</p>
          </div>
        </a>
      </div>

      <!-- Mis Cobros y Pedidos Pendientes -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Mis Cobros Recientes -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold text-white mb-6">Mis Cobros Recientes</h2>
          <div class="space-y-3 max-h-96 overflow-y-auto">
            @forelse($misCobros as $cobro)
              <div class="flex items-center justify-between p-4 bg-zinc-800/50 rounded-xl">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <span class="text-blue-400 font-bold text-sm">#{{ $cobro['pedido_id'] }}</span>
                  </div>
                  <div>
                    <p class="text-white font-medium">{{ $cobro['mesa'] }}</p>
                    <p class="text-zinc-400 text-xs">{{ $cobro['metodo'] }} • {{ $cobro['fecha'] }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-green-400 font-bold">${{ number_format($cobro['importe'], 0) }}</p>
                  <span class="text-xs text-green-400">✓ Cobrado</span>
                </div>
              </div>
            @empty
              <p class="text-zinc-400 text-center py-8">No hay cobros registrados</p>
            @endforelse
          </div>
        </div>

        <!-- Pedidos Pendientes de Cobro -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold text-white mb-6">⏳ Pendientes de Cobro</h2>
          <div class="space-y-3 max-h-96 overflow-y-auto">
            @forelse($pedidosPendientes as $pedido)
              <div class="flex items-center justify-between p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-xl">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <span class="text-yellow-400 font-bold text-sm">#{{ $pedido['id'] }}</span>
                  </div>
                  <div>
                    <p class="text-white font-medium">{{ $pedido['tipo'] }}</p>
                    <p class="text-zinc-400 text-xs">{{ $pedido['mesa'] }} • {{ $pedido['items_count'] }} items</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-white font-bold">${{ number_format($pedido['total'], 0) }}</p>
                  <a href="{{ route('cobro_caja.create', ['pedido_id' => $pedido['id']]) }}" class="text-xs text-amber-400 hover:text-amber-300">
                    Cobrar →
                  </a>
                </div>
              </div>
            @empty
              <p class="text-zinc-400 text-center py-8">✅ No hay pedidos pendientes</p>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Ventas por Método y Hora -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Mis Ventas por Método -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold text-white mb-6">Mis Ventas por Método (Hoy)</h2>
          <div class="space-y-4">
            @forelse($misVentasPorMetodo as $metodo)
              @php 
                $total = $misVentasPorMetodo->sum('total');
                $porcentaje = $total > 0 ? ($metodo->total / $total) * 100 : 0;
              @endphp
              <div>
                <div class="flex justify-between items-center mb-2">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                      <span class="text-xl">
                        @if($metodo->metodo == 'efectivo') 💵
                        @elseif($metodo->metodo == 'pos') 💳
                        @else 📱
                        @endif
                      </span>
                    </div>
                    <div>
                      <p class="text-white font-medium">
                        {{ $metodo->metodo == 'efectivo' ? 'Efectivo' : ($metodo->metodo == 'pos' ? 'Tarjeta' : 'QR/Transfer') }}
                      </p>
                      <p class="text-zinc-400 text-xs">{{ $metodo->cantidad }} transacciones</p>
                    </div>
                  </div>
                  <span class="text-white font-bold">${{ number_format($metodo->total, 0) }}</span>
                </div>
                <div class="w-full bg-zinc-800 rounded-full h-2">
                  <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                </div>
              </div>
            @empty
              <p class="text-zinc-400 text-center py-8">No hay ventas registradas</p>
            @endforelse
          </div>
        </div>

        <!-- Ventas por Hora -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold text-white mb-6">Mis Ventas por Hora (Hoy)</h2>
          <div class="space-y-3">
            @forelse($ventasPorHora as $hora)
              <div class="flex items-center justify-between p-3 bg-zinc-800/50 rounded-xl">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <span class="text-blue-400 font-bold text-sm">{{ sprintf('%02d', $hora->hora) }}h</span>
                  </div>
                  <p class="text-zinc-400 text-sm">{{ $hora->cantidad }} cobros</p>
                </div>
                <span class="text-white font-bold">${{ number_format($hora->total, 0) }}</span>
              </div>
            @empty
              <p class="text-zinc-400 text-center py-8">No hay actividad por hora</p>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Especial del Día -->
      @if($especialHoy)
      <div class="mt-8 bg-gradient-to-r from-amber-500/10 to-orange-500/10 border border-amber-500/20 rounded-2xl p-6">
        <h2 class="text-xl font-bold text-white mb-4">⭐ Especial del Día</h2>
        <div class="flex items-center gap-6">
          <div class="w-24 h-24 bg-amber-500/20 rounded-2xl overflow-hidden">
            @if($especialHoy->producto->imagen_url)
              <img src="{{ $especialHoy->producto->imagen_url }}" alt="{{ $especialHoy->producto->nombre }}" class="w-full h-full object-cover">
            @else
              <div class="w-full h-full flex items-center justify-center">
                <span class="text-3xl">☕</span>
              </div>
            @endif
          </div>
          <div class="flex-1">
            <h3 class="text-xl font-bold text-white mb-1">{{ $especialHoy->producto->nombre }}</h3>
            <p class="text-zinc-300 text-sm mb-2">{{ $especialHoy->getDescripcionCompleta() }}</p>
            <div class="flex items-center gap-3">
              @if($especialHoy->tieneDescuento())
                <span class="text-zinc-400 line-through">${{ number_format($especialHoy->producto->precio, 2) }}</span>
                <span class="text-2xl font-bold text-amber-400">${{ number_format($especialHoy->getPrecioFinal(), 2) }}</span>
              @else
                <span class="text-2xl font-bold text-amber-400">${{ number_format($especialHoy->producto->precio, 2) }}</span>
              @endif
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</x-layouts.app>