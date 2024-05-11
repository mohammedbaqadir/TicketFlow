<div>
    <h3>Timeline</h3>
    <ul>
        @foreach($events as $event)
            <li>
                @php
                    $timestamp = $event->created_at;
                    $formattedTime = $timestamp->isToday() ? $timestamp->format('h:i A') : $timestamp->format('M d, h:i A');
                @endphp
                {{ $formattedTime }} - {{ $event->description }}
            </li>
        @endforeach
    </ul>

    <textarea wire:model="newComment"></textarea>
    <button wire:click="addComment">Add Comment</button>
</div>