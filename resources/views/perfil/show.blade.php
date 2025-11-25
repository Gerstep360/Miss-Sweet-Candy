{{-- resources/views/perfil/show.blade.php --}}
<x-layouts.app :title="__('Mi Perfil - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-2xl">{{ $user->initials() }}</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white">Mi Perfil</h1>
                            <p class="text-zinc-300">Información personal y preferencias</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('perfil.edit') }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2.5 px-5 rounded-lg transition-colors flex items-center gap-2 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Perfil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mensajes de éxito -->
            @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/30 rounded-lg p-4 flex items-start gap-3 animate-fade-in">
                <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-400 font-medium">{{ session('success') }}</p>
            </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-2">
                
                <!-- Información Personal -->
                <div class="dashboard-card">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Información Personal</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-zinc-800/30 rounded-lg p-4">
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Nombre Completo</label>
                            <p class="text-white font-medium text-lg">{{ $user->name }}</p>
                        </div>
                        
                        <div class="bg-zinc-800/30 rounded-lg p-4">
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Correo Electrónico</label>
                            <p class="text-white font-medium">{{ $user->email }}</p>
                        </div>
                        
                        <div class="bg-zinc-800/30 rounded-lg p-4">
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Teléfono</label>
                            <p class="text-white font-medium">
                                {{ $perfil->telefono ?? 'No registrado' }}
                            </p>
                        </div>
                        
                        <div class="bg-zinc-800/30 rounded-lg p-4">
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Dirección</label>
                            <p class="text-white font-medium">
                                {{ $perfil->direccion ?? 'No registrada' }}
                            </p>
                        </div>

                        <div class="bg-zinc-800/30 rounded-lg p-4">
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Marketing</label>
                            @if($perfil->acepta_marketing)
                                <span class="bg-green-500/20 text-green-400 text-sm px-3 py-1 rounded-full inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Acepto recibir promociones
                                </span>
                            @else
                                <span class="bg-zinc-700 text-zinc-400 text-sm px-3 py-1 rounded-full">
                                    No acepto promociones
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Alergias -->
                <div class="dashboard-card">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1-1.964-1-2.732 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Alergias e Intolerancias</h2>
                    </div>
                    
                    @if($perfil && $perfil->tieneAlergias())
                        <div class="space-y-3">
                            @foreach($perfil->alergias as $alergia)
                                @php
                                    $severidad = $alergia['severidad'] ?? 'leve';
                                    $color = \App\Models\ClientePerfil::getColorSeveridad($severidad);
                                    $icono = \App\Models\ClientePerfil::getIconoSeveridad($severidad);
                                @endphp
                                <div class="bg-{{ $color }}-500/10 border border-{{ $color }}-500/30 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <span class="text-2xl">{{ $icono }}</span>
                                        <div class="flex-1">
                                            <p class="text-white font-medium text-lg">{{ $alergia['nombre'] }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="bg-{{ $color }}-500/20 text-{{ $color }}-400 text-xs px-2.5 py-0.5 rounded-full font-medium uppercase">
                                                    {{ $severidad }}
                                                </span>
                                                @if($severidad === 'grave')
                                                    <span class="text-{{ $color }}-400 text-xs">⚠️ Riesgo vital</span>
                                                @elseif($severidad === 'moderado')
                                                    <span class="text-{{ $color }}-400 text-xs">Requiere atención</span>
                                                @else
                                                    <span class="text-{{ $color }}-400 text-xs">Molestias menores</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($perfil->tieneAlergiasGraves())
                        <div class="mt-4 bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1-1.964-1-2.732 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <h3 class="text-red-400 font-medium mb-1">⚠️ Alergias Graves Registradas</h3>
                                    <p class="text-red-300 text-sm">Nuestro personal está informado de tus alergias graves y tomará precauciones especiales.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-zinc-400 mb-2">Sin alergias registradas</p>
                            <a href="{{ route('perfil.edit') }}" class="text-amber-400 hover:text-amber-300 text-sm font-medium">
                                Agregar alergias
                            </a>
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
                    
                    @if($perfil && $perfil->tienePreferencias())
                        <div class="flex flex-wrap gap-3">
                            @foreach($perfil->preferencias as $preferencia)
                                @php
                                    $badge = \App\Models\ClientePerfil::getBadgePreferencia($preferencia);
                                @endphp
                                <div class="bg-{{ $badge['color'] }}-500/10 border border-{{ $badge['color'] }}-500/30 rounded-lg px-4 py-3 flex items-center gap-3">
                                    <span class="text-2xl">{{ $badge['icon'] }}</span>
                                    <div>
                                        <p class="text-white font-medium">{{ $badge['label'] }}</p>
                                        <p class="text-{{ $badge['color'] }}-400 text-xs">
                                            {{ $preferenciasDisponibles[$preferencia] ?? $preferencia }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <p class="text-zinc-400 mb-2">Sin preferencias alimentarias registradas</p>
                            <a href="{{ route('perfil.edit') }}" class="text-amber-400 hover:text-amber-300 text-sm font-medium">
                                Agregar preferencias
                            </a>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Información de última actualización -->
            @if($perfil && $perfil->updated_at)
            <div class="mt-6 text-center">
                <p class="text-zinc-500 text-sm">
                    Última actualización: {{ $perfil->updated_at->format('d/m/Y H:i') }}
                </p>
            </div>
            @endif

        </div>
    </div>
</x-layouts.app>
