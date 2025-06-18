import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input:
                [
                    'resources/css/app.css', 'resources/js/app.js',
                    'resources/css/filament/admin/theme.css',
                ],

            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
            ],
        }),
    ],
    server: {
        host: '192.168.1.200', // Change to '0.0.0.0' when using Tunnel
        port: 5173,
        cors: true,
        https: false,
    },
});
