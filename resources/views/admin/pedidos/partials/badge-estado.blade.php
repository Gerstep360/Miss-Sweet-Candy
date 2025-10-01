{{-- Badge de Estado de Pedido --}}
{{-- Espera: $estado (pendiente | confirmado | en_preparacion | preparado | entregado | servido | anulado | cancelado | retirado) --}}
@php $e = strtolower($estado ?? ''); @endphp
@php
  $map = [
    'pendiente'       => 'bg-yellow-500/20 text-yellow-400 ring-1 ring-yellow-500/30',
    'confirmado'      => 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30',
    'en_preparacion'  => 'bg-orange-500/20 text-orange-400 ring-1 ring-orange-500/30',
    'preparado'       => 'bg-cyan-500/20 text-cyan-400 ring-1 ring-cyan-500/30',
    'entregado'       => 'bg-green-500/20 text-green-400 ring-1 ring-green-500/30',
    'servido'         => 'bg-green-500/20 text-green-400 ring-1 ring-green-500/30',
    'retirado'        => 'bg-green-500/20 text-green-400 ring-1 ring-green-500/30',
    'anulado'         => 'bg-red-500/20 text-red-400 ring-1 ring-red-500/30',
    'cancelado'       => 'bg-red-500/20 text-red-400 ring-1 ring-red-500/30',
  ];
  $cls = $map[$e] ?? 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40';
@endphp

<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $cls }}">
  <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
  {{ strtoupper(str_replace('_',' ', $estado ?? 'DESCONOCIDO')) }}
</span>
