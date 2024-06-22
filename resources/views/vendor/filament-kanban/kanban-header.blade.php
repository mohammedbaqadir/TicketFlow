@php
    $icon = '';
    $color = '';
    $title = $status['title'];

    match ($title) {
        'Open' => [$icon = 'heroicon-o-ticket', $color = 'text-green-500'],
        'In Progress' => [$icon = 'heroicon-o-wrench-screwdriver', $color = 'text-yellow-500'],
        'Awaiting Acceptance' => [$icon = 'heroicon-o-clock', $color = 'text-blue-500'],
    };
@endphp

<h3 class="mb-2 px-4 font-semibold text-lg {{ $color }} ">
    <x-dynamic-component :component="$icon" class="inline h-6 w-6" />
    {{ $title }}
</h3>