export default function registerAlpineComponents(Alpine) {
    // ==========================================
    // 1. Layout Principal (Sidebar)
    // ==========================================
    Alpine.data('mainLayout', (initialUnread) => ({
        mobileOpen: false,
        unread: parseInt(initialUnread) || 0,
        toggle() { this.mobileOpen = !this.mobileOpen; },
        close() { this.mobileOpen = false; }
    }));

    // ==========================================
    // 2. Buscador Global de Clientes
    // ==========================================
    Alpine.data('searchClient', (endpointUrl) => ({
        isOpen: false,
        query: '',
        results: [],
        isLoading: false,
        searchUrl: endpointUrl,

        init() {
            window.buscarClienteModal = () => this.open();
            window.cerrarBuscarClienteModal = () => this.close();
            window.addEventListener('open-search-client', () => this.open());
        },
        open() {
            this.isOpen = true;
            this.$nextTick(() => { if(this.$refs.searchInput) this.$refs.searchInput.focus(); });
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
            setTimeout(() => { this.query = ''; this.results = []; }, 300);
        },
        async performSearch() {
            if (this.query.length < 2) { this.results = []; return; }
            this.isLoading = true;
            try {
                const url = new URL(this.searchUrl, window.location.origin);
                url.searchParams.append('q', this.query);
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                
                const response = await fetch(url, {
                    headers: { 
                        'Accept': 'application/json', 
                        'X-CSRF-TOKEN': token 
                    }
                });
                const data = await response.json();
                this.results = data.data; 
            } catch (e) { console.error(e); this.results = []; } 
            finally { this.isLoading = false; }
        },
        getInitials(name) { return name ? name.substring(0, 2).toUpperCase() : '??'; },
        hasAllergy(c) { return c.tiene_alergias_graves || c.tiene_alergias; }
    }));

    // ==========================================
    // 3. Sistema de Notificaciones
    // ==========================================
    Alpine.data('notificationSystem', () => ({
        openDesktop: false,
        openMobile: false,
        unreadCount: 0,
        notifications: [],
        userId: null,

        init() {
            console.log('Notification System Initialized');
            // Cargar configuración desde window si existe
            if (window.MissSweetNotifsConfig) {
                this.unreadCount = window.MissSweetNotifsConfig.count || 0;
                this.notifications = window.MissSweetNotifsConfig.data || [];
                this.userId = window.MissSweetNotifsConfig.userId;
            }

            if (window.Echo && this.userId) {
                window.Echo.private(`App.Models.User.${this.userId}`)
                    .listen('.notificacion.enviada', (e) => this.handleNewNotification(e));
            }
        },

        triggerPanel() {
            console.log('Trigger Panel Called');
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

            if (window.Flux) Flux.toast({ text: data.mensaje, heading: 'Notificación', variant: 'info' });
        },

        async markAsRead(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index > -1) {
                this.notifications.splice(index, 1);
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                await fetch(`/notificaciones/${id}/marcar-leida`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
            } catch (e) { console.error(e); }
        }
    }));
}
