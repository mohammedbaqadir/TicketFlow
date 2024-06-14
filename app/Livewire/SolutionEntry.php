<?php

    namespace App\Livewire;

    use App\Models\Solution;
    use Filament\Forms\Concerns\InteractsWithForms;
    use Filament\Forms\Contracts\HasForms;
    use Filament\Infolists\Concerns\InteractsWithInfolists;
    use Filament\Infolists\Contracts\HasInfolists;
    use Filament\Infolists\Infolist;
    use Filament\Notifications\Notification;
    use Livewire\Component;
    use Njxqlus\Filament\Components\Infolists\LightboxSpatieMediaLibraryImageEntry;

    class SolutionEntry extends Component
    {


        public $solution;


        public function mount( Solution $solution)
        {
            $this->solution = $solution;
        }

        public function markAsValid( Solution $solution ) : void
        {
            $solution->markValid();
            Notification::make()
                ->title( 'Valid' )
                ->body( 'Solution Marked Valid. Ticket Closed' )
                ->success()
                ->send();
        }

        public function markAsInvalid( Solution $solution ) : void
        {
            $solution->markInvalid();
            Notification::make()
                ->title( 'Invalid' )
                ->body( 'Solution Marked Invalid.' )
                ->info()
                ->send();
        }

        public function render()
        {
            return view( 'livewire.solution-entry' );
        }
    }