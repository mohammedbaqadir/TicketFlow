<?php
    declare( strict_types = 1 );

    namespace App\Actions\Answer;

    use App\Models\Answer;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\DB;

    class CreateAnswerAction
    {

        /**
         * @param  array{content: string, ticket_id: int}  $data
         */
        public function execute( array $data ) : ?Ticket
        {
            return DB::transaction( function () use ( $data ) {
                $answer = Answer::create( $this->prepareAnswerData( $data ) );

                if ( $answer->ticket ) {
                    $answer->ticket->update( [ 'status' => 'awaiting-acceptance' ] );
                    return $answer->ticket->fresh( [ 'requestor', 'assignee', 'answers' ] );
                }

                return null;
            } );
        }

        /**
         * @param  array{content: string, ticket_id: int}  $data
         * @return array{content: string, submitter_id: int, ticket_id: int}
         */
        private function prepareAnswerData( array $data ) : array
        {
            return [
                'content' => (string) $data['content'],
                'submitter_id' => (int) auth()->id(),
                'ticket_id' => (int) $data['ticket_id'],
            ];
        }

    }