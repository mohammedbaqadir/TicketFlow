<?php

    namespace App\Livewire;

    use App\Models\Comment;
    use App\Models\Solution;
    use App\Models\Ticket;
    use App\Services\EventService;
    use Livewire\Component;

    class TicketTimeline extends Component
    {
        public Ticket $ticket;
        public $newComment = '';

        public function mount( Ticket $ticket )
        {
            $this->ticket = $ticket;
        }

        /**
         * Mark a solution as valid.
         *
         * @param  Solution  $solution
         * @return void
         */
        public function markSolutionAsValid( Solution $solution ) : void
        {
            $solution->update( [ 'resolved' => true ] );
            $solution->ticket->update( [ 'status' => 'closed' ] );
            EventService::createEvent( $solution->ticket->id, auth()->id(),
                'Solution successfully resolved the issue, ticket closed' );

            session()->flash( 'message', 'Solution marked as valid and ticket closed.' );
        }

        /**
         * Mark a solution as invalid.
         *
         * @param  Solution  $solution
         * @return void
         */
        public function markSolutionAsInvalid( Solution $solution ) : void
        {
            $solution->update( [ 'resolved' => false ] );
            $solution->ticket->update( [ 'status' => 'in-progress' ] );
            EventService::createEvent( $solution->ticket->id, 'Ticket status changed to `In-Progress`.' );

            session()->flash( 'message', 'Solution marked as invalid.' );
        }

        /**
         * Undo the marking of a solution.
         *
         * @param  Solution  $solution
         * @return void
         */
        public function undoMarking( Solution $solution ) : void
        {
            $solution->update( [ 'resolved' => null ] );
            session()->flash( 'message', 'Solution marking undone.' );
        }

        public function addComment() : void
        {
            Comment::create( [
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->user()->id,
                'content' => $this->newComment,
            ] );

            $this->newComment = '';
        }

        public function render()
        {
            $comments = $this->ticket->comments;
            $events = $this->ticket->events;

            return view( 'livewire.ticket-timeline', compact( 'comments', 'events' ) );
        }
    }