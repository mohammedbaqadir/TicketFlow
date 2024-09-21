<?php
    declare( strict_types = 1 );

    namespace App\Actions\Answer;

    use App\Models\Answer;
    use Illuminate\Support\Facades\DB;

    class CreateAnswerAction
    {

        public function execute( array $data ) : Answer
        {
            return DB::transaction( function () use ( $data ) {
                $answer = Answer::create( $this->prepareAnswerData( $data ) );
                $answer->ticket->update( [ 'status' => 'awaiting-acceptance' ] );

                return $answer->ticket->fresh( [ 'requestor', 'assignee', 'answers' ] );
            } );
        }

        private function prepareAnswerData( array $data ) : array
        {
            return array_filter( [
                'content' => $data['content'],
                'submitter_id' => auth()->id(),
                'ticket_id' => $data['ticket_id'],
            ] );
        }

    }