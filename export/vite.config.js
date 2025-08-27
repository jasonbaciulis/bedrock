import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'
// import vue2 from '@vitejs/plugin-vue2';

export default defineConfig(({ command, mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  return {
    plugins: [
      laravel({
        input: [
          'resources/css/site.css',
          'resources/css/fonts.css',
          'resources/js/site.js',
          'resources/js/embla.js',
          'resources/js/lite-yt-embed.js',
          'resources/css/lite-yt-embed.css',
          'resources/js/fetchEntries.js',
          'resources/js/components/cookieDialog.js',
          'resources/js/components/newsletter.js',
          'resources/js/components/combobox.js',
          'resources/js/components/stepper.js',

          // Control Panel assets.
          // https://statamic.dev/extending/control-panel#adding-css-and-js-assets
          // 'resources/css/cp.css',
          'resources/js/cp.js',
        ],
        refresh: true,
        detectTls: false,
      }),
      tailwindcss(),
      // vue2(),
    ],
    server: {
      open: env.APP_URL,
    },
    define: {
      appName: JSON.stringify(env.APP_NAME),
    },
  }
})
