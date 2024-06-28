<?php
    declare( strict_types = 1 );

    namespace App\Policies;

    use App\Helpers\AuthHelper;
    use App\Models\Solution;
    use App\Models\User;
    use Illuminate\Auth\Access\HandlesAuthorization;

    class SolutionPolicy
    {
        use HandlesAuthorization;

        /**
         * Determine whether the user can view any models.
         */
        public function viewAny( User $user ) : bool
        {
            return AuthHelper::userHasRole( 'admin' );
        }

        /**
         * Determine whether the user can view the model.
         */
        public function view( User $user, Solution $solution ) : bool
        {
            return AuthHelper::userHasRole( 'admin' ) ||
                $solution->ticket->isRequestor( $user ) ||
                $solution->ticket->isAssignee( $user );
        }

        /**
         * Determine whether the user can create models.
         */
        public function create( User $user ) : bool
        {
            return AuthHelper::userHasRole( 'agent' ) || AuthHelper::userHasRole( 'admin' );
        }

        /**
         * Determine whether the user can update the model.
         */
        public function update( User $user, Solution $solution ) : bool
        {
            return $solution->ticket->isRequestor( $user ) ||
                $solution->ticket->isAssignee( $user );
        }

        /**
         * Determine whether the user can delete the model.
         */
        public function delete( User $user, Solution $solution ) : bool
        {
            return $solution->isSubmitter( $user );
        }

        /**
         * Determine whether the user can restore the model.
         */
        public function restore( User $user, Solution $solution ) : bool
        {
            return AuthHelper::userHasRole( 'admin' );
        }

        /**
         * Determine whether the user can permanently delete the model.
         */
        public function forceDelete( User $user, Solution $solution ) : bool
        {
            return AuthHelper::userHasRole( 'admin' );
        }
    }