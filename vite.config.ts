import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import vuetify from 'vite-plugin-vuetify'
import DefineOptions from 'unplugin-vue-define-options/vite'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'

export default defineConfig({
  resolve: {
    alias: {
      'ziggy-js': path.resolve(__dirname, 'vendor/tightenco/ziggy/dist/index.js'),
      '@': path.resolve(__dirname, 'resources/js'),
    },
  },
  server: {
    hmr: {
      host: 'localhost',
    },
  },
  plugins: [
    laravel({
      input: 'resources/js/app.ts',
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
    DefineOptions(),
    vuetify({
      styles: {
        configFile: 'resources/css/settings.scss',
      },
    }),
    tailwindcss(),
  ],
  // ssr: {
  //   noExternal: [/^vuetify/],
  // },
})
