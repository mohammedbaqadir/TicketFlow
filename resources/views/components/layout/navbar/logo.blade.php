<a href="{{ route('home') }}" class="flex items-center space-x-4 group">
    <div class="relative">
        <img src="{{ asset('images/logo.png') }}"
             class="h-12 transform transition-transform duration-500 ease-in-out group-hover:scale-110"
             alt="{{ config('app.name') }} Logo" />
        <div class="absolute inset-0 rounded-full bg-gradient-to-r from-teal-500 to-green-500 opacity-0 dark:opacity-70 blur-lg dark:block"></div>
    </div>
    <span class="self-center text-4xl font-extrabold text-gray-900 dark:text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-green-500 dark:from-teal-200 dark:to-green-200 tracking-wide">
        {{ config('app.name') }}
    </span>
</a>