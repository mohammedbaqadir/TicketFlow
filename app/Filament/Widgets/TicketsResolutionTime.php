<?php

    namespace App\Filament\Widgets;

    use App\Models\Ticket;
    use Carbon\Carbon;
    use Filament\Widgets\ChartWidget;

    class TicketsResolutionTime extends ChartWidget
    {
        protected static ?string $heading = 'Tickets Resolution Time';
        protected static ?string $description = 'The average resolution time of tickets. filterable by period of time.';

        public ?string $filter = 'year';

        protected function getData() : array
        {
            $resolutionTimes = Ticket::whereNotNull( 'updated_at' )
                ->get()
                ->groupBy( function ( $date ) {
                    return Carbon::parse( $date->created_at )->format( 'Y-m-d' );
                } )
                ->map( function ( $day ) {
                    return $day->avg( function ( $ticket ) {
                        return Carbon::parse( $ticket->created_at )->diffInHours( $ticket->updated_at );
                    } );
                } );

            return [
                'datasets' => [
                    [
                        'label' => 'Average Resolution Time (hours)',
                        'data' => $resolutionTimes->values(),
                        'borderColor' => '#36A2EB',
                        'fill' => false,
                    ],
                ],
                'labels' => $resolutionTimes->keys(),
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


        protected function getType(): string
        {
            return 'line';
        }
    }