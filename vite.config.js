import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { glob } from 'glob';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                // Assets dos módulos
                ...glob.sync('resources/modules/**/css/*.css'),
                ...glob.sync('resources/modules/**/js/*.js'),
                ...glob.sync('resources/sass/*.scss')
            ],
            refresh: true,
        }),
    ],
});
