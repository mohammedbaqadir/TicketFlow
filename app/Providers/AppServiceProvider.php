<?php
    declare( strict_types = 1 );

    namespace App\Providers;


    use App\Models\User;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\ServiceProvider;
    use Illuminate\Cache\RateLimiting\Limit;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\RateLimiter;
    use Illuminate\Support\Str;
    use Symfony\Component\HttpFoundation\Response;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         */
        public function register() : void
        {
        }

        /**
         * Bootstrap any application services.
         */
        public function boot() : void
        {
            $this->configureRateLimiting();

            // Add a custom macro for toast notifications
            RedirectResponse::macro( 'withToast',
                function ( $message, $description = '', $type = 'success', $position = 'top-right', $html = '' ) {
                    return $this->with( 'toast', compact( 'message', 'description', 'type', 'position', 'html' ) );
                } );

            if ( App::environment( 'production' ) ) {
                $user = User::firstOrCreate(
                    [ 'email' => 'deploy@ticketflow.local' ],
                    [
                        'name' => 'Deployment Bot',
                        'password' => Hash::make( Str::random( 64 ) )
                    ]
                );

                // Create token if doesn't exist
                if ( !$user->tokens()->where( 'name', 'opcache-reset' )->exists() ) {
                    $token = $user->createToken( 'opcache-reset', [ 'opcache:reset' ] );
                    Log::info( 'New deployment token created: ' . $token->plainTextToken );
                }
            }
        }

        /**
         * Configure rate limiting for various parts of the application.
         */
        protected function configureRateLimiting() : void
        {
            $this->setGlobalRateLimit();
            $this->setAuthRateLimit();
            $this->setLoginRateLimit();
            $this->setGeminiApiRateLimit();
            $this->setOpcacheResetRateLimit();
        }

        /**
         * Set global rate limit for all routes.
         */
        private function setGlobalRateLimit() : void
        {
            RateLimiter::for( 'global', function ( Request $request ) {
                return Limit::perMinute( config( 'lockout.global_limit', 60 ) )
                    ->by( optional( $request->user() )->id ? : $request->ip() )
                    ->response( function () {
                        return response( 'Too Many Requests', Response::HTTP_TOO_MANY_REQUESTS );
                    } );
            } );
        }

        /**
         * Set rate limit for authenticated routes.
         */
        private function setAuthRateLimit() : void
        {
            RateLimiter::for( 'auth', function ( Request $request ) {
                return Limit::perMinute( config( 'lockout.auth_limit', 5 ) )
                    ->by( optional( $request->user() )->id ? : $request->ip() );
            } );
        }

        /**
         * Set rate limit for login attempts, considering both IP and email.
         */
        private function setLoginRateLimit() : void
        {
            RateLimiter::for( 'login', function ( Request $request ) {
                return [
                    Limit::perMinute( config( 'lockout.ip_limit', 20 ) )->by( $request->ip() ),
                    Limit::perMinute( config( 'lockout.email_limit', 5 ) )->by( $request->input( 'email' ) ),
                ];
            } );
        }

        /**
         * Set rate limit for Gemini API requests.
         */
        private function setGeminiApiRateLimit() : void
        {
            RateLimiter::for( 'gemini-api', function ( Request $request ) {
                return Limit::perMinute( 10 )->by( $request->ip() );
            } );
        }

        private function setOpcacheResetRateLimit() : void
        {
            RateLimiter::for( 'opcache-reset', function ( Request $request ) {
                return [
                    Limit::perMinute( 3 )
                        ->by( 'opcache:deployment' )
                        ->response( function ( Request $request, array $headers ) {
                            return response()->json( [
                                'message' => 'Too many cache reset attempts.'
                            ], 429, $headers );
                        } ),
                ];
            } );
        }
    }