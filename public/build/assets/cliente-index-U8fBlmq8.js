const o=document.getElementById("cards-container"),c=document.getElementById("empty-state"),d=document.getElementById("pedidos-count"),r=new Map;function u(e){if(e==null||e==="")return"—";const t=Number(e);return Number.isNaN(t)?"—":t<=1?"1 min":`${t} min`}function g(e){return{pendiente:"bg-amber-500/10 text-amber-200 border-amber-500/30",confirmado:"bg-amber-500/10 text-amber-200 border-amber-500/30",en_preparacion:"bg-sky-500/10 text-sky-200 border-sky-500/30",preparado:"bg-green-500/10 text-green-200 border-green-500/30"}[e]||"bg-zinc-500/10 text-zinc-200 border-zinc-500/30"}function p(e){return{pendiente:"EN COLA",confirmado:"CONFIRMADO",en_preparacion:"PREPARANDO",preparado:"LISTO PARA RETIRAR"}[e]||e.toUpperCase()}function b(e){const t=e.estado==="preparado",n=document.createElement("a");n.href=`/turnos/cola/${e.token}`,n.dataset.token=e.token,n.className=`group relative block rounded-2xl border border-zinc-800 bg-zinc-950/90 backdrop-blur hover:bg-zinc-900/60 transition overflow-hidden card-enter ${t?"ring-2 ring-green-500/30 shadow-lg shadow-green-500/10 card-ready":""}`;const a=e.created_at?new Date(e.created_at).toLocaleTimeString([],{hour:"2-digit",minute:"2-digit"}):"—";return n.innerHTML=`
        ${t?'<div class="absolute top-0 right-0 w-24 h-24 bg-green-500/5 blur-3xl"></div>':""}
        
        <div class="relative p-5">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <div class="text-[10px] tracking-[0.3em] text-zinc-400 uppercase">Turno</div>
                    <div class="text-3xl sm:text-4xl font-black tracking-wider text-white mt-0.5">
                        ${e.token}
                    </div>
                </div>

                <span class="estado-pill px-2.5 py-1 rounded-full border text-[10px] font-bold tracking-widest ${g(e.estado)}">
                    ${p(e.estado)}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="rounded-xl border border-zinc-800 bg-zinc-900/40 px-3 py-2">
                    <div class="text-[9px] tracking-widest text-zinc-400 uppercase">ETA</div>
                    <div class="eta-value text-lg font-bold text-amber-200 tabular-nums mt-0.5">
                        ${u(e.eta_minutes)}
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-800 bg-zinc-900/40 px-3 py-2">
                    <div class="text-[9px] tracking-widest text-zinc-400 uppercase">Tipo</div>
                    <div class="text-sm font-bold text-zinc-100 uppercase mt-0.5">
                        ${e.tipo}
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-zinc-400">
                <span>${a}</span>
                <span class="flex items-center gap-1 text-zinc-300 group-hover:text-amber-200 transition">
                    Ver detalles
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </div>
    `,n}function x(e,t){if(t.estado==="preparado"&&!e.classList.contains("card-ready")&&(e.classList.add("ring-2","ring-green-500/30","shadow-lg","shadow-green-500/10","card-ready","card-pulse"),setTimeout(()=>e.classList.remove("card-pulse"),1100),!e.querySelector(".bg-green-500\\/5"))){const i=document.createElement("div");i.className="absolute top-0 right-0 w-24 h-24 bg-green-500/5 blur-3xl",e.insertBefore(i,e.firstChild)}const a=e.querySelector(".estado-pill");a&&(a.className=`estado-pill px-2.5 py-1 rounded-full border text-[10px] font-bold tracking-widest ${g(t.estado)}`,a.textContent=p(t.estado));const s=e.querySelector(".eta-value");s&&(s.textContent=u(t.eta_minutes))}function m(){if(!o)return;const e=Array.from(r.values()).sort((n,a)=>new Date(a.created_at)-new Date(n.created_at));c&&c.classList.toggle("hidden",e.length>0),d&&(d.textContent=e.length),e.forEach(n=>{let a=o.querySelector(`[data-token="${n.token}"]`);a?x(a,n):(a=b(n),o.appendChild(a))}),o.querySelectorAll("[data-token]").forEach(n=>{const a=n.dataset.token;r.has(a)||(n.classList.add("card-leave"),setTimeout(()=>n.remove(),300))})}function f(e){if(!e||!e.token)return;const t=r.has(e.token)&&r.get(e.token).estado==="preparado",n=e.estado==="preparado";r.set(e.token,e),m(),!t&&n&&console.log(`🎉 Pedido ${e.token} está LISTO`)}function v(){const e=document.getElementById("initial-pedidos-data");if(e)try{JSON.parse(e.textContent).forEach(n=>r.set(n.token,n)),m()}catch(t){console.error("Error al parsear pedidos iniciales:",t)}}const l=window.Echo;l?(console.log("✅ Conectando a canal turnero..."),l.channel("turnero").listen(".pedido.actualizado",e=>{console.log("📡 Evento recibido:",e);const t=e?.pedido||e?.data||e;if(!t||!t.token){console.warn("Payload inválido:",e);return}const n=document.querySelector("[data-user-id]")?.dataset.userId;n&&String(t.cliente_id)===String(n)&&f(t)}),console.log("✅ Escuchando eventos en tiempo real")):console.warn("⚠️ Echo no disponible, no habrá actualizaciones en tiempo real");v();setInterval(()=>{const e=document.getElementById("clock");e&&(e.textContent=new Date().toLocaleTimeString([],{hour:"2-digit",minute:"2-digit"}))},1e3);
