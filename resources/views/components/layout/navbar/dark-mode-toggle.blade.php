<button
        x-data="{
        isDark: document.body.dataset.theme === 'dark',
        toggleTheme() {
            this.isDark = !this.isDark;
            document.body.dataset.theme = this.isDark ? 'dark' : 'light';
            $dispatch('theme-toggle');
        }
    }"
        @theme-changed.window="isDark = $event.detail.theme === 'dark'"
        @click="toggleTheme"
        type="button"
        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5"
>
    <span x-show="!isDark">
        <x-heroicon-o-moon class="w-5 h-5" />
    </span>
    <span x-show="isDark">
        <x-heroicon-o-sun class="w-5 h-5" />
    </span>
</button>