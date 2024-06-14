
<div class="p-4 rounded-lg ">
    <h3 class="text-lg font-semibold">{{ $solution->user->name }}</h3>
    <p>{{ $solution->content }}</p>
    <p class="text-sm text-gray-600">Created at: {{ $solution->created_at->format('Y-m-d H:i') }}</p>


    <div class="mt-4 flex space-x-2">
        <button wire:click="markAsValid({{ $solution }})"
                class="px-4 py-2 bg-green-500 text-green-50 rounded"
        >
            <x-heroicon-o-check class="w-6 h-6" />

        </button>
        <button wire:click="markAsInvalid({{ $solution }})"
                class="px-4 py-2 bg-red-500 text-black rounded"
        >
            <x-heroicon-o-x-mark class="w-6 h-6"/>

        </button>
    </div>
</div>