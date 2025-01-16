<?php
    declare( strict_types = 1 );

    namespace App\Validators;

    use Illuminate\Support\Facades\Validator;
    use RuntimeException;

    class TicketConfigValidator
    {
        private const ARRAY_VALIDATION_RULES = [
            'groupings' => [
                'title' => 'required|string',
                'status' => 'required|array',
                'status.*' => 'string',
                'assignee_required' => 'sometimes|boolean',
                'no_tickets_msg' => 'required|string',
            ]
        ];

        /**
         * Validate that an array exists and is not empty
         *
         * @throws RuntimeException
         */
        private static function validateArrayExists( mixed $array, string $context ) : void
        {
            if ( !is_array( $array ) ) {
                throw new RuntimeException( "{$context} configuration must be a valid array" );
            }
            if ( empty( $array ) ) {
                throw new RuntimeException( "{$context} configuration cannot be empty" );
            }
        }

        /**
         * Validate that parallel arrays match in length and exist
         *
         * @throws RuntimeException
         */
        private static function validateParallelArrays( array $arrays, array $arrayNames, string $context ) : void
        {
            $firstLength = count( $arrays[0] );

            foreach ( $arrays as $index => $array ) {
                self::validateArrayExists( $array, $arrayNames[ $index ] );

                if ( count( $array ) !== $firstLength ) {
                    throw new RuntimeException(
                        "{$context}: {$arrayNames[$index]} must have same number of elements as {$arrayNames[0]}"
                    );
                }
            }
        }

        /**
         * Validate string array elements
         *
         * @throws RuntimeException
         */
        private static function validateStringArray( array $array, string $context ) : void
        {
            foreach ( $array as $value ) {
                if ( !is_string( $value ) || empty( trim( $value ) ) ) {
                    throw new RuntimeException( "{$context} must contain non-empty strings" );
                }
            }
        }

        /**
         * Validate positive integer array elements
         *
         * @throws RuntimeException
         */
        private static function validatePositiveIntegerArray( array $array, string $context ) : void
        {
            foreach ( $array as $value ) {
                if ( !is_int( $value ) || $value <= 0 ) {
                    throw new RuntimeException( "{$context} must contain positive integers" );
                }
            }
        }

        /**
         * Validate groupings configuration
         *
         * @throws RuntimeException
         */
        public static function validateGroupings( ?array $groupings ) : void
        {
            self::validateArrayExists( $groupings, 'Groupings' );

            foreach ( $groupings as $index => $group ) {
                if ( !is_array( $group ) ) {
                    throw new RuntimeException(
                        "Invalid groupings configuration: Group at index {$index} must be an array"
                    );
                }

                $validator = Validator::make( $group, self::ARRAY_VALIDATION_RULES['groupings'] );
                if ( $validator->fails() ) {
                    throw new RuntimeException(
                        'Invalid groupings configuration: ' . $validator->errors()->first()
                    );
                }
            }
        }

        /**
         * Validate status configuration structure
         *
         * @throws RuntimeException
         */
        public static function validateStatuses( ?array $statuses ) : void
        {
            self::validateArrayExists( $statuses, 'Statuses' );

            // Validate basic structure
            $requiredKeys = [ 'keys', 'labels', 'badges', 'cards' ];
            foreach ( $requiredKeys as $key ) {
                if ( !isset( $statuses[ $key ] ) ) {
                    throw new RuntimeException( "Statuses configuration must contain '{$key}' section" );
                }
            }

            // Validate parallel arrays
            self::validateParallelArrays(
                [
                    $statuses['keys'], $statuses['labels'], $statuses['badges']['styles'],
                    $statuses['badges']['icons'], $statuses['cards']['backgrounds']
                ],
                [ 'keys', 'labels', 'badge styles', 'badge icons', 'card backgrounds' ],
                'Statuses'
            );

            // Validate content
            self::validateStringArray( $statuses['keys'], 'Status keys' );
            self::validateStringArray( $statuses['labels'], 'Status labels' );
            self::validateStringArray( $statuses['badges']['styles'], 'Status badge styles' );
            self::validateStringArray( $statuses['badges']['icons'], 'Status badge icons' );
            self::validateStringArray( $statuses['cards']['backgrounds'], 'Status card backgrounds' );
        }

        /**
         * Validate priority configuration structure
         *
         * @throws RuntimeException
         */
        public static function validatePriorities( ?array $priorities ) : void
        {
            self::validateArrayExists( $priorities, 'Priorities' );

            // Validate basic structure
            $requiredKeys = [ 'keys', 'labels', 'timeouts', 'badges' ];
            foreach ( $requiredKeys as $key ) {
                if ( !isset( $priorities[ $key ] ) ) {
                    throw new RuntimeException( "Priorities configuration must contain '{$key}' section" );
                }
            }

            // Validate parallel arrays
            self::validateParallelArrays(
                [
                    $priorities['keys'], $priorities['labels'], $priorities['timeouts'],
                    $priorities['badges']['styles'], $priorities['badges']['icons']
                ],
                [ 'keys', 'labels', 'timeouts', 'badge styles', 'badge icons' ],
                'Priorities'
            );

            // Validate content
            self::validateStringArray( $priorities['keys'], 'Priority keys' );
            self::validateStringArray( $priorities['labels'], 'Priority labels' );
            self::validatePositiveIntegerArray( $priorities['timeouts'], 'Priority timeouts' );
            self::validateStringArray( $priorities['badges']['styles'], 'Priority badge styles' );
            self::validateStringArray( $priorities['badges']['icons'], 'Priority badge icons' );
        }

        /**
         * Validate existence of a key in valid values
         *
         * @throws RuntimeException
         */
        public static function validateExistence( string $key, array $validValues, string $context ) : void
        {
            if ( !in_array( $key, $validValues, true ) ) {
                throw new RuntimeException( "Invalid {$context}: {$key}" );
            }
        }
    }