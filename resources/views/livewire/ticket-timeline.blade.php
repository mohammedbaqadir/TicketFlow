<!-- resources/views/livewire/ticket-timeline.blade.php -->
<div class="p-4 bg-gray-800 rounded-lg shadow-lg">
    <h3 class="text-lg text-gray-100 mb-4">Timeline</h3>
    <ul class="text-gray-300">
        @foreach($events as $event)
            <li class="mb-2">
                @php
                    $timestamp = $event->created_at;
                    $formattedTime = $timestamp->isToday() ? $timestamp->format('h:i A') : $timestamp->format('M d, h:i A');
                @endphp
                {{ $formattedTime }} - {{ $event->description }}
            </li>
        @endforeach
    </ul>

    <textarea wire:model="newComment"
              class="w-full bg-gray-900 text-gray-300 border-gray-700 rounded-md p-2 mt-4 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    <button wire:click="addComment"
            class="mt-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded hover:bg-blue-400 transition duration-150">
        Add Comment
    </button>
</div>