<?php

    namespace App\Livewire;

    use App\Models\Comment;
    use App\Models\Ticket;
    use Livewire\Component;

    class TicketTimeline extends Component
    {
        public Ticket $ticket;
        public $newComment = '';

        public function mount( Ticket $ticket )
        {
            $this->ticket = $ticket;
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