@props(['ticket'])


<div class="bg-gradient-to-r from-purple-100 to-blue-200 dark:from-gray-800 dark:to-gray-600 p-6 rounded-t-lg shadow-lg">
    <div class="flex justify-center lg:justify-end">
        <div class="flex flex-wrap lg:flex-nowrap space-x-2 lg:space-x-4 items-center">
            <x-tickets.show.header.priority :priority="$ticket->priority"
                                            :label="$ticket->formatted_priority" />

            <x-tickets.index.card.status :status="$ticket->status" class="text-xs sm:text-lg px-4 py-2" />

            <x-tickets.show.header.assignee
                    :ticket="$ticket"
            />
        </div>
    </div>

    <div class="flex justify-center items-center pt-6">
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white break-words max-w-3xl mb-6">
                {{ $ticket->title }}
            </h1>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mt-4">
        <div class="flex flex-row items-center space-y-0 space-x-4 order-2 lg:order-1">
            <x-tickets.index.card.requestor :avatar_url="$ticket->requestor->getFirstMediaUrl('avatar')"
                                            :name="$ticket->requestor->name" />
            <span class="block text-gray-800 dark:text-gray-300">|</span>
            <x-tickets.index.card.creation-time :created-at="$ticket->created_at"
                                                class="text-gray-800 dark:text-gray-300" />
        </div>

        <div class="flex order-1 lg:order-2 mt-4 lg:mt-0 lg:flex-grow-0 lg:self-end mb-8 lg:mb-0">
            <x-tickets.index.card.countdown :timeoutAt="$ticket->timeout_at"
                                            :ticketStatus="$ticket->status"
                                            :compact="true"
                                            class="text-lg px-2 py-1" />
        </div>
    </div>

    <div class="flex-1 py-12 px-6">
        <x-tickets.show.header.content :description="$ticket->description" />
    </div>

    @canany(['update', 'delete'], $ticket)
        <div class="px-4 pt-4 border-t border-gray-400 dark:border-gray-900 flex justify-end items-center space-x-5">

            @can('update', $ticket)
                <a href="{{route('tickets.edit', $ticket)}}">
                    <button type="button"
                            class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200
                        transition duration-200 transform hover:scale-110 hover:shadow-lg mb-1.5">
                        <x-heroicon-o-pencil-square class="w-5 h-5 sm:w-6 sm:h-6" />
                    </button>
                </a>
            @endcan

            @can('delete', $ticket)
                <x-tickets.show.header.delete :ticket="$ticket" />
            @endcan
        </div>
    @endcanany
</div>