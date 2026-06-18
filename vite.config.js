import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/app.jsx',
            ],
            refresh: true,
        }),
        tailwindcss(),
        react({
            // The React template uses .js files with JSX — include them
            include: /\.(jsx?|tsx?)$/,
        }),
    ],
    resolve: {
        alias: {
            // Points to the React template's src directory
            '@react': path.resolve(__dirname, 'react-frontend/src'),
            // Shim react-router-dom Link → Inertia Link so template components work unchanged
            'react-router-dom': path.resolve(__dirname, 'resources/js/shims/react-router-dom.jsx'),
        },
    },
    css: {
        // Suppress "missing sourcemap" warnings from minified vendor CSS files
        devSourcemap: false,
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
