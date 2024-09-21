import "./bootstrap";
import "flowbite";
import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
import DOMPurify from 'dompurify';
import Pace from 'pace-js';

Pace.start();

// Configure Alpine
Alpine.plugin(focus);

// Make Alpine and Editor globally accessible
window.Alpine = Alpine;
window.DOMPurify = DOMPurify;

const themeManager = {
  init () {
    this.theme = document.body.dataset.theme;
    this.setupEventListeners();
  },
  setupEventListeners () {
    // Listen for theme toggle events
    document.addEventListener('theme-toggle', () => this.toggleTheme());

    // Listen for theme select changes
    const themeSelect = document.getElementById('theme-select');
    if (themeSelect) {
      themeSelect.addEventListener('change', (e) => this.setTheme(e.target.value));
    }
  },
  toggleTheme () {
    this.setTheme(this.theme === 'light' ? 'dark' : 'light');
  },
  setTheme (newTheme) {
    this.theme = newTheme;
    document.documentElement.classList.toggle('dark', this.theme === 'dark');
    document.body.dataset.theme = this.theme;
    this.saveTheme();
    document.dispatchEvent(new CustomEvent('theme-changed', {detail: {theme: this.theme}}));
  },
  saveTheme () {
    fetch('/preferences/theme', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({theme: this.theme})
    }).then(response => response.json())
      .then(data => console.log('Theme saved:', data))
      .catch(error => console.error('Error saving theme:', error));
  }
};

// Toast function

window.toast = function (message, options = {}) {
  const {description = '', type = 'default', position = 'top-right', html = ''} = options;
  console.log('Toast triggered:', message, description);
  window.dispatchEvent(new CustomEvent('toast-show', {
    detail: {message, description, type, position, html}
  }));
};

// Initialize Alpine.js and other functionalities
document.addEventListener('DOMContentLoaded', () => {
  themeManager.init();
  Alpine.start();
  console.log("Alpine.js started");
  document.addEventListener('theme-changed', (e) => {
    const themeSelect = document.getElementById('theme-select');
    if (themeSelect) {
      themeSelect.value = e.detail.theme;
    }
  });

});