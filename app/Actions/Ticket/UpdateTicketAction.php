<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Jobs\DetermineTicketPriorityJob;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\DB;

    class UpdateTicketAction
    {
        /**
         * @param  array{title?: string, description?: string}  $data
         */
        public function execute( Ticket $ticket, array $data ) : Ticket
        {
            return DB::transaction( function () use ( $ticket, $data ) {
                $ticket->update( $this->prepareTicketData( $data ) );
                DB::afterCommit( static function () use ( $ticket ) {
                    DetermineTicketPriorityJob::dispatch( $ticket );
                } );

                return $ticket->fresh( [ 'requestor', 'assignee', 'answers' ] );
            } );
        }

        /**
         * @param  array{title?: string, description?: string}  $data
         * @return array{title?: string, description?: string}
         */
        private function prepareTicketData( array $data ) : array
        {
            return array_filter( [
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
            ], fn( $value ) => $value !== null );
        }

    }