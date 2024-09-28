import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build', // This ensures files go into the public/build directory
        assetsDir: 'assets',    // Place assets into a subdirectory called 'assets'
        manifest: true,         // Ensure the manifest is created
    },
});
