{{-- resources/views/admin/roles/edit.blade.php --}}
<x-layouts.app :title="__('Editar Rol - Café Aroma')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="dashboard-card mb-8">
                <div class="flex items-center gap-3">
                    <a href="{{ route('roles.show', $role) }}" class="bg-zinc-700 hover:bg-zinc-600 text-white p-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Editar Rol: {{ $role->name }}</h1>
                        <p class="text-zinc-300">Modifica el nombre y permisos del rol</p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="dashboard-card">
                <form action="{{ route('roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Nombre del rol -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-white mb-2">Nombre del Rol</label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" required 
                               class="w-full px-4 py-3 bg-zinc-800/50 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none transition-colors">
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permisos -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-white mb-4">Permisos</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($permissions as $permission)
                            <label class="flex items-center space-x-3 bg-zinc-800/30 p-3 rounded-lg cursor-pointer hover:bg-zinc-800/50 transition-colors">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                       {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                       class="rounded border-zinc-600 bg-zinc-800 text-amber-500 focus:ring-amber-500">
                                <span class="text-zinc-300 text-sm">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-3">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-medium py-3 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Actualizar Rol
                        </button>
                        <a href="{{ route('roles.show', $role) }}" class="bg-zinc-700 hover:bg-zinc-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>