<x-filament::page>
    <div class="grid grid-cols-1 gap-4 ">
        @livewire(App\Filament\Widgets\PendingTickets::class)
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 ">
        @livewire(App\Filament\Widgets\TotalTickets::class)
        @livewire(App\Filament\Widgets\ResolutionPerformance::class)
        @livewire(App\Filament\Widgets\TicketsStatusOverview::class)
        @livewire(App\Filament\Widgets\TicketsByPriority::class)
    </div>

    <div class="mt-8 grid grid-cols-1 ">
        @livewire(App\Filament\Widgets\AgentsWorkload::class)
    </div>
</x-filament::page>