import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    root: '.',
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                plugin: path.resolve(__dirname, 'resources/css/plugin.css'),
            },
            output: {
                assetFileNames: 'plugin.css',
            },
        },
    },
    css: {
        postcss: './postcss.config.js',
    },
});
