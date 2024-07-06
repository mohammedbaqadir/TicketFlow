<?php
    declare( strict_types = 1 );

    namespace App\Repositories;

    use App\Models\Comment;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Pagination\LengthAwarePaginator;

    class CommentRepository extends BaseRepository
    {
        public function __construct( Comment $model )
        {
            parent::__construct( $model );
        }

        public function getCommentsByCommentable(
            Model $commentable,
            array $filters = [],
            array $sort = [ 'created_at' => 'asc' ],
            int $perPage = 15,
            array $relations = [ 'commenter' ]
        ) : LengthAwarePaginator {
            $query = $commentable->comments()->with( $relations )->getQuery();
            return $this->applyFiltersAndSort( $query, $filters, $sort )->paginate( $perPage );
        }
    }