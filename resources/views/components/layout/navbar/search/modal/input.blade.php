<div class="relative">
    <x-heroicon-o-magnifying-glass class="w-5 h-5 absolute left-3 top-3 text-gray-400" />
    <input
            type="text"
            x-model="query"
            @input.debounce.300ms="search"
            placeholder="Search for tickets..."
            class="w-full pl-10 pr-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
    >
</div>