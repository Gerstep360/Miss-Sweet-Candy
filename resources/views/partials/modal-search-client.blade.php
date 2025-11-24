{{-- resources/views/partials/modal-search-client.blade.php --}}
@can('consultar-perfil-cliente')
{{-- 
    👇 AQUÍ ESTÁ LA CLAVE: 
    Pasamos la ruta nombrada de Laravel al componente de Alpine.
    Esto genera la URL correcta: http://midominio.test/api/clientes/buscar
--}}
<div x-data="searchClient('{{ route('api.clientes.buscar') }}')"
     @keydown.escape.window="close()"
     class="relative z-[100]"
     role="dialog"
     aria-modal="true"
     style="display: none;"
     x-cloak
     x-show="isOpen">

    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/70 backdrop-blur-md transition-opacity"
         @click="close()"></div>

    {{-- Panel del modal --}}
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
             class="mx-auto max-w-2xl transform overflow-hidden rounded-2xl bg-zinc-900/95 border border-zinc-800 shadow-2xl ring-1 ring-white/10 backdrop-blur-xl transition-all"
             @click.stop>

            {{-- Header + Input --}}
            <div class="relative border-b border-zinc-800/80 p-4">
                <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-amber-500/30 to-transparent"></div>
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-zinc-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <input x-ref="searchInput"
                           x-model.debounce.300ms="query"
                           @input="performSearch()"
                           type="text"
                           class="h-10 w-full border-0 bg-transparent p-0 text-zinc-100 placeholder-zinc-500 focus:ring-0 sm:text-sm"
                           placeholder="Buscar por nombre, email o teléfono..."
                           role="combobox"
                           aria-expanded="false">

                    {{-- Spinner --}}
                    <div x-show="isLoading" class="shrink-0" style="display: none;">
                        <svg class="animate-spin h-4 w-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <button @click="close()" class="rounded-md border border-zinc-700 px-2 py-1 text-xs font-medium text-zinc-400 hover:bg-zinc-800 hover:text-white transition">ESC</button>
                </div>
            </div>

            {{-- Lista de resultados --}}
            <ul x-show="query.length > 0" 
                class="max-h-[60vh] overflow-y-auto custom-scrollbar scroll-py-2 p-2 bg-zinc-950/20"
                id="options"
                role="listbox"
                style="display: none;">
                
                {{-- Sin resultados --}}
                <li x-show="!isLoading && results.length === 0 && query.length >= 2" class="p-12 text-center" style="display: none;">
                    <div class="mx-auto h-10 w-10 text-4xl">😕</div>
                    <p class="mt-2 text-sm text-zinc-500">
                        No encontramos nada para "<span class="font-semibold text-zinc-300" x-text="query"></span>"
                    </p>
                </li>

                {{-- Escribe para buscar --}}
                <li x-show="query.length < 2" class="p-12 text-center">
                    <svg class="mx-auto h-10 w-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="mt-2 text-sm text-zinc-500">Escribe al menos 2 caracteres...</p>
                </li>

                {{-- Loop de resultados --}}
                <template x-for="client in results" :key="client.id">
                    <li>
                        {{-- Usamos :href para enlace dinámico --}}
                        <a :href="`/clientes/${client.id}/perfil`"
                           class="group flex select-none items-center gap-4 rounded-xl p-3 hover:bg-zinc-800/80 hover:border-zinc-700 border border-transparent transition-all duration-200 cursor-pointer relative overflow-hidden">
                            
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

                            <div class="relative flex h-10 w-10 flex-none items-center justify-center rounded-full bg-zinc-800 ring-1 ring-white/10 shadow-inner group-hover:scale-105 transition-transform">
                                <span class="text-sm font-bold text-zinc-300 group-hover:text-white" x-text="getInitials(client.name)"></span>
                                <template x-if="hasAllergy(client)">
                                    <span class="absolute -top-0.5 -right-0.5 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-zinc-900"></span>
                                </template>
                            </div>

                            <div class="flex-auto truncate relative z-10">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-zinc-200 group-hover:text-white truncate" x-text="client.name"></span>
                                    <template x-if="client.tiene_alergias_graves">
                                        <span class="ml-2 inline-flex items-center rounded-full bg-red-500/10 px-2 py-0.5 text-[10px] font-medium text-red-400 border border-red-500/20">ALERGIAS</span>
                                    </template>
                                </div>
                                <p class="truncate text-xs text-zinc-500 group-hover:text-zinc-400" x-text="client.email"></p>
                            </div>

                            <svg class="h-5 w-5 flex-none text-zinc-600 group-hover:text-amber-500 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </li>
                </template>
            </ul>

            <div class="flex items-center justify-between border-t border-zinc-800/50 bg-zinc-900/50 px-4 py-2.5 text-xs text-zinc-500">
                <span>Buscando en base de datos...</span>
                <div class="flex items-center gap-2">
                    <span>Navegar</span>
                    <div class="flex gap-0.5">
                        <kbd class="font-sans rounded bg-zinc-800 border border-zinc-700 px-1.5 py-0.5 text-zinc-400">↑</kbd>
                        <kbd class="font-sans rounded bg-zinc-800 border border-zinc-700 px-1.5 py-0.5 text-zinc-400">↓</kbd>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan