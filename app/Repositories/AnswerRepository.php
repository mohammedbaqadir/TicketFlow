<?php
    declare( strict_types = 1 );

    namespace App\Repositories;

    use App\Models\Answer;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Pagination\LengthAwarePaginator;

    class AnswerRepository extends BaseRepository
    {
        public function __construct( Answer $model )
        {
            parent::__construct( $model );
        }

        public function getAnswersByTicket(
            int $ticketId,
            array $filters = [],
            array $sort = [ 'created_at' => 'desc' ],
            int $perPage = 15,
            array $relations = [ 'submitter', 'comments' ]
        ) : LengthAwarePaginator {
            $query = $this->model->where( 'ticket_id', $ticketId )->with( $relations );
            return $this->applyFiltersAndSort( $query, $filters, $sort )->paginate( $perPage );
        }

        public function getAcceptedAnswer( int $ticketId ) : ?Answer
        {
            return $this->model->where( 'ticket_id', $ticketId )->where( 'is_accepted', true )->first();
        }
    }