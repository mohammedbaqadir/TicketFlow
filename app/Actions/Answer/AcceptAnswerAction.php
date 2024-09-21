<?php
    declare( strict_types = 1 );

    namespace App\Actions\Answer;

    use App\Actions\Ticket\ResolveTicketAction;
    use App\Models\Answer;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\DB;

    class AcceptAnswerAction
    {
        protected ResolveTicketAction $resolveTicketAction;

        public function __construct( ResolveTicketAction $resolveTicketAction )
        {
            $this->resolveTicketAction = $resolveTicketAction;
        }

        public function execute( Answer $answer ) : Ticket
        {
            return DB::transaction( function () use ( $answer ) {
                $answer->update( [ 'is_accepted' => true ] );
                $ticket = $answer->ticket->withRelations()->with( [ 'answers' ] )->get();
                return $this->resolveTicketAction->execute( $ticket, $answer->id );
            } );
        }

    }