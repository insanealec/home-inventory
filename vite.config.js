import vue from '@vitejs/plugin-vue';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { wayfinder } from "@laravel/vite-plugin-wayfinder";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    // The src attribute from img tags
                    img: ['src', 'srcset'],
                },
            },
        }),
        tailwindcss(),
        wayfinder(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
