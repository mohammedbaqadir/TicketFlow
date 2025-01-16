<?php
    declare( strict_types = 1 );

    namespace App\Actions\Answer;

    use App\Models\Answer;
    use Illuminate\Support\Facades\DB;

    class UpdateAnswerAction
    {

        public function execute( Answer $answer, array $data ) : Answer
        {
            return DB::transaction( function () use ( $answer, $data ) {
                $answer->update( $this->prepareAnswerData( $data ) );
                return $answer->ticket->fresh( [ 'ticket.requestor', 'ticket.assignee', 'ticket.answers' ] );
            } );
        }

        private function prepareAnswerData( array $data ) : array
        {
            return array_filter( [ 'content' => $data['content'] ] );
        }


    }