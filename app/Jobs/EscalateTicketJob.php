<?php
    declare( strict_types = 1 );

    namespace App\Jobs;

    use App\Actions\Ticket\EscalateTicketAction;
    use App\Models\Ticket;
    use Exception;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Database\QueryException;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Illuminate\Foundation\Queue\Queueable;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use RuntimeException;

    /**
     *
     * This job checks for a cancellation flag in the cache before proceeding.
     * The cancellation flag is set via Cache::put() with a 24-hour expiration to avoid memory leaks.
     *
     * To cancel this job, use:
     * Cache::put("cancel_escalate_ticket_job_{$ticket->id}", true, now()->addDay());
     */
    class EscalateTicketJob implements ShouldQueue
    {
        use Queueable;
        use InteractsWithQueue;
        use SerializesModels;
        use Dispatchable;

        /**
         * Create a new job instance.
         *
         * @param  Ticket  $ticket
         */
        public function __construct( public Ticket $ticket )
        {
            $this->ticket = $ticket;
        }

        public function retryUntil()
        {
            return now()->addMinutes( 10 ); // Allow retries for 10 minutes
        }

        public function handle( EscalateTicketAction $escalateTicketAction ) : void
        {
            $uniqueJobId = "cancel_escalate_ticket_job_{$this->ticket->id}";

            // Check if cancellation flag is present, with an expiration to avoid memory leaks
            if ( Cache::has( $uniqueJobId ) ) {
                Log::info( "EscalateTicketJob for ticket ID {$this->ticket->id} was cancelled." );
                return;
            }

            $this->ticket->refresh();
            if ( $this->ticket->status === 'resolved' ) {
                Log::info( "Ticket ID {$this->ticket->id} is already resolved. Skipping escalation." );
                return;
            }

            try {
                DB::transaction( function () use ( $escalateTicketAction ) {
                    $escalateTicketAction->execute( $this->ticket );
                } );
            } catch (Exception $e) {
                Log::error( "Error escalating ticket ID {$this->ticket->id}: " . $e->getMessage() );
                $this->fail( $e );
            }
        }

    }