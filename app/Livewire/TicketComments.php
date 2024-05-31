<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Ticket;
use App\Services\EventService;
use Livewire\Component;

    class TicketComments extends Component
    {
        public $ticket;
        public $content;

        public function mount( Ticket $ticket ) : void
        {
            $this->ticket = $ticket;
        }

        public function addComment() : void
        {
            Comment::create( [
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'content' => $this->content,
            ] );

            EventService::createEvent( $this->ticket->id, auth()->id(), 'Comment added: ' . $this->content );
        }

        public function render()
        {
            return view( 'livewire.ticket-comments', [
                'comments' => $this->ticket->comments,
            ] );
        }
    }