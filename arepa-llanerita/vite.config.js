import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Optimización para producción
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        // Chunk splitting para mejor caching
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['bootstrap', 'alpinejs'],
                },
            },
        },
        // Aumentar límite de chunk para advertencias
        chunkSizeWarningLimit: 1000,
        // Source maps solo en desarrollo
        sourcemap: false,
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
