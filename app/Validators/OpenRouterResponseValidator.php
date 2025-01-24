<?php
    declare( strict_types = 1 );

    namespace App\Validators;

    use App\Exceptions\OpenRouterApiException;

    /**
     * Class OpenRouterResponseValidator
     */
    class OpenRouterResponseValidator
    {
        /**
         * Validate and transform the API response
         *
         * @param  array{
         *          choices: array<int, array{
         *              message: array{
         *                  content?: string
         *              },
         *              finish_reason?: string
         *          }>,
         *          model?: string,
         *          usage?: array{prompt_tokens: int, completion_tokens: int, total_tokens: int}
         * }  $responseData
         * @return array{
         *      text: string,
         *      model: string|null,
         *      finish_reason: string|null,
         *      usage: array{prompt_tokens: int, completion_tokens: int, total_tokens: int},
         *      full_response: array<string, mixed>
         *  }
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