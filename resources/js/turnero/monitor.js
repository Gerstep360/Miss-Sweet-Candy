// resources/js/turnero/monitor.js

const cols = ['pendiente', 'preparando', 'preparado'];

const state = {
  pendiente: new Map(),
  preparando: new Map(),
  preparado: new Map(),
};

const els = {
  pendiente: {
    ul: document.getElementById('col-pendiente'),
    empty: document.querySelector('[data-empty="pendiente"]'),
    count: document.getElementById('count-pendiente'),
  },
  preparando: {
    ul: document.getElementById('col-preparando'),
    empty: document.querySelector('[data-empty="preparando"]'),
    count: document.getElementById('count-preparando'),
  },
  preparado: {
    ul: document.getElementById('col-preparado'),
    empty: document.querySelector('[data-empty="preparado"]'),
    count: document.getElementById('count-preparado'),
  },
};

const nowCallingEl = document.getElementById('now-calling');
const nowEtaEl = document.getElementById('now-eta');
const tickerEl = document.getElementById('ticker');
const ding = document.getElementById('ding');

let soundEnabled = false;
function enableSoundOnce(){
  soundEnabled = true;
  try{
    ding.muted = true;
    ding.play().then(() => {
      ding.pause();
      ding.currentTime = 0;
      ding.muted = false;
    }).catch(()=>{});
  }catch(e){}
}
document.addEventListener('click', enableSoundOnce, { once:true });
document.addEventListener('keydown', enableSoundOnce, { once:true });

function safeArr(x){ return Array.isArray(x) ? x : []; }

function fmtEta(m){
  if(m == null || m <= 0) return '—';
  if(m <= 1) return '1 min';
  return `${m} min`;
}

function tipoChipClass(tipo=''){
  const t = tipo.toLowerCase();
  if(t === 'mesa') return 'chip mesa';
  if(t === 'mostrador') return 'chip mostrador';
  return 'chip web';
}

// 🔥 CORRECCIÓN CLAVE: Definir claramente qué va a cada columna
// Si devuelve NULL, significa que el ticket debe salir del tablero (entregado/cancelado)
function columnForEstado(estado){
  if(['pendiente','confirmado'].includes(estado)) return 'pendiente';
  if(estado === 'en_preparacion') return 'preparando';
  if(['preparado', 'listo'].includes(estado)) return 'preparado';
  return null; // Para 'entregado', 'servido', 'retirado', 'cancelado', etc.
}

function findColumn(token){
  for(const c of cols){
    if(state[c].has(token)) return c;
  }
  return null;
}

function buildTicketEl(p, isReadyCol=false, highlight=false){
  const li = document.createElement('li');
  const tipo = (p.tipo || 'web').toLowerCase();

  li.className =
    'ticket ' +
    (isReadyCol ? 'ticket-ready' : '') +
    (highlight ? ' ticket-glow' : '');

  li.dataset.tipo = tipo;
  li.dataset.token = p.token;
  li.setAttribute('data-token', p.token); // Importante para el selector CSS

  // Hora local formateada
  const created = p.created_at ? new Date(p.created_at) : new Date();
  const hora = created.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

  li.innerHTML = `
    <div class="flex items-center justify-between gap-3">
      <div class="ticket-token">${p.token ?? '—'}</div>
      <div class="text-right">
        <div class="text-[10px] text-zinc-400 tracking-widest">ETA</div>
        <div class="ticket-eta text-amber-200">${fmtEta(p.eta)}</div>
      </div>
    </div>
    <div class="ticket-meta">
      <span class="${tipoChipClass(tipo)}">${tipo.toUpperCase()}</span>
      <span>${hora}</span>
    </div>
  `;
  return li;
}

function updateTicketEl(li, p){
  li.querySelector('.ticket-token').textContent = p.token ?? '—';
  li.querySelector('.ticket-eta').textContent = fmtEta(p.eta);

  const tipo = (p.tipo || 'web').toLowerCase();
  li.dataset.tipo = tipo;
  
  const chip = li.querySelector('.chip');
  if(chip) {
      chip.className = tipoChipClass(tipo);
      chip.textContent = tipo.toUpperCase();
  }
}

function showEmptyIfNeeded(col){
  const hasItems = state[col].size > 0;
  els[col].empty.classList.toggle('hidden', hasItems);
}

function setCounts(){
  els.pendiente.count.textContent = state.pendiente.size;
  els.preparando.count.textContent = state.preparando.size;
  els.preparado.count.textContent = state.preparado.size;
}

function updateNowCalling(){
  const firstReady = els.preparado.ul.querySelector('li.ticket');
  if(!firstReady){
    nowCallingEl.textContent = '—';
    nowEtaEl.textContent = '—';
    return;
  }
  // Tomamos datos del DOM o del state
  const token = firstReady.dataset.token;
  const p = state.preparado.get(token);
  
  nowCallingEl.textContent = token;
  nowEtaEl.textContent = p ? fmtEta(p.eta) : '—';
}

function updateTicker(){
  // Tomamos los últimos 12 listos
  const tokens = Array.from(state.preparado.keys()).slice(0, 12).join('   ·   ');
  tickerEl.textContent = tokens || '—';
}

function addTicket(col, p, highlight=false){
  state[col].set(p.token, p);

  const li = buildTicketEl(p, col === 'preparado', highlight);
  
  // Orden: Preparado arriba (LIFO para llamar la atención), los demás abajo (FIFO)
  if(col === 'preparado') els[col].ul.prepend(li);
  else els[col].ul.append(li);

  showEmptyIfNeeded(col);
}

function removeTicket(col, token){
  if(!state[col].has(token)) return;
  
  state[col].delete(token);

  const li = els[col].ul.querySelector(`[data-token="${token}"]`);
  if(li){
    li.classList.add('leave'); // Clase CSS para animación de salida
    li.addEventListener('transitionend', () => li.remove(), { once:true });
    // Fallback por si transitionend falla
    setTimeout(() => li.remove(), 500);
  }

  showEmptyIfNeeded(col);
}

// 🔥 LÓGICA DE ACTUALIZACIÓN EN TIEMPO REAL
function upsertTicket(p){
  if(!p || !p.token) return;

  const token = p.token;
  const toCol = columnForEstado(p.estado); // Puede ser NULL si es 'entregado'
  const fromCol = findColumn(token);

  // CASO 1: El pedido se completó (entregado/retirado/cancelado) -> ELIMINAR
  if (!toCol) {
    if (fromCol) {
      console.log(`👋 Pedido ${token} completado/retirado. Eliminando del monitor.`);
      removeTicket(fromCol, token);
      setCounts();
      updateNowCalling();
      updateTicker();
    }
    return;
  }

  const isNewReady = (toCol === 'preparado' && fromCol !== 'preparado');

  // CASO 2: Mover de columna
  if(fromCol && fromCol !== toCol){
    removeTicket(fromCol, token);
    // Pequeño delay visual para que se vea el movimiento
    setTimeout(() => addTicket(toCol, p, isNewReady), 100);
  } 
  // CASO 3: Actualizar en la misma columna (ej: cambió ETA)
  else if(fromCol){
    state[fromCol].set(token, p);
    const li = els[fromCol].ul.querySelector(`[data-token="${token}"]`);
    if(li) updateTicketEl(li, p);
  } 
  // CASO 4: Nuevo ticket
  else {
    addTicket(toCol, p, isNewReady);
  }

  setCounts();
  
  // Si entró a "Listo", actualizamos el banner principal y tocamos timbre
  if(toCol === 'preparado') {
      updateNowCalling();
      updateTicker();
      if(isNewReady && soundEnabled){
        try { 
            ding.currentTime = 0; 
            ding.play().catch(e => console.log("Audio play error", e)); 
        } catch(e){}
      }
  }
}

// Sincronización inicial completa
async function silentSync(){
  try {
    const res = await fetch('/turnos/feed', { headers:{Accept:'application/json'} });
    const feed = await res.json();

    // Normalizar arrays
    feed.pendiente  = safeArr(feed.pendiente);
    feed.preparando = safeArr(feed.preparando);
    feed.preparado  = safeArr(feed.preparado);

    // Reconciliar cada columna
    for(const col of cols){
      const list = feed[col];
      // Crear set de tokens que DEBEN estar
      const newTokens = new Set(list.map(x => x.token));

      // 1. Borrar los que sobran en el monitor actual
      for(const token of [...state[col].keys()]){
        if(!newTokens.has(token)) removeTicket(col, token);
      }

      // 2. Agregar o actualizar los que vienen del server
      for(const p of list){
        // El campo puede venir como eta_minutes o eta, normalizamos
        p.eta = p.eta_minutes || p.eta; 
        
        const existingCol = findColumn(p.token);
        
        if(existingCol && existingCol !== col){
          removeTicket(existingCol, p.token);
          addTicket(col, p, false);
        } else if(existingCol){
          // Ya está, solo actualizamos data
          state[col].set(p.token, p);
          const li = els[col].ul.querySelector(`[data-token="${p.token}"]`);
          if(li) updateTicketEl(li, p);
        } else {
          addTicket(col, p, false);
        }
      }
      showEmptyIfNeeded(col);
    }

    setCounts();
    updateNowCalling();
    updateTicker();
  } catch (e) {
    console.error("Error sync:", e);
  }
}

// -------- INIT --------
silentSync();

// -------- ECHO realtime --------
if(window.Echo){
  // Canal Público 'turnero'
  window.Echo.channel('turnero')
    .listen('.pedido.actualizado', (e) => {
      console.log("⚡ Evento recibido:", e);
      // Normalizamos la estructura por si acaso viene anidada
      const p = e.pedido || e; 
      
      // Mapeamos el campo 'eta' si viene distinto
      if(p.eta_minutes && !p.eta) p.eta = p.eta_minutes;
      
      if(p && p.token) {
          upsertTicket(p);
      } else {
          // Si el payload es raro, resincronizamos todo por seguridad
          silentSync();
      }
    });
}

// Reloj
setInterval(() => {
  const clock = document.getElementById('clock');
  if(clock){
    clock.textContent = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
  }
}, 1000);

// Auto-refresco de seguridad cada 60s por si se pierde conexión socket
setInterval(silentSync, 60000);