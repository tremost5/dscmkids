import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/landing.css',
                'resources/js/landing.js',
                'resources/css/admin.css',
                'resources/css/news.css',
                'resources/js/news.js',
                'resources/css/gallery-event.css',
                'resources/js/gallery-event.js',
                'resources/css/student-auth.css',
                'resources/css/student-arcade.css',
                'resources/js/student-arcade.js',
            ],
            refresh: true,
        }),
    ],
});
