<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Submit Solution Modal -->
    <x-filament::modal id="submitSolutionModal" width="lg">
        <x-slot name="header">
            Submit a Solution
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="submitSolution" class="space-y-4">
                <textarea wire:model="content"
                          placeholder="Type your solution here..."
                          class="w-full border rounded-md p-2 @if ($ticket->status === 'closed') cursor-not-allowed opacity-50 @endif"
                          @if ($ticket->status === 'closed') readonly @endif></textarea>
                <input type="file"
                       wire:model="attachments"
                       multiple
                       class="w-full @if ($ticket->status === 'closed') cursor-not-allowed opacity-50 @endif"
                       @if ($ticket->status === 'closed') disabled @endif>
                <div class="flex justify-end">
                    <x-filament::button type="submit"
                                        class="bg-green-500 text-white"
                                        :disabled="$ticket->status === 'closed'">Submit
                    </x-filament::button>
                </div>
            </form>
        </x-slot>
    </x-filament::modal>

    <!-- Solution Validation Buttons -->
    @foreach ($solutions as $solution)
        <div class="p-4 border rounded-md mb-2 @if($solution->resolved === true) bg-green-100 border-green-500 shadow-lg @elseif($solution->resolved === false) bg-red-100 border-red-500 @else bg-white border-gray-300 @endif">
            <div class="flex items-center">
                <span class="inline-block bg-gray-200 text-gray-800 text-sm px-2 rounded-full mr-2">{{ $solution->user->name }}</span>
                <span class="text-gray-500 text-sm">{{ $solution->created_at->format('M d, h:i A') }}</span>
            </div>
            <p class="mt-2">{{ $solution->content }}</p>
            <div class="mt-4">
                @if ($solution->resolved === null)
                    <x-filament::button wire:click="markSolutionAsValid({{ $solution->id }})"
                                        class="bg-green-500 text-white">✔️
                    </x-filament::button>
                    <x-filament::button wire:click="markSolutionAsInvalid({{ $solution->id }})"
                                        class="bg-red-500 text-white">❌
                    </x-filament::button>
                @elseif($solution->resolved === true)
                    <span class="text-green-500 text-2xl">✔️</span>
                @elseif($solution->resolved === false)
                    <x-filament::button wire:click="undoMarking({{ $solution->id }})" class="bg-red-500 text-white">❌
                    </x-filament::button>
                @endif
            </div>
        </div>
    @endforeach
</div>