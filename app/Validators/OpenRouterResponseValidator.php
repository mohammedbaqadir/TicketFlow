<?php
    declare( strict_types = 1 );

    namespace App\Validators;

    use App\Exceptions\OpenRouterApiException;

    class OpenRouterResponseValidator
    {
        /**
         * Validate and transform the API response
         *
         * @param  array  $responseData
         * @return array
         * @throws OpenRouterApiException
         */
        public function validateAndTransform( array $responseData ) : array
        {
            if ( empty( $responseData['choices'] ) ) {
                throw new OpenRouterApiException( 'Invalid API response: No choices found' );
            }

            $choice = $responseData['choices'][0];

            if ( !isset( $choice['message']['content'] ) ) {
                throw new OpenRouterApiException( 'Invalid API response: No content in message' );
            }

            return [
                'text' => $choice['message']['content'],
                'model' => $responseData['model'] ?? null,
                'finish_reason' => $choice['finish_reason'] ?? null,
                'usage' => $responseData['usage'] ?? [
                        'prompt_tokens' => 0,
                        'completion_tokens' => 0,
                        'total_tokens' => 0
                    ],
                'full_response' => $responseData
            ];
        }

    }