<x-filament-panels::page :full-height="true">
    <h2>Tickets pending action, of this week.</h2>
    <div x-data wire:ignore.self class="md:flex  gap-4 pb-4">
        @foreach($statuses as $status)
            @include(static::$statusView)
        @endforeach

        <div wire:ignore>
            @include(static::$scriptsView)
        </div>
    </div>

    @unless($disableEditModal)
        <x-filament-kanban::edit-record-modal/>
    @endunless
</x-filament-panels::page>