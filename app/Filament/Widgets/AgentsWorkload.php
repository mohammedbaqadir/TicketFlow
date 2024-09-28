<?php
    declare( strict_types = 1 );

    namespace App\Filament\Widgets;

    use App\Models\User;
    use Filament\Widgets\ChartWidget;

    class AgentsWorkload extends ChartWidget
    {
        protected static ?string $heading = 'Agents Workload';
        protected static ?string $description = 'Overview of the number of tickets assigned to each agent';

        protected function getData() : array
        {
            $agents = User::isAgent()
                ->withCount( 'assignedTickets' )
                ->orderBy( 'assigned_tickets_count', 'desc' )
                ->get()
                ->pluck( 'assigned_tickets_count', 'name' );

            return [
                'datasets' => [
                    [
                        'label' => 'Assigned Tickets',
                        'data' => $agents->values(),
                        'backgroundColor' => '#36A2EB',
                    ],
                ],
                'labels' => $agents->keys(),
            ];
        }

        protected function getType() : string
        {
            return 'bar';
        }
    }