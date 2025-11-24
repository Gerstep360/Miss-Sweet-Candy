/* resources/js/layouts/app/sidebar.js */

export default function initSidebar() {
    // Prevenir doble carga
    if (window.__sidebar_loaded) return;
    window.__sidebar_loaded = true;

    const body = document.body;
    const overlay = document.getElementById('sidebar-overlay');

    // 1. Restaurar estado en Desktop (Memoria)
    if (window.innerWidth >= 1024) {
        const isClosed = localStorage.getItem('sidebar-desktop') === 'closed';
        if (isClosed) body.classList.add('body-sidebar-closed');
    }

    // 2. Función Global Toggle (Accesible desde HTML onclick)
    window.toggleSidebar = function() {
        const isMobile = window.innerWidth < 1024;

        if (isMobile) {
            // Lógica Móvil
            const isOpen = body.classList.contains('body-sidebar-open');
            
            if (isOpen) {
                body.classList.remove('body-sidebar-open');
                body.style.overflow = ''; // Permitir scroll body
            } else {
                body.classList.add('body-sidebar-open');
                body.style.overflow = 'hidden'; // Bloquear scroll body
            }
        } else {
            // Lógica Desktop
            const isClosed = body.classList.contains('body-sidebar-closed');
            
            if (isClosed) {
                body.classList.remove('body-sidebar-closed'); // ABRIR
                localStorage.setItem('sidebar-desktop', 'open');
            } else {
                body.classList.add('body-sidebar-closed'); // CERRAR
                localStorage.setItem('sidebar-desktop', 'closed');
            }
        }
    };

    // 3. Clic en Overlay (Solo móvil)
    if (overlay) {
        overlay.addEventListener('click', () => {
            body.classList.remove('body-sidebar-open');
            body.style.overflow = '';
        });
    }

    // 4. Tecla 'B' (Atajo)
    document.addEventListener('keydown', (e) => {
        if ((e.key.toLowerCase() === 'b') && !e.ctrlKey && e.target.tagName !== 'INPUT') {
            window.toggleSidebar();
        }
    });
}

// Inicializar
document.addEventListener('DOMContentLoaded', initSidebar);
document.addEventListener('livewire:navigated', initSidebar);