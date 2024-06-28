<?php

    namespace App\Http\Middleware;

    use App\Helpers\AuthHelper;
    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class FilamentAdminMiddleware
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            if ( !auth()->check() || !AuthHelper::userHasRole( 'admin' ) ) {
                abort( 403, 'Forbidden' );
            }

            return $next($request);
        }
    }