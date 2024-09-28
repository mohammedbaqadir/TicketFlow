<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Models\Ticket;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;

    class ResolveTicketAction
    {

        /**
         * @throws \Exception
         */
        public function execute( Ticket $ticket, int $acceptedAnswerId ) : Ticket
        {
            try {
                return DB::transaction( function () use ( $ticket, $acceptedAnswerId ) {
                    $ticket->update( [
                        'accepted_answer_id' => $acceptedAnswerId,
                        'status' => 'resolved',
                    ] );
                    DB::afterCommit( function () use ( $ticket ) {
                        $this->cancelEscalation( $ticket );
                        $cacheKey = "tickets_show_{$ticket->id}";
                        Cache::forever( $cacheKey, $ticket );
                    } );
                    return $ticket->fresh();
                } );
            } catch (\Exception $e) {
                Log::error( "Error resolving ticket ID {$ticket->id}: " . $e->getMessage() );
                throw $e;
            }
        }

        // Somewhere in a service or controller responsible for cancelling the job:
        private function cancelEscalation( Ticket $ticket ) : void
        {
            $uniqueJobId = "cancel_escalate_ticket_job_{$ticket->id}";

            // Dynamically calculate the expiration time (match timeout_at)
            $timeUntilEscalation = $ticket->timeout_at->diffInSeconds( now() );
            Cache::put( $uniqueJobId, true, now()->addSeconds( $timeUntilEscalation ) );

            Log::info( "Escalation for ticket ID {$ticket->id} has been cancelled." );
        }

    }