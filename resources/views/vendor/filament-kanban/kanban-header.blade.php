@php
    $icon = '';
    $color = '';
    $title = $status['title'];

    match ($title) {
        'Open' => [$icon = 'heroicon-o-plus-circle', $color = 'text-green-500'],
        'In Progress' => [$icon = 'heroicon-o-wrench-screwdriver', $color = 'text-yellow-500'],
        'Awaiting Acceptance' => [$icon = 'heroicon-o-clock', $color = 'text-blue-500'],
        'Elevated' => [$icon = 'heroicon-o-exclamation-circle', $color = 'text-red-500'],
        'Closed' => [$icon = 'heroicon-o-check-circle', $color = 'text-gray-500'],
        default => [$icon = 'heroicon-o-question-mark-circle', $color = 'text-gray-500'],
    };
@endphp

<h3 class="mb-2 px-4 font-semibold text-lg {{ $color }} ">
    <x-dynamic-component :component="$icon" class="inline h-6 w-6" />
    {{ $title }}
</h3>