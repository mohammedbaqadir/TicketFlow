<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Jobs\DetermineTicketPriorityJob;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\DB;

    class UpdateTicketAction
    {

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

        private function prepareTicketData( array $data ) : array
        {
            return array_filter( [
                'title' => $data['title'],
                'description' => $data['description'],
            ] );
        }

    }