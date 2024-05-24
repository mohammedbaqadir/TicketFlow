<!-- resources/views/livewire/my-tickets.blade.php -->
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search..." class="border p-2 rounded shadow">

        <div class="flex space-x-2">
            <select wire:model="statusFilter" class="border p-2 rounded shadow">
                <option value="">All Statuses</option>
                <option value="open">Open</option>
                <option value="in-progress">In Progress</option>
                <option value="awaiting-acceptance">Awaiting Acceptance</option>
                <option value="elevated">Elevated</option>
                <option value="closed">Closed</option>
            </select>

            <select wire:model="priorityFilter" class="border p-2 rounded shadow">
                <option value="">All Priorities</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
        </div>
    </div>

    @if ($tickets->isEmpty())
        <div class="text-center py-4">
            <p>No tickets found.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($tickets as $ticket)
                <x-ticket-card :ticket="$ticket" />
            @endforeach
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    @endif
</div>