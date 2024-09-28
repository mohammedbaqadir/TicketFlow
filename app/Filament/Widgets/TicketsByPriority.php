<?php
    declare( strict_types = 1 );

    namespace App\Filament\Widgets;

    use App\Models\Ticket;
    use Filament\Widgets\ChartWidget;

    class TicketsByPriority extends ChartWidget
    {
        protected static ?string $heading = 'Tickets by Priority';
        protected static ?string $description = 'Overview of all tickets in the system by their priority, filterable by period of time.';
        public ?string $filter = 'year';

        protected function getData() : array
        {
            $priorities = Ticket::select( 'priority' )
                ->groupBy( 'priority' )
                ->orderBy( 'priority' )
                ->get()
                ->mapWithKeys( function ( $item ) {
                    return [ $item->priority => Ticket::where( 'priority', $item->priority )->count() ];
                } );

            return [
                'datasets' => [
                    [
                        'label' => 'Tickets by Priority',
                        'data' => $priorities->values(),
                        'backgroundColor' => [ '#FF6384', '#36A2EB', '#FFCE56' ],
                    ],
                ],
                'labels' => $priorities->keys(),
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
            return 'bar';
        }
    }