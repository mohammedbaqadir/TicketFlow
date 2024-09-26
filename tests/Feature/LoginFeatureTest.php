<?php
    declare( strict_types = 1 );

    use App\Models\User;
    use Illuminate\Support\Facades\RateLimiter;

    beforeEach( function () {
        $this->artisan( 'migrate' );

        RateLimiter::clear( 'login_email:test@example.com' );
        RateLimiter::clear( 'login_ip:' . request()->ip() );

        $this->user = User::factory()->create( [
            'email' => 'test@example.com',
            'password' => bcrypt( 'password' ),
        ] );
    } );

    test( 'user can login successfully with correct credentials', function () {
        $response = $this->post( '/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ] );

        $response->assertRedirect( route( 'home' ) );
        $this->assertAuthenticated();
    } );

    test( 'user cannot login after exceeding max attempts', function () {
        // Enable the throttle middleware for this test
        $this->withoutMiddleware()->withMiddleware( [ 'web', 'throttle:login' ] );

        for ( $i = 0; $i < config( 'lockout.max_attempts', 5 ); $i++ ) {
            $this->post( '/login', [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ] );
        }

        $response = $this->post( '/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ] );

        $response->assertStatus( 302 ); // Expect a redirect
        $response->assertSessionHasErrors( 'email' );
        $this->assertGuest();
    } );