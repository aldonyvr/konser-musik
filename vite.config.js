import { fileURLToPath, URL } from "node:url";

import process from "node:process";
import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import path from 'path';

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
    process.env = { ...process.env, ...loadEnv(mode, process.cwd()) };

    return {
        server: {
            host: process.env.VITE_HOST,
            hmr: {
                overlay: false
            }
        },
        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/js/app.ts"],
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
                "@": path.resolve(__dirname, './resources/js')
            },
        },
        optimizeDeps: {
            esbuildOptions: {
                target: ["es2020", "safari14"],
            },
            include: ['vue', 'vue-router']
        },
        build: {
            chunkSizeWarningLimit: 3000,
            target: ["es2020", "safari14"],
            rollupOptions: {
                output: {
                    // expose jQuery as a global variable
                    globals: {
                        jquery: "jQuery",
                    },
                },
            },
        },
    };
});
