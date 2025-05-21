import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // point the plugin at your public dev server URL:
            devServer: 'https://smooth-maddalena-awalter7-721ca856.koyeb.app:5173',
        }),
        tailwindcss(),
        react(),
    ],
    server: {
        host: '0.0.0.0',               // listen on all addrs
        port: 5173,                    // default Vite port
        strictPort: true,              // fail if 5173 is taken
        cors: true,                    // enable CORS for all origins
        origin: 'https://smooth-maddalena-awalter7-721ca856.koyeb.app:5173',
        hmr: {
            protocol: 'wss',             // secure WS
            host: 'smooth-maddalena-awalter7-721ca856.koyeb.app',
            clientPort: 443,             // browser ws port
        },
    },
});
