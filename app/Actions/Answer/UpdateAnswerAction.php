<?php
    declare( strict_types = 1 );

    namespace App\Actions\Answer;

    use App\Models\Answer;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\DB;

    class UpdateAnswerAction
    {

        /**
         * @param  array{content: string}  $data
         */

        public function execute( Answer $answer, array $data ) : ?Ticket
        {
            return DB::transaction( function () use ( $answer, $data ) {
                $answer->update( $this->prepareAnswerData( $data ) );

                if ( $answer->ticket ) {
                    return $answer->ticket->fresh( [ 'requestor', 'assignee', 'answers' ] );
                }

                return null;
            } );
        }

        /**
         * @param  array{content: string}  $data
         * @return array{content: string}
         */
        private function prepareAnswerData( array $data ) : array
        {
            return [
                'content' => (string) $data['content'],
            ];
        }


    }