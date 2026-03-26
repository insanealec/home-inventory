import { defineConfig } from 'vite'
import analog from '@analogjs/vite-plugin-angular'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'
import { wayfinder } from '@laravel/vite-plugin-wayfinder'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.ts'],
      refresh: true,
    }),
    analog(),
    tailwindcss(),
    wayfinder(),
  ],
  server: {
    watch: {
      ignored: ['**/storage/framework/views/**'],
    },
  },
})
