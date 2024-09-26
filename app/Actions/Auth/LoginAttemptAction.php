<?php
    declare( strict_types = 1 );

    namespace App\Actions\Auth;

    use Illuminate\Support\Facades\RateLimiter;
    use Illuminate\Validation\ValidationException;

    class LoginAttemptAction
    {
        /**
         * Execute the login attempt action.
         *
         * @param  string  $email
         * @param  string  $ip
         * @throws ValidationException
         */
        public function execute( string $email, string $ip ) : void
        {
            $emailKey = 'login_email:' . $email;

            if ( RateLimiter::tooManyAttempts( $emailKey, config( 'lockout.email_limit', 5 ) ) ) {
                throw ValidationException::withMessages( [
                    'email' => [ __( 'auth.throttle', [ 'seconds' => RateLimiter::availableIn( $emailKey ) ] ) ],
                ] );
            }

            $delay = $this->applyProgressiveDelay( $emailKey );
            RateLimiter::hit( $emailKey, $delay );
        }

        /**
         * Apply a progressive delay based on the number of attempts.
         *
         * @param  string  $key
         * @return int
         */
        private function applyProgressiveDelay( string $key ) : int
        {
            $attempts = RateLimiter::attempts( $key );
            return min( 2 ** $attempts, 8 ); // Max delay of 8 seconds
        }

    }