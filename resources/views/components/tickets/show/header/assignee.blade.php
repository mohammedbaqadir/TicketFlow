@props(['ticket'])

@php
    $baseStyle = 'bg-blue-100 text-blue-800 border-blue-400 dark:bg-blue-700 dark:text-blue-200 dark:border-blue-600';
    $hoverStyle = 'hover:bg-red-200 hover:text-red-800 dark:hover:bg-red-900 dark:hover:text-red-200';
@endphp

@can('unassign', $ticket)
    <!-- Modal for Unassign Confirmation -->
    <x-modal>
        <x-slot name="trigger">
            <!-- Apply group class to handle hover effects -->
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs sm:text-sm font-medium {{ $baseStyle }} {{ $hoverStyle }} cursor-pointer transition-colors duration-200 group">
                <!-- X-mark icon, hidden by default and shown on hover -->
                <x-heroicon-o-x-mark class="w-4 h-4 sm:w-5 sm:h-5 mr-1 hidden group-hover:inline-block" />
                <!-- Wrench icon, visible by default and hidden on hover -->
                <x-heroicon-o-wrench-screwdriver class="w-4 h-4 sm:w-5 sm:h-5 mr-1 group-hover:hidden" />
                {{ $ticket->assignee->name }}
            </span>
        </x-slot>

        <x-slot name="title">Unassign from Ticket?</x-slot>

        <x-slot name="content">
            <p>Are you sure you want to unassign yourself from this ticket?</p>
        </x-slot>

        <x-slot name="footer">
            <form method="POST" action="{{ route('tickets.unassign', $ticket) }}">
                @csrf
                <button type="submit"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-500 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Unassign
                </button>
            </form>
        </x-slot>
    </x-modal>
@else
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium {{ $baseStyle }}">
                    <x-heroicon-o-wrench-screwdriver class="w-4 h-4 sm:w-5 sm:h-5 mr-1" />
                    {{ $ticket->assignee->name ?? 'Unassigned' }}
                </span>
@endcan