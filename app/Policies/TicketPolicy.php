<?php
    declare( strict_types = 1 );

    namespace App\Policies;

    use App\Helpers\AuthHelper;
    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Auth\Access\HandlesAuthorization;
    use Illuminate\Auth\Access\Response;

    class TicketPolicy
    {
        use HandlesAuthorization;

        public function viewAny( User $user ) : bool
        {
            return true;
        }

        public function view( User $user, Ticket $ticket ) : bool
        {
            $can_view = false;

            if ( AuthHelper::userHasRole( 'admin' ) ) {
                $can_view = true;
            } elseif ( AuthHelper::userHasRole( 'agent' ) ) {
                $can_view = $ticket->isAssignee( $user );
            } elseif ( AuthHelper::userHasRole( 'employee' ) ) {
                $can_view = $ticket->isRequestor( $user );
            }

            return $can_view;
        }


        public function create( User $user ) : bool
        {
            return AuthHelper::userHasRole( 'employee' ) || AuthHelper::userHasRole( 'admin' );
        }


        public function update( User $user, Ticket $ticket ) : bool
        {
            return $ticket->status !== 'closed' && $ticket->isRequestor( $user );
        }


        public function delete( User $user, Ticket $ticket ) : bool
        {
            return $ticket->isRequestor( $user ) || AuthHelper::userHasRole( 'admin' );
        }


        public function restore( User $user, Ticket $ticket ) : bool
        {
            return AuthHelper::userHasRole( 'admin' );
        }


        public function forceDelete( User $user, Ticket $ticket ) : bool
        {
            return AuthHelper::userHasRole( 'admin' );
        }

        public function assign( Ticket $ticket ) : bool
        {
            return $ticket->assigned_to === null &&
                AuthHelper::userHasRole( 'agent' );
        }

        public function unassign( User $user, Ticket $ticket ) : bool
        {
            return $ticket->isAssignee( $user);
        }


    }