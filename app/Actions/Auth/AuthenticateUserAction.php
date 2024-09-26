<?php
    declare( strict_types = 1 );

    namespace App\Actions\Auth;

    use App\Models\User;
    use Illuminate\Support\Facades\Auth;

    class AuthenticateUserAction
    {
        /**
         * Attempt to authenticate the user.
         *
         * @param  array  $credentials
         * @param  bool  $remember
         * @return bool
         */
        public function execute( array $credentials, bool $remember ) : bool
        {
            $authenticated = Auth::attempt( $credentials, $remember );

            if ( $authenticated ) {
                $user = Auth::user();
                $this->resetLockoutCount( $user );
            }

            return $authenticated;
        }

        /**
         * Reset the lockout count for a successfully authenticated user.
         *
         * @param  User  $user
         */
        private function resetLockoutCount( User $user ) : void
        {
            $user->update( [ 'lockout_count' => 0 ] );
        }

    }