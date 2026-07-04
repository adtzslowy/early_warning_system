import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // Bind ke IPv4 supaya URL di public/hot (dipakai @vite) selalu
        // terjangkau, baik saat buka app via localhost maupun 127.0.0.1.
        // Tanpa ini Vite hanya listen di IPv6 [::1] dan aset gagal dimuat.
        host: '127.0.0.1',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
