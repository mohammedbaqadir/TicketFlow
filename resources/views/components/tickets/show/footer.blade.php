@props(['ticket'])
@php
    use App\Helpers\AuthHelper;

@endphp
<div class="bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-gray-700 dark:to-gray-500 px-6 py-3 flex justify-between items-center rounded-b-lg shadow-inner">
    <div class="flex flex-col space-y-1 text-sm text-gray-600 dark:text-gray-300">
        <span class="inline-flex items-center">
            <x-heroicon-o-calendar class="w-4 h-4 mr-1" />
            Created: {{ $ticket->created_at->format('M d, Y H:i') }}
        </span>
        <span class="inline-flex items-center">
            <x-heroicon-o-arrow-path class="w-4 h-4 mr-1" />
            Updated: {{ $ticket->updated_at->format('M d, Y H:i') }}
        </span>
    </div>

</div>