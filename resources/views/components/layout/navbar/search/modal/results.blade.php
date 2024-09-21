<div x-show="!isLoading && results.length > 0" class="mt-4 max-h-60 overflow-y-auto">
    <template x-for="result in results" :key="result.id">
        <div>
            <x-layout.navbar.search.modal.results-item />
        </div>
    </template>
    <div x-show="currentPage < lastPage" class="mt-4 text-center">
        <button @click="loadMore" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Load More
        </button>
    </div>
    <div x-show="results.length > 0" class="mt-2 text-center text-sm text-gray-500">
        Showing <span x-text="results.length"></span> of <span x-text="totalResults"></span> results
    </div>
</div>