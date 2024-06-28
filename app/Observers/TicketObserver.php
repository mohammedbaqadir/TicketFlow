<?php

    namespace App\Observers;

    use App\Models\Ticket;
    use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
    use Illuminate\Support\Facades\Auth;

    class TicketObserver implements ShouldHandleEventsAfterCommit
    {
        /**
         * Handle the Ticket "created" event.
         */
        public function created(Ticket $ticket): void
        {
            activity()
                ->performedOn( $ticket )
                ->causedBy( $ticket->requestor )
                ->log( 'Ticket created' );
        }

        /**
         * Handle the Ticket "updated" event.
         */
        public function updated(Ticket $ticket): void
        {
            $changes = $ticket->getChanges();

            if ( isset( $changes['status'] ) ) {
                activity()
                    ->performedOn( $ticket )
                    ->causedBy( Auth::user() )
                    ->log( "Ticket is {$ticket->formatted_status}" );
            }

            if ( isset( $changes['assigned_to'] ) ) {
                $assignee = $ticket->assignee->name ?? 'Unassigned';
                if ($assignee !== 'Unassigned') {
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

        /**
         * Handle the Ticket "deleted" event.
         */
        public function deleted(Ticket $ticket): void
        {
            activity()
                ->performedOn( $ticket )
                ->causedBy( Auth::user() )
                ->log( 'Ticket deleted' );
        }

        /**
         * Handle the Ticket "restored" event.
         */
        public function restored(Ticket $ticket): void
        {
            //
        }

        /**
         * Handle the Ticket "force deleted" event.
         */
        public function forceDeleted(Ticket $ticket): void
        {
            //
        }
    }