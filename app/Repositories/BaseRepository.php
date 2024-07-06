<?php
    declare( strict_types = 1 );

    namespace App\Repositories;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Pagination\LengthAwarePaginator;

    abstract class BaseRepository
    {
        protected Model $model;

        public function __construct( Model $model )
        {
            $this->model = $model;
        }

        public function getAll(
            array $filters = [],
            array $sort = [ 'created_at' => 'desc' ],
            int $perPage = 15,
            array $relations = []
        ) : LengthAwarePaginator {
            return $this->applyFiltersAndSort( $this->model::with( $relations ), $filters,
                $sort )->paginate( $perPage );
        }

        public function getById( int $id, array $relations = [] ) : ?Model
        {
            return $this->model::with( $relations )->find( $id );
        }

        public function create( array $data ) : Model
        {
            return $this->model->create( $data );
        }

        public function update( Model $model, array $data ) : bool
        {
            return $model->update( $data );
        }

        public function delete( Model $model ) : bool
        {
            return $model->delete();
        }

        protected function applyFiltersAndSort( Builder $query, array $filters, array $sort ) : Builder
        {
            foreach ( $filters as $field => $value ) {
                $query->where( $field, $value );
            }

            foreach ( $sort as $field => $direction ) {
                $query->orderBy( $field, $direction );
            }

            return $query;
        }

    }