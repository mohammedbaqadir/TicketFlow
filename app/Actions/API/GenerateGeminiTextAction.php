<?php
    declare( strict_types = 1 );

    namespace App\Actions\API;

    use GeminiAPI\Laravel\Facades\Gemini;
    use Illuminate\Support\Facades\RateLimiter;
    use Illuminate\Validation\ValidationException;

    class GenerateGeminiTextAction
    {
        /**
         * @throws ValidationException
         */
        public function execute( string $prompt ) : string
        {
            $this->checkRateLimit();
            return Gemini::generateText( $prompt );
        }

        /**
         * @throws ValidationException
         */
        private function checkRateLimit() : void
        {
            $ipAddress = request()->ip();
            $executed = RateLimiter::attempt(
                'gemini-api:' . $ipAddress,
                10,
                function () {
                    // Empty closure
                }
            );

            if ( !$executed ) {
                $seconds = RateLimiter::availableIn( 'gemini-api:' . $ipAddress );
                throw ValidationException::withMessages( [
                    'gemini' => "Too many requests. Try again in $seconds seconds.",
                ] );
            }
        }
    }