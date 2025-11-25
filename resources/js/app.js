// ========================================
// 1. ALPINE.JS (PRIMERO - Antes de todo)
// ========================================
import Alpine from 'alpinejs';

// Exponer Alpine globalmente
window.Alpine = Alpine;

// Iniciar Alpine
Alpine.start();

// ========================================
// 2. MODAL STORE (Depende de Alpine)
// ========================================
import './modal-store.js'

// ========================================
// 3. SIDEBAR
// ========================================
import './layouts/app/sidebar.js'

// ========================================
// 4. ECHO (Laravel WebSockets)
// ========================================
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */
import './echo';
