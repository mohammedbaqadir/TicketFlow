@props(['title', 'tickets', 'no_tickets_msg'])

<section class="mb-8 bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-700 rounded-lg shadow-lg p-6">
    <x-tickets.index.section.title :$title class="mb-6"></x-tickets.index.section.title>
    @if($tickets->isEmpty())
        <div class="bg-gray-200 dark:bg-gray-600 rounded-lg p-4 text-center text-gray-700 dark:text-gray-300">
            {{$no_tickets_msg}}
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($tickets as $ticket)
                <x-tickets.index.card :$ticket></x-tickets.index.card>
            @endforeach
        </div>
    @endif
</section>