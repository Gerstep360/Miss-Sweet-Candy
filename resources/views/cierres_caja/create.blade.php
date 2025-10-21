{{-- filepath: resources/views/cierres_caja/create.blade.php --}}
<x-layouts.app :title="__('Nuevo Cierre de Caja')">
    <script>
        function cierreForm() {
            return {
                efectivoDeclarado: 0,
                posDeclarado: 0,
                qrDeclarado: 0,
                totalDeclarado: 0,
                totalSistema: {{ $totalesSistema['total'] }},
                diferencia: 0,

                init() {
                    this.$watch('efectivoDeclarado', () => this.calcular());
                    this.$watch('posDeclarado', () => this.calcular());
                    this.$watch('qrDeclarado', () => this.calcular());
                },

                calcular() {
                    const efectivo = parseFloat(this.efectivoDeclarado) || 0;
                    const pos = parseFloat(this.posDeclarado) || 0;
                    const qr = parseFloat(this.qrDeclarado) || 0;
                    
                    this.totalDeclarado = efectivo + pos + qr;
                    this.diferencia = this.totalDeclarado - this.totalSistema;
                },

                getTipoDiferencia() {
                    if (Math.abs(this.diferencia) < 0.01) return 'Cuadrado';
                    return this.diferencia < 0 ? 'Faltante' : 'Sobrante';
                },

                getColorDiferencia() {
                    if (Math.abs(this.diferencia) < 0.01) return 'green';
                    return this.diferencia < 0 ? 'red' : 'amber';
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('es-BO', {
                        style: 'currency',
                        currency: 'BOB',
                        minimumFractionDigits: 2
                    }).format(value).replace('BOB', 'Bs');
                }
            }
        }
    </script>

    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 py-6 sm:py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="dashboard-card mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-2">
                            <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Nuevo Cierre de Caja
                        </h1>
                        <p class="text-zinc-400">Arqueo y cierre del turno</p>
                    </div>
                    <a href="{{ route('cierres_caja.index') }}" 
                       class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-all">
                        Volver
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="dashboard-card bg-red-500/10 border-red-500/30 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-red-400 font-semibold mb-2">Errores en el formulario:</h3>
                            <ul class="list-disc list-inside text-red-300 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('cierres_caja.store') }}" 
                  method="POST"
                  x-data="cierreForm()"
                  x-init="init()">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Columna izquierda: Datos del turno y arqueo --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Datos del turno --}}
                        <div class="dashboard-card">
                            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Datos del Turno
                            </h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">Inicio del Turno</label>
                                    <input type="datetime-local" 
                                           name="inicio" 
                                           value="{{ $inicio->format('Y-m-d\TH:i') }}"
                                           class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">Fin del Turno</label>
                                    <input type="datetime-local" 
                                           name="fin" 
                                           value="{{ $fin->format('Y-m-d\TH:i') }}"
                                           class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500"
                                           required>
                                </div>
                            </div>

                            <div class="mt-4 p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-sm text-blue-300">
                                        <p class="font-semibold mb-1">Período del cierre:</p>
                                        <p>Se contarán todas las ventas realizadas entre estas dos fechas y horas.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Arqueo de dinero --}}
                        <div class="dashboard-card">
                            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Arqueo de Dinero
                            </h3>

                            <div class="space-y-4">
                                {{-- Efectivo --}}
                                <div class="bg-zinc-800/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="text-white font-medium flex items-center gap-2">
                                            💵 Efectivo
                                        </label>
                                        <span class="text-sm text-zinc-400">
                                            Sistema: <span class="text-green-400 font-bold">Bs {{ number_format($totalesSistema['efectivo'], 2) }}</span>
                                        </span>
                                    </div>
                                    <input type="number" 
                                           name="efectivo_declarado" 
                                           x-model="efectivoDeclarado"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white text-lg font-bold focus:ring-2 focus:ring-green-500"
                                           placeholder="0.00"
                                           required>
                                </div>

                                {{-- POS --}}
                                <div class="bg-zinc-800/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="text-white font-medium flex items-center gap-2">
                                            💳 Tarjeta (POS)
                                        </label>
                                        <span class="text-sm text-zinc-400">
                                            Sistema: <span class="text-blue-400 font-bold">Bs {{ number_format($totalesSistema['pos'], 2) }}</span>
                                        </span>
                                    </div>
                                    <input type="number" 
                                           name="pos_declarado" 
                                           x-model="posDeclarado"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white text-lg font-bold focus:ring-2 focus:ring-blue-500"
                                           placeholder="0.00"
                                           required>
                                </div>

                                {{-- QR --}}
                                <div class="bg-zinc-800/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="text-white font-medium flex items-center gap-2">
                                            📱 QR/Transferencia
                                        </label>
                                        <span class="text-sm text-zinc-400">
                                            Sistema: <span class="text-purple-400 font-bold">Bs {{ number_format($totalesSistema['qr'], 2) }}</span>
                                        </span>
                                    </div>
                                    <input type="number" 
                                           name="qr_declarado" 
                                           x-model="qrDeclarado"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white text-lg font-bold focus:ring-2 focus:ring-purple-500"
                                           placeholder="0.00"
                                           required>
                                </div>
                            </div>
                        </div>

                        {{-- Observaciones --}}
                        <div class="dashboard-card">
                            <label class="block text-sm font-medium text-zinc-300 mb-2">Observaciones (opcional)</label>
                            <textarea name="observaciones" 
                                      rows="3"
                                      class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500"
                                      placeholder="Ej: Billetes dañados, transacciones anuladas, etc.">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>

                    {{-- Columna derecha: Resumen --}}
                    <div class="lg:col-span-1">
                        <div class="dashboard-card sticky top-6">
                            <h3 class="text-lg font-bold text-white mb-4">Resumen del Cierre</h3>

                            {{-- Total Sistema --}}
                            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4 mb-4">
                                <p class="text-sm text-green-300 mb-1">Total Sistema</p>
                                <p class="text-2xl font-bold text-green-400">
                                    Bs {{ number_format($totalesSistema['total'], 2) }}
                                </p>
                                <p class="text-xs text-zinc-400 mt-1">{{ $totalesSistema['cantidad_cobros'] }} cobros</p>
                            </div>

                            {{-- Total Declarado --}}
                            <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 mb-4">
                                <p class="text-sm text-blue-300 mb-1">Total Declarado</p>
                                <p class="text-2xl font-bold text-blue-400" x-text="formatCurrency(totalDeclarado)">
                                    Bs 0.00
                                </p>
                            </div>

                            {{-- Diferencia --}}
                            <div class="rounded-lg p-4 mb-6"
                                 :class="`bg-${getColorDiferencia()}-500/10 border border-${getColorDiferencia()}-500/20`">
                                <p class="text-sm mb-1" :class="`text-${getColorDiferencia()}-300`">Diferencia</p>
                                <p class="text-2xl font-bold mb-1" 
                                   :class="`text-${getColorDiferencia()}-400`"
                                   x-text="(diferencia >= 0 ? '+' : '') + formatCurrency(diferencia)">
                                    Bs 0.00
                                </p>
                                <span class="text-xs font-semibold px-2 py-1 rounded-full"
                                      :class="`bg-${getColorDiferencia()}-500/20 text-${getColorDiferencia()}-400`"
                                      x-text="getTipoDiferencia()">
                                    Cuadrado
                                </span>
                            </div>

                            {{-- Botones --}}
                            <div class="space-y-3">
                                <button type="submit" 
                                        class="w-full px-6 py-3 bg-amber-500 hover:bg-amber-400 text-black font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Registrar Cierre
                                </button>

                                <a href="{{ route('cierres_caja.index') }}" 
                                   class="w-full px-6 py-3 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-layouts.app>
