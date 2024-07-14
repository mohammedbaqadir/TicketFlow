import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import Editor from '@toast-ui/editor';

window.Alpine = Alpine;
window.Editor = Editor;
Alpine.plugin(focus);

// Initialize Alpine.js
document.addEventListener('alpine:init', () => {
  console.log('Alpine.js initialized');

  // Define the darkMode store
  Alpine.store('darkMode', {
    on: localStorage.getItem('color-theme') === 'dark'
      || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

    toggle () {
      this.on = !this.on;
      localStorage.setItem('color-theme', this.on ? 'dark' : 'light');
      this.updateClasses();
      console.log('Dark mode toggled:', this.on);
    },

    updateClasses () {
      if (this.on) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
      console.log('Classes updated for dark mode:', this.on);
    }
  });

  // Update classes based on initial state
  Alpine.store('darkMode').updateClasses();
});

Alpine.start();

console.log('Alpine.js started');

// Watch for OS theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
  if (!('color-theme' in localStorage)) {
    Alpine.store('darkMode').on = e.matches;
    Alpine.store('darkMode').updateClasses();
    console.log('OS theme change detected:', e.matches);
  }
});