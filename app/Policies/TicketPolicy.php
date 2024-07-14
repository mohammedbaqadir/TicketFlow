<?php
    declare( strict_types = 1 );

    namespace App\Policies;

    use App\Helpers\AuthHelper;
    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class TicketPolicy
    {
        use HandlesAuthorization;

        public function viewAny( User $user ) : bool
        {
            return AuthHelper::userHasRole( 'admin' )
                || AuthHelper::userHasRole( 'agent' );
        }

        public function view( User $user, Ticket $ticket ) : bool
        {
            $can_view = false;

            if ( AuthHelper::userHasRole( 'admin' ) ) {
                $can_view = true;
            } elseif ( AuthHelper::userHasRole( 'agent' ) ) {
                $can_view = AuthHelper::userIsAssignee( $ticket);
            } elseif ( AuthHelper::userHasRole( 'employee' ) ) {
                $can_view = AuthHelper::userIsRequestor( $ticket);
            }

            return $can_view;
        }

        public function create( User $user ) : bool
        {
            return AuthHelper::userHasRole( 'employee' ) || AuthHelper::userHasRole( 'admin' );
        }

        public function update( User $user, Ticket $ticket ) : bool
        {
            return $ticket->status !== 'resolved' && AuthHelper::userIsRequestor( $ticket );
        }


        public function delete( User $user, Ticket $ticket ) : bool
        {
            return AuthHelper::userIsRequestor( $ticket ) || AuthHelper::userHasRole( 'admin' );
        }


        public function restore( User $user, Ticket $ticket ) : bool
        {
            return AuthHelper::userHasRole( 'admin' );
        }


        public function forceDelete( User $user, Ticket $ticket ) : bool
        {
            return AuthHelper::userHasRole( 'admin' );
        }

        public function assign( User $user, Ticket $ticket ) : bool
        {
            return $ticket->assignee_id === null &&
                AuthHelper::userHasRole( 'agent' );
        }

        public function unassign( User $user, Ticket $ticket ) : bool
        {
            return AuthHelper::userIsAssignee( $ticket );
        }

        public function answer( User $user, Ticket $ticket ) : bool
        {
            return AuthHelper::userIsAssignee( $ticket);
        }


    }