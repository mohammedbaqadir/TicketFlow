<?php
    declare( strict_types = 1 );

    namespace App\Providers;


    use Illuminate\Http\RedirectResponse;
    use Illuminate\Support\ServiceProvider;
    use Illuminate\Cache\RateLimiting\Limit;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\RateLimiter;

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
            RedirectResponse::macro( 'withToast',
                function ( $message, $description = '', $type = 'success', $position = 'top-right', $html = '' ) {
                    return $this->with( 'toast', compact( 'message', 'description', 'type', 'position', 'html' ) );
                } );
            RateLimiter::for( 'web', function ( Request $request ) {
                return [
                    $request->user()
                        ? Limit::perMinute( 100 )->by( $request->user()->id )
                        : Limit::perMinute( 10 )->by( $request->ip() ),
                    Limit::perMinute( 60 )->by( $request->user()?->id ? : $request->ip() ),
                ];
            } );
        }
    }