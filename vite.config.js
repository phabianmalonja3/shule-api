import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/tanzaniaGeoData.js', // Add this line to include your custom JS
            ],
            refresh: true,
        }),
    ],
});
