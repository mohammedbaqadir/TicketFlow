<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Models\Ticket;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;

    class DeleteTicketAction
    {
        public function execute( Ticket $ticket ) : bool
        {
            return DB::transaction( static function () use ( $ticket ) {
                $cacheKey = "tickets_show_{$ticket->id}";
                if ( Cache::has( $cacheKey ) ) {
                    Cache::forget( $cacheKey );
                }
                return $ticket->delete();
            } );
        }

    }