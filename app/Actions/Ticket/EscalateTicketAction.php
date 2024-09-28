<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Models\Ticket;
    use Exception;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;

    class EscalateTicketAction
    {
        /**
         * Escalate the given ticket.
         *
         * @param  Ticket  $ticket
         * @return Ticket
         * @throws Exception
         */
        public function execute( Ticket $ticket ) : Ticket
        {
            try {
                if ( $ticket->status !== 'resolved' ) {
                    DB::transaction( function () use ( $ticket ) {
                        $ticket->update( [
                            'status' => 'escalated',
                            'assignee_id' => 1,
                        ] );
                    } );
                    Log::info( "Ticket ID {$ticket->id} has been escalated." );
                } else {
                    Log::info( "Ticket ID {$ticket->id} is already resolved. No escalation needed." );
                }
                return $ticket->fresh();
            } catch (Exception $e) {
                Log::error( "Error escalating ticket ID {$ticket->id}: " . $e->getMessage() );
                throw $e;
            }
        }
    }