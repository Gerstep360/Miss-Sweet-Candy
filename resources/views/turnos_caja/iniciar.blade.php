{{-- filepath: resources/views/turnos_caja/iniciar.blade.php --}}
<x-layouts.app :title="__('Iniciar Turno de Caja')">
  <main class="max-w-3xl mx-auto px-4 py-8 text-white">
    <!-- Breadcrumb + título -->
    <nav class="text-sm text-zinc-400 mb-3 flex items-center gap-2">
      <a href="{{ route('dashboard') }}" class="hover:text-amber-400 transition">Dashboard</a>
      <span>/</span>
      <span class="text-zinc-300">Iniciar turno</span>
    </nav>

    <header class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Iniciar Turno de Caja</h1>
      <p class="text-zinc-400 mt-1">Registra el monto inicial y deja observaciones si es necesario.</p>
    </header>

    <!-- Alert info -->
    <div class="rounded-xl border border-amber-500/20 bg-amber-500/10 px-4 py-3 mb-6">
      <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg bg-amber-500/20 grid place-items-center">
          <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
          </svg>
        </div>
        <p class="text-sm text-amber-200/90 leading-relaxed">
          Mientras tu turno esté activo, serás el único autorizado para cobrar y registrar ventas hasta realizar el cierre.
        </p>
      </div>
    </div>

    <!-- Tarjeta -->
    <div class="rounded-2xl border border-zinc-800 bg-zinc-900/60 backdrop-blur p-6 shadow-[0_0_40px_-12px_rgba(245,158,11,.12)]">
      <form id="form-iniciar-turno" action="{{ route('turnos_caja.iniciar') }}" method="POST" novalidate>
        @csrf

        {{-- Monto inicial --}}
        <div class="mb-6">
          <label for="monto_inicial" class="block text-sm font-semibold text-zinc-200 mb-2">
            Monto inicial en efectivo <span class="text-red-400" aria-hidden="true">*</span>
          </label>

          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-400 select-none">Bs.</span>
            <input
              type="number"
              id="monto_inicial"
              name="monto_inicial"
              step="0.01"
              min="0"
              inputmode="decimal"
              value="{{ old('monto_inicial', '0.00') }}"
              class="w-full bg-zinc-950/70 border {{ $errors->has('monto_inicial') ? 'border-red-500/60 focus:border-red-500 focus:ring-red-500/30' : 'border-zinc-700 focus:border-amber-500/70 focus:ring-amber-500/20' }} rounded-xl pl-12 pr-3 py-3 outline-none transition ring-2 ring-transparent"
              aria-describedby="monto-help @error('monto_inicial') monto-error @enderror"
              @error('monto_inicial') aria-invalid="true" aria-errormessage="monto-error" @enderror
              required
            >
          </div>

          {{-- chips de montos rápidos --}}
          <div class="mt-3 flex flex-wrap gap-2">
            @foreach([20,50,100,150,200,300] as $chip)
              <button type="button"
                      class="quick-amount px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-200 hover:border-amber-500/50 hover:text-amber-300 transition text-sm"
                      data-amount="{{ number_format($chip, 2, '.', '') }}">
                {{ 'Bs. '.number_format($chip, 2, ',', '.') }}
              </button>
            @endforeach
            <button type="button"
                    class="quick-amount px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-300 hover:border-amber-500/50 hover:text-amber-300 transition text-sm"
                    data-amount="0.00">Vaciar</button>
          </div>

          <p id="monto-help" class="mt-2 text-xs text-zinc-400">
            Ingresa el fondo de cambio con el que abres caja. Se puede ajustar en el cierre.
          </p>

          @error('monto_inicial')
            <p id="monto-error" class="mt-2 text-sm text-red-400 flex items-center gap-2">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 15h2v2h-2v-2zm0-8h2v6h-2V7zm1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>
              {{ $message }}
            </p>
          @enderror
        </div>

        {{-- Observaciones --}}
        <div class="mb-6">
          <label for="observaciones_apertura" class="block text-sm font-semibold text-zinc-200 mb-2">
            Observaciones (opcional)
          </label>
          <textarea
            id="observaciones_apertura"
            name="observaciones_apertura"
            rows="4"
            class="w-full bg-zinc-950/70 border {{ $errors->has('observaciones_apertura') ? 'border-red-500/60 focus:border-red-500 focus:ring-red-500/30' : 'border-zinc-700 focus:border-amber-500/70 focus:ring-amber-500/20' }} rounded-xl px-3 py-3 outline-none transition ring-2 ring-transparent"
            placeholder="Notas o detalles relevantes del inicio del turno...">{{ old('observaciones_apertura') }}</textarea>

          @error('observaciones_apertura')
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
          @enderror
        </div>

        {{-- Errores generales --}}
        @if ($errors->has('mensaje'))
          <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3">
            <p class="text-sm text-red-300">{{ $errors->first('mensaje') }}</p>
          </div>
        @endif

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-800">
          <a href="{{ route('dashboard') }}"
             class="px-4 py-2 rounded-lg border border-zinc-700 text-zinc-300 hover:bg-zinc-800 transition">
            Cancelar
          </a>

          <button id="btn-submit" type="submit"
                  class="inline-flex items-center gap-2 px-6 py-2 rounded-xl bg-amber-500 text-black font-semibold hover:bg-amber-400 transition focus:outline-none focus:ring-2 focus:ring-amber-500/40">
            <svg id="icon-plus" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <svg id="icon-spinner" class="hidden w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4A4 4 0 004 12z"></path>
            </svg>
            <span>Iniciar turno</span>
          </button>
        </div>
      </form>
    </div>

    <!-- ¿Qué sucede al iniciar? -->
    <section class="mt-6 rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
      <h3 class="text-sm font-semibold text-zinc-200 mb-3">¿Qué sucede al iniciar?</h3>
      <ul class="text-sm text-zinc-400 grid sm:grid-cols-2 gap-2">
        <li class="flex items-start gap-2">
          <span class="text-lg">✅</span><span>Tu turno queda activo y visible para los demás cajeros.</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-lg">🔒</span><span>Nadie más puede iniciar turno hasta que cierres el tuyo.</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-lg">🧾</span><span>Podrás registrar ventas y cobros.</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-lg">🧮</span><span>Al terminar, realiza el cierre con arqueo.</span>
        </li>
      </ul>
    </section>
  </main>

  {{-- Vanilla JS: chips, formato, evitar doble submit --}}
  <script>
    (function () {
      const $ = (s, r=document) => r.querySelector(s);
      const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

      const amount = $('#monto_inicial');
      const form   = $('#form-iniciar-turno');
      const submit = $('#btn-submit');
      const iconPlus = $('#icon-plus');
      const iconSpinner = $('#icon-spinner');

      // Chips de montos rápidos
      $$('.quick-amount').forEach(btn => {
        btn.addEventListener('click', () => {
          const val = btn.dataset.amount || '0.00';
          amount.value = val;
          amount.dispatchEvent(new Event('change', { bubbles: true }));
          amount.focus();
        });
      });

      // Formato a 2 decimales al salir del campo
      amount?.addEventListener('blur', () => {
        const v = parseFloat((amount.value || '0').toString().replace(',', '.'));
        if (!isNaN(v)) amount.value = v.toFixed(2);
      });

      // Atajo: Alt+I enfoca el monto
      document.addEventListener('keydown', (e) => {
        if ((e.altKey || e.metaKey) && (e.key?.toLowerCase() === 'i')) {
          e.preventDefault(); amount?.focus();
        }
      });

      // Evitar doble envío y mostrar spinner (mejor UX post-submit)
      // Recomendado desactivar tras el primer submit. :contentReference[oaicite:1]{index=1}
      form?.addEventListener('submit', (e) => {
        // Si ya está deshabilitado, no hagas nada
        if (submit.disabled) { e.preventDefault(); return; }
        submit.disabled = true;
        iconPlus.classList.add('hidden');
        iconSpinner.classList.remove('hidden');
      });
    })();
  </script>
</x-layouts.app>
