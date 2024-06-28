<?php
    declare( strict_types = 1 );

    namespace App\Observers;

    use App\Models\Solution;

    class SolutionObserver
    {
        /**
         * Handle the Solution "created" event.
         */
        public function created(Solution $solution): void
        {
            $submitter = $solution->submitter;
            activity()
                ->performedOn( $solution->ticket )
                ->causedBy( $submitter )
                ->log( "Solution submitted by {$submitter}" );
        }

        /**
         * Handle the Solution "updated" event.
         */
        public function updated(Solution $solution): void
        {
            if ( $solution->isDirty( 'resolved' ) ) {
                $action = $solution->resolved ? 'marked as valid' : 'marked as invalid';
                $ticket = $solution->ticket;
                $requestor = $ticket->requestor;
                activity()
                    ->performedOn( $ticket )
                    ->causedBy( $requestor )
                    ->log( "Solution submitted by {$solution->submitter} {$action}" );
            }
        }

        /**
         * Handle the Solution "deleted" event.
         */
        public function deleted(Solution $solution): void
        {
            //
        }

        /**
         * Handle the Solution "restored" event.
         */
        public function restored(Solution $solution): void
        {
            //
        }

        /**
         * Handle the Solution "force deleted" event.
         */
        public function forceDeleted(Solution $solution): void
        {
            //
        }
    }