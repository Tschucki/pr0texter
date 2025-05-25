import vue from '@vitejs/plugin-vue';
import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from 'tailwindcss';
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig(({ mode }) => {
    const isProd = mode === 'production';

    return {
        plugins: [
            laravel({
                input: ['resources/js/app.ts', 'resources/css/app.css'],
                ssr: 'resources/js/ssr.ts',
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/js'),
                'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
            },
        },
        css: {
            postcss: {
                plugins: [tailwindcss, autoprefixer],
            },
        },
        build: {
            target: 'es2015',
            minify: isProd ? 'terser' : false,
            terserOptions: {
                compress: {
                    drop_console: isProd,
                    drop_debugger: isProd,
                },
            },
            rollupOptions: {
                output: {
                    manualChunks: {
                        vendor: ['vue', 'axios'],
                    },
                },
            },
            reportCompressedSize: false,
            chunkSizeWarningLimit: 1000,
        },
        optimizeDeps: {
            include: ['vue', 'axios', '@vueuse/core'],
            exclude: ['ziggy-js'],
        },
        cacheDir: '.vite_cache',
        sourcemap: isProd ? false : 'inline',
        server: {
            hmr: {
                overlay: true,
            },
            watch: {
                usePolling: false,
            },
        },
        esbuild: {
            legalComments: 'none',
            tsconfigRaw: {
                compilerOptions: {
                    target: 'es2020',
                    module: 'esnext',
                },
            },
        },
    };
});
