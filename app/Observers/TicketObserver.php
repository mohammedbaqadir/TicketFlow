<?php
    declare( strict_types = 1 );

    namespace App\Observers;

    use App\Models\Ticket;
    use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
    use Illuminate\Support\Facades\Auth;

    class TicketObserver implements ShouldHandleEventsAfterCommit
    {
        public function created( Ticket $ticket ) : void
        {
            activity()
                ->performedOn( $ticket )
                ->causedBy( $ticket->requestor )
                ->log( 'Ticket created' );
        }

        public function updated( Ticket $ticket ) : void
        {
            $changes = $ticket->getChanges();

            if ( isset( $changes['status'] ) ) {
                activity()
                    ->performedOn( $ticket )
                    ->causedBy( Auth::user() )
                    ->log( "Ticket is {$ticket->formatted_status}" );
            }

            if ( isset( $changes['assignee_id'] ) ) {
                $assignee = $ticket->assignee->name ?? 'Unassigned';
                if ( $assignee !== 'Unassigned' ) {
                    activity()
                        ->performedOn( $ticket )
                        ->causedBy( $ticket->assignee )
                        ->log( "Ticket assigned to {$assignee}" );
                } else {
                    activity()
                        ->performedOn( $ticket )
                        ->causedBy( Auth::user() )
                        ->log( "Ticket is {$assignee}" );
                }
            }
        }

    }