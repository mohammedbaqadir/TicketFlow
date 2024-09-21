<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Jobs\DetermineTicketPriorityJob;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\DB;

    class CreateTicketAction
    {

        public function execute( array $data ) : Ticket
        {
            return DB::transaction( function () use ( $data ) {
                $ticket = Ticket::create( $this->prepareTicketData( $data ) );

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
                'requestor_id' => auth()->id(),
                'status' => 'open',
            ] );
        }

    }