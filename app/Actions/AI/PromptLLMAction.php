<?php

    declare( strict_types = 1 );

    namespace App\Actions\AI;

    use Illuminate\Support\Facades\Http;
    use App\Exceptions\OpenRouterApiException;
    use App\Validators\OpenRouterResponseValidator;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Http\Client\RequestException;

    class PromptLLMAction
    {
        private const API_ENDPOINT = 'https://openrouter.ai/api/v1/chat/completions';
        private string $defaultModel;
        private OpenRouterResponseValidator $validator;

        public function __construct(
            OpenRouterResponseValidator $validator,
            ?string $defaultModel = null
        ) {
            $this->validator = $validator;
            $this->defaultModel = $defaultModel ??
                config( 'services.openrouter.default_model', 'meta-llama/llama-3.1-70b-instruct:free' );
        }

        /**
         * Generate text using OpenRouter AI
         *
         * @param  string  $prompt
         * @param  array  $options  Additional options to customize the AI request
         * @return array Response details including text and metadata
         * @throws OpenRouterApiException
         */
        public function execute( string $prompt, array $options = [] ) : array
        {
            $requestPayload = $this->preparePayload( $prompt, $options );

            try {
                $response = Http::withHeaders( $this->prepareHeaders() )
                    ->timeout( 10 )
                    ->retry( 3, 100, function ( $exception ) {
                        return $this->shouldRetry( $exception );
                    } )
                    ->post( self::API_ENDPOINT, $requestPayload );

                if ( $response->failed() ) {
                    throw new OpenRouterApiException(
                        'API request failed',
                        $response->status(),
                        null,
                        $response->body()
                    );
                }

                return $this->validator->validateAndTransform( $response->json() );
            } catch (\Exception $e) {
                $this->logError( $e, $prompt, $options );
                throw $e;
            }
        }

        /**
         * Prepare the request payload
         */
        private function preparePayload( string $prompt, array $options ) : array
        {
            $defaultPayload = [
                'model' => $this->defaultModel,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ];

            return array_merge( $defaultPayload, $options );
        }

        /**
         * Determine if the exception warrants a retry.
         */
        private function shouldRetry( \Exception $e ) : bool
        {
            $retriableConditions = [
                'rate limit',
                'timeout',
                'connection',
                'network',
                'service unavailable',
                'too many requests',
            ];

            $isRetriable = false;

            $errorMessage = strtolower( $e->getMessage() );
            foreach ( $retriableConditions as $condition ) {
                if ( stripos( $errorMessage, $condition ) !== false ) {
                    $isRetriable = true;
                    break;
                }
            }

            if ( $e instanceof RequestException && $e->response ) {
                $responseStatus = $e->response->status(); // Access status via response property
                $retriableStatuses = [ 429, 500, 502, 503, 504 ];

                $isRetriable = $isRetriable || \in_array( $responseStatus, $retriableStatuses, true );
            }

            return $isRetriable;
        }


        /**
         * Prepare API request headers
         */
        private function prepareHeaders() : array
        {
            return [
                'Authorization' => 'Bearer ' . config( 'services.openrouter.api_key' ),
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config( 'app.url' ),
                'X-Title' => config( 'app.name' )
            ];
        }

        /**
         * Log error information
         */
        private function logError( \Exception $e, string $prompt, array $options ) : void
        {
            Log::error( 'OpenRouter AI Text Generation Error', [
                'message' => $e->getMessage(),
                'prompt' => substr( preg_replace( '/\s+/', ' ', $prompt ), 0, 500 ),
                'model' => $this->defaultModel,
                'options' => array_diff_key( $options, [ 'messages' => null ] )
            ] );
        }
    }