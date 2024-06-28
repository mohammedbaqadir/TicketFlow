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