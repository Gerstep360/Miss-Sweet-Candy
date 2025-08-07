<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950 text-white antialiased">
        <!-- Background con patrón de café -->
        <div class="fixed inset-0 bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
            <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23f59e0b" fill-opacity="0.1"%3E%3Ccircle cx="7" cy="7" r="2"/%3E%3Ccircle cx="53" cy="53" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
        </div>

        <!-- Header con logo -->
        <header class="relative z-10 py-6">
            <div class="max-w-md mx-auto px-4 text-center">
                <a href="{{ route('welcome') }}" class="inline-flex items-center space-x-3 hover:opacity-80 transition-opacity">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-white">Café Aroma</span>
                </a>
            </div>
        </header>

        <!-- Main content -->
        <main class="relative z-10 flex-1">
            <div class="max-w-md mx-auto px-4 py-8">
                <!-- Auth card -->
                <div class="bg-zinc-900/80 backdrop-blur border border-zinc-800 rounded-2xl p-8 shadow-2xl">
                    {{ $slot }}
                </div>

                <!-- Back to home -->
                <div class="mt-8 text-center">
                    <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 text-zinc-400 hover:text-white transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Volver al inicio
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 py-6">
            <div class="max-w-md mx-auto px-4">
                <div class="text-center text-sm text-zinc-400">
                    <p>&copy; 2025 Café Aroma. El mejor café artesanal desde 1995.</p>
                </div>
            </div>
        </footer>

        @vite(['resources/js/app.js'])
    </body>
</html>