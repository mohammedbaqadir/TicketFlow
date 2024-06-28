@props(['solution', 'canRate'])

<div class="bg-white dark:bg-gray-700 shadow rounded-lg p-6 mb-4">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Submitted by
                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $solution->user->name }}</span>
                on {{ $solution->created_at->format('M d, Y H:i') }}
            </p>
            <div class="mt-2 text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ $solution->content }}
            </div>
        </div>
        @if($solution->resolved === true)
            <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                Solved
            </span>
        @endif
    </div>
    @if($canRate)
        <div class="mt-4 flex justify-end space-x-2">
            <x-button
                    @click="rateSolution({{ $solution->id }}, true)"
                    variant="secondary"
                    class="text-green-500 hover:text-green-600"
            >
                <x-heroicon-o-hand-thumb-up class="h-6 w-6" />
            </x-button>
            <x-button
                    @click="rateSolution({{ $solution->id }}, false)"
                    variant="secondary"
                    class="text-red-500 hover:text-red-600"
            >
                <x-heroicon-o-hand-thumb-down class="h-6 w-6" />
            </x-button>
        </div>
    @endif
</div>