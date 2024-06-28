@props(['ticket'])

@if($ticket->assignee && $ticket->assignee->id === auth()->id() && $ticket->status !== 'closed')
    <div class="mt-6 flex justify-end">
        <x-button
                href="{{ route('tickets.solutions.create', $ticket) }}"
                variant="primary"
                class="px-6 py-3"
        >
            Submit Solution
        </x-button>
    </div>
@endif