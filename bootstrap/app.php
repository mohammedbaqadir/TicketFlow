<?php
    declare( strict_types = 1 );

    use App\Http\Middleware\ApplyUserPreferences;
    use Illuminate\Foundation\Application;
    use Illuminate\Foundation\Configuration\Exceptions;
    use Illuminate\Foundation\Configuration\Middleware;
    use Illuminate\Http\Middleware\TrustProxies;
    use Illuminate\Routing\Middleware\ThrottleRequests;

    return Application::configure( basePath: dirname( __DIR__ ) )
        ->withRouting(
            web: __DIR__ . '/../routes/web.php',
            commands: __DIR__ . '/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware( function ( Middleware $middleware ) {
            $middleware->web( append: [
                ApplyUserPreferences::class,
                ThrottleRequests::class . ':global',
            ] );
            if ( env( 'APP_ENV' ) === 'production' ) {
                $middleware->append( TrustProxies::class );
            }
        } )
        ->withExceptions( function ( Exceptions $exceptions ) {
            //
        } )->create();