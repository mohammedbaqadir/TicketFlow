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



        public function render()
        {
            return view( 'livewire.solution-entry' );
        }
    }