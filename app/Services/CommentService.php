<?php
    declare( strict_types = 1 );

    namespace App\Services;

    use App\Models\Comment;
    use App\Models\User;
    use App\Repositories\CommentRepository;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Exception;

    class CommentService
    {
        protected CommentRepository $repository;

        public function __construct( CommentRepository $repository )
        {
            $this->repository = $repository;
        }

        public function createOnTicket( array $data ) : Comment
        {
            return DB::transaction( function () use ( $data ) {
                $commentData = $this->prepareCommentData( $data );
                return $this->repository->create( $commentData )->fresh( [ 'commenter', 'commentable' ] );
            } );
        }

        public function createOnAnswer( array $data ) : Comment
        {
            return DB::transaction( function () use ( $data ) {
                $commentData = $this->prepareCommentData( $data );
                return $this->repository->create( $commentData )->fresh( [ 'commenter', 'commentable' ] );
            } );
        }

        public function delete( int $id ) : bool
        {
            return DB::transaction( function () use ( $id ) {
                $comment = $this->repository->getById( $id );
                if ( !$comment ) {
                    throw new Exception( "Comment with ID {$id} not found" );
                }

                return $this->repository->delete( $comment );
            } );
        }

        public function getCommentsByCommentable(
            Model $commentable,
            array $filters = [],
            array $sort = [ 'created_at' => 'asc' ],
            int $perPage = 15
        ) : LengthAwarePaginator {
            return $this->repository->getCommentsByCommentable( $commentable, $filters, $sort, $perPage,
                [ 'commenter' ] );
        }

        private function prepareCommentData( array $data, bool $isNewComment = true ) : array
        {
            $commentData = [ 'content' => $data['content'] ];

            if ( $isNewComment ) {
                $commentData['user_id'] = auth()->id();
                $commentData['commentable_id'] = $data['commentable_id'];
                $commentData['commentable_type'] = $data['commentable_type'];
            }

            return $commentData;
        }
    }