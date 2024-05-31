<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Submit Solution Modal -->
    <x-filament::modal id="submitSolutionModal" width="lg">
        <x-slot name="header">
            <span class="text-gray-100">Submit a Solution</span>
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="submitSolution" class="space-y-4">
                <textarea wire:model="content"
                          placeholder="Type your solution here..."
                          class="w-full bg-gray-900 text-gray-300 border-gray-700 rounded-md p-2 focus:border-indigo-500 focus:ring-indigo-500 @if ($ticket->status === 'closed') cursor-not-allowed opacity-50 @endif"
                          @if ($ticket->status === 'closed') readonly @endif></textarea>
                <input type="file"
                       wire:model="attachments"
                       multiple
                       class="w-full bg-gray-900 text-gray-300 border-gray-700 rounded-md focus:border-indigo-500 focus:ring-indigo-500 @if ($ticket->status === 'closed') cursor-not-allowed opacity-50 @endif"
                       @if ($ticket->status === 'closed') disabled @endif>
                <div class="flex justify-end">
                    <x-filament::button type="submit"
                                        class="bg-gradient-to-r from-green-500 to-teal-500 text-white hover:bg-green-400 transition duration-150"
                                        :disabled="$ticket->status === 'closed'">Submit
                    </x-filament::button>
                </div>
            </form>
        </x-slot>
    </x-filament::modal>

    <!-- Solution Validation Buttons -->
    @foreach ($solutions as $solution)
        <div class="p-4 border rounded-md mb-2 @if($solution->resolved === true) bg-green-900/50 border-green-500 shadow-lg @elseif($solution->resolved === false) bg-red-900/50 border-red-500 @else bg-gray-800 border-gray-700 @endif">
            <div class="flex items-center">
                <span class="inline-block bg-gray-700 text-gray-200 text-sm px-2 rounded-full mr-2">{{ $solution->user->name }}</span>
                <span class="text-gray-400 text-sm">{{ $solution->created_at->format('M d, h:i A') }}</span>
            </div>
            <p class="mt-2 text-gray-100">{{ $solution->content }}</p>
            <div class="mt-4 flex space-x-2">
                @if ($solution->resolved === null)
                    <x-filament::button wire:click="markSolutionAsValid({{ $solution->id }})"
                                        class="bg-gradient-to-r from-green-500 to-teal-500 text-white hover:bg-green-400 transition duration-150">
                        ✔️
                    </x-filament::button>
                    <x-filament::button wire:click="markSolutionAsInvalid({{ $solution->id }})"
                                        class="bg-gradient-to-r from-red-500 to-pink-500 text-white hover:bg-red-400 transition duration-150">
                        ❌
                    </x-filament::button>
                @elseif($solution->resolved === true)
                    <span class="text-green-500 text-2xl">✔️</span>
                @elseif($solution->resolved === false)
                    <x-filament::button wire:click="undoMarking({{ $solution->id }})"
                                        class="bg-gradient-to-r from-red-500 to-pink-500 text-white hover:bg-red-400 transition duration-150">
                        ❌
                    </x-filament::button>
                @endif
            </div>
        </div>
    @endforeach
</div>