@props(['title' , 'url'])

<h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white truncate">
    <a href="{{$url}}" class="relative group">
        {{ Str::limit($title, 100) }}
        <span class="absolute left-0 bottom-0 h-1 w-full bg-gradient-to-r from-teal-500 to-green-500 dark:from-teal-200 dark:to-green-200 scale-x-0 group-hover:scale-x-100 transform transition-transform duration-300 ease-in-out"></span>
        <span class="absolute left-0 bottom-0 h-1 w-full opacity-50 blur-sm bg-gradient-to-r from-teal-500 to-green-500 dark:from-teal-200 dark:to-green-200"></span>
    </a>
</h3>