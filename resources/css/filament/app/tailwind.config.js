import preset from '../../../../vendor/filament/filament/tailwind.config.preset';

export default {
  presets: [preset],
  content: [
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
    extend: {
      // Any Filament-specific theme customizations can be added here
    },
  },
  plugins: [
    // Add any Filament-specific plugins here if needed
  ],
};