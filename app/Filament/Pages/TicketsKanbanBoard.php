<?php

    namespace App\Filament\Pages;

    use App\Enums\TicketStatus;
    use App\Helpers\AuthHelper;
    use App\Models\Ticket;
    use Illuminate\Support\Str;
    use Mokhosh\FilamentKanban\Pages\KanbanBoard;

    class TicketsKanbanBoard extends KanbanBoard
    {
        public static function canAccess() : bool
        {
            return AuthHelper::userHasRole( 'admin' );
        }

        protected static string $model = Ticket::class;
        public bool $disableEditModal = true;
        protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

        protected function records() : \Illuminate\Support\Collection
        {
            return Ticket::where( 'created_at', '>=', ( now( 'Asia/Riyadh' ) )->subWeek() )->get();
        }

        public function onStatusChanged(
            int $recordId,
            string $status,
            array $fromOrderedIds,
            array $toOrderedIds
        ) : void {
            Ticket::find( $recordId )->update( [ 'status' => $status ] );
        }

        public function onSortChanged( int $recordId, string $status, array $orderedIds ) : void
        {
        }

        protected function statuses() : \Illuminate\Support\Collection
        {
            return collect( [
                [ 'id' => 'open', 'title' => 'Open' ],
                [ 'id' => 'in-progress', 'title' => 'In Progress' ],
                [ 'id' => 'awaiting-acceptance', 'title' => 'Awaiting Acceptance' ],
            ] );
        }

    }