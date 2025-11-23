import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/dashboard.js',
                'resources/js/modal-store.js',
                'resources/js/turnero/monitor.js',
                'resources/css/turnero/monitor.css',
                'resources/js/turnero/cliente.js',
                'resources/css/turnero/cliente.css',
                'resources/js/turnero/cliente-index.js',
                'resources/css/turnero/cliente-index.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
});