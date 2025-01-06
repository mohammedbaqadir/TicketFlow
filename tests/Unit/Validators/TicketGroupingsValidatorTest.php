<?php
    declare( strict_types = 1 );

    use App\Validators\TicketGroupingsValidator;

    describe( 'TicketGroupingsValidator', function () {
        it( 'should validate correct grouping configuration', function () {
            $validConfig = [
                [
                    'title' => 'Test Group',
                    'status' => [ 'open' ],
                    'no_tickets_msg' => 'No tickets',
                    'assignee_required' => true
                ]
            ];

            expect( fn() => TicketGroupingsValidator::validate( $validConfig ) )
                ->not->toThrow( RuntimeException::class );
        } );

        it( 'should reject configuration with missing required fields', function () {
            $invalidConfig = [
                [
                    'title' => 'Test Group',
                    // missing status and no_tickets_msg
                ]
            ];

            expect( fn() => TicketGroupingsValidator::validate( $invalidConfig ) )
                ->toThrow( RuntimeException::class );
        } );

        it( 'should validate all groups in configuration array', function () {
            $mixedConfig = [
                [
                    'title' => 'Valid Group',
                    'status' => [ 'open' ],
                    'no_tickets_msg' => 'No tickets'
                ],
                [
                    'title' => 'Invalid Group'
                    // missing required fields
                ]
            ];

            expect( fn() => TicketGroupingsValidator::validate( $mixedConfig ) )
                ->toThrow( RuntimeException::class );
        } );
    } );