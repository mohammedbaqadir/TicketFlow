<?php
    declare( strict_types = 1 );

    namespace App\Config;

    use App\Validators\TicketGroupingsValidator;
    use Illuminate\Support\Facades\Config;

    /**
     * Class TicketGroupings
     * Provides an abstraction layer for accessing ticket grouping configurations.
     */
    class TicketGroupings
    {
        /**
         * Get index page grouping configuration
         *
         * @return array
         */
        public static function getIndexGroupings() : array
        {
            $groupings = Config::get( 'tickets.groupings.index', [] );
            TicketGroupingsValidator::validate( $groupings );

            return $groupings;
        }

        /**
         * Get my tickets page grouping configuration
         *
         * @return array
         */
        public static function getMyTicketsGroupings() : array
        {
            $groupings = Config::get( 'tickets.groupings.my_tickets', [] );
            TicketGroupingsValidator::validate( $groupings );

            return $groupings;
        }
    }