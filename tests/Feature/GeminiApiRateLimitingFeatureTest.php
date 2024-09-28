<?php
    declare( strict_types = 1 );

    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\RateLimiter;
    use Illuminate\Validation\ValidationException;

    beforeEach( function () {
        $ipAddress = '127.0.0.1'; // Explicitly set IP address
        $this->withServerVariables( [ 'REMOTE_ADDR' => $ipAddress ] );

        // Clear the rate limiter for the IP address
        RateLimiter::clear( 'gemini-api:' . $ipAddress );

        dump( [ 'cleared_attempts' => RateLimiter::attempts( 'gemini-api:' . $ipAddress ) ] );

        // Adjust the mock response to match what the Gemini client expects
        Http::fake( [
            '*' => Http::response( [
                'content' => 'Generated text response',  // Adjusted to match expected key
            ], 200 ),
        ] );
    } );


    test( 'it applies rate limit on Gemini API requests', function () {
        $action = new \App\Actions\API\GenerateGeminiTextAction();

        // Simulate reaching the rate limit
        for ( $i = 0; $i < 10; $i++ ) {
            dump( "Executing request {$i}" );
            $action->execute( 'prompt' );
        }

        // Check current status of the rate limiter
        $hitCount = RateLimiter::attempts( 'gemini-api:127.0.0.1' );
        $availableInSeconds = RateLimiter::availableIn( 'gemini-api:127.0.0.1' );

        dump( [
            'hit_count' => $hitCount,
            'available_in' => $availableInSeconds,
        ] );

        // The next request should trigger a rate limit error
        expect( fn() => $action->execute( 'prompt' ) )->toThrow( ValidationException::class );
    } );