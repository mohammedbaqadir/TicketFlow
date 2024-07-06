@php
    $ticketGroups = [
                [
                    'title' => 'Pending Action',
                    'tickets' => $tickets->filter( fn( $ticket ) => $ticket->status === 'awaiting-acceptance' ),
                    'no_tickets_msg' => 'no tickets are pending action from you',
                ],
                [
                    'title' => 'On-Going',
                    'tickets' => $tickets->filter( fn( $ticket ) => \in_array( $ticket->status,
                        [ 'open', 'in-progress', 'elevated' ] ) ),
                    'no_tickets_msg' => "you don't have ongoing tickets",
                ],
                [
                    'title' => 'Resolved',
                    'tickets' => $tickets->filter( fn( $ticket ) => $ticket->status === 'closed' ),
                    'no_tickets_msg' => "you don't have any closed tickets yet",
                ]
            ]
@endphp
@extends('components.layout.app')
@section('content')
    <div class="mb-6 flex justify-end">
        <x-button href="{{ route('tickets.create') }}" variant="primary" class="px-6 py-3">
            Create New Ticket
        </x-button>
    </div>
    @foreach ($ticketGroups as $group)
        <x-tickets.index.section.content :title="$group['title']"
                                         :tickets="$group['tickets']"
                                         :no_tickets_msg="$group['no_tickets_msg']"></x-tickets.index.section.content>
    @endforeach
@endsection