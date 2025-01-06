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
            ],
            'key_value_pairs' => [
                'key_type' => 'string',
                'value_type' => [ 'string', 'integer' ], // Updated to support both string and integer values
                'allow_empty' => false
            ]
        ];

        /**
         * Validate configuration array structure
         */
        private static function validateArrayStructure(
            array $data,
            string $type,
            string $context
        ) : void {
            if ( empty( $data ) ) {
                throw new RuntimeException( "{$context} configuration cannot be empty" );
            }

            if ( $type === 'key_value_pairs' ) {
                $rules = self::ARRAY_VALIDATION_RULES[ $type ];
                foreach ( $data as $key => $value ) {
                    if ( !\is_string( $key ) ) {
                        throw new RuntimeException( "{$context} keys must be strings" );
                    }

                    $valueValid = match ( $context ) {
                        'Priority timeout' => \is_int( $value ) && $value > 0,
                        default => \is_string( $value ) && !empty( $value )
                    };

                    if ( !$valueValid ) {
                        throw new RuntimeException(
                            $context === 'Priority timeout'
                                ? "{$context} values must be positive integers"
                                : "{$context} values must be non-empty strings"
                        );
                    }
                }
            } elseif ( $type === 'groupings' ) {
                foreach ( $data as $index => $group ) {
                    if ( !\is_array( $group ) ) {
                        throw new RuntimeException(
                            "Invalid {$context} configuration: Group at index {$index} must be an array"
                        );
                    }

                    $validator = Validator::make( $group, self::ARRAY_VALIDATION_RULES[ $type ] );
                    if ( $validator->fails() ) {
                        throw new RuntimeException(
                            "Invalid {$context} configuration: " . $validator->errors()->first()
                        );
                    }
                }
            }
        }

        public static function validateGroupings( ?array $groupings ) : void
        {
            if ( !\is_array( $groupings ) ) {
                throw new RuntimeException( 'Ticket groupings configuration must be a valid array' );
            }
            self::validateArrayStructure( $groupings, 'groupings', 'Groupings' );
        }

        public static function validateStatuses( $statuses ) : void
        {
            if ( !\is_array( $statuses ) ) {
                throw new RuntimeException( 'Ticket statuses configuration must be a valid array' );
            }
            self::validateArrayStructure( $statuses, 'key_value_pairs', 'Status' );
        }

        public static function validatePriorities( $priorities ) : void
        {
            if ( !\is_array( $priorities ) ) {
                throw new RuntimeException( 'Ticket priorities configuration must be a valid array' );
            }
            self::validateArrayStructure( $priorities, 'key_value_pairs', 'Priority' );
        }

        public static function validatePriorityTimeouts( $timeouts ) : void
        {
            if ( !\is_array( $timeouts ) ) {
                throw new RuntimeException( 'Priority timeouts configuration must be a valid array' );
            }
            self::validateArrayStructure(
                $timeouts,
                'key_value_pairs',
                'Priority timeout'
            );
        }

        public static function validateExistence( string $key, array $validValues, string $context ) : void
        {
            if ( !isset( $validValues[ $key ] ) ) {
                throw new RuntimeException( "Invalid {$context}: {$key}" );
            }
        }
    }