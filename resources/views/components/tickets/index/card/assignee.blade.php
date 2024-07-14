@props(['assignee'])

<span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-gray-800 text-white dark:bg-gray-200 dark:text-gray-800 shadow-md">
    <x-heroicon-o-wrench-screwdriver
            class="w-3 h-3 sm:w-4 sm:h-4 mr-1" />
    {{ $assignee->name }}
</span>