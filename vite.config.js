import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        react(),

    ],
    server: {
        host: '0.0.0.0',           // listen on all interfaces
        port: 5173,                // match Vite’s default port
        strictPort: true,          // fail if 5173 is taken
        cors: {
            origin: 'https://smooth-maddalena-awalter7-721ca856.koyeb.app',
            methods: ['GET','POST'],
            credentials: true,
        },
        hmr: {
            protocol: 'wss',         // use secure websockets
            host: 'smooth-maddalena-awalter7-721ca856.koyeb.app',
            port: 5173,
        },
    },
});
