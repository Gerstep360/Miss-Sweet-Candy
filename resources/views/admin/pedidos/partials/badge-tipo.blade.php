{{-- Badge de Tipo de Pedido --}}
{{-- Espera: $tipo (mesa | mostrador | web | otro) --}}
@php $t = strtolower($tipo ?? ''); @endphp

<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
  {{ $t === 'mesa' ? 'bg-blue-500/20 text-blue-400 ring-1 ring-blue-500/30' : '' }}
  {{ $t === 'mostrador' ? 'bg-amber-500/20 text-amber-400 ring-1 ring-amber-500/30' : '' }}
  {{ $t === 'web' ? 'bg-purple-500/20 text-purple-400 ring-1 ring-purple-500/30' : '' }}
  {{ !in_array($t, ['mesa','mostrador','web']) ? 'bg-zinc-700/30 text-zinc-300 ring-1 ring-zinc-600/40' : '' }}">
  
  @if($t === 'mesa')
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
    </svg>
    MESA
  @elseif($t === 'mostrador')
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
    </svg>
    MOSTRADOR
  @elseif($t === 'web')
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
    </svg>
    WEB
  @else
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ strtoupper($t ?: 'OTRO') }}
  @endif
</span>
