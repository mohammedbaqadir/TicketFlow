import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import preset from './vendor/filament/support/tailwind.config.preset';

/** @type {import('tailwindcss').Config} */
export default {
  presets: [preset],
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/**/*.js',
    './resources/views/**/*.blade.php',
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
    "./node_modules/flowbite/**/*.js"
  ],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
        body: ['Inter', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: {
          "50": "#eff6ff",
          "100": "#dbeafe",
          "200": "#bfdbfe",
          "300": "#93c5fd",
          "400": "#60a5fa",
          "500": "#3b82f6",
          "600": "#2563eb",
          "700": "#1d4ed8",
          "800": "#1e40af",
          "900": "#1e3a8a",
          "950": "#172554"
        }
      },
      boxShadow: {
        'glow-green-400': '0 0 15px rgba(74, 222, 128, 0.5)',
        'glow-green-600': '0 0 15px rgba(22, 163, 74, 0.5)',
      },

    },
  },
  plugins: [forms, typography, require('flowbite/plugin'), function ({addUtilities}) {
    const newUtilities = {
      '.glow-green-400': {
        boxShadow: '0 0 15px rgba(74, 222, 128, 0.5)',
      },
      '.glow-green-600': {
        boxShadow: '0 0 15px rgba(22, 163, 74, 0.5)',
      },
    }
    addUtilities(newUtilities, ['responsive', 'hover'])
  },]
};