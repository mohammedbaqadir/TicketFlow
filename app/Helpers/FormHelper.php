<?php

    namespace App\Helpers;

    use App\Services\TicketService;
    use App\Helpers\AuthHelper;
    use App\Models\User;


    /**
     * Class FormHelper
     *
     * Provides helper methods for form data processing.
     */
    class FormHelper
    {
        /**
         * Handle form data for creating a ticket.
         *
         * @param  array  $data
         * @return array
         */
        public static function handleCreateTicketFormData( array $data ) : array
        {
            // Set the default values for a new ticket
            $data['status'] = 'open';
            $data['created_by'] = auth()->id();
            $data['priority'] = TicketService::determinePriority( $data['title'], $data['description'] );
            $data['timeout_at'] = now()->addHours( TicketService::determineTimeout( $data['priority'] ) );
            $data['assigned_to'] = null;

            return $data;
        }

        /**
         * Handle form data for editing a ticket.
         *
         * @param  array  $data
         * @return array
         */
        public static function handleEditTicketFormData( array $data ) : array
        {
            // Determine the timeout and priority based on the user's role
            if ( AuthHelper::userHasRole( 'agent' ) ) {
                $data['timeout_at'] = now()->addHours( TicketService::determineTimeout( $data['priority'] ) );
            } else {
                $data['priority'] = TicketService::determinePriority( $data['title'], $data['description'] );
                $data['timeout_at'] = now()->addHours( TicketService::determineTimeout( $data['priority'] ) );
            }

            return $data;
        }
    }