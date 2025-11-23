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
document.addEventListener('pointerdown', enableSoundOnce, { once:true });
document.addEventListener('keydown', enableSoundOnce, { once:true });

function safeArr(x){ return Array.isArray(x) ? x : []; }

function fmtEta(m){
  if(m == null) return '—';
  if(m <= 1) return '1 min';
  return `${m} min`;
}

function tipoChipClass(tipo=''){
  const t = tipo.toLowerCase();
  if(t === 'mesa') return 'chip mesa';
  if(t === 'mostrador') return 'chip mostrador';
  return 'chip web';
}

function columnForEstado(estado){
  if(['pendiente','confirmado'].includes(estado)) return 'pendiente';
  if(estado === 'en_preparacion') return 'preparando';
  if(estado === 'preparado') return 'preparado';
  return 'pendiente';
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

  const created = p.created_at ? new Date(p.created_at) : null;
  const hora = created ? created.toLocaleTimeString() : '';

  li.innerHTML = `
    <div class="flex items-center justify-between gap-3">
      <div class="ticket-token">${p.token ?? '—'}</div>
      <div class="text-right">
        <div class="text-[10px] text-zinc-400 tracking-widest">ETA</div>
        <div class="ticket-eta text-amber-200">${fmtEta(p.eta_minutes)}</div>
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
  li.querySelector('.ticket-eta').textContent = fmtEta(p.eta_minutes);

  const tipo = (p.tipo || 'web').toLowerCase();
  li.dataset.tipo = tipo;

  const chip = li.querySelector('.chip');
  chip.className = tipoChipClass(tipo);
  chip.textContent = tipo.toUpperCase();
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
  nowCallingEl.textContent = firstReady.dataset.token;
  const etaText = firstReady.querySelector('.ticket-eta')?.textContent ?? '—';
  nowEtaEl.textContent = etaText;
}

function updateTicker(){
  const tokens = [...els.preparado.ul.querySelectorAll('li.ticket')]
    .slice(0, 12)
    .map(li => li.dataset.token)
    .join('   ·   ');
  tickerEl.textContent = tokens || '—';
}

function addTicket(col, p, highlight=false){
  state[col].set(p.token, p);

  const li = buildTicketEl(p, col === 'preparado', highlight);
  // preparado arriba, los otros abajo (mantén tu orden visual)
  if(col === 'preparado') els[col].ul.prepend(li);
  else els[col].ul.append(li);

  showEmptyIfNeeded(col);
}

function removeTicket(col, token){
  state[col].delete(token);

  const li = els[col].ul.querySelector(`[data-token="${token}"]`);
  if(li){
    li.classList.add('leave');
    li.addEventListener('transitionend', () => li.remove(), { once:true });
  }

  showEmptyIfNeeded(col);
}

function upsertTicket(p){
  if(!p || !p.token) return;

  const token = p.token;
  const toCol = columnForEstado(p.estado);
  const fromCol = findColumn(token);

  const isNewReady = (toCol === 'preparado' && fromCol !== 'preparado');

  if(fromCol && fromCol !== toCol){
    // mover de columna
    removeTicket(fromCol, token);
    addTicket(toCol, p, isNewReady);
  }else if(fromCol){
    // actualizar en misma columna
    state[fromCol].set(token, p);
    const li = els[fromCol].ul.querySelector(`[data-token="${token}"]`);
    if(li) updateTicketEl(li, p);
  }else{
    // nuevo
    addTicket(toCol, p, isNewReady);
  }

  setCounts();
  updateNowCalling();
  updateTicker();

  if(isNewReady && soundEnabled){
    try { ding.currentTime = 0; ding.play(); } catch(e){}
  }
}

async function silentSync(){
  const res = await fetch('/turnos/feed', { headers:{Accept:'application/json'} });
  const feed = await res.json();

  // normaliza
  feed.pendiente  = safeArr(feed.pendiente);
  feed.preparando = safeArr(feed.preparando);
  feed.preparado  = safeArr(feed.preparado);

  // Reconciliar sin vaciar
  for(const col of cols){
    const list = feed[col];
    const newSet = new Set(list.map(x => x.token));

    // remover los que ya no están
    for(const token of [...state[col].keys()]){
      if(!newSet.has(token)) removeTicket(col, token);
    }

    // upsert en orden del feed
    for(const p of list){
      const existingCol = findColumn(p.token);
      if(existingCol && existingCol !== col){
        removeTicket(existingCol, p.token);
        addTicket(col, p, false);
      }else if(existingCol){
        state[col].set(p.token, p);
        const li = els[col].ul.querySelector(`[data-token="${p.token}"]`);
        if(li) updateTicketEl(li, p);
      }else{
        addTicket(col, p, false);
      }
    }

    showEmptyIfNeeded(col);
  }

  setCounts();
  updateNowCalling();
  updateTicker();
}

// -------- INIT --------
silentSync();

// -------- ECHO realtime (SIN polling) --------
const EchoRef = window.Echo;
if(EchoRef){
  EchoRef.channel('turnero')
    .listen('.pedido.actualizado', (e) => {
      // Ideal: e.pedido viene del broadcastWith()
      const p = e?.pedido ?? e;
      if(p?.token) upsertTicket(p);
      else silentSync(); // fallback silencioso
    });
}

// reloj igual que antes
setInterval(() => {
  const clock = document.getElementById('clock');
  if(clock){
    clock.textContent = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
  }
}, 1000);
