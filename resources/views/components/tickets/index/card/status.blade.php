@props(['status'])

@php
    $statusStyles = [
        'open' => 'bg-teal-300 text-teal-800 border-teal-400 dark:bg-teal-600 dark:text-teal-200 dark:border-teal-600',
        'in-progress' => 'bg-amber-300 text-amber-800 border-amber-400 dark:bg-amber-600 dark:text-amber-200 dark:border-amber-600',
        'awaiting-acceptance' => 'bg-lime-300 text-lime-800 border-lime-400 dark:bg-lime-600 dark:text-lime-200 dark:border-lime-600',
        'escalated' => 'bg-rose-300 text-rose-800 border-rose-400 dark:bg-rose-600 dark:text-rose-200 dark:border-rose-600',
        'resolved' => 'bg-emerald-300 text-emerald-800 border-emerald-400 dark:bg-emerald-600 dark:text-emerald-200
        dark:border-emerald-600',
    ];

    $statusIcons = [
    'open' => 'heroicon-o-envelope-open',
    'in-progress' => 'heroicon-o-play',
    'awaiting-acceptance' => 'heroicon-o-clock',
    'escalated' => 'heroicon-o-arrow-trending-up',
    'resolved' => 'heroicon-o-check-circle',
];


    $statusLabels = config('enums.ticket_status');

    $statusStyle = $statusStyles[$status] ?? $statusStyles['open'];
    $statusIcon = $statusIcons[$status] ?? $statusIcons['open'];
    $statusLabel = $statusLabels[$status] ?? $statusLabels['open'];
@endphp

<span class="inline-flex items-center px-2 py-1 rounded-full text-xs sm:text-sm font-medium {{ $statusStyle }}">
    <x-dynamic-component :component="$statusIcon" class="w-3 h-3 sm:w-4 sm:h-4 mr-1" />
    {{ $statusLabel }}
</span>