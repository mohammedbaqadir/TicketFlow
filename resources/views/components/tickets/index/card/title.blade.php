@props(['ticket'])

<!-- Modal for Assign Confirmation -->
@can('assign', $ticket)
    <x-modal>
        <x-slot name="trigger">
            <!-- Title as the trigger -->
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white truncate cursor-pointer">
                <a href="#" @click.prevent>
                    {{ Str::limit($ticket->title, 100) }}
                </a>
            </h3>
        </x-slot>

        <x-slot name="title">Get Assigned to Ticket?</x-slot>

        <x-slot name="content">
            <p>Are you sure you want to assign this ticket to yourself?</p>
        </x-slot>

        <x-slot name="footer">
            <form method="POST" action="{{ route('tickets.assign', $ticket) }}">
                @csrf
                <button type="submit"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-500 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Assign
                </button>
            </form>
        </x-slot>
    </x-modal>
@else
    <!-- Title as a link when assignment is not allowed -->
    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white truncate">
        <a href="{{ route('tickets.show', $ticket) }}">
            {{ Str::limit($ticket->title, 100) }}
        </a>
    </h3>
@endcan