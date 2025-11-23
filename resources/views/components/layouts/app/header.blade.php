<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('Search')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Search')" />
                </flux:tooltip>
                
                <!-- Notificaciones Desktop -->
                <flux:dropdown position="top" align="end">
                    <flux:button variant="ghost" size="sm" class="relative !h-10">
                        <flux:icon.bell class="size-5" />
                        @php
                            $notificacionesNoLeidas = \App\Models\Notificacion::where('usuario_destino_id', auth()->id())
                                ->where('leido', false)
                                ->count();
                        @endphp
                        @if($notificacionesNoLeidas > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white">
                                {{ $notificacionesNoLeidas > 9 ? '9+' : $notificacionesNoLeidas }}
                            </span>
                        @endif
                    </flux:button>

                    <flux:menu class="w-80 max-h-96 overflow-y-auto">
                        <div class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                            <span class="font-semibold text-sm">{{ __('Notificaciones') }}</span>
                            @if($notificacionesNoLeidas > 0)
                                <form method="POST" action="{{ route('notificaciones.marcar-todas-leidas') }}">
                                    @csrf
                                    <flux:button variant="ghost" size="xs" type="submit">
                                        {{ __('Marcar todas como leídas') }}
                                    </flux:button>
                                </form>
                            @endif
                        </div>

                        @php
                            $notificaciones = \App\Models\Notificacion::where('usuario_destino_id', auth()->id())
                                ->orderByDesc('created_at')
                                ->limit(10)
                                ->get();
                        @endphp

                        @forelse($notificaciones as $notificacion)
                            <flux:menu.item 
                                :href="route('notificaciones.show', $notificacion->id)" 
                                class="block px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ !$notificacion->leido ? 'bg-blue-50 dark:bg-blue-950' : '' }}"
                            >
                                <div class="flex items-start gap-2">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($notificacion->tipo === 'stock')
                                            <flux:icon.exclamation-triangle class="size-4 text-orange-600" />
                                        @elseif($notificacion->tipo === 'pedido')
                                            <flux:icon.shopping-cart class="size-4 text-blue-600" />
                                        @elseif($notificacion->tipo === 'reserva')
                                            <flux:icon.calendar class="size-4 text-green-600" />
                                        @else
                                            <flux:icon.bell class="size-4 text-zinc-600" />
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm {{ !$notificacion->leido ? 'font-semibold' : '' }} truncate">
                                            {{ $notificacion->mensaje }}
                                        </p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                            {{ $notificacion->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if(!$notificacion->leido)
                                        <div class="flex-shrink-0">
                                            <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </flux:menu.item>
                        @empty
                            <div class="px-3 py-4 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('No tienes notificaciones') }}
                            </div>
                        @endforelse

                        @if($notificaciones->count() > 0)
                            <flux:menu.separator />
                            <flux:menu.item :href="route('notificaciones.index')" class="text-center text-sm font-medium text-blue-600 dark:text-blue-400">
                                {{ __('Ver todas las notificaciones') }}
                            </flux:menu.item>
                        @endif
                    </flux:menu>
                </flux:dropdown>

                <flux:tooltip :content="__('Repository')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="folder-git-2"
                        href="https://github.com/laravel/livewire-starter-kit"
                        target="_blank"
                        :label="__('Repository')"
                    />
                </flux:tooltip>
                <flux:tooltip :content="__('Documentation')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="book-open-text"
                        href="https://laravel.com/docs/starter-kits#livewire"
                        target="_blank"
                        label="Documentation"
                    />
                </flux:tooltip>
            </flux:navbar>

            <!-- Desktop User Menu -->
            <flux:dropdown position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Configuración') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')">
                    <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                    </flux:navlist.item>
                    
                    <!-- Notificaciones Mobile -->
                    <flux:navlist.item icon="bell" :href="route('notificaciones.index')" :current="request()->routeIs('notificaciones.*')" wire:navigate>
                        <div class="flex items-center justify-between w-full">
                            <span>{{ __('Notificaciones') }}</span>
                            @php
                                $notificacionesNoLeidas = \App\Models\Notificacion::where('usuario_destino_id', auth()->id())
                                    ->where('leido', false)
                                    ->count();
                            @endphp
                            @if($notificacionesNoLeidas > 0)
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white">
                                    {{ $notificacionesNoLeidas > 9 ? '9+' : $notificacionesNoLeidas }}
                                </span>
                            @endif
                        </div>
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
