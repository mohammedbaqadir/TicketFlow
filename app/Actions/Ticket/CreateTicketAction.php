<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Jobs\DetermineTicketPriorityJob;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\DB;

    class CreateTicketAction
    {
        /**
         * Create a new ticket and queue priority determination.
         * @param  array{title: string, description: string}  $data
         * * @return Ticket
         */
        public function execute( array $data ) : Ticket
        {
            return DB::transaction( function () use ( $data ) {
                $ticket = Ticket::create( $this->prepareTicketData( $data ) );

                DB::afterCommit( static function () use ( $ticket ) {
                    // Queue the priority determination job
                    DetermineTicketPriorityJob::dispatch( $ticket );
                } );

                return $ticket->fresh( [ 'requestor', 'assignee', 'answers' ] );
            } );
        }

        /**
         * Prepare ticket data for creation.
         * @param  array{title: string, description: string}  $data
         * @return array{title: string, description: string, requestor_id: int, status: string}
         */
        private function prepareTicketData( array $data ) : array
        {
            return [
                'title' => (string) $data['title'],
                'description' => (string) $data['description'],
                'requestor_id' => (int) auth()->id(),
                'status' => 'open',
            ];
        }

    }