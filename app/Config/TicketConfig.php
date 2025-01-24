<?php
    declare( strict_types = 1 );

    namespace App\Config;

    use App\Validators\TicketConfigValidator;
    use Illuminate\Support\Facades\Config;
    use RuntimeException;

    /**
     * Class TicketConfig
     *
     * Provides an abstraction layer for accessing all ticket-related configurations.
     * This includes ticket groupings, statuses, priorities, and their associated UI elements.
     *
     * @package App\Config
     */
    class TicketConfig
    {
        /**
         * Get all status keys
         *
         * @return array<string>
         */
        public static function getStatusKeys() : array
        {
            $statuses = Config::get( 'tickets.statuses' );
            TicketConfigValidator::validateStatuses( $statuses );
            return $statuses['keys'];
        }

        /**
         * Get all priority keys
         *
         * @return array<string>
         */
        public static function getPriorityKeys() : array
        {
            $priorities = Config::get( 'tickets.priorities' );
            TicketConfigValidator::validatePriorities( $priorities );
            return $priorities['keys'];
        }

        /**
         * Get index page grouping configuration
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
         * Get all available ticket statuses with their labels
         *
         * @return array<string, string>
         * @throws RuntimeException
         */
        public static function getStatuses() : array
        {
            $keys = self::getStatusKeys();
            $labels = Config::get( 'tickets.statuses.labels' );
            return array_combine( $keys, $labels );
        }

        /**
         * Get status badge styles for UI rendering
         *
         * @return array<string, string>
         * @throws RuntimeException
         */
        public static function getStatusBadgeStyles() : array
        {
            $keys = self::getStatusKeys();
            $styles = Config::get( 'tickets.statuses.badges.styles' );
            return array_combine( $keys, $styles );
        }

        /**
         * Get badge style for a specific status
         *
         * @param  string  $status  status key to get badge style for
         * @return string badge styles tailwind classes
         * @throws RuntimeException
         */
        public static function getBadgeStyleForStatus( string $status ) : string
        {
            $badgeStyles = self::getStatusBadgeStyles();
            TicketConfigValidator::validateExistence( $status, self::getStatusKeys(), 'status' );
            return $badgeStyles[ $status ];
        }

        /**
         * Get status icons for UI rendering
         *
         * @return array<string, string>
         * @throws RuntimeException
         */
        public static function getStatusIcons() : array
        {
            $keys = self::getStatusKeys();
            $icons = Config::get( 'tickets.statuses.badges.icons' );
            return array_combine( $keys, $icons );
        }

        /**
         * Get icon for a specific status
         *
         * @param  string  $status  status key to get icon for
         * @return string hero icon class
         * @throws RuntimeException
         */
        public static function getIconForStatus( string $status ) : string
        {
            $icons = self::getStatusIcons();
            TicketConfigValidator::validateExistence( $status, self::getStatusKeys(), 'status' );
            return $icons[ $status ];
        }


        /**
         * Get status card background styles
         *
         * @return array<string, string>
         * @throws RuntimeException
         */
        public static function getStatusCardBackgrounds() : array
        {
            $keys = self::getStatusKeys();
            $backgrounds = Config::get( 'tickets.statuses.cards.backgrounds' );
            return array_combine( $keys, $backgrounds );
        }

        /**
         * Get card background for a specific status
         *
         * @param  string  $status  status key to get card background for
         * @return string card background tailwind classes
         * @throws RuntimeException
         */
        public static function getCardBackgroundForStatus( string $status ) : string
        {
            $cardBackgrounds = self::getStatusCardBackgrounds();
            TicketConfigValidator::validateExistence( $status, self::getStatusKeys(), 'status' );
            return $cardBackgrounds[ $status ];
        }

        /**
         * Get display label for a specific status
         *
         * @param  string  $status  Status key to get label for
         * @return string The display label for the status
         * @throws RuntimeException
         */
        public static function getStatusLabel( string $status ) : string
        {
            $statuses = self::getStatuses();
            TicketConfigValidator::validateExistence( $status, self::getStatusKeys(), 'status' );
            return $statuses[ $status ];
        }

        /**
         * Get all available ticket priorities with their labels
         *
         * @return array<string, string>
         * @throws RuntimeException
         */
        public static function getPriorities() : array
        {
            $keys = self::getPriorityKeys();
            $labels = Config::get( 'tickets.priorities.labels' );
            return array_combine( $keys, $labels );
        }

        /**
         * Get priority badge styles for UI rendering
         *
         * @return array<string, string>
         * @throws RuntimeException
         */
        public static function getPriorityBadgeStyles() : array
        {
            $keys = self::getPriorityKeys();
            $styles = Config::get( 'tickets.priorities.badges.styles' );
            return array_combine( $keys, $styles );
        }

        /**
         * Get badge style for a specific priority
         *
         * @param  string  $priority  priority key to get badge style for
         * @return string badge styles tailwind classes
         * @throws RuntimeException
         */
        public static function getBadgeStyleForPriority( string $priority ) : string
        {
            $badgeStyles = self::getPriorityBadgeStyles();
            TicketConfigValidator::validateExistence( $priority, self::getPriorityKeys(), 'priority' );
            return $badgeStyles[ $priority ];
        }


        /**
         * Get priority icons for UI rendering
         *
         * @return array<string, string>
         * @throws RuntimeException
         */
        public static function getPriorityIcons() : array
        {
            $keys = self::getPriorityKeys();
            $icons = Config::get( 'tickets.priorities.badges.icons' );
            return array_combine( $keys, $icons );
        }

        /**
         * Get icon for a specific priority
         *
         * @param  string  $priority  priority key to get icon for
         * @return string hero icon class
         * @throws RuntimeException
         */
        public static function getIconForPriority( string $priority ) : string
        {
            $icons = self::getPriorityIcons();
            TicketConfigValidator::validateExistence( $priority, self::getPriorityKeys(), 'priority' );
            return $icons[ $priority ];
        }

        /**
         * Get display label for a specific priority
         *
         * @param  string  $priority  Priority key to get label for
         * @return string The display label for the priority
         * @throws RuntimeException
         */
        public static function getPriorityLabel( string $priority ) : string
        {
            $priorities = self::getPriorities();
            TicketConfigValidator::validateExistence( $priority, self::getPriorityKeys(), 'priority' );
            return $priorities[ $priority ];
        }

        /**
         * Get all priority timeout configurations
         *
         * @return array<string, int>
         * @throws RuntimeException
         */
        public static function getPriorityTimeouts() : array
        {
            $keys = self::getPriorityKeys();
            $timeouts = Config::get( 'tickets.priorities.timeouts' );
            return array_combine( $keys, $timeouts );
        }

        /**
         * Get timeout hours for a specific priority
         *
         * @param  string  $priority  Priority key to get timeout for
         * @return int Number of hours allowed for response
         * @throws RuntimeException
         */
        public static function getTimeoutForPriority( string $priority ) : int
        {
            $timeouts = self::getPriorityTimeouts();
            TicketConfigValidator::validateExistence( $priority, self::getPriorityKeys(), 'priority' );
            return $timeouts[ $priority ];
        }
    }