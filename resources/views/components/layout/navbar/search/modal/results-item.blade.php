<div class="p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
    <a :href="'/tickets/' + result.id" class="block">
        <h3 x-text="result.title" class="font-semibold text-gray-900 dark:text-white"></h3>
        <p x-text="result.excerpt" class="text-sm text-gray-600 dark:text-gray-300"></p>
        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            <span x-text="'Created by: ' + (result.created_by || 'Unknown')"></span> |
            <span x-text="'Created at: ' + formatDate(result.created_at)"></span>
            <template x-if="result.assigned_to">
                | <span x-text="'Assigned to: ' + result.assigned_to"></span>
            </template>
        </div>
    </a>
</div>