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
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use RuntimeException;
    use Throwable;

    class DetermineTicketPriorityJob implements ShouldQueue
    {
        use Queueable;
        use InteractsWithQueue;
        use SerializesModels;

        protected Ticket $ticket;
        protected DetermineTicketPriorityAction $determineTicketPriorityAction;

        public function __construct( Ticket $ticket )
        {
            $this->ticket = $ticket;
        }

        public function handle( DetermineTicketPriorityAction $determineTicketPriorityAction ) : void
        {
            try {
                DB::transaction( function () use ( $determineTicketPriorityAction ) {
                    $data = $determineTicketPriorityAction->execute( $this->ticket );
                    $this->ticket->update( $data );
                } );
            } catch (QueryException $e) {
                Log::error( 'Database error while updating ticket priority: ' . $e->getMessage() );
                throw new RuntimeException( 'Database error occurred while updating ticket priority.' );
            } catch (RuntimeException $e) {
                Log::error( 'Runtime exception: ' . $e->getMessage() );
                throw $e;
            } catch (Exception $e) {
                Log::error( 'Error determining priority: ' . $e->getMessage() );
                throw new RuntimeException( 'Error occurred while determining priority.' );
            }
        }

    }