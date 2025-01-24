<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Support\Collection;
    use Throwable;

    class GroupTicketsAction
    {
        /**
         * Group tickets based on provided configuration
         * @param  Collection<int, Ticket>  $tickets
         * @param  array<int, array{
         *      title: string,
         *      no_tickets_msg: string,
         *      status: array<string>,
         *      assignee_required?: bool
         *  }>  $groupConfigs
         * @return array<int, array{
         *      title: string,
         *      tickets: Collection<int, Ticket>,
         *      no_tickets_msg: string
         * }>
         */
        public function execute( Collection $tickets, array $groupConfigs, ?User $user = null ) : array
        {
            try {
                if ( $tickets->isEmpty() ) {
                    return $this->getEmptyGroups( $groupConfigs );
                }

                return array_map(
                    function ( array $config ) use ( $tickets, $user ) : array {
                        return [
                            'title' => $config['title'],
                            'tickets' => $this->filterTickets( $tickets, $config, $user ),
                            'no_tickets_msg' => $config['no_tickets_msg']
                        ];
                    },
                    $groupConfigs
                );
            } catch (Throwable $e) {
                report( $e );
                return $this->getFallbackGroups( $tickets );
            }
        }

        /**
         * @param  Collection<int, Ticket>  $tickets
         * @param  array{
         *     title: string,
         *     no_tickets_msg: string,
         *     status: array<string>,
         *     assignee_required?: bool
         * }  $config
         * @return Collection<int, Ticket>
         */
        private function filterTickets( Collection $tickets, array $config, ?User $user ) : Collection
        {
            try {
                return $tickets->filter( function ( $ticket ) use ( $config, $user ) {
                    // Status check is mandatory
                    if ( !$this->matchesStatus( $ticket, $config ) ) {
                        return false;
                    }

                    // Assignee check if required
                    if ( isset( $config['assignee_required'] ) ) {
                        if ( $config['assignee_required'] && ( !$user || $ticket->assignee_id !== $user->id ) ) {
                            return false;
                        }
                        if ( !$config['assignee_required'] && $ticket->assignee_id !== null ) {
                            return false;
                        }
                    }

                    return true;
                } );
            } catch (Throwable $e) {
                report( $e );
                return collect();
            }
        }

        /**
         * Check if ticket status matches configuration
         * @param  Ticket  $ticket
         * @param  array{
         *     title: string,
         *     no_tickets_msg: string,
         *     status: array<string>,
         *     assignee_required?: bool
         * }  $config
         */
        private function matchesStatus( $ticket, array $config ) : bool
        {
            return \in_array( $ticket->status, $config['status'], true );
        }


        /**
         * Generate empty groups from configuration
         * @param  array<int, array{
         *     title: string,
         *     no_tickets_msg: string,
         *     status: array<string>,
         *     assignee_required?: bool
         * }>  $groupConfigs
         * @return array<int, array{
         *     title: string,
         *     tickets: Collection<int, Ticket>,
         *     no_tickets_msg: string
         * }>
         */
        private function getEmptyGroups( array $groupConfigs ) : array
        {
            return array_map(
                function ( array $config ) : array {
                    return [
                        'title' => $config['title'],
                        'tickets' => collect(),
                        'no_tickets_msg' => $config['no_tickets_msg']
                    ];
                },
                $groupConfigs
            );
        }

        /**
         * Generate fallback groups in case of error
         * @param  Collection<int, Ticket>  $tickets
         * @return array<int, array{
         *     title: string,
         *     tickets: Collection<int, Ticket>,
         *     no_tickets_msg: string
         * }>
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