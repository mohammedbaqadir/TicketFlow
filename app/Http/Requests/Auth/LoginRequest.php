<?php
    declare( strict_types = 1 );

    namespace App\Http\Requests\Auth;

    use App\Actions\Auth\AccountLockoutAction;
    use App\Actions\Auth\AuthenticateUserAction;
    use App\Actions\Auth\LoginAttemptAction;
    use App\Models\User;
    use Illuminate\Contracts\Validation\ValidationRule;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Support\Facades\RateLimiter;
    use Illuminate\Support\Str;
    use Illuminate\Validation\ValidationException;

    class LoginRequest extends FormRequest
    {

        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize() : bool
        {
            return true;
        }

        /**
         * Get the validation rules that apply to the request.
         * @return array<string, array<int, string>|ValidationRule>
         */
        public function rules() : array
        {
            return [
                'email' => [ 'required', 'string', 'email' ],
                'password' => [ 'required', 'string' ],
            ];
        }

        /**
         * Attempt to authenticate the request's credentials.
         *
         * @throws \Illuminate\Validation\ValidationException
         */
        public function authenticate() : void
        {
            $user = User::where( 'email', $this->input( 'email' ) )->first();

            if ( $user ) {
                // Check if the user account is locked
                app( AccountLockoutAction::class )->checkLockout( $user );
            }

            // Record the login attempt and apply rate limiting
            app( LoginAttemptAction::class )->execute( $this->input( 'email' ), $this->ip() );


            // Attempt authentication
            $credentials = $this->only( 'email', 'password' );
            /** @var array{email: string, password: string} $credentials */

            if ( !app( AuthenticateUserAction::class )->execute( [
                'email' => (string) $credentials['email'],
                'password' => (string) $credentials['password'],
            ], $this->boolean( 'remember' ) ) ) {
                if ( $user ) {
                    app( AccountLockoutAction::class )->execute( $user );
                }

                throw ValidationException::withMessages( [
                    'email' => 'authentication failed'
                ] );
            }

            // Clear rate limiting for successful login
            RateLimiter::clear( $this->throttleKey() );
        }

        /**
         * Get the rate limiting throttle key for the request.
         */
        public function throttleKey() : string
        {
            return Str::transliterate( Str::lower( $this->input( 'email' ) ) . '|' . $this->ip() );
        }
    }