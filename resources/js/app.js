import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus'

window.Alpine = Alpine;
Alpine.plugin(focus)

document.addEventListener('alpine:init', () => {
  Alpine.store('darkMode', {
    on: localStorage.getItem('color-theme') === 'dark'
      || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

    toggle () {
      this.on = !this.on;
      localStorage.setItem('color-theme', this.on ? 'dark' : 'light');
      this.updateClasses();
    },

    updateClasses () {
      if (this.on) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    }
  });
  Alpine.store('darkMode').updateClasses();

});

// Watch for OS theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
  if (!('color-theme' in localStorage)) {
    Alpine.store('darkMode').on = e.matches;
    Alpine.store('darkMode').updateClasses();
  }
});

Alpine.start();