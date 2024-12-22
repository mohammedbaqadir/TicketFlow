<?php

    declare( strict_types = 1 );

    use App\Actions\AI\PromptLLMAction;
    use App\Actions\Ticket\DetermineTicketPriorityAction;
    use App\Exceptions\OpenRouterApiException;
    use App\Models\Ticket;
    use App\Validators\OpenRouterResponseValidator;
    use Carbon\Carbon;
    use Illuminate\Http\Client\ConnectionException;
    use Illuminate\Http\Client\RequestException;
    use Illuminate\Support\Facades\Http;

    it( 'uses configured default model for API requests', function () {
        $mockValidator = Mockery::mock( OpenRouterResponseValidator::class );
        $mockValidator->shouldReceive( 'validateAndTransform' )->once()->andReturn( [
            'text' => 'test response',
            'model' => 'test-model'
        ] );

        $action = new PromptLLMAction( $mockValidator );

        Http::fake( [
            'https://openrouter.ai/api/v1/chat/completions' => Http::response( [
                'choices' => [
                    [ 'message' => [ 'content' => 'test response' ] ]
                ],
                'model' => 'test-model'
            ], 200 )
        ] );

        $action->execute( 'Test Prompt' );

        Http::assertSent( function ( $request ) {
            return $request['model'] === config( 'services.openrouter.default_model',
                    'meta-llama/llama-3.1-70b-instruct:free' );
        } );
    } );

    it( /**
     * @throws OpenRouterApiException
     */ 'handles API rate limiting with retries', function () {
        $mockValidator = Mockery::mock( OpenRouterResponseValidator::class );
        $action = new PromptLLMAction( $mockValidator );

        Http::fake( [
            'https://openrouter.ai/api/v1/chat/completions' => Http::sequence()
                ->push( null, 429 )  // Too Many Requests
                ->push( [ 'choices' => [ [ 'message' => [ 'content' => 'Success' ] ] ] ], 200 )
        ] );

        $mockValidator->shouldReceive( 'validateAndTransform' )->once()->andReturn( [
            'text' => 'Success'
        ] );

        $result = $action->execute( 'Test Prompt' );
        expect( $result['text'] )->toBe( 'Success' );
    } );

    it( 'validates successful API responses', function () {
        $validator = new OpenRouterResponseValidator();
        $validResponse = [
            'choices' => [
                [ 'message' => [ 'content' => 'test content' ] ]
            ],
            'model' => 'test-model',
            'usage' => [
                'prompt_tokens' => 10,
                'completion_tokens' => 20,
                'total_tokens' => 30
            ]
        ];

        $result = $validator->validateAndTransform( $validResponse );

        expect( $result )
            ->toHaveKey( 'text', 'test content' )
            ->toHaveKey( 'model', 'test-model' )
            ->toHaveKey( 'usage.total_tokens', 30 );
    } );

    it( 'throws exception for invalid API response format', function () {
        $validator = new OpenRouterResponseValidator();

        expect( /**
         * @throws OpenRouterApiException
         */ fn() => $validator->validateAndTransform( [] ) )
            ->toThrow( OpenRouterApiException::class, 'Invalid API response: No choices found' );
    } );

    it( 'determines high priority tickets correctly', function () {
        Http::fake( [
            'https://openrouter.ai/api/v1/chat/completions' => Http::response( [
                'choices' => [
                    [ 'message' => [ 'content' => 'high' ] ]
                ]
            ], 200 ),
        ] );

        $mockValidator = new OpenRouterResponseValidator();
        $promptLLMAction = new PromptLLMAction( $mockValidator );
        $action = new DetermineTicketPriorityAction( $promptLLMAction );

        $ticket = new Ticket( [
            'title' => 'Critical System Failure',
            'description' => 'Production system is down'
        ] );

        $result = $action->execute( $ticket );

        expect( $result )
            ->toHaveKey( 'priority', 'high' )
            ->and( $result['timeout_at'] )->toBeInstanceOf( Carbon::class );
    } );

    it( 'determines medium priority tickets correctly', function () {
        Http::fake( [
            'https://openrouter.ai/api/v1/chat/completions' => Http::response( [
                'choices' => [
                    [ 'message' => [ 'content' => 'medium' ] ]
                ]
            ], 200 ),
        ] );

        $mockValidator = new OpenRouterResponseValidator();
        $promptLLMAction = new PromptLLMAction( $mockValidator );
        $action = new DetermineTicketPriorityAction( $promptLLMAction );

        $ticket = new Ticket( [
            'title' => 'System Performance Degradation',
            'description' => 'Users experiencing slowdown but system still operational'
        ] );

        $result = $action->execute( $ticket );

        expect( $result )
            ->toHaveKey( 'priority', 'medium' )
            ->and( $result['timeout_at'] )->toBeInstanceOf( Carbon::class );
    } );

    it( 'defaults to low priority when AI service fails', function () {
        Http::fake( [
            'https://openrouter.ai/api/v1/chat/completions' => Http::response( null, 500 ),
        ] );

        $mockValidator = new OpenRouterResponseValidator();
        $promptLLMAction = new PromptLLMAction( $mockValidator );
        $action = new DetermineTicketPriorityAction( $promptLLMAction );

        $ticket = new Ticket( [
            'title' => 'Test Ticket',
            'description' => 'Test Description'
        ] );

        $result = $action->execute( $ticket );
        expect( $result['priority'] )->toBe( 'low' );
    } );

    it( 'handles network timeouts appropriately', function () {
        $mockValidator = Mockery::mock( OpenRouterResponseValidator::class );
        $action = new PromptLLMAction( $mockValidator );

        Http::fake( [
            'https://openrouter.ai/api/v1/chat/completions' => Http::sequence()
                ->pushStatus( 408 )
                ->whenEmpty( Http::response( null, 408 ) )
        ] );

        $mockValidator->shouldReceive( 'validateAndTransform' )->never();

        expect( fn() => $action->execute( 'Test Prompt' ) )
            ->toThrow( RequestException::class );
    } );

    it( 'handles malformed API responses', function () {
        $mockValidator = new OpenRouterResponseValidator();
        $action = new PromptLLMAction( $mockValidator );

        Http::fake( [
            'https://openrouter.ai/api/v1/chat/completions' => Http::response(
                'Invalid JSON response {[]',
                200
            )
        ] );

        expect( fn() => $action->execute( 'Test Prompt' ) )
            ->toThrow( TypeError::class );
    } );