<?php
    declare( strict_types = 1 );

    namespace App\Validators;

    use Illuminate\Support\Facades\Validator;

    class TicketGroupingsValidator
    {
        public static function validate( array $groupings ) : void
        {
            foreach ( $groupings as $group ) {
                $validator = Validator::make( $group, [
                    'title' => 'required|string',
                    'status' => 'required|array',
                    'status.*' => 'string',
                    'assignee_required' => 'sometimes|boolean',
                    'no_tickets_msg' => 'required|string',
                ] );

                if ( $validator->fails() ) {
                    throw new \RuntimeException( 'Invalid ticket group configuration: ' . $validator->errors()->first() );
                }
            }
        }
    }