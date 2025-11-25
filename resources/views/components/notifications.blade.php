@php
    $userId = auth()->id();

    $notifications = \App\Models\Notificacion::where('usuario_destino_id', $userId)
        ->where('leido', false)
        ->latest('id')
        ->limit(15)
        ->get()
        ->map(
            fn($n) => [
                'id' => $n->id,
                'mensaje' => $n->mensaje,
                'time' => $n->created_at?->diffForHumans() ?? 'Hace un momento',
                'url' => '#',
            ],
        );

    $count = $notifications->count();
@endphp

<script>
    window.MissSweetNotifsConfig = {
        data: @json($notifications),
        count: {{ $count }},
        userId: {{ $userId ?? 0 }}
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('notificationSystem', () => ({
            openDesktop: false,
            openMobile: false,
            unreadCount: window.MissSweetNotifsConfig.count,
            notifications: window.MissSweetNotifsConfig.data,
            userId: window.MissSweetNotifsConfig.userId,

            init() {
                if (window.Echo && this.userId) {
                    window.Echo.private(`App.Models.User.${this.userId}`)
                        .listen('.notificacion.enviada', (e) => this.handleNewNotification(e));
                }
            },

            triggerPanel() {
                if (window.innerWidth >= 1024) {
                    this.openDesktop = true;
                    this.openMobile = false;
                } else {
                    this.openMobile = true;
                    this.openDesktop = false;
                }
            },

            handleNewNotification(data) {
                const audio = this.$refs.sound;
                if (audio) {
                    audio.currentTime = 0;
                    audio.play().catch(() => {});
                }

                this.unreadCount++;
                this.notifications.unshift({
                    id: data.id,
                    mensaje: data.mensaje,
                    time: 'Hace un momento',
                    url: data.url || '#'
                });

                if (window.Flux) Flux.toast({
                    text: data.mensaje,
                    heading: 'Notificación',
                    variant: 'info'
                });
            },

            async markAsRead(id) {
                const index = this.notifications.findIndex(n => n.id === id);
                if (index > -1) {
                    this.notifications.splice(index, 1);
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }

                try {
                    await fetch(`/notificaciones/${id}/marcar-leida`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content
                        }
                    });
                } catch (e) {
                    console.error(e);
                }
            }
        }));
    });
</script>

<div x-data="notificationSystem" @open-notifications.window="triggerPanel()" x-cloak>

    {{-- <audio x-ref="sound" src="{{ asset('sounds/notification.mp3') }}" preload="auto" class="hidden"></audio> --}}

    {{-- ==================== DESKTOP PANEL ==================== --}}

    {{-- Backdrop Desktop (z-99) --}}
    <div x-show="openDesktop" x-transition.opacity.duration.300ms @click="openDesktop = false"
        class="fixed inset-0 z-[99] hidden lg:block cursor-default bg-transparent"></div>

    {{-- Sidebar Derecho Desktop (z-100) --}}
    {{-- CORRECCIÓN: Se eliminó la clase 'hidden' del final. Ahora Alpine controla la visibilidad --}}
    <aside x-show="openDesktop" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full opacity-90" x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-90"
        class="fixed inset-y-0 right-0 w-full max-w-sm bg-zinc-900 border-l border-zinc-800 z-[100] shadow-2xl lg:flex flex-col"
        style="display: none;" @keydown.escape.window="openDesktop = false">

        {{-- Header --}}
        <div
            class="px-5 py-4 border-b border-zinc-800 flex items-center justify-between bg-zinc-900/95 backdrop-blur-sm shrink-0">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white">Notificaciones</h2>
                    <p class="text-[11px] text-zinc-500"
                        x-text="unreadCount > 0 ? unreadCount + ' sin leer' : 'Todo al día'"></p>
                </div>
            </div>
            <button @click="openDesktop = false"
                class="p-2 rounded-lg hover:bg-zinc-800 text-zinc-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Lista --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-3">
            <template x-if="notifications.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-zinc-600 space-y-3 opacity-60 py-10">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-sm">No hay notificaciones</p>
                </div>
            </template>

            <template x-for="n in notifications" :key="n.id">
                <a :href="n.url" @click="markAsRead(n.id)"
                    class="block p-3 rounded-xl bg-zinc-800/40 hover:bg-zinc-800 border border-zinc-800 hover:border-amber-500/30 transition-all group relative">
                    <div class="flex gap-3">
                        <div class="pt-1.5">
                            <div
                                class="w-2 h-2 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)] animate-pulse">
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-zinc-200 group-hover:text-white leading-snug" x-text="n.mensaje"></p>
                            <span class="text-[10px] text-zinc-500 mt-1.5 block font-medium" x-text="n.time"></span>
                        </div>
                    </div>
                </a>
            </template>
        </div>

        {{-- Footer --}}
        <div class="p-3 border-t border-zinc-800 bg-zinc-900 shrink-0">
            <a href="{{ route('notificaciones.index') }}" wire:navigate
                class="flex items-center justify-center w-full py-2 text-xs font-medium text-zinc-400 hover:text-amber-400 hover:bg-zinc-800 rounded-lg transition-colors">
                Ver historial completo
            </a>
        </div>
    </aside>

    {{-- ==================== MOBILE SHEET ==================== --}}
    {{-- Z-Index: 100 Para superar al sidebar móvil (z-50) --}}
    <div x-show="openMobile" class="fixed inset-0 z-[100] lg:hidden" role="dialog" aria-modal="true"
        style="display: none;">

        <div x-show="openMobile" x-transition.opacity @click="openMobile = false"
            class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

        <div x-show="openMobile" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="absolute left-0 right-0 bottom-0 w-full bg-zinc-900 border-t border-zinc-800 rounded-t-2xl shadow-2xl flex flex-col max-h-[85vh]">

            <div class="p-4 border-b border-zinc-800 flex justify-between items-center shrink-0">
                <h2 class="text-sm font-bold text-white">Notificaciones</h2>
                <button @click="openMobile = false" class="text-zinc-400 p-2 bg-zinc-800 rounded-lg"><svg
                        class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                <template x-if="notifications.length === 0">
                    <p class="text-center text-zinc-500 text-sm py-8">Sin notificaciones</p>
                </template>
                <template x-for="n in notifications" :key="n.id">
                    <a :href="n.url" @click="markAsRead(n.id)"
                        class="block p-3 rounded-xl bg-zinc-800 border border-zinc-700/50 text-zinc-100">
                        <p class="text-sm leading-snug" x-text="n.mensaje"></p>
                        <span class="text-xs text-zinc-500 mt-1 block" x-text="n.time"></span>
                    </a>
                </template>
            </div>

            <div class="p-4 border-t border-zinc-800 shrink-0 text-center">
                <a href="{{ route('notificaciones.index') }}" wire:navigate
                    class="text-amber-400 text-sm font-medium">Ver todas las notificaciones</a>
            </div>
        </div>
    </div>
</div>
