<?php
    declare( strict_types = 1 );

    namespace App\Policies;

    use App\Helpers\AuthHelper;
    use App\Models\Answer;
    use App\Models\User;

    class AnswerPolicy
    {

        public function create( User $user ) : bool
        {
            return AuthHelper::userHasRole( 'admin' ) || AuthHelper::userHasRole( 'agent' );
        }


        public function update( User $user, Answer $answer ) : bool
        {
            return $answer->isSubmitter( $user );
        }


        public function delete( User $user, Answer $answer ) : bool
        {
            return $answer->isSubmitter( $user );
        }


        public function restore( User $user, Answer $answer ) : bool
        {
            return AuthHelper::userHasRole( 'admin' );
        }

        public function forceDelete( User $user, Answer $answer ) : bool
        {
            return AuthHelper::userHasRole( 'admin' );
        }

        public function accept( User $user, Answer $answer )
        {
            return $answer->ticket->isRequestor( $user );
        }
    }