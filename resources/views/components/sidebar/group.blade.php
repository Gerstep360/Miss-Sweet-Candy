@props(['id'=>'group','icon'=>'folder','text'=>''])

@php
  $iconMap = [
    'cup'=>'building-storefront','cafeteria'=>'building-storefront',
    'operaciones'=>'rectangle-stack','ventas'=>'banknotes',
    'bitacora'=>'clipboard-document-list','cuenta'=>'user','folder'=>'folder',
  ];
  $resolvedIcon = $iconMap[$icon] ?? $icon;
  $safeIcon = in_array($resolvedIcon, [
    'users','user','home','folder','rectangle-stack','building-storefront',
    'banknotes','clipboard-document-list','tag','cube','clock','chart-bar',
    'document-text','book-open','arrow-top-right-on-square','plus-circle',
    'table-cells','beaker','key','shield-check'
  ], true) ? $resolvedIcon : 'folder';
@endphp

<div class="nav-group">
  <button class="nav-group-header"
          onclick="toggleSection('{{ $id }}')"
          aria-controls="{{ $id }}-section"
          aria-expanded="false"
          data-group="{{ $id }}">
    <div class="flex items-center gap-2 flex-1">
      <flux:icon :name="$safeIcon" class="w-4 h-4 text-amber-400" />
      <span>{{ $text }}</span>
    </div>
    <svg id="{{ $id }}-icon" class="w-4 h-4 nav-group-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
    </svg>
  </button>

  <div id="{{ $id }}-section" class="nav-group-content" hidden>
    <flux:navlist variant="outline" class="space-y-1 mt-2">
      {{ $slot }}
    </flux:navlist>
  </div>
</div>
