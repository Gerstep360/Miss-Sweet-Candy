{{-- resources/views/admin/security/index.blade.php --}}
<x-layouts.app :title="__('Control de Acceso - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-2">Control de Acceso Back-Office</h1>
                        <p class="text-zinc-300">Gestiona la seguridad del sistema con 2FA y control de IPs</p>
                    </div>
                    <div class="flex items-center gap-3 {{ auth()->user()->hasTwoFactorEnabled() ? 'bg-green-900/30 border border-green-700' : 'bg-amber-900/30 border border-amber-700' }} rounded-lg px-4 py-2">
                        <div class="w-2 h-2 {{ auth()->user()->hasTwoFactorEnabled() ? 'bg-green-400' : 'bg-amber-400' }} rounded-full animate-pulse"></div>
                        <span class="{{ auth()->user()->hasTwoFactorEnabled() ? 'text-green-300' : 'text-amber-300' }} text-sm font-medium">
                            {{ auth()->user()->hasTwoFactorEnabled() ? 'Sistema Seguro' : '2FA No Configurado' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-1">2FA Activado</h3>
                    <p class="text-2xl font-bold text-blue-400">{{ $twoFactorEnabledCount }}/{{ $totalUsers }}</p>
                    <p class="text-zinc-400 text-sm mt-1">Usuarios protegidos</p>
                </div>

                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-1">IPs Autorizadas</h3>
                    <p class="text-2xl font-bold text-green-400">{{ $ipWhitelists->count() }}</p>
                    <p class="text-zinc-400 text-sm mt-1">En lista blanca</p>
                </div>

                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-1">Accesos Fallidos</h3>
                    <p class="text-2xl font-bold text-red-400">{{ $failedLoginsToday }}</p>
                    <p class="text-zinc-400 text-sm mt-1">Intentos hoy</p>
                </div>

                <div class="dashboard-card text-center">
                    <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold mb-1">Auditorías</h3>
                    <p class="text-2xl font-bold text-amber-400">{{ $recentAudits->count() }}</p>
                    <p class="text-zinc-400 text-sm mt-1">Eventos recientes</p>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                @can('gestionar-2fa')
                <a href="{{ route('control-acceso.index') }}#2fa" class="dashboard-card group hover:bg-zinc-800 transition-colors cursor-pointer">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 {{ auth()->user()->hasTwoFactorEnabled() ? 'bg-green-500/20' : 'bg-blue-500/20' }} rounded-lg flex items-center justify-center group-hover:{{ auth()->user()->hasTwoFactorEnabled() ? 'bg-green-500/30' : 'bg-blue-500/30' }} transition-colors">
                            <svg class="w-5 h-5 {{ auth()->user()->hasTwoFactorEnabled() ? 'text-green-400' : 'text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-medium">2FA</h3>
                            <p class="text-zinc-400 text-sm">
                                {{ auth()->user()->hasTwoFactorEnabled() ? 'Activado' : 'Configurar' }}
                            </p>
                        </div>
                    </div>
                </a>
                @endcan

                @can('gestionar-ips')
                <a href="{{ route('control-acceso.index') }}#ip-whitelist" class="dashboard-card group hover:bg-zinc-800 transition-colors cursor-pointer">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-medium">Control de IPs</h3>
                            <p class="text-zinc-400 text-sm">Listas blanca/negra</p>
                        </div>
                    </div>
                </a>
                @endcan

                @can('ver-reportes-seguridad')
                <a href="{{ route('control-acceso.recent-activity') }}" class="dashboard-card group hover:bg-zinc-800 transition-colors cursor-pointer">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-medium">Reportes</h3>
                            <p class="text-zinc-400 text-sm">Estadísticas seguridad</p>
                        </div>
                    </div>
                </a>
                @endcan
            </div>

            <!-- Estado Actual de Seguridad -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Estado 2FA por Rol -->
                <div class="dashboard-card">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Estado 2FA por Rol
                    </h3>
                    
                    <div class="space-y-3">
                        @foreach($twoFactorByRole as $role)
                        <div class="flex items-center justify-between p-3 bg-zinc-800/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full {{ $role['enabled'] ? 'bg-green-400' : 'bg-zinc-600' }}"></div>
                                <span class="text-white font-medium">{{ $role['name'] }}</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-zinc-400 text-sm">{{ $role['users_count'] }} usuarios</span>
                                <span class="text-xs px-2 py-1 rounded-full {{ $role['enabled'] ? 'bg-green-500/20 text-green-400' : 'bg-zinc-500/20 text-zinc-400' }}">
                                    {{ $role['enabled'] ? 'Obligatorio' : 'Opcional' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Actividad Reciente -->
                <div class="dashboard-card">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Actividad Reciente
                    </h3>
                    
                    <div class="space-y-3">
                        @forelse($recentAudits as $audit)
                        <div class="flex items-center justify-between p-3 bg-zinc-800/50 rounded-lg">
                            <div>
                                <div class="text-white font-medium text-sm">{{ $audit->accion }}</div>
                                <div class="text-zinc-400 text-xs">{{ $audit->entidad }} #{{ $audit->entidad_id }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-zinc-400 text-xs">{{ $audit->created_at->diffForHumans() }}</div>
                                <div class="text-zinc-500 text-xs font-mono">{{ $audit->ip }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-zinc-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p>No hay actividad reciente</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
            
               <!-- Acciones Rápidas para Administradores -->
                @can('gestionar-permisos')
                <div class="dashboard-card mt-8">
                    <h3 class="text-lg font-semibold text-white mb-4">Acciones de Seguridad</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Configuración de IPs -->
                        <a href="{{ route('control-acceso.config-ips') }}" class="dashboard-card group hover:bg-zinc-800 transition-colors cursor-pointer border-l-4 border-green-500">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-white font-semibold">Configurar IPs</h3>
                                    <p class="text-zinc-400 text-sm mt-1">Gestionar lista blanca y negra de IPs</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded-full">
                                            {{ $ipWhitelists->count() }} IPs autorizadas
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Autenticación 2FA -->
                        <a href="{{ route('control-acceso.two-factor') }}" class="dashboard-card group hover:bg-zinc-800 transition-colors cursor-pointer border-l-4 border-blue-500">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 {{ auth()->user()->hasTwoFactorEnabled() ? 'bg-green-500/20' : 'bg-blue-500/20' }} rounded-lg flex items-center justify-center group-hover:{{ auth()->user()->hasTwoFactorEnabled() ? 'bg-green-500/30' : 'bg-blue-500/30' }} transition-colors">
                                    <svg class="w-6 h-6 {{ auth()->user()->hasTwoFactorEnabled() ? 'text-green-400' : 'text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-white font-semibold">Autenticación 2FA</h3>
                                    <p class="text-zinc-400 text-sm mt-1">Configurar verificación en dos pasos</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-xs {{ auth()->user()->hasTwoFactorEnabled() ? 'bg-green-500/20 text-green-400' : 'bg-amber-500/20 text-amber-400' }} px-2 py-1 rounded-full">
                                            {{ auth()->user()->hasTwoFactorEnabled() ? 'Activado' : 'Pendiente' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Bitácora de Seguridad -->
                        <a href="{{ route('control-acceso.bitacora') }}" class="dashboard-card group hover:bg-zinc-800 transition-colors cursor-pointer border-l-4 border-purple-500">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-white font-semibold">Bitácora</h3>
                                    <p class="text-zinc-400 text-sm mt-1">Ver historial de actividad y eventos</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-xs bg-purple-500/20 text-purple-400 px-2 py-1 rounded-full">
                                            {{ $recentAudits->count() }} eventos recientes
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Acciones Adicionales -->
                    <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t border-zinc-700">
                        <a href="{{ route('control-acceso.import-ips-form') }}" class="bg-blue-500 hover:bg-blue-400 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                            Importar IPs
                        </a>
                        @if(auth()->user()->hasTwoFactorEnabled())
                        <a href="{{ route('control-acceso.backup-codes') }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Códigos Respaldo
                        </a>
                        @endif
                        <a href="{{ route('users.index') }}" class="bg-green-500 hover:bg-green-400 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            Gestionar Usuarios
                        </a>
                        <a href="{{ route('roles.index') }}" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Gestionar Roles
                        </a>
                    </div>
                </div>
                @endcan
            
        </div>
    </div>
</x-layouts.app>