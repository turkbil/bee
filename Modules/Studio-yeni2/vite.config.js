import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    build: {
        outDir: "../../public/build-studio",
        emptyOutDir: true,
        manifest: true,
        sourcemap: false,
    },
    plugins: [
        laravel({
            publicDirectory: "../../public",
            buildDirectory: "build-studio",
            input: [
                __dirname + "/resources/assets/sass/app.scss",
                __dirname + "/resources/assets/js/app.js",
            ],
            refresh: true,
        }),
    ],
});
