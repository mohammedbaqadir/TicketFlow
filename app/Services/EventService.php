<?php

    namespace App\Services;

    use App\Models\Event;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\Auth;
    use InvalidArgumentException;

    /**
     * Class EventService
     *
     * Provides services related to event operations.
     */
    class EventService
    {
        /**
         * Create an event for a ticket.
         *
         * @param  int  $ticketId
         * @param  string  $description
         * @param  int|null  $userId
         * @return Event
         */
        public static function createEvent( int $ticketId, int $userId, string $description ) : Event
        {
            return Event::create( [
                'ticket_id' => $ticketId,
                'user_id' => $userId,
                'description' => $description,
            ] );
        }
    }