<?php
    declare( strict_types = 1 );

    namespace App\Config;

    use App\Validators\TicketConfigValidator;
    use Illuminate\Support\Facades\Config;

    /**
     * Class TicketConfig
     *
     * Provides an abstraction layer for accessing all ticket-related configurations.
     *
     *
     * @package App\Config
     */
    class TicketConfig
    {
        /**
         * Get index page grouping configuration
         *
         * Returns the configuration for how tickets should be grouped in the agent's index view.
         *
         * @return array{
         *     title: string,
         *     status: array<string>,
         *     assignee_required?: bool,
         *     no_tickets_msg: string
         * }[]
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
         * Returns the configuration for how tickets should be grouped in the employee's view.
         *
         * @return array{
         *     title: string,
         *     status: array<string>,
         *     no_tickets_msg: string
         * }[]
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
         * Returns mapping of status keys to their display labels.
         *
         * @return array<string, string> Map of status keys to display labels
         */
        public static function getStatuses() : array
        {
            $statuses = Config::get( 'tickets.statuses' );
            TicketConfigValidator::validateStatuses( $statuses );
            return $statuses;
        }

        /**
         * Get display label for a specific status
         *
         * Returns the user-facing label for a given status key.
         *
         * @param  string  $status  Status key to get label for
         * @return string The display label for the status
         * @throws \RuntimeException If status doesn't exist in configuration
         */
        public static function getStatusLabel( string $status ) : string
        {
            $statuses = self::getStatuses();
            TicketConfigValidator::validateExistence( $status, $statuses, 'status' );
            return $statuses[ $status ];
        }

        /**
         * Get all available ticket priorities
         *
         * Returns mapping of priority keys to their display labels.
         *
         * @return array<string, string> Map of priority keys to display labels
         */
        public static function getPriorities() : array
        {
            $priorities = Config::get( 'tickets.priorities' );
            TicketConfigValidator::validatePriorities( $priorities );
            return $priorities;
        }

        /**
         * Get display label for a specific priority
         *
         * Returns the user-facing label for a given priority key.
         *
         * @param  string  $priority  Priority key to get label for
         * @return string The display label for the priority
         * @throws \RuntimeException If priority doesn't exist in configuration
         */
        public static function getPriorityLabel( string $priority ) : string
        {
            $priorities = self::getPriorities();
            TicketConfigValidator::validateExistence( $priority, $priorities, 'priority' );
            return $priorities[ $priority ];
        }

        /**
         * Get all priority timeout configurations
         *
         * Returns mapping of priority levels to their required response times in hours.
         *
         * @return array<string, int> Map of priority levels to timeout hours
         */
        public static function getPriorityTimeouts() : array
        {
            $timeouts = Config::get( 'tickets.priority_timeouts' );
            TicketConfigValidator::validatePriorityTimeouts( $timeouts );
            return $timeouts;
        }

        /**
         * Get timeout hours for a specific priority
         *
         * Returns the number of hours allowed for initial response for a given priority level.
         *
         * @param  string  $priority  Priority key to get timeout for
         * @return int Number of hours allowed for response
         * @throws \RuntimeException If priority doesn't exist in configuration
         */
        public static function getTimeoutForPriority( string $priority ) : int
        {
            $timeouts = self::getPriorityTimeouts();
            TicketConfigValidator::validateExistence( $priority, $timeouts, 'priority timeout' );
            return $timeouts[ $priority ];
        }
    }