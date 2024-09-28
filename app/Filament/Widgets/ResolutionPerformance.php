<?php
    declare( strict_types = 1 );

    namespace App\Filament\Widgets;

    use App\Models\Ticket;
    use Carbon\Carbon;
    use Filament\Widgets\ChartWidget;

    class ResolutionPerformance extends ChartWidget
    {
        protected static ?string $heading = 'Daily Ticket Resolution Performance';
        protected static ?string $description = 'This chart shows how well tickets were resolved within the expected time on each day. '
        . 'It reflects both the number of tickets resolved and their priority. '
        . 'High-priority tickets should be resolved in less than 2 hours, medium-priority within 4 hours, and low-priority within 8 hours. '
        . 'The performance score (%) indicates the percentage of tickets resolved on time. 100% means all tickets were resolved within their expected time.';

        /**
         * Fetches and calculates performance data for the chart.
         * This focuses on the percentage of tickets resolved on time based on priority and resolution time per day.
         *
         * @return array
         */
        protected function getData() : array
        {
            // Mapping priorities to max resolution time in hours
            $priorityTimes = [
                'high' => 2,   // High priority tickets should be resolved within 2 hours
                'medium' => 4, // Medium priority tickets should be resolved within 4 hours
                'low' => 8,    // Low priority tickets should be resolved within 8 hours
            ];

            // Fetch only tickets with 'resolved' status
            $tickets = Ticket::where( 'status', 'resolved' )
                ->whereNotNull( 'updated_at' )
                ->get();

            // Group tickets by the day they were created (Y-m-d format)
            $performanceByDay = $tickets->groupBy( function ( $ticket ) {
                return Carbon::parse( $ticket->created_at )->format( 'Y-m-d' );
            } )->map( function ( $ticketsPerDay ) use ( $priorityTimes ) {
                $totalTickets = $ticketsPerDay->count(); // Number of tickets resolved that day
                $resolvedOnTimeCount = 0; // Counter for tickets resolved within allowed time

                // Iterate over each ticket of that day
                foreach ( $ticketsPerDay as $ticket ) {
                    $priority = $ticket->priority;
                    // Get max allowed time based on ticket priority, default to 'low' if unknown
                    $maxAllowedTime = $priorityTimes[ $priority ] ?? $priorityTimes['low'];

                    // Calculate the actual resolution time in hours (difference between created_at and updated_at)
                    $resolutionTime = Carbon::parse( $ticket->created_at )->diffInHours( $ticket->updated_at );

                    // Check if the ticket was resolved within the allowed time
                    if ( $resolutionTime <= $maxAllowedTime ) {
                        $resolvedOnTimeCount++; // Increment if resolved on time
                    }
                }

                // Calculate performance score as the percentage of tickets resolved on time
                return ( $resolvedOnTimeCount / $totalTickets ) * 100;
            } );

            // Return the chart data structure
            return [
                'datasets' => [
                    [
                        'label' => 'Daily Ticket Resolution Performance (%)',
                        'data' => $performanceByDay->values(), // Performance scores for each day
                        'borderColor' => '#36A2EB', // Color of the line in the chart
                        'fill' => false, // No fill below the line
                    ],
                ],
                'labels' => $performanceByDay->keys(), // Days (X-axis labels) representing when tickets were resolved
            ];
        }

        /**
         * Provides filtering options for the widget, allowing users to view data for different time ranges.
         *
         * @return ?array
         */
        protected function getFilters() : ?array
        {
            return [
                'today' => 'Today',
                'week' => 'Last week',
                'month' => 'Last month',
                'year' => 'This year',
            ];
        }

        /**
         * Defines the type of chart to display. In this case, a line chart.
         *
         * @return string
         */
        protected function getType() : string
        {
            return 'line';
        }

    }