<div>
    @foreach ($comments as $comment)
        <div class="p-4 border rounded-md mb-2">
            <strong>{{ $comment->user->name }}</strong>
            <p>{{ $comment->content }}</p>
            <span class="text-gray-500 text-sm">{{ $comment->created_at->format('M d, h:i A') }}</span>
        </div>
    @endforeach

    <form wire:submit.prevent="addComment">
        <textarea wire:model="content" placeholder="Type your comment here..."></textarea>
        <x-filament::button type="submit">Add Comment</x-filament::button>
    </form>
</div>