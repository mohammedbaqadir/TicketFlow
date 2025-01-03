<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Models\User;
    use Illuminate\Support\Collection;
    use InvalidArgumentException;
    use Throwable;

    class GroupTicketsAction
    {
        /**
         * Group tickets based on provided configuration
         *
         * @param  Collection  $tickets  The collection of tickets to group
         * @param  array  $groupConfigs  Array of group configurations
         * @param  User|null  $user  The current user (optional)
         * @return array Grouped tickets with titles and messages
         * @throws InvalidArgumentException When group configuration is invalid
         */
        public function execute( Collection $tickets, array $groupConfigs, ?User $user = null ) : array
        {
            $ticketGroups = [];

            try {
                if ( $tickets->isEmpty() ) {
                    $ticketGroups = $this->getEmptyGroups( $groupConfigs );
                } else {
                    $ticketGroups = array_map(
                        fn( array $config ) : array => [
                            'title' => $config['title'],
                            'tickets' => $this->filterTickets( $tickets, $config, $user ),
                            'no_tickets_msg' => $config['no_tickets_msg']
                        ],
                        $groupConfigs
                    );
                }
            } catch (Throwable $e) {
                report( $e );
                $ticketGroups = $this->getFallbackGroups( $tickets );
            }
            return $ticketGroups;
        }

        /**
         * Filter tickets based on configuration
         *
         * @param  Collection  $tickets
         * @param  array  $config
         * @param  User|null  $user
         * @return Collection
         */
        private function filterTickets( Collection $tickets, array $config, ?User $user ) : Collection
        {
            try {
                return $tickets->filter( function ( $ticket ) use ( $config, $user ) {
                    $statusMatch = \in_array( $ticket->status, $config['status'], true );

                    if ( !$statusMatch ) {
                        return false;
                    }

                    if ( isset( $config['assignee_required'] ) && $config['assignee_required'] ) {
                        return $user && $ticket->assignee_id === $user->id;
                    }

                    return true;
                } );
            } catch (Throwable $e) {
                report( $e );
                return collect();
            }
        }

        /**
         * Generate empty groups from configuration
         *
         * @param  array  $groupConfigs
         * @return array
         */
        private function getEmptyGroups( array $groupConfigs ) : array
        {
            return array_map(
                fn( array $config ) : array => [
                    'title' => $config['title'],
                    'tickets' => collect(),
                    'no_tickets_msg' => $config['no_tickets_msg']
                ],
                $groupConfigs
            );
        }

        /**
         * Generate fallback groups in case of error
         *
         * @param  Collection  $tickets
         * @return array
         */
        private function getFallbackGroups( Collection $tickets ) : array
        {
            return [
                [
                    'title' => 'All Tickets',
                    'tickets' => $tickets,
                    'no_tickets_msg' => 'No tickets available'
                ]
            ];
        }
    }