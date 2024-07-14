@php
    use App\Models\Ticket;
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
                        'no_tickets_msg' => 'you do not have ongoing tickets',
                    ],
                    [
                        'title' => 'Resolved',
                        'tickets' => $tickets->filter( fn( $ticket ) => $ticket->status === 'resolved' ),
                        'no_tickets_msg' => 'you do not have any closed tickets yet',
                    ]
                ]
@endphp
@extends('components.layout.app')
@section('content')
    <div class="mb-6 flex justify-end">
        @can('create', Ticket::class)
            <a href="{{route('tickets.create')}}"
               class="relative inline-flex items-center justify-start py-3 pl-4 pr-12 overflow-hidden font-semibold
               text-indigo-600 dark:text-white transition-all duration-150 ease-in-out rounded hover:pl-10 hover:pr-6
               bg-gray-50 dark:bg-slate-700 group">
                <span class="absolute bottom-0 left-0 w-full h-1 transition-all duration-150 ease-in-out
                bg-gradient-to-r from-blue-500 to-purple-500 dark:from-blue-300 dark:to-purple-300 group-hover:h-full"></span>
                <span class="absolute right-0 pr-4 duration-200 ease-out group-hover:translate-x-12">
                    <x-heroicon-o-arrow-right class="w-5 h-5 text-green-400" />
                </span>
                <span class="absolute left-0 pl-2.5 -translate-x-12 group-hover:translate-x-0 ease-out duration-200">
                    <x-heroicon-o-arrow-right class="w-5 h-5 text-green-200 dark:text-lime-200" />
                </span>
                <span class="relative w-full text-left transition-colors duration-200 ease-in-out
                group-hover:text-white">Open Ticket</span>
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