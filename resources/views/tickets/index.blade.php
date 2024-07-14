@php
    use App\Models\Ticket;

        $ticketGroups = [
                    [
                        'title' => 'Working On It',
                        'tickets' => $tickets->filter( fn( $ticket ) =>
                            $ticket->assignee_id === auth()->id() &&
                            in_array($ticket->status, ['in-progress', 'awaiting-acceptance'])
                          ),
                        'no_tickets_msg' => 'You Are Not Currently Working on Any Tickets',
                    ],
                    [
                        'title' => 'Un-Assigned Tickets',
                        'tickets' => $tickets->filter( fn( $ticket ) => $ticket->status === 'open'),
                        'no_tickets_msg' => 'There are No Un-Assigned Tickets',
                    ],
                    [
                        'title' => 'Resolved It',
                        'tickets' => $tickets->filter( fn( $ticket ) =>
                                        $ticket->status === 'resolved' &&
                                        $ticket->assignee_id === auth()->id() ),
                        'no_tickets_msg' => 'You did NOT Resolve Any Tickets Yet',
                    ],
                    [
                        'title' => 'Resolved',
                        'tickets' => $tickets->filter( fn( $ticket ) => $ticket->status === 'resolved' ),
                        'no_tickets_msg' => 'There are No Resolved Tickets',
                    ]
                ]
@endphp
@extends('components.layout.app')
@section('content')
    <div class="mb-6 flex justify-end">
        @can('create', Ticket::class)
            <a href="{{ route('tickets.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 px-6 py-3">
                Create New Ticket
            </a>

        @endcan

    </div>
    @foreach ($ticketGroups as $group)
        <x-tickets.index.section.content :title="$group['title']"
                                         :tickets="$group['tickets']"
                                         :no_tickets_msg="$group['no_tickets_msg']">
        </x-tickets.index.section.content>
    @endforeach
@endsection