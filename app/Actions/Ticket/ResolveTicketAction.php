<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Models\Ticket;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;

    class ResolveTicketAction
    {

        public function execute( Ticket $ticket, int $acceptedAnswerId ) : Ticket
        {
            return DB::transaction( static function () use ( $ticket, $acceptedAnswerId ) {
                $ticket->update( [
                    'status' => 'resolved',
                    'accepted_answer_id' => $acceptedAnswerId,
                ] );
                DB::afterCommit( static function () use ( $ticket ) {
                    $cacheKey = "tickets_show_{$ticket->id}";
                    Cache::forever( $cacheKey, $ticket );
                    return Cache::get( $cacheKey );
                } );
            } );
        }
    }