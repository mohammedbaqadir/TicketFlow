<?php

    namespace App\Filament\Pages;

    use App\Filament\Widgets\AgentLeaderboard;
    use App\Filament\Widgets\AgentsWorkload;
    use App\Filament\Widgets\PendingTickets;
    use App\Filament\Widgets\TicketsByPriority;
    use App\Filament\Widgets\TicketsResolutionTime;
    use App\Filament\Widgets\TicketsStatusOverview;
    use App\Filament\Widgets\TotalTickets;
    use App\Helpers\AuthHelper;
    use Filament\Pages\Page;

    class Dashboard extends Page
    {
        protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
        protected ?string $heading = '';
        protected static string $view = 'filament.pages.dashboard';

        public static function canAccess() : bool
        {
            return AuthHelper::userHasRole( 'admin');
        }

//        protected function getHeaderWidgets() : array
//        {
//            return [
//                TotalTickets::class,
//                AgentLeaderboard::class,
//                PendingTickets::class,
//            ];
//        }
//        protected function getFooterWidgets() : array
//        {
//            return [
//                TicketsResolutionTime::class,
//                AgentsWorkload::class,
//                TicketsStatusOverview::class,
//                TicketsByPriority::class,
//            ];
//        }


    }