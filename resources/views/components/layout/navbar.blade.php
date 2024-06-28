<nav class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">
            <x-layout.navbar.logo />

        </div>
        <div class="flex items-center lg:order-2">
            <x-layout.navbar.search />
            <x-layout.navbar.notifications.button />
            <x-layout.navbar.user-menu.button />
            <x-layout.navbar.dark-mode-toggle />
        </div>
    </div>
</nav>