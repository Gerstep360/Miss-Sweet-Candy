{{-- resources/views/perfil/consultar.blade.php --}}
<x-layouts.app :title="__('Perfil del Cliente - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <button onclick="history.back()" class="bg-zinc-700 hover:bg-zinc-600 text-white p-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-2xl">{{ $user->initials() }}</span>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white">Perfil del Cliente</h1>
                                <p class="text-zinc-300">Información para atención personalizada</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($perfil && ($perfil->tieneAlergias() || $perfil->tienePreferencias()))
                    <div class="flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 rounded-lg px-4 py-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-amber-400 font-medium text-sm">Cliente con restricciones</span>
                    </div>
                    @endif
                </div>
            </div>

            @if(!$perfil)
                <!-- Cliente sin perfil -->
                <div class="dashboard-card text-center py-12">
                    <div class="w-20 h-20 bg-zinc-800 rounded-xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Perfil No Disponible</h2>
                    <p class="text-zinc-400 mb-6">Este cliente aún no ha completado su perfil.</p>
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 max-w-md mx-auto">
                        <p class="text-blue-400 text-sm">
                            <strong>Información básica:</strong><br>
                            Nombre: {{ $user->name }}<br>
                            Email: {{ $user->email }}
                        </p>
                    </div>
                </div>
            @else
                <div class="grid gap-6 lg:grid-cols-2">
                    
                    <!-- Información Personal -->
                    <div class="dashboard-card">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white">Datos del Cliente</h2>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="bg-zinc-800/30 rounded-lg p-4">
                                <label class="block text-sm font-medium text-zinc-400 mb-1">Nombre Completo</label>
                                <p class="text-white font-medium text-lg">{{ $user->name }}</p>
                            </div>
                            
                            <div class="bg-zinc-800/30 rounded-lg p-4">
                                <label class="block text-sm font-medium text-zinc-400 mb-1">Correo Electrónico</label>
                                <p class="text-white font-medium break-all">{{ $user->email }}</p>
                            </div>
                            
                            <div class="bg-zinc-800/30 rounded-lg p-4">
                                <label class="block text-sm font-medium text-zinc-400 mb-1">Teléfono</label>
                                <p class="text-white font-medium">
                                    @if($perfil->telefono)
                                        <a href="tel:{{ $perfil->telefono }}" class="text-blue-400 hover:text-blue-300">
                                            {{ $perfil->telefono }}
                                        </a>
                                    @else
                                        <span class="text-zinc-500">No registrado</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="bg-zinc-800/30 rounded-lg p-4">
                                <label class="block text-sm font-medium text-zinc-400 mb-1">Dirección</label>
                                <p class="text-white font-medium">
                                    {{ $perfil->direccion ?? 'No registrada' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Alergias - PRIORITARIO -->
                    <div class="dashboard-card {{ $perfil->tieneAlergias() ? 'ring-2 ring-red-500/50' : '' }}">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1-1.964-1-2.732 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white">⚠️ Alergias e Intolerancias</h2>
                        </div>
                        
                        @if($perfil->tieneAlergias())
                            <div class="space-y-3">
                                @foreach($perfil->alergias as $alergia)
                                    @php
                                        $severidad = $alergia['severidad'] ?? 'leve';
                                        $color = \App\Models\ClientePerfil::getColorSeveridad($severidad);
                                        $icono = \App\Models\ClientePerfil::getIconoSeveridad($severidad);
                                    @endphp
                                    <div class="bg-{{ $color }}-500/10 border-2 border-{{ $color }}-500/50 rounded-lg p-4 {{ $severidad === 'grave' ? 'animate-pulse' : '' }}">
                                        <div class="flex items-start gap-3">
                                            <span class="text-3xl">{{ $icono }}</span>
                                            <div class="flex-1">
                                                <p class="text-white font-bold text-lg">{{ $alergia['nombre'] }}</p>
                                                <div class="flex items-center gap-2 mt-2">
                                                    <span class="bg-{{ $color }}-500/30 text-{{ $color }}-300 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wide">
                                                        {{ $severidad }}
                                                    </span>
                                                    @if($severidad === 'grave')
                                                        <span class="text-{{ $color }}-300 text-sm font-bold">🚨 RIESGO VITAL - MÁXIMA PRECAUCIÓN</span>
                                                    @elseif($severidad === 'moderado')
                                                        <span class="text-{{ $color }}-300 text-sm font-medium">⚠️ Requiere atención</span>
                                                    @else
                                                        <span class="text-{{ $color }}-300 text-sm">⚡ Molestias menores</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($perfil->tieneAlergiasGraves())
                            <div class="mt-4 bg-red-500/20 border-2 border-red-500 rounded-lg p-4 animate-pulse">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-red-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1-1.964-1-2.732 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div>
                                        <h3 class="text-red-300 font-bold text-lg mb-2">🚨 ALERTA CRÍTICA - ALERGIAS GRAVES</h3>
                                        <ul class="text-red-200 text-sm space-y-1">
                                            <li>✓ Verificar ingredientes antes de confirmar pedido</li>
                                            <li>✓ Informar a cocina sobre restricciones</li>
                                            <li>✓ Evitar contaminación cruzada</li>
                                            <li>✓ Confirmar con el cliente antes de servir</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-green-500/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-green-400 font-medium">✓ Sin alergias registradas</p>
                                <p class="text-zinc-500 text-sm mt-1">Cliente sin restricciones conocidas</p>
                            </div>
                        @endif
                    </div>

                    <!-- Preferencias Alimentarias -->
                    <div class="dashboard-card lg:col-span-2">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white">Preferencias Alimentarias</h2>
                        </div>
                        
                        @if($perfil->tienePreferencias())
                            <div class="flex flex-wrap gap-3">
                                @foreach($perfil->preferencias as $preferencia)
                                    @php
                                        $badge = \App\Models\ClientePerfil::getBadgePreferencia($preferencia);
                                    @endphp
                                    <div class="bg-{{ $badge['color'] }}-500/10 border border-{{ $badge['color'] }}-500/30 rounded-lg px-5 py-3 flex items-center gap-3">
                                        <span class="text-3xl">{{ $badge['icon'] }}</span>
                                        <div>
                                            <p class="text-white font-bold">{{ $badge['label'] }}</p>
                                            <p class="text-{{ $badge['color'] }}-400 text-xs">
                                                Sugerir opciones {{ strtolower($badge['label']) }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h3 class="text-blue-400 font-medium mb-1">💡 Recomendación</h3>
                                        <p class="text-blue-300 text-sm">Sugiere productos que coincidan con las preferencias del cliente para mejorar su experiencia.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <p class="text-zinc-400">Sin preferencias alimentarias registradas</p>
                                <p class="text-zinc-500 text-sm mt-1">Cliente sin restricciones de dieta</p>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Información adicional -->
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <!-- Última actualización -->
                    <div class="bg-zinc-800/30 rounded-lg p-4 border border-zinc-700">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-zinc-400 text-sm">Última actualización</p>
                                <p class="text-white font-medium">{{ $perfil->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="bg-zinc-800/30 rounded-lg p-4 border border-zinc-700">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <div>
                                <p class="text-zinc-400 text-sm">Resumen del perfil</p>
                                <p class="text-white font-medium">
                                    {{ $perfil->tieneAlergias() ? count($perfil->alergias) : 0 }} Alergias • 
                                    {{ $perfil->tienePreferencias() ? count($perfil->preferencias) : 0 }} Preferencias
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-layouts.app>
