<?php

    namespace App\Policies;

    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Auth\Access\HandlesAuthorization;
    use Illuminate\Auth\Access\Response;

    class TicketPolicy
    {
        use HandlesAuthorization;

        /**
         * Determine whether the user can view any tickets.
         *
         * @param  User  $user
         * @return bool
         */
        public function viewAny( User $user ) : bool
        {
            return userHasRole( 'admin') || userHasRole( 'agent');
        }


        /**
         * Determine whether the user can view the ticket.
         *
         * @param  User  $user
         * @param  Ticket  $ticket
         * @return bool
         */
        public function view( User $user, Ticket $ticket ) : bool
        {
            $can_view = false;

            if ( userHasRole( 'admin' ) ) {
                $can_view = true;
            } elseif ( userHasRole( 'agent' ) ) {
                $can_view = $ticket->isAssignee( $user);
            } elseif ( userHasRole( 'employee' ) ) {
                $can_view = $ticket->isRequestor( $user);
            }

            return $can_view;
        }


        /**
         * Determine whether the user can create tickets.
         *
         * @param  User  $user
         * @return bool
         */
        public function create( User $user ) : bool
        {
            return userHasRole( 'employee' );
        }


        /**
         * Determine whether the user can update the ticket.
         *
         * @param  User  $user
         * @param  Ticket  $ticket
         * @return bool
         */
        public function update( User $user, Ticket $ticket ) : bool
        {
            $can_update = false;

            if ( $ticket->status !== 'closed') {
                if ( userHasRole( 'agent' ) ) {
                    $can_update = $ticket->isAssignee( $user);
                } elseif ( userHasRole( 'employee' ) ) {
                    $can_update = $ticket->isRequestor( $user);
                }
            }

            return $can_update;
        }


        /**
         * Determine whether the user can delete the ticket.
         *
         * @param  User  $user
         * @param  Ticket  $ticket
         * @return bool
         */
        public function delete( User $user, Ticket $ticket ) : bool
        {
            return userHasRole( 'admin' );
        }

        /**
         * Determine whether the user can restore the ticket.
         *
         * @param  User  $user
         * @param  Ticket  $ticket
         * @return bool
         */
        public function restore( User $user, Ticket $ticket ) : bool
        {
            return userHasRole( 'admin' );
        }

        /**
         * Determine whether the user can permanently delete the ticket.
         *
         * @param  User  $user
         * @param  Ticket  $ticket
         * @return bool
         */
        public function forceDelete( User $user, Ticket $ticket ) : bool
        {
            return userHasRole( 'admin' );
        }

    }