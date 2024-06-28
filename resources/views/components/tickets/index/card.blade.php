@props(['ticket'])

@php
    $statusBackgrounds = [
        'open' => 'bg-teal-200 dark:bg-teal-700',
        'in-progress' => 'bg-amber-200 dark:bg-amber-700',
        'awaiting-acceptance' => 'bg-lime-200 dark:bg-lime-700',
        'elevated' => 'bg-rose-200 dark:bg-rose-700',
        'closed' => 'bg-emerald-200 dark:bg-emerald-700',
    ];

    $backgroundClass = $statusBackgrounds[$ticket->status] ?? $statusBackgrounds['open'];
    $isPendingAction = $ticket->status === 'awaiting-acceptance';
@endphp

<div class="rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg {{ $backgroundClass }} {{ $isPendingAction ? 'col-span-full' : '' }}">
    <div class="p-4">
        <div class="flex justify-between items-start mb-4">
            <x-tickets.index.card.title :title="$ticket->title" :url="route('tickets.show', $ticket)" />
            <x-tickets.index.card.status :status="$ticket->status" />
        </div>


        <x-tickets.index.card.requestor :avatar_url="$ticket->requestor->getFirstMediaUrl('avatar')"
                                        :name="$ticket->requestor->name" />

        <div class="flex justify-between items-end mt-4">
            <x-tickets.index.card.creation-time :created-at="$ticket->created_at" />

            <div class="flex items-center space-x-2">
                @if($ticket->assigned_to)
                    <x-tickets.index.card.assignee :assignee="$ticket->assignee" />
                @endif
            </div>
        </div>

        <x-tickets.index.card.countdown
                :timeoutAt="$ticket->timeout_at"
                :ticketStatus="$ticket->status"
        />
    </div>
</div>