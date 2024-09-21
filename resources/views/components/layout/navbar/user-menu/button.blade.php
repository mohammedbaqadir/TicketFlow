<div x-data="{ open: false }" class="relative">
    <button
            @click="open = !open"
            type="button"
            class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-4 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
            id="user-menu-button"
            aria-expanded="false"
    >
        <span class="sr-only">Open user menu</span>
        <img class="w-8 h-8 rounded-full"
             src="{{ Auth::user()->getFirstMediaUrl('avatar') ?: asset('/images/default-avatar.jpg') }}"
             alt="user photo">
    </button>
    <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 mt-2 z-50 w-56 text-base list-none bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
            id="dropdown"
            style="display: none;"
    >
        @include('components.layout.navbar.user-menu.dropdown')
    </div>
</div>