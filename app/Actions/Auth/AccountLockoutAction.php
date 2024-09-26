<?php
    declare( strict_types = 1 );

    namespace App\Actions\Auth;

    use App\Models\User;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Validation\ValidationException;

    class AccountLockoutAction
    {
        /**
         * Execute the account lockout process.
         *
         * @param  User  $user
         */
        public function execute( User $user ) : void
        {
            if ( $this->shouldLockout( $user ) ) {
                $this->lockoutUser( $user );
                return;
            }

            $user->increment( 'lockout_count' );

            if ( $this->shouldLockout( $user ) ) {
                $this->lockoutUser( $user );
            }
        }

        /**
         * Determine if the user should be locked out.
         *
         * @param  User  $user
         * @return bool
         */
        private function shouldLockout( User $user ) : bool
        {
            return $user->lockout_count >= config( 'lockout.max_attempts' );
        }

        /**
         * Lock the user account.
         *
         * @param  User  $user
         */
        private function lockoutUser( User $user ) : void
        {
            DB::transaction( function () use ( $user ) {
                $user->update( [
                    'is_locked' => true,
                    'lockout_time' => now(),
                ] );
            } );

            Log::warning( "User account locked: {$user->email}" );
        }


        /**
         * Check if the user account is locked and throw an exception if it is.
         *
         * @param  User  $user
         * @throws ValidationException
         */
        public function checkLockout( User $user ) : void
        {
            if ( !$user->is_locked ) {
                return;
            }

            $lockoutExpiresAt = $user->lockout_time->addMinutes( config( 'lockout.lockout_duration' ) );

            if ( now()->lt( $lockoutExpiresAt ) ) {
                throw ValidationException::withMessages( [
                    'email' => __( 'auth.locked', [ 'minutes' => now()->diffInMinutes( $lockoutExpiresAt ) ] ),
                ] );
            }

            $this->unlockUser( $user );
        }

        /**
         * Unlock a user account.
         *
         * @param  User  $user
         */
        public function unlockUser( User $user ) : void
        {
            DB::transaction( function () use ( $user ) {
                $user->update( [
                    'is_locked' => false,
                    'lockout_time' => null,
                    'lockout_count' => 0,
                ] );
            } );

            Log::info( "User account unlocked: {$user->email}" );
        }

    }