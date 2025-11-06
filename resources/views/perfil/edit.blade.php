{{-- resources/views/perfil/edit.blade.php --}}
<x-layouts.app :title="__('Editar Mi Perfil - Miss Sweet Candy')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center gap-3">
                    <a href="{{ route('perfil.show') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white p-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-xl">{{ $user->initials() }}</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Editar Mi Perfil</h1>
                            <p class="text-zinc-300">Actualiza tu información personal y preferencias</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensajes de error -->
            @if(session('error'))
            <div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-lg p-4 flex items-start gap-3 animate-fade-in">
                <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-400 font-medium">{{ session('error') }}</p>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                <div class="flex items-start gap-3 mb-2">
                    <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-red-400 font-medium mb-2">Por favor corrige los siguientes errores:</p>
                        <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Formulario -->
            <form action="{{ route('perfil.update') }}" method="POST" x-data="perfilForm()" class="space-y-6">
                @csrf
                @method('PUT')

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

                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">
                                Nombre Completo <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                                   class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors">
                            @error('name')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">
                                Correo Electrónico <span class="text-red-400">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                                   class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors">
                            @error('email')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">
                                Teléfono
                            </label>
                            <input type="text" name="telefono" value="{{ old('telefono', $perfil->telefono) }}" 
                                   placeholder="Ej: 77123456"
                                   class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors">
                            @error('telefono')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dirección -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">
                                Dirección
                            </label>
                            <input type="text" name="direccion" value="{{ old('direccion', $perfil->direccion) }}" 
                                   placeholder="Ej: Av. 6 de Agosto #1234, La Paz"
                                   class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors">
                            @error('direccion')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Marketing -->
                    <div class="mt-6 bg-zinc-800/30 rounded-lg p-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="acepta_marketing" value="1" 
                                   {{ old('acepta_marketing', $perfil->acepta_marketing) ? 'checked' : '' }}
                                   class="w-5 h-5 bg-zinc-700 border-zinc-600 rounded text-amber-500 focus:ring-2 focus:ring-amber-500/20">
                            <div>
                                <span class="text-white font-medium">Acepto recibir promociones y ofertas especiales</span>
                                <p class="text-zinc-400 text-sm">Recibirás noticias sobre descuentos y nuevos productos</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Preferencias Alimentarias -->
                <div class="dashboard-card">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Preferencias Alimentarias</h2>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($preferenciasDisponibles as $key => $descripcion)
                            @php
                                $badge = \App\Models\ClientePerfil::getBadgePreferencia($key);
                                $checked = in_array($key, old('preferencias', $perfil->preferencias ?? []));
                            @endphp
                            <label class="bg-zinc-800/30 border-2 border-zinc-700 hover:border-{{ $badge['color'] }}-500/50 rounded-lg p-4 cursor-pointer transition-all duration-200 {{ $checked ? 'border-' . $badge['color'] . '-500 bg-' . $badge['color'] . '-500/10' : '' }}">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" name="preferencias[]" value="{{ $key }}" 
                                           {{ $checked ? 'checked' : '' }}
                                           class="mt-1 w-5 h-5 bg-zinc-700 border-zinc-600 rounded text-{{ $badge['color'] }}-500 focus:ring-2 focus:ring-{{ $badge['color'] }}-500/20">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xl">{{ $badge['icon'] }}</span>
                                            <span class="text-white font-medium">{{ $badge['label'] }}</span>
                                        </div>
                                        <p class="text-zinc-400 text-xs">{{ $descripcion }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Alergias e Intolerancias -->
                <div class="dashboard-card">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1-1.964-1-2.732 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white">Alergias e Intolerancias</h2>
                        </div>
                        <button type="button" @click="agregarAlergia()" class="bg-red-500 hover:bg-red-400 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar Alergia
                        </button>
                    </div>

                    <div class="space-y-4" id="alergias-container">
                        <template x-for="(alergia, index) in alergias" :key="index">
                            <div class="bg-zinc-800/30 border border-zinc-700 rounded-lg p-4">
                                <div class="flex items-start gap-4">
                                    <div class="flex-1 grid gap-4 md:grid-cols-2">
                                        <!-- Nombre de la alergia -->
                                        <div>
                                            <label class="block text-sm font-medium text-white mb-2">
                                                Nombre de la alergia <span class="text-red-400">*</span>
                                            </label>
                                            <input type="text" :name="'alergias[' + index + '][nombre]'" x-model="alergia.nombre" 
                                                   placeholder="Ej: Nueces, Mariscos, Gluten..."
                                                   class="w-full px-4 py-2.5 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-none transition-colors">
                                        </div>

                                        <!-- Nivel de severidad -->
                                        <div>
                                            <label class="block text-sm font-medium text-white mb-2">
                                                Nivel de severidad <span class="text-red-400">*</span>
                                            </label>
                                            <select :name="'alergias[' + index + '][severidad]'" x-model="alergia.severidad"
                                                    class="w-full px-4 py-2.5 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-none transition-colors">
                                                @foreach($nivelesSeveridad as $key => $descripcion)
                                                    <option value="{{ $key }}">{{ ucfirst($key) }} - {{ $descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Botón eliminar -->
                                    <button type="button" @click="eliminarAlergia(index)" 
                                            class="bg-red-500/20 hover:bg-red-500/30 text-red-400 p-2 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Mensaje si no hay alergias -->
                        <div x-show="alergias.length === 0" class="text-center py-8 bg-zinc-800/20 rounded-lg border-2 border-dashed border-zinc-700">
                            <div class="w-16 h-16 bg-zinc-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <p class="text-zinc-400 mb-2">Sin alergias registradas</p>
                            <p class="text-zinc-500 text-sm">Haz clic en "Agregar Alergia" para comenzar</p>
                        </div>
                    </div>

                    <!-- Info importante -->
                    <div class="mt-4 bg-amber-500/10 border border-amber-500/30 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h3 class="text-amber-400 font-medium mb-1">Información Importante</h3>
                                <p class="text-amber-300 text-sm">Nuestro personal revisará tus alergias antes de preparar tu pedido. Las alergias graves serán destacadas con alertas especiales.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-3 justify-end">
                    <a href="{{ route('perfil.show') }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2 shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>

            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        function perfilForm() {
            return {
                alergias: @json(old('alergias', $perfil->alergias ?? [])),
                
                agregarAlergia() {
                    this.alergias.push({
                        nombre: '',
                        severidad: 'leve'
                    });
                },
                
                eliminarAlergia(index) {
                    this.alergias.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>
