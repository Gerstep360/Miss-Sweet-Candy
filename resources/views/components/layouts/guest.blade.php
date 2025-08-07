<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    @stack('scripts')
    <body class="min-h-screen bg-zinc-950 text-white antialiased">
        {{ $slot }}
        @vite(['resources/js/app.js', 'resources/js/welcome.js'])
    </body>
</html>