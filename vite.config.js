import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/custom-login.css',
                'resources/js/custom-login.js',
            ],
            refresh: true,
        }),
    ],
});
