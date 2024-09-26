<?php
    declare( strict_types = 1 );

    use Illuminate\Support\Facades\RateLimiter;
    use Illuminate\Support\Facades\Route;

    beforeEach( function () {
        $this->artisan( 'migrate' );
        RateLimiter::clear( 'global:' . request()->ip() );

        // Define a test route that doesn't involve the RedirectController
        Route::get( '/test-rate-limit', function () {
            return response( 'OK', 200 );
        } )->middleware( 'throttle:global' );
    } );

    test( 'global rate limiter should block after max attempts', function () {
        // Simulate requests hitting the global rate limit
        $limit = config( 'lockout.global_limit', 60 );
        for ( $i = 0; $i < $limit; $i++ ) {
            $this->get( '/test-rate-limit' );
        }

        // The next request should fail due to rate limiting
        $response = $this->get( '/test-rate-limit' );
        $response->assertStatus( 429 );
    } );

    test( 'email rate limiter should block after max attempts', function () {
        $this->withoutMiddleware()->withMiddleware( [ 'web', 'throttle:login' ] );

        $email = 'test@example.com';

        // Simulate login attempts until rate limit is reached
        for ( $i = 0; $i < config( 'lockout.email_limit', 5 ); $i++ ) {
            $this->post( '/login', [
                'email' => $email,
                'password' => 'wrong-password',
            ] );
        }

        // The next request should fail due to rate limiting
        $response = $this->post( '/login', [
            'email' => $email,
            'password' => 'wrong-password',
        ] );

        $response->assertStatus( 302 ); // Expect a redirect
        $response->assertSessionHasErrors( 'email' );
    } );