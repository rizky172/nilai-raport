import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue2";
import { viteStaticCopy } from 'vite-plugin-static-copy';
import ckeditor5 from '@ckeditor/vite-plugin-ckeditor5';

const path = require('path');

export default defineConfig({
    base: '/build/',
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/css/style.css',
                'resources/js/MathExtended.js',
                'resources/vue/App/App.js'
            ],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
              {
                src: 'node_modules/admin-lte/plugins',
                dest: 'node_modules/admin-lte'
              },
              {
                src: 'node_modules/admin-lte/dist',
                dest: 'node_modules/admin-lte'
              },
              {
                src: 'node_modules/admin-lte/pages',
                dest: 'node_modules/admin-lte'
              }
            ]
        }),
        ckeditor5( { theme: require.resolve( '@ckeditor/ckeditor5-theme-lark' ) } )
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/vue/App'),
            '@sidebar': path.resolve(__dirname, 'resources/vue/App/Partials/Sidebars'),
            vue: 'vue/dist/vue.min.js'
        }
    },
    build: {
        manifest: true
    }
});
