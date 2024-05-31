<!-- resources/views/components/ticket-card.blade.php -->
@props(['ticket'])

<div class="bg-gradient-to-r from-blue-500 to-purple-500 p-6 rounded-lg shadow-md transition-transform transform hover:scale-105 hover:shadow-xl">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold text-white">{{ $ticket->title }}</h3>
        <span class="text-sm text-gray-200">{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
    </div>
    <p class="text-white mb-4">{{ $ticket->description }}</p>
    <div class="flex justify-between items-center">
        <div>
            <span class="text-sm text-gray-200">Status: </span>
            <span class="text-sm text-gray-100">{{ $ticket->status }}</span>
        </div>
        <div>
            <span class="text-sm text-gray-200">Priority: </span>
            <span class="text-sm text-gray-100">{{ $ticket->priority }}</span>
        </div>
    </div>
    <div class="flex justify-between items-center mt-4">
        <div>
            <span class="text-sm text-gray-200">Created By: </span>
            <span class="text-sm text-gray-100">{{ optional($ticket->creator)->name ?? 'Unknown' }}</span>
        </div>
        <div>
            <span class="text-sm text-gray-200">Assigned To: </span>
            <span class="text-sm text-gray-100">{{ optional($ticket->assignee)->name ?? 'Unassigned' }}</span>
        </div>
    </div>
    <div class="mt-4">
        <span class="text-sm text-gray-200">Timeout At: </span>
        <span class="text-sm text-gray-100">{{ $ticket->timeout_at->format('Y-m-d H:i') }}</span>
    </div>
</div>