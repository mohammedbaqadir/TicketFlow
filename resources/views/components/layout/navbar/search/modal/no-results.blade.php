<div x-show="!isLoading && errorMessage" class="mt-4 text-center text-red-500">
    <span x-text="errorMessage"></span>
</div>

<div x-show="!isLoading && query && results.length === 0 && !errorMessage"
     class="mt-4 text-center text-gray-500 dark:text-gray-400">
    No results found for "<span x-text="query"></span>"
</div>