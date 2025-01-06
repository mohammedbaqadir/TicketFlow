<?php
    declare( strict_types = 1 );

    namespace App\Validators;

    use Illuminate\Support\Facades\Validator;
    use RuntimeException;

    class TicketGroupingsValidator
    {
        public static function validate( ?array $groupings ) : void
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
    }