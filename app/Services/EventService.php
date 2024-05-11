<?php

    namespace App\Services;

    use App\Models\Event;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\Auth;
    use InvalidArgumentException;

    class EventService
    {
        /**
         * Create an event.
         *
         * @param  int|Ticket  $ticket
         * @param  string  $description
         * @param  int|null  $userId
         * @return Event
         * @throws InvalidArgumentException
         */
        public static function createEvent( Ticket|int $ticket, string $description, ?int $userId = null ) : Event
        {
            // Ensure $ticket is either a Ticket instance or an integer ID
            if ( !( $ticket instanceof Ticket ) && !\is_int( $ticket ) ) {
                throw new InvalidArgumentException( 'Ticket must be a Ticket instance or an integer ID' );
            }

            $ticketId = $ticket instanceof Ticket ? $ticket->id : $ticket;


            return Event::create( [
                'ticket_id' => $ticketId,
                'user_id' => $userId ?? auth()->id(),  // Use userId if provided, otherwise auth()->id()
                'description' => $description,
            ] );
        }


    }