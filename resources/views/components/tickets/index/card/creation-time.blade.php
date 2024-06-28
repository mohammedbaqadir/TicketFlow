@props(['createdAt'])

<div class="flex items-center text-xs sm:text-sm text-gray-800 dark:text-gray-300">
    <x-heroicon-s-clock class="w-3 h-3 sm:w-4 sm:h-4 mr-1" />
    {{ $createdAt->diffForHumans() }}
</div>