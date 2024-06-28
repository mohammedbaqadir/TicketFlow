@props(['ticket'])

@php
    $priorityStyles = [
        'low' => 'bg-green-100 text-green-800 border-green-400 dark:bg-green-700 dark:text-green-200 dark:border-green-600',
        'medium' => 'bg-yellow-100 text-yellow-800 border-yellow-400 dark:bg-yellow-700 dark:text-yellow-200 dark:border-yellow-600',
        'high' => 'bg-red-100 text-red-800 border-red-400 dark:bg-red-700 dark:text-red-200 dark:border-red-600',
    ];

    $priorityIcons = [
        'low' => 'heroicon-o-arrow-down-circle',
        'medium' => 'heroicon-o-minus-circle',
        'high' => 'heroicon-o-arrow-up-circle',
    ];

    $priorityStyle = $priorityStyles[$ticket->priority] ?? $priorityStyles['low'];
    $priorityIcon = $priorityIcons[$ticket->priority] ?? $priorityIcons['low'];
@endphp

<div class='grid grid-cols-1 lg:grid-cols-3 gap-6 p-6 bg-gradient-to-r from-indigo-100 to-purple-100  dark:from-gray-700 dark:to-gray-500 rounded-lg shadow-lg'>
    <div class='lg:col-span-2'>
        <h2 class='text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100'>Description</h2>
        <p class='text-gray-600 dark:text-gray-300 leading-relaxed'>{{ $ticket->description }}</p>
    </div>
    <div class="flex flex-col space-y-4">
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs sm:text-sm font-medium {{ $priorityStyle }}">
                <x-dynamic-component :component="$priorityIcon" class="w-4 h-4 sm:w-5 sm:h-5 mr-1" />
                Priority: {{ ucfirst($ticket->priority) }}
            </span>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs sm:text-sm font-medium bg-blue-100 text-blue-800 border-blue-400 dark:bg-blue-700 dark:text-blue-200 dark:border-blue-600">
                <x-heroicon-o-user class="w-4 h-4 sm:w-5 sm:h-5 mr-1" />
                Assignee:
                @if($ticket->assignee)
                    {{ $ticket->assignee->name }}
                @else
                Unassigned
                @endif
            </span>
        </div>
    </div>
</div>