<?php
    declare( strict_types = 1 );

    namespace App\Validators;

    use Illuminate\Support\Facades\Validator;
    use RuntimeException;

    class TicketConfigValidator
    {
        /**
         * Validate ticket groupings configuration
         *
         * @param ?array  $groupings
         * @throws RuntimeException
         */
        public static function validateGroupings( ?array $groupings ) : void
        {
            if ( !\is_array( $groupings ) ) {
                throw new RuntimeException( 'Ticket groupings configuration must be a valid array' );
            }

            if ( empty( $groupings ) ) {
                throw new RuntimeException( 'Ticket groupings configuration cannot be empty' );
            }

            foreach ( $groupings as $index => $group ) {
                if ( !\is_array( $group ) ) {
                    throw new RuntimeException(
                        "Invalid ticket group configuration: Group at index {$index} must be an array"
                    );
                }

                $validator = Validator::make( $group, [
                    'title' => 'required|string',
                    'status' => 'required|array',
                    'status.*' => 'string',
                    'assignee_required' => 'sometimes|boolean',
                    'no_tickets_msg' => 'required|string',
                ] );

                if ( $validator->fails() ) {
                    throw new RuntimeException(
                        'Invalid ticket group configuration: ' . $validator->errors()->first()
                    );
                }
            }
        }

        /**
         * Validate ticket statuses configuration
         *
         * @param  mixed  $statuses
         * @throws RuntimeException
         */
        public static function validateStatuses( $statuses ) : void
        {
            if ( !\is_array( $statuses ) ) {
                throw new RuntimeException( 'Ticket statuses configuration must be a valid array' );
            }

            if ( empty( $statuses ) ) {
                throw new RuntimeException( 'Ticket statuses configuration cannot be empty' );
            }

            foreach ( $statuses as $key => $value ) {
                if ( !\is_string( $key ) ) {
                    throw new RuntimeException( 'Ticket status keys must be strings' );
                }
                if ( !\is_string( $value ) ) {
                    throw new RuntimeException( 'Ticket status values must be strings' );
                }
                if ( empty( $key ) ) {
                    throw new RuntimeException( 'Ticket status keys cannot be empty strings' );
                }
                if ( empty( $value ) ) {
                    throw new RuntimeException( 'Ticket status values cannot be empty strings' );
                }
            }
        }

        /**
         * Validate single status exists
         *
         * @param  mixed  $status
         * @param  array  $validStatuses
         * @throws RuntimeException
         */
        public static function validateSingleStatus( $status, array $validStatuses ) : void
        {
            if ( !\is_string( $status ) ) {
                throw new RuntimeException( 'Status must be a string' );
            }

            if ( !isset( $validStatuses[ $status ] ) ) {
                throw new RuntimeException( "Invalid status: {$status}" );
            }
        }

        /**
         * Validate ticket priorities configuration
         *
         * @param  mixed  $priorities
         * @throws RuntimeException
         */
        public static function validatePriorities( $priorities ) : void
        {
            if ( !\is_array( $priorities ) ) {
                throw new RuntimeException( 'Ticket priorities configuration must be a valid array' );
            }

            if ( empty( $priorities ) ) {
                throw new RuntimeException( 'Ticket priorities configuration cannot be empty' );
            }

            foreach ( $priorities as $key => $value ) {
                if ( !\is_string( $key ) ) {
                    throw new RuntimeException( 'Ticket priority keys must be strings' );
                }
                if ( !\is_string( $value ) ) {
                    throw new RuntimeException( 'Ticket priority values must be strings' );
                }
                if ( empty( $key ) ) {
                    throw new RuntimeException( 'Ticket priority keys cannot be empty strings' );
                }
                if ( empty( $value ) ) {
                    throw new RuntimeException( 'Ticket priority values cannot be empty strings' );
                }
            }
        }

        /**
         * Validate single priority exists
         *
         * @param  mixed  $priority
         * @param  array  $validPriorities
         * @throws RuntimeException
         */
        public static function validateSinglePriority( $priority, array $validPriorities ) : void
        {
            if ( !\is_string( $priority ) ) {
                throw new RuntimeException( 'Priority must be a string' );
            }

            if ( !isset( $validPriorities[ $priority ] ) ) {
                throw new RuntimeException( "Invalid priority: {$priority}" );
            }
        }

        /**
         * Validate priority timeouts configuration
         *
         * @param  mixed  $timeouts
         * @throws RuntimeException
         */
        public static function validatePriorityTimeouts( $timeouts ) : void
        {
            if ( !\is_array( $timeouts ) ) {
                throw new RuntimeException( 'Priority timeouts configuration must be a valid array' );
            }

            if ( empty( $timeouts ) ) {
                throw new RuntimeException( 'Priority timeouts configuration cannot be empty' );
            }

            foreach ( $timeouts as $key => $value ) {
                if ( !\is_string( $key ) ) {
                    throw new RuntimeException( 'Priority timeout keys must be strings' );
                }
                if ( !\is_int( $value ) || $value <= 0 ) {
                    throw new RuntimeException( 'Priority timeout values must be positive integers' );
                }
            }
        }
    }