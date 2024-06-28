@props(['avatar_url', 'name'])

<div class="flex items-center">
    <img src="{{ $avatar_url }}"
         class="w-6 h-6 sm:w-8 sm:h-8 rounded-full mr-2"
         alt="{{ $name }}'s Avatar">
    <span class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white">{{ $name }}</span>
</div>