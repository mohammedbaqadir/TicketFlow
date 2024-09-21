<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Support\Facades\DB;

    class AssignTicketAction
    {
        public function execute( Ticket $ticket, User $user ) : void
        {
            DB::transaction( static function () use ( $ticket, $user ) {
                $ticket->update( [
                    'assignee_id' => $user->id,
                    'status' => 'in-progress',
                ] );
            } );
        }

    }