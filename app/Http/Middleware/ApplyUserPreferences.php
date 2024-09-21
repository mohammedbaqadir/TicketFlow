<?php
    declare( strict_types = 1 );

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Symfony\Component\HttpFoundation\Response;

    class ApplyUserPreferences
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle( Request $request, Closure $next ) : Response
        {
           /* if ( Auth::check() ) {
                $theme = Auth::user()->preferred_theme;
                $response = $next( $request );
                $response->headers->set( 'X-User-Theme', $theme );
                return $response;
            }*/

            return $next( $request );
        }

    }