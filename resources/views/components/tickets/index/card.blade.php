@props(['ticket'])

@php
    $statusBackgrounds = [
        'open' => 'bg-teal-200 dark:bg-teal-700',
        'in-progress' => 'bg-amber-200 dark:bg-amber-700',
        'awaiting-acceptance' => 'bg-lime-200 dark:bg-lime-700',
        'escalated' => 'bg-rose-200 dark:bg-rose-700',
        'resolved' => 'bg-emerald-200 dark:bg-emerald-700',
    ];

    $backgroundClass = $statusBackgrounds[$ticket->status] ?? $statusBackgrounds['open'];
    $isPendingAction = in_array($ticket->status, ['in-progress', 'awaiting-acceptance']);
@endphp

<div class="rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg {{ $backgroundClass }} {{ $isPendingAction ? 'col-span-full' : '' }}">
    <div class="p-4 flex flex-col justify-between h-full">
        <div class="flex flex-col sm:flex-row justify-between items-start mb-4">

            <x-tickets.index.card.title :ticket="$ticket" />
            <x-tickets.index.card.status :status="$ticket->status" />
        </div>

        <!-- Countdown Timer -->
        <div class="flex justify-center mt-4">
            <x-tickets.index.card.countdown :timeoutAt="$ticket->timeout_at"
                                            :ticketStatus="$ticket->status"
                                            :compact="true" />
        </div>

        <!-- Footer with Creation Time and Assignee -->
        <div class="flex justify-between items-end mt-4">
            <div class="flex items-center space-x-2">
                <x-tickets.index.card.requestor :avatar_url="$ticket->requestor->getFirstMediaUrl('avatar')"
                                                :name="$ticket->requestor->name" />

                <span>|</span>
                <x-tickets.index.card.creation-time :created-at="$ticket->created_at" />
            </div>

            @if($ticket->assignee_id)
                <x-tickets.index.card.assignee :assignee="$ticket->assignee" class="ml-auto" />
            @endif
        </div>
    </div>
</div>