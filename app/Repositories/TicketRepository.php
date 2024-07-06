<?php
    declare( strict_types = 1 );

    namespace App\Repositories;

    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Pagination\LengthAwarePaginator;

    class TicketRepository extends BaseRepository
    {
        public function __construct( Ticket $model )
        {
            parent::__construct( $model );
        }

        public function getTicketsByUser(
            User $user,
            array $filters = [],
            array $sort = [ 'created_at' => 'desc' ],
            int $perPage = 15,
            array $relations = []
        ) : LengthAwarePaginator {
            $query = $user->tickets()->with( $relations )->getQuery();
            return $this->applyFiltersAndSort( $query, $filters, $sort )->paginate( $perPage );
        }

        public function getAssignedTicketsByUser(
            User $user,
            array $filters = [],
            array $sort = [ 'created_at' => 'desc' ],
            int $perPage = 15,
            array $relations = []
        ) : LengthAwarePaginator {
            $query = $user->assignedTickets()->with( $relations )->getQuery();
            return $this->applyFiltersAndSort( $query, $filters, $sort )->paginate( $perPage );
        }
    }