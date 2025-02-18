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

    /**
     * @method RedirectResponse withToast( string $message, string $description = '', string $type = 'success', string $position = 'top-right', string $html = '' )
     */
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
        }

        /**
         * Configure rate limiting for various parts of the application.
         */
        protected function configureRateLimiting() : void
        {
            $this->setGlobalRateLimit();
            $this->setAuthRateLimit();
            $this->setLoginRateLimit();
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


    }