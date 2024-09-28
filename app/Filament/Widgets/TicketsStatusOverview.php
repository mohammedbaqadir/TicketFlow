<?php
    declare( strict_types = 1 );

    namespace App\Filament\Widgets;

    use App\Models\Ticket;
    use Filament\Widgets\ChartWidget;

    class TicketsStatusOverview extends ChartWidget
    {
        protected static ?string $heading = 'Tickets Statuses';
        protected static ?string $description = 'Overview of tickets statuses of all tickets in the system, filterable by period of time.';
        public ?string $filter = 'year';

        protected function getData() : array
        {
            $statuses = Ticket::select( 'status' )
                ->groupBy( 'status' )
                ->orderBy( 'status' )
                ->get()
                ->mapWithKeys( function ( $item ) {
                    return [ $item->status => Ticket::where( 'status', $item->status )->count() ];
                } );


            return [
                'datasets' => [
                    [
                        'data' => $statuses->values(),
                        'backgroundColor' => [ '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF' ],
                    ],
                ],
                'labels' => $statuses->keys(),
            ];
        }

        protected function getFilters() : ?array
        {
            return [
                'today' => 'Today',
                'week' => 'Last week',
                'month' => 'Last month',
                'year' => 'This year',
            ];
        }

        protected function getType() : string
        {
            return 'pie';
        }
    }