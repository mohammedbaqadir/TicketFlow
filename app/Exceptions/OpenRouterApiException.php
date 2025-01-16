<?php
    declare( strict_types = 1 );

    namespace App\Exceptions;

    use Exception;
    use Throwable;
    use Illuminate\Support\Arr;

    class OpenRouterApiException extends Exception
    {
        /**
         * Raw API response body
         *
         * @var string|null
         */
        protected $apiResponseBody;

        /**
         * Parsed error metadata
         *
         * @var array
         */
        protected $errorMetadata;

        /**
         * Create a new OpenRouter API Exception
         *
         * @param  string  $message  Error message
         * @param  int  $code  Error code
         * @param  Throwable|null  $previous  Previous exception
         * @param  string|null  $apiResponseBody  Raw API response body
         */
        public function __construct(
            string $message = '',
            int $code = 0,
            ?Throwable $previous = null,
            ?string $apiResponseBody = null
        ) {
            parent::__construct( $message, $code, $previous );

            $this->apiResponseBody = $apiResponseBody;
            $this->parseErrorMetadata();
        }

        /**
         * Get the raw API response body
         *
         * @return string|null
         */
        public function getApiResponseBody() : ?string
        {
            return $this->apiResponseBody;
        }

        /**
         * Parse error metadata from the API response
         *
         * @return void
         */
        protected function parseErrorMetadata() : void
        {
            if ( !$this->apiResponseBody ) {
                $this->errorMetadata = [];
                return;
            }

            try {
                $parsedResponse = json_decode( $this->apiResponseBody, true, 512, JSON_THROW_ON_ERROR );

                // Check if the response contains an error
                $error = Arr::get( $parsedResponse, 'error', [] );

                $this->errorMetadata = [
                    'code' => Arr::get( $error, 'code' ),
                    'message' => Arr::get( $error, 'message' ),
                    'metadata' => Arr::get( $error, 'metadata', [] )
                ];
            } catch (\JsonException $e) {
                // If JSON parsing fails, set minimal metadata
                $this->errorMetadata = [
                    'code' => $this->code,
                    'message' => $this->message,
                    'metadata' => []
                ];
            }
        }

        /**
         * Get parsed error metadata
         *
         * @return array
         */
        public function getErrorMetadata() : array
        {
            return $this->errorMetadata;
        }

        /**
         * Get a specific metadata field
         *
         * @param  string  $key
         * @param  mixed|null  $default
         * @return mixed
         */
        public function getMetadataField( string $key, mixed $default = null ) : mixed
        {
            return Arr::get( $this->errorMetadata, $key, $default );
        }

    }