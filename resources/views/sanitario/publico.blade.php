<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cumplimiento Sanitario - Miss Sweet Candy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-950 text-white antialiased min-h-screen">

    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-amber-500 mb-2">Miss Sweet Candy</h1>
            <p class="text-xl text-zinc-300">Transparencia y Cumplimiento Sanitario (SENASAG)</p>
            <p class="text-sm text-zinc-500 mt-2">Actualizado: {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Checklists Status -->
            <div class="lg:col-span-1 space-y-6">
                <h2 class="text-2xl font-semibold text-white border-b border-zinc-800 pb-2">Protocolos</h2>
                @foreach ($listas as $lista)
                    @php
                        $lastResponse = $lista->respuestas->first();
                        $statusColor = $lastResponse ? 'bg-green-500' : 'bg-zinc-700';
                        $statusText = $lastResponse ? 'Al día' : 'Pendiente';
                        $lastDate = $lastResponse ? $lastResponse->created_at->diffForHumans() : 'Sin registros';
                    @endphp
                    <div class="bg-zinc-900 p-5 rounded-xl border border-zinc-800 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-lg">{{ $lista->nombre }}</h3>
                            <p class="text-sm text-zinc-500">{{ $lastDate }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full {{ $statusColor }} animate-pulse"></span>
                            <span
                                class="text-sm font-medium {{ $lastResponse ? 'text-green-400' : 'text-zinc-400' }}">{{ $statusText }}</span>
                        </div>
                    </div>
                @endforeach

                <h2 class="text-2xl font-semibold text-white border-b border-zinc-800 pb-2 pt-4">Temperaturas Recientes
                </h2>
                <div class="bg-zinc-900 rounded-xl border border-zinc-800 overflow-hidden">
                    <div class="divide-y divide-zinc-800">
                        @forelse($temperaturas as $temp)
                            <div class="p-4 flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-zinc-200">{{ $temp->equipo }}</p>
                                    <p class="text-xs text-zinc-500">{{ $temp->created_at->format('H:i') }}</p>
                                </div>
                                <span
                                    class="font-bold {{ $temp->temperatura > 5 ? 'text-red-400' : 'text-blue-400' }}">{{ $temp->temperatura }}°C</span>
                            </div>
                        @empty
                            <p class="p-4 text-center text-zinc-500">Sin registros recientes</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Evidence Gallery -->
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-semibold text-white border-b border-zinc-800 pb-2 mb-6">Evidencias Recientes
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @forelse($evidencias as $evidencia)
                        <div
                            class="group relative aspect-video bg-zinc-900 rounded-xl overflow-hidden border border-zinc-800">
                            <img src="{{ Storage::url($evidencia->foto_ruta) }}" alt="Evidencia"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                                <p class="text-white font-bold">{{ $evidencia->lista->nombre }}</p>
                                <p class="text-xs text-zinc-300">{{ $evidencia->created_at->format('d/m/Y H:i') }} por
                                    {{ $evidencia->usuario->name }}</p>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-12 text-center text-zinc-500 bg-zinc-900/50 rounded-xl border border-zinc-800 border-dashed">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <p>No hay evidencias fotográficas recientes.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</body>

</html>
