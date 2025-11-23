<x-layouts.app.sidebar :title="$title ?? null">
    <style>
       [x-cloak]{
            display: none !important;
        }
    </style>
    <flux:main>
        {{ $slot }}
    </flux:main>
    
    {{-- Slot nombrado para modales --}}
    {{ $modals ?? '' }}
    
    {{-- Slot nombrado para scripts --}}
    {{ $footerScripts ?? '' }}
</x-layouts.app.sidebar>