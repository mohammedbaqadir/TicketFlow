<div x-cloak>
    <div class="py-3 px-4 flex items-center space-x-4">
        <img class="w-10 h-10 rounded-full"
             src="{{ Auth::user()->getFirstMediaUrl('avatar') ?: asset('/images/default-avatar.jpg') }}"
             alt="user photo">
        <div>
            <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
            <span class="block text-sm text-gray-900 truncate dark:text-white">{{ Auth::user()->email }}</span>
        </div>
    </div>
    <ul class="py-1 text-gray-700 dark:text-gray-300">
        <x-layout.navbar.user-menu.dropdown-item href="{{route('profile.index')}}" label="My profile" />
        <x-layout.navbar.user-menu.dropdown-item href="{{route('preferences.index')}}" label="My Preferences" />
    </ul>
    <ul class="py-1 text-gray-700 dark:text-gray-300">
        <x-layout.navbar.user-menu.dropdown-item href="#" label="Log out"
                                                 x-on:click.prevent="$refs.logoutForm.submit()" />
    </ul>
</div>

<form x-ref="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>