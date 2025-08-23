import path from "path";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

import { defineConfig, loadEnv } from "vite";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), "");

    return {
        define: {
            __APP_ENV__: JSON.stringify(env.APP_ENV),
        },

        server: {
            host: env.VITE_HOST,
            port: env.VITE_PORT,
            cors: {
                origin: "*",
            },
        },

        resolve: {
            alias: {
                "@": path.resolve(__dirname, "resources/js"),
            },
        },

        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/js/app.js"],
                ssr: "resources/js/ssr.js",
                refresh: true,
            }),

            tailwindcss(),

            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
    };
});
