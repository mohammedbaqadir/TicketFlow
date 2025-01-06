<?php
    declare( strict_types = 1 );

    use App\Validators\TicketConfigValidator;

    describe( 'TicketConfigValidator', function () {
        describe( 'status validation', function () {
            it( 'should validate correct status configuration', function () {
                $validConfig = [
                    'open' => 'OPEN',
                    'resolved' => 'RESOLVED'
                ];

                expect( fn() => TicketConfigValidator::validateStatuses( $validConfig ) )
                    ->not->toThrow( RuntimeException::class );
            } );

            it( 'should reject non-array status configuration', function () {
                expect( fn() => TicketConfigValidator::validateStatuses( 'invalid' ) )
                    ->toThrow( RuntimeException::class, 'Ticket statuses configuration must be a valid array' );
            } );

            it( 'should reject empty status configuration', function () {
                expect( fn() => TicketConfigValidator::validateStatuses( [] ) )
                    ->toThrow( RuntimeException::class, 'Ticket statuses configuration cannot be empty' );
            } );

            it( 'should reject non-string status keys', function () {
                $invalidConfig = [
                    123 => 'OPEN'
                ];

                expect( fn() => TicketConfigValidator::validateStatuses( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Ticket status keys must be strings' );
            } );

            it( 'should reject non-string status values', function () {
                $invalidConfig = [
                    'open' => 123
                ];

                expect( fn() => TicketConfigValidator::validateStatuses( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Ticket status values must be strings' );
            } );

            it( 'should reject empty string status keys', function () {
                $invalidConfig = [
                    '' => 'OPEN'
                ];

                expect( fn() => TicketConfigValidator::validateStatuses( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Ticket status keys cannot be empty strings' );
            } );

            it( 'should reject empty string status values', function () {
                $invalidConfig = [
                    'open' => ''
                ];

                expect( fn() => TicketConfigValidator::validateStatuses( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Ticket status values cannot be empty strings' );
            } );

            it( 'should validate single status existence', function () {
                $validStatuses = [ 'open' => 'OPEN' ];

                expect( fn() => TicketConfigValidator::validateSingleStatus( 'open', $validStatuses ) )
                    ->not->toThrow( RuntimeException::class );
            } );

            it( 'should reject non-string single status', function () {
                $validStatuses = [ 'open' => 'OPEN' ];

                expect( fn() => TicketConfigValidator::validateSingleStatus( 123, $validStatuses ) )
                    ->toThrow( RuntimeException::class, 'Status must be a string' );
            } );

            it( 'should reject invalid single status', function () {
                $validStatuses = [ 'open' => 'OPEN' ];

                expect( fn() => TicketConfigValidator::validateSingleStatus( 'invalid', $validStatuses ) )
                    ->toThrow( RuntimeException::class, 'Invalid status: invalid' );
            } );
        } );

        describe( 'priority validation', function () {
            it( 'should validate correct priority configuration', function () {
                $validConfig = [
                    'low' => 'LOW',
                    'high' => 'HIGH'
                ];

                expect( fn() => TicketConfigValidator::validatePriorities( $validConfig ) )
                    ->not->toThrow( RuntimeException::class );
            } );

            it( 'should reject non-array priority configuration', function () {
                expect( fn() => TicketConfigValidator::validatePriorities( 'invalid' ) )
                    ->toThrow( RuntimeException::class, 'Ticket priorities configuration must be a valid array' );
            } );

            it( 'should reject empty priority configuration', function () {
                expect( fn() => TicketConfigValidator::validatePriorities( [] ) )
                    ->toThrow( RuntimeException::class, 'Ticket priorities configuration cannot be empty' );
            } );

            it( 'should reject non-string priority keys', function () {
                $invalidConfig = [
                    123 => 'LOW'
                ];

                expect( fn() => TicketConfigValidator::validatePriorities( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Ticket priority keys must be strings' );
            } );

            it( 'should reject non-string priority values', function () {
                $invalidConfig = [
                    'low' => 123
                ];

                expect( fn() => TicketConfigValidator::validatePriorities( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Ticket priority values must be strings' );
            } );

            it( 'should reject empty string priority keys', function () {
                $invalidConfig = [
                    '' => 'LOW'
                ];

                expect( fn() => TicketConfigValidator::validatePriorities( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Ticket priority keys cannot be empty strings' );
            } );

            it( 'should reject empty string priority values', function () {
                $invalidConfig = [
                    'low' => ''
                ];

                expect( fn() => TicketConfigValidator::validatePriorities( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Ticket priority values cannot be empty strings' );
            } );

            it( 'should validate single priority existence', function () {
                $validPriorities = [ 'low' => 'LOW' ];

                expect( fn() => TicketConfigValidator::validateSinglePriority( 'low', $validPriorities ) )
                    ->not->toThrow( RuntimeException::class );
            } );

            it( 'should reject non-string single priority', function () {
                $validPriorities = [ 'low' => 'LOW' ];

                expect( fn() => TicketConfigValidator::validateSinglePriority( 123, $validPriorities ) )
                    ->toThrow( RuntimeException::class, 'Priority must be a string' );
            } );

            it( 'should reject invalid single priority', function () {
                $validPriorities = [ 'low' => 'LOW' ];

                expect( fn() => TicketConfigValidator::validateSinglePriority( 'invalid', $validPriorities ) )
                    ->toThrow( RuntimeException::class, 'Invalid priority: invalid' );
            } );
        } );

        describe( 'timeout validation', function () {
            it( 'should validate correct timeout configuration', function () {
                $validConfig = [
                    'low' => 8,
                    'high' => 2
                ];

                expect( fn() => TicketConfigValidator::validatePriorityTimeouts( $validConfig ) )
                    ->not->toThrow( RuntimeException::class );
            } );

            it( 'should reject non-array timeout configuration', function () {
                expect( fn() => TicketConfigValidator::validatePriorityTimeouts( 'invalid' ) )
                    ->toThrow( RuntimeException::class, 'Priority timeouts configuration must be a valid array' );
            } );

            it( 'should reject empty timeout configuration', function () {
                expect( fn() => TicketConfigValidator::validatePriorityTimeouts( [] ) )
                    ->toThrow( RuntimeException::class, 'Priority timeouts configuration cannot be empty' );
            } );

            it( 'should reject non-string timeout keys', function () {
                $invalidConfig = [
                    123 => 8
                ];

                expect( fn() => TicketConfigValidator::validatePriorityTimeouts( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Priority timeout keys must be strings' );
            } );

            it( 'should reject non-integer timeout values', function () {
                $invalidConfig = [
                    'low' => 'invalid'
                ];

                expect( fn() => TicketConfigValidator::validatePriorityTimeouts( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Priority timeout values must be positive integers' );
            } );

            it( 'should reject zero timeout values', function () {
                $invalidConfig = [
                    'low' => 0
                ];

                expect( fn() => TicketConfigValidator::validatePriorityTimeouts( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Priority timeout values must be positive integers' );
            } );

            it( 'should reject negative timeout values', function () {
                $invalidConfig = [
                    'low' => -1
                ];

                expect( fn() => TicketConfigValidator::validatePriorityTimeouts( $invalidConfig ) )
                    ->toThrow( RuntimeException::class, 'Priority timeout values must be positive integers' );
            } );
        } );
    } );