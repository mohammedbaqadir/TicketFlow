<?php
    declare( strict_types = 1 );

    use App\Actions\Auth\LoginAttemptAction;
    use Illuminate\Support\Facades\RateLimiter;
    use Illuminate\Validation\ValidationException;

    beforeEach( function () {
        // Clear rate limiting for the email and IP before each test
        RateLimiter::clear( 'login_email:test@example.com' );
        RateLimiter::clear( 'login_ip:127.0.0.1' );
    } );

    test( 'it applies progressive delay on multiple login attempts', function () {
        $email = 'test@example.com';
        $ip = '127.0.0.1';
        $action = new LoginAttemptAction();

        // Simulate multiple login attempts
        for ( $i = 1; $i <= 5; $i++ ) {
            $action->execute( $email, $ip );
            expect( RateLimiter::attempts( 'login_email:' . $email ) )->toBe( $i );
        }

        // Check that after enough attempts, rate limiting starts
        expect( fn() => $action->execute( $email, $ip ) )->toThrow( ValidationException::class );
    } );

    test( 'it resets rate limiter after successful login', function () {
        $email = 'test@example.com';
        $ip = '127.0.0.1';
        $action = new LoginAttemptAction();

        // Simulate a few login attempts
        $action->execute( $email, $ip );
        $action->execute( $email, $ip );

        expect( RateLimiter::attempts( 'login_email:' . $email ) )->toBe( 2 );

        // Clear attempts after successful login
        RateLimiter::clear( 'login_email:' . $email );

        expect( RateLimiter::attempts( 'login_email:' . $email ) )->toBe( 0 );
    } );