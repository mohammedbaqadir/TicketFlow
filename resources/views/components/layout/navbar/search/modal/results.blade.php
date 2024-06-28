<div x-show="!isLoading && results.length > 0" class="mt-4 max-h-60 overflow-y-auto">
    <template x-for="result in results" :key="result.id">
        <div>
            <x-layout.navbar.search.modal.results-item />
        </div>
    </template>
</div>