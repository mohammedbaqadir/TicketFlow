<?php
    declare( strict_types = 1 );

    namespace App\Config;

    use App\Validators\TicketConfigValidator;
    use Illuminate\Support\Facades\Config;
    use RuntimeException;

    /**
     * Class TicketConfig
     * Provides an abstraction layer for accessing all ticket-related configurations.
     */
    class TicketConfig
    {
        /**
         * Get index page grouping configuration
         *
         * @return array
         */
        public static function getIndexGroupings() : array
        {
            $groupings = Config::get( 'tickets.groupings.index' );
            TicketConfigValidator::validateGroupings( $groupings );

            return $groupings;
        }

        /**
         * Get my tickets page grouping configuration
         *
         * @return array
         */
        public static function getMyTicketsGroupings() : array
        {
            $groupings = Config::get( 'tickets.groupings.my_tickets' );
            TicketConfigValidator::validateGroupings( $groupings );

            return $groupings;
        }

        /**
         * Get all available ticket statuses
         *
         * @return array<string, string>
         */
        public static function getStatuses() : array
        {
            $statuses = Config::get( 'tickets.status' );
            TicketConfigValidator::validateStatuses( $statuses );

            return $statuses;
        }

        /**
         * Get label for a specific status
         *
         * @param  string  $status
         * @return string
         * @throws RuntimeException if status doesn't exist
         */
        public static function getStatusLabel( string $status ) : string
        {
            $statuses = self::getStatuses();

            if ( !isset( $statuses[ $status ] ) ) {
                throw new RuntimeException( "Invalid status: {$status}" );
            }

            return $statuses[ $status ];
        }

        /**
         * Get all available ticket priorities
         *
         * @return array<string, string>
         */
        public static function getPriorities() : array
        {
            $priorities = Config::get( 'tickets.priority' );
            TicketConfigValidator::validatePriorities( $priorities );

            return $priorities;
        }

        /**
         * Get label for a specific priority
         *
         * @param  string  $priority
         * @return string
         * @throws RuntimeException if priority doesn't exist
         */
        public static function getPriorityLabel( string $priority ) : string
        {
            $priorities = self::getPriorities();

            if ( !isset( $priorities[ $priority ] ) ) {
                throw new RuntimeException( "Invalid priority: {$priority}" );
            }

            return $priorities[ $priority ];
        }


        /**
         * Get priority timeout configuration
         *
         * @return array<string, int>
         */
        public static function getPriorityTimeouts() : array
        {
            $timeouts = Config::get( 'tickets.priority_timeout' );
            TicketConfigValidator::validatePriorityTimeouts( $timeouts );

            return $timeouts;
        }

        /**
         * Get timeout for a specific priority
         *
         * @param  string  $priority
         * @return int
         * @throws RuntimeException if priority doesn't exist
         */
        public static function getTimeoutForPriority( string $priority ) : int
        {
            $timeouts = self::getPriorityTimeouts();

            if ( !isset( $timeouts[ $priority ] ) ) {
                throw new RuntimeException( "No timeout configured for priority: {$priority}" );
            }

            return $timeouts[ $priority ];
        }
    }