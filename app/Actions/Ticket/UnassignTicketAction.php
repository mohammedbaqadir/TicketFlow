<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Models\Ticket;
    use Illuminate\Support\Facades\DB;

    class UnassignTicketAction
    {

        public function execute( Ticket $ticket ) : void
        {
            DB::transaction( static function () use ( $ticket ) {
                $ticket->update( [
                    'assignee_id' => null,
                    'status' => 'open',
                ] );
            } );
        }

    }