<?php
    declare( strict_types = 1 );

    namespace App\Filament\Widgets;

    use App\Models\Ticket;
    use Filament\Widgets\StatsOverviewWidget as BaseWidget;
    use Filament\Widgets\StatsOverviewWidget\Stat;

    class TotalTickets extends BaseWidget
    {
        protected function getStats() : array
        {
            return [
                Stat::make( 'Total Tickets', Ticket::count() )
                    ->description( 'Total tickets created in the system' )
                    ->descriptionColor( 'gray' )
            ];
        }
    }