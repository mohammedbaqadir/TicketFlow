<?php
    declare( strict_types = 1 );

    namespace App\Actions\Answer;

    use App\Models\Answer;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\DB;

    class DeleteAnswerAction
    {

        public function execute( Answer $answer ) : ?Ticket
        {
            return DB::transaction( static function () use ( $answer ) {
                $ticket = $answer->ticket;
                $answer->delete();
                return $ticket->fresh( [ 'requestor', 'assignee', 'answers' ] );
            } );
        }

    }