import {defineConfig} from 'vite';
import laravel, {refreshPaths} from 'laravel-vite-plugin';

export default defineConfig(({mode}) => ({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/filament/app/theme.css',
        'resources/js/vendor/pace.min.js',
        'resources/css/vendor/flash.css',
      ],
      refresh: [
        ...refreshPaths,
        'app/Livewire/**',
      ],
    }),
  ],
  // Production-specific optimizations
  build: {
    manifest: true,
    sourcemap: mode === 'production', // Enable sourcemaps only in production for easier debugging.
    minify: mode === 'production', // Minifies the assets in production to reduce bundle size.
  },
}));