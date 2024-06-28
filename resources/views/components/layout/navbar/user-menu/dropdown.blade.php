<div x-cloak>
    <div class="py-3 px-4">
        <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
        <span class="block text-sm text-gray-900 truncate dark:text-white">{{ Auth::user()->email }}</span>
    </div>
    <ul class="py-1 text-gray-700 dark:text-gray-300">
        <x-layout.navbar.user-menu.dropdown-item href="#" label="My profile" />
        <x-layout.navbar.user-menu.dropdown-item href="#" label="Account settings" />
    </ul>
    <ul class="py-1 text-gray-700 dark:text-gray-300">
        <x-layout.navbar.user-menu.dropdown-item href="#" label="My likes">
            <x-slot name="icon">
                <x-heroicon-o-heart class="mr-2 w-5 h-5 text-gray-400" />
            </x-slot>
        </x-layout.navbar.user-menu.dropdown-item>
        <x-layout.navbar.user-menu.dropdown-item href="#" label="Collections">
            <x-slot name="icon">
                <x-heroicon-o-circle-stack class="mr-2 w-5 h-5 text-gray-400" />
            </x-slot>
        </x-layout.navbar.user-menu.dropdown-item>
        <x-layout.navbar.user-menu.dropdown-item href="#" label="Pro version">
            <x-slot name="icon">
                <x-heroicon-o-fire class="mr-2 w-5 h-5 text-primary-600 dark:text-primary-500" />
            </x-slot>
            <x-slot name="suffix">
                <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
            </x-slot>
        </x-layout.navbar.user-menu.dropdown-item>
    </ul>
    <ul class="py-1 text-gray-700 dark:text-gray-300">
        <x-layout.navbar.user-menu.dropdown-item href="#" label="Log out"
                                                 x-on:click.prevent="$refs.logoutForm.submit()" />
    </ul>
</div>

<form x-ref="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>