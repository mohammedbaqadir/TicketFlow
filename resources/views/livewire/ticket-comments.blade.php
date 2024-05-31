<!-- resources/views/livewire/ticket-comments.blade.php -->
<div class="p-4 bg-gray-800 rounded-lg shadow-lg">
    @foreach ($comments as $comment)
        <div class="p-4 border rounded-md mb-2 bg-gray-700 border-gray-600">
            <strong class="text-gray-100">{{ $comment->user->name }}</strong>
            <p class="text-gray-300">{{ $comment->content }}</p>
            <span class="text-gray-500 text-sm">{{ $comment->created_at->format('M d, h:i A') }}</span>
        </div>
    @endforeach

    <form wire:submit.prevent="addComment" class="mt-4">
        <textarea wire:model="content"
                  placeholder="Type your comment here..."
                  class="w-full bg-gray-900 text-gray-300 border-gray-700 rounded-md p-2 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        <x-filament::button type="submit"
                            class="mt-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white hover:bg-blue-400 transition duration-150">
            Add Comment
        </x-filament::button>
    </form>
</div>