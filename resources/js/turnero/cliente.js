// resources/js/turnero-cliente.js
const root = document.getElementById('turnero-cliente');
if (!root) { console.warn('turnero-cliente root not found'); }

const TOKEN = root?.dataset.token;

const elToken = document.getElementById('token');
const elEta   = document.getElementById('eta');
const elTipo  = document.getElementById('tipo');
const elPill  = document.getElementById('estado-pill');
const stepsEl = document.getElementById('steps');

function fmtEta(m){
  if (m == null || m === '') return '—';
  const n = Number(m);
  if (Number.isNaN(n)) return '—';
  if (n <= 1) return '1 min';
  return `${n} min`;
}

function estadoUI(estado){
  const map = {
    pendiente:  { label:'EN COLA', step:0, cls:'bg-amber-500/10 text-amber-200 border-amber-500/30' },
    confirmado: { label:'CONFIRMADO', step:0, cls:'bg-amber-500/10 text-amber-200 border-amber-500/30' },
    en_preparacion:{ label:'PREPARANDO', step:1, cls:'bg-sky-500/10 text-sky-200 border-sky-500/30' },
    preparado: { label:'LISTO PARA RETIRAR', step:2, cls:'bg-green-500/10 text-green-200 border-green-500/30' },
  };
  return map[estado] || {
    label: (estado||'—').toUpperCase(),
    step: 0,
    cls:'bg-zinc-500/10 text-zinc-200 border-zinc-500/30'
  };
}

function updateSteps(stepIndex){
  if (!stepsEl) return;
  stepsEl.querySelectorAll('[data-step]').forEach(li => {
    const i = Number(li.dataset.step);
    if (i <= stepIndex){
      li.classList.remove('opacity-60');
      li.classList.add('ring-1','ring-white/10');
      const dot = li.querySelector('.step-dot');
      if (dot) dot.classList.add('shadow-[0_0_10px_rgba(255,255,255,.35)]');
    } else {
      li.classList.add('opacity-60');
      li.classList.remove('ring-1','ring-white/10');
      const dot = li.querySelector('.step-dot');
      if (dot) dot.classList.remove('shadow-[0_0_10px_rgba(255,255,255,.35)]');
    }
  });
}

function applyPedido(p){
  if (!p) return;

  const estado = (p.estado || root.dataset.estado || 'pendiente').toLowerCase();
  const tipo   = (p.tipo || root.dataset.tipo || 'web').toUpperCase();

  elToken.textContent = p.token ?? TOKEN;
  elEta.textContent   = fmtEta(p.eta_minutes);
  elTipo.textContent  = tipo;

  const ui = estadoUI(estado);

  elPill.textContent = ui.label;
  elPill.className =
    'mt-3 inline-flex items-center gap-2 px-2.5 py-1 rounded-full border text-xs font-bold tracking-widest ' +
    ui.cls;

  updateSteps(ui.step);

  // mini “pulse” si ya está listo
  if (estado === 'preparado'){
    const shell = document.querySelector('.ticket-shell');
    shell?.classList.remove('ticket-ready-pulse');
    // reflow
    void shell?.offsetWidth;
    shell?.classList.add('ticket-ready-pulse');
  }
}

// Estado inicial desde dataset (por si quieres)
applyPedido({
  token: TOKEN,
  eta_minutes: root.dataset.eta,
  tipo: root.dataset.tipo,
  estado: root.dataset.estado,
});

// Reverb / Echo realtime
const EchoRef = window.Echo;
if (EchoRef && TOKEN){
  EchoRef.channel('turnero')
    .listen('.pedido.actualizado', (e) => {
      // soporta varias formas de payload
      const pedido = e?.pedido || e?.data || e;

      if (!pedido) return;
      if (String(pedido.token) !== String(TOKEN)) return;

      applyPedido(pedido);
    });
} else {
  console.warn('Echo/Reverb not available in cliente, no realtime updates.');
}

// Reloj suave
setInterval(() => {
  const clock = document.getElementById('clock');
  if (clock){
    clock.textContent = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
  }
}, 1000);
