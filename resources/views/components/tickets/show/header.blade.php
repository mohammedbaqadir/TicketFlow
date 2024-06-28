@props(['ticket'])

<div class="bg-gradient-to-r from-purple-100 to-blue-200 dark:from-gray-800 dark:to-gray-600 p-6 rounded-t-lg shadow-lg">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white break-words max-w-3xl">{{ $ticket->title }}</h1>
            <div class="mt-4 flex items-center space-x-4">
                <x-tickets.index.card.requestor :avatar_url="$ticket->requestor->getFirstMediaUrl('avatar')"
                                                :name="$ticket->requestor->name" />
                <span class="text-gray-800 dark:text-gray-300">|</span>
                <x-tickets.index.card.creation-time :created-at="$ticket->created_at"
                                                    class="text-gray-800 dark:text-gray-300" />
            </div>
        </div>
        <div class="mt-4 lg:mt-0 flex flex-col items-end space-y-4 lg:space-y-0 lg:items-center lg:flex-row lg:space-x-4">
            <x-tickets.index.card.status :status="$ticket->status" class="text-lg px-4 py-2" />
            <x-tickets.index.card.countdown :timeoutAt="$ticket->timeout_at" :ticketStatus="$ticket->status" />
        </div>
    </div>
</div>