<?php
    declare( strict_types = 1 );

    namespace App\Observers;

    use App\Models\Answer;
    use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

    class AnswerObserver implements ShouldHandleEventsAfterCommit
    {
        public function created( Answer $answer ) : void
        {
            $submitter = $answer->submitter;
            activity()
                ->performedOn( $answer->ticket )
                ->causedBy( $submitter )
                ->withProperties( [
                    'answer_id' => $answer->id
                ] )
                ->log( "{$submitter->name} submitted an answer" );
        }

        public function updated( Answer $answer ) : void
        {
            $ticket = $answer->ticket;
            $submitter = $answer->submitter;

            if ( $answer->isDirty( 'content' ) ) {
                activity()
                    ->performedOn( $ticket )
                    ->causedBy( $submitter )
                    ->withProperties( [
                        'answer_id' => $answer->id
                    ] )
                    ->log( "{$submitter->name } updated their answer" );
            }

            if ( $answer->isDirty( 'is_accepted' ) && $answer->is_accepted ) {
                activity()
                    ->performedOn( $ticket )
                    ->causedBy( $ticket->requestor )
                    ->withProperties( [
                        'answer_id' => $answer->id
                    ] )
                    ->log( 'Answer accepted' );
            }
        }

        public function deleted( Answer $answer ) : void
        {
            $user = auth()->user();
            activity()
                ->performedOn( $answer->ticket )
                ->causedBy( $user )
                ->log( "{$user} deleted their answer" );
        }

    }