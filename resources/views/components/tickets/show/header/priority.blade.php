@props(['priority', 'label' ])

@php
    $priorityStyles = [
      'low' => 'bg-green-100 text-green-800 border-green-400 dark:bg-green-700 dark:text-green-200 dark:border-green-600',
      'medium' => 'bg-yellow-100 text-yellow-800 border-yellow-400 dark:bg-yellow-700 dark:text-yellow-200 dark:border-yellow-600',
      'high' => 'bg-red-100 text-red-800 border-red-400 dark:bg-red-700 dark:text-red-200 dark:border-red-600',
  ];

  $priorityIcons = [
    'low' => 'heroicon-o-signal',
    'medium' => 'heroicon-o-signal-slash',
    'high' => 'heroicon-o-bell-alert',
];

  $priorityStyle = $priorityStyles[$priority] ?? $priorityStyles['low'];
  $priorityIcon = $priorityIcons[$priority] ?? $priorityIcons['low'];

@endphp

<span class="inline-flex items-center px-2 py-1 rounded-full text-xs sm:text-sm font-medium {{ $priorityStyle }}">
                <x-dynamic-component :component="$priorityIcon" class='w-4 h-4 sm:w-5 sm:h-5 mr-1' />
                {{ $label }}
</span>