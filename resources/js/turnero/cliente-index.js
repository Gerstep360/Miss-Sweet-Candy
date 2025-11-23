// resources/js/turnero/cliente-index.js - Realtime updates con Reverb

const cardsContainer = document.getElementById('cards-container');
const emptyState = document.getElementById('empty-state');
const pedidosCount = document.getElementById('pedidos-count');

// Estado local de pedidos (Map por token)
const state = new Map();

/**
 * Formatear ETA
 */
function fmtEta(m) {
    if (m == null || m === '') return '—';
    const n = Number(m);
    if (Number.isNaN(n)) return '—';
    if (n <= 1) return '1 min';
    return `${n} min`;
}

/**
 * Obtener clase de estado
 */
function estadoClass(estado) {
    const map = {
        'pendiente': 'bg-amber-500/10 text-amber-200 border-amber-500/30',
        'confirmado': 'bg-amber-500/10 text-amber-200 border-amber-500/30',
        'en_preparacion': 'bg-sky-500/10 text-sky-200 border-sky-500/30',
        'preparado': 'bg-green-500/10 text-green-200 border-green-500/30',
    };
    return map[estado] || 'bg-zinc-500/10 text-zinc-200 border-zinc-500/30';
}

/**
 * Obtener label de estado
 */
function estadoLabel(estado) {
    const map = {
        'pendiente': 'EN COLA',
        'confirmado': 'CONFIRMADO',
        'en_preparacion': 'PREPARANDO',
        'preparado': 'LISTO PARA RETIRAR',
    };
    return map[estado] || estado.toUpperCase();
}

/**
 * Crear elemento de tarjeta de pedido
 */
function createCard(pedido) {
    const isReady = pedido.estado === 'preparado';
    const card = document.createElement('a');
    
    card.href = `/turnos/cola/${pedido.token}`;
    card.dataset.token = pedido.token;
    card.className = `group relative block rounded-2xl border border-zinc-800 bg-zinc-950/90 backdrop-blur hover:bg-zinc-900/60 transition overflow-hidden card-enter ${
        isReady ? 'ring-2 ring-green-500/30 shadow-lg shadow-green-500/10 card-ready' : ''
    }`;

    const hora = pedido.created_at ? new Date(pedido.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) : '—';

    card.innerHTML = `
        ${isReady ? '<div class="absolute top-0 right-0 w-24 h-24 bg-green-500/5 blur-3xl"></div>' : ''}
        
        <div class="relative p-5">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <div class="text-[10px] tracking-[0.3em] text-zinc-400 uppercase">Turno</div>
                    <div class="text-3xl sm:text-4xl font-black tracking-wider text-white mt-0.5">
                        ${pedido.token}
                    </div>
                </div>

                <span class="estado-pill px-2.5 py-1 rounded-full border text-[10px] font-bold tracking-widest ${estadoClass(pedido.estado)}">
                    ${estadoLabel(pedido.estado)}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="rounded-xl border border-zinc-800 bg-zinc-900/40 px-3 py-2">
                    <div class="text-[9px] tracking-widest text-zinc-400 uppercase">ETA</div>
                    <div class="eta-value text-lg font-bold text-amber-200 tabular-nums mt-0.5">
                        ${fmtEta(pedido.eta_minutes)}
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-800 bg-zinc-900/40 px-3 py-2">
                    <div class="text-[9px] tracking-widest text-zinc-400 uppercase">Tipo</div>
                    <div class="text-sm font-bold text-zinc-100 uppercase mt-0.5">
                        ${pedido.tipo}
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-zinc-400">
                <span>${hora}</span>
                <span class="flex items-center gap-1 text-zinc-300 group-hover:text-amber-200 transition">
                    Ver detalles
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </div>
    `;

    return card;
}

/**
 * Actualizar tarjeta existente
 */
function updateCard(card, pedido) {
    const isReady = pedido.estado === 'preparado';
    
    // Actualizar estado visual
    if (isReady && !card.classList.contains('card-ready')) {
        card.classList.add('ring-2', 'ring-green-500/30', 'shadow-lg', 'shadow-green-500/10', 'card-ready', 'card-pulse');
        setTimeout(() => card.classList.remove('card-pulse'), 1100);
        
        // Agregar glow si no existe
        if (!card.querySelector('.bg-green-500\\/5')) {
            const glow = document.createElement('div');
            glow.className = 'absolute top-0 right-0 w-24 h-24 bg-green-500/5 blur-3xl';
            card.insertBefore(glow, card.firstChild);
        }
    }

    // Actualizar pill de estado
    const pill = card.querySelector('.estado-pill');
    if (pill) {
        pill.className = `estado-pill px-2.5 py-1 rounded-full border text-[10px] font-bold tracking-widest ${estadoClass(pedido.estado)}`;
        pill.textContent = estadoLabel(pedido.estado);
    }

    // Actualizar ETA
    const etaEl = card.querySelector('.eta-value');
    if (etaEl) {
        etaEl.textContent = fmtEta(pedido.eta_minutes);
    }
}

/**
 * Renderizar todas las tarjetas
 */
function render() {
    if (!cardsContainer) return;

    const pedidos = Array.from(state.values()).sort((a, b) => 
        new Date(b.created_at) - new Date(a.created_at)
    );

    // Mostrar/ocultar empty state
    if (emptyState) {
        emptyState.classList.toggle('hidden', pedidos.length > 0);
    }

    // Actualizar contador
    if (pedidosCount) {
        pedidosCount.textContent = pedidos.length;
    }

    // Actualizar o crear tarjetas
    pedidos.forEach(pedido => {
        let card = cardsContainer.querySelector(`[data-token="${pedido.token}"]`);
        
        if (!card) {
            // Crear nueva tarjeta
            card = createCard(pedido);
            cardsContainer.appendChild(card);
        } else {
            // Actualizar existente
            updateCard(card, pedido);
        }
    });

    // Remover tarjetas que ya no existen
    const existingCards = cardsContainer.querySelectorAll('[data-token]');
    existingCards.forEach(card => {
        const token = card.dataset.token;
        if (!state.has(token)) {
            card.classList.add('card-leave');
            setTimeout(() => card.remove(), 300);
        }
    });
}

/**
 * Agregar o actualizar pedido
 */
function upsertPedido(pedido) {
    if (!pedido || !pedido.token) return;
    
    const wasReady = state.has(pedido.token) && state.get(pedido.token).estado === 'preparado';
    const isNowReady = pedido.estado === 'preparado';
    
    state.set(pedido.token, pedido);
    render();

    // Notificación si pasó a LISTO
    if (!wasReady && isNowReady) {
        console.log(`🎉 Pedido ${pedido.token} está LISTO`);
        // Aquí podrías agregar un ding o notificación de navegador
    }
}

/**
 * Remover pedido
 */
function removePedido(token) {
    state.delete(token);
    render();
}

/**
 * Cargar estado inicial desde el DOM
 */
function loadInitialState() {
    const initialData = document.getElementById('initial-pedidos-data');
    if (initialData) {
        try {
            const pedidos = JSON.parse(initialData.textContent);
            pedidos.forEach(p => state.set(p.token, p));
            render();
        } catch (e) {
            console.error('Error al parsear pedidos iniciales:', e);
        }
    }
}

// ========================================
// REVERB / ECHO REALTIME
// ========================================
const EchoRef = window.Echo;

if (EchoRef) {
    console.log('✅ Conectando a canal turnero...');

    EchoRef.channel('turnero')
        .listen('.pedido.actualizado', (e) => {
            console.log('📡 Evento recibido:', e);
            
            const pedido = e?.pedido || e?.data || e;
            
            if (!pedido || !pedido.token) {
                console.warn('Payload inválido:', e);
                return;
            }

            // Solo actualizar si es del usuario actual
            const userId = document.querySelector('[data-user-id]')?.dataset.userId;
            if (userId && String(pedido.cliente_id) === String(userId)) {
                upsertPedido(pedido);
            }
        });

    console.log('✅ Escuchando eventos en tiempo real');
} else {
    console.warn('⚠️ Echo no disponible, no habrá actualizaciones en tiempo real');
}

// ========================================
// INIT
// ========================================
loadInitialState();

// Reloj
setInterval(() => {
    const clock = document.getElementById('clock');
    if (clock) {
        clock.textContent = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
    }
}, 1000);
