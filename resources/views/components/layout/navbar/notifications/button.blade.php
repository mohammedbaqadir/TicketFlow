<div x-data="{ open: false }" class="relative">
    <button
            @click="open = !open"
            type="button"
            class="p-2 mr-1 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
    >
        <span class="sr-only">View notifications</span>
        <x-heroicon-o-bell class="w-6 h-6" aria-hidden="true" />
    </button>

    <div
            x-show="open"
            @click.outside="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 mt-2 z-50 w-80 max-w-md bg-white dark:bg-gray-700 rounded-lg shadow-lg overflow-hidden"
            style="display: none;"
    >
        @include('components.layout.navbar.notifications.dropdown')
    </div>
</div>