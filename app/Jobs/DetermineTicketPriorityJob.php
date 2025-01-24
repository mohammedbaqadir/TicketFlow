<?php
    declare( strict_types = 1 );

    namespace App\Jobs;

    use App\Actions\Ticket\DetermineTicketPriorityAction;
    use App\Models\Ticket;
    use Exception;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Database\QueryException;
    use Illuminate\Foundation\Queue\Queueable;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use RuntimeException;

    class DetermineTicketPriorityJob implements ShouldQueue
    {
        use Queueable;
        use InteractsWithQueue;
        use SerializesModels;

        protected Ticket $ticket;

        public function __construct( Ticket $ticket )
        {
            $this->ticket = $ticket;
        }

        /**
         * @return Carbon
         */
        public function retryUntil()
        {
            return now()->addMinutes( 10 ); // Allow retries for 10 minutes
        }

        public function handle( DetermineTicketPriorityAction $determineTicketPriorityAction ) : void
        {
            try {
                DB::transaction( function () use ( $determineTicketPriorityAction ) {
                    $data = $determineTicketPriorityAction->execute( $this->ticket );
                    $this->ticket->update( $data );

                    DB::afterCommit( function () use ( $data ) {
                        EscalateTicketJob::dispatch( $this->ticket )
                            ->delay( (int) ( $data['timeout_at']->diffInSeconds( now() ) + 5 ) );
                    } );
                } );
            } catch (QueryException $e) {
                // Check if it's a deadlock or timeout issue
                if ( str_contains( $e->getMessage(), 'Deadlock' ) || str_contains( $e->getMessage(),
                        'Lock wait timeout' ) ) {
                    // Re-throw exception so it triggers a retry
                    $this->release( 10 ); // Delay 10 seconds before retrying
                } else {
                    Log::error( 'Database error while updating ticket priority: ' . $e->getMessage() );
                    throw new RuntimeException( 'Database error occurred while updating ticket priority.' );
                }
            } catch (Exception $e) {
                Log::error( 'Error determining priority: ' . $e->getMessage() );
                throw new RuntimeException( 'Error occurred while determining priority.' );
            }
        }

    }