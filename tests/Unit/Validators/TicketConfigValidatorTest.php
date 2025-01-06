<?php
    declare( strict_types = 1 );

    use App\Validators\TicketConfigValidator;

    it( 'validates correct groupings structure', function () {
        $groupings = [
            [
                'title' => 'Test Group',
                'status' => [ 'open' ],
                'assignee_required' => true,
                'no_tickets_msg' => 'No tickets'
            ]
        ];

        expect( fn() => TicketConfigValidator::validateGroupings( $groupings ) )
            ->not->toThrow( RuntimeException::class );
    } );

    it( 'throws exception for empty groupings', function () {
        TicketConfigValidator::validateGroupings( [] );
    } )->throws( RuntimeException::class, 'Groupings configuration cannot be empty' );

    it( 'throws exception for invalid grouping structure', function () {
        $groupings = [
            [
                'title' => 'Test Group',
                // Missing required 'status' field
                'no_tickets_msg' => 'No tickets'
            ]
        ];

        TicketConfigValidator::validateGroupings( $groupings );
    } )->throws( RuntimeException::class );

    it( 'validates correct statuses structure', function () {
        $statuses = [
            'open' => 'OPEN',
            'closed' => 'CLOSED'
        ];

        expect( fn() => TicketConfigValidator::validateStatuses( $statuses ) )
            ->not->toThrow( RuntimeException::class );
    } );

    it( 'throws exception for empty statuses', function () {
        TicketConfigValidator::validateStatuses( [] );
    } )->throws( RuntimeException::class, 'Status configuration cannot be empty' );

    it( 'throws exception for invalid status value type', function () {
        $statuses = [
            'open' => 123 // Should be string
        ];

        TicketConfigValidator::validateStatuses( $statuses );
    } )->throws( RuntimeException::class );

    it( 'validates correct priorities structure', function () {
        $priorities = [
            'low' => 'LOW',
            'high' => 'HIGH'
        ];

        expect( fn() => TicketConfigValidator::validatePriorities( $priorities ) )
            ->not->toThrow( RuntimeException::class );
    } );

    it( 'validates correct priority timeouts structure', function () {
        $timeouts = [
            'low' => 8,
            'high' => 2
        ];

        expect( fn() => TicketConfigValidator::validatePriorityTimeouts( $timeouts ) )
            ->not->toThrow( RuntimeException::class );
    } );

    it( 'throws exception for invalid priority timeout value', function () {
        $timeouts = [
            'low' => 'invalid' // Should be integer
        ];

        TicketConfigValidator::validatePriorityTimeouts( $timeouts );
    } )->throws( RuntimeException::class );

    it( 'validates existence of config key', function () {
        $values = [ 'key' => 'value' ];

        expect( fn() => TicketConfigValidator::validateExistence( 'key', $values, 'test' ) )
            ->not->toThrow( RuntimeException::class );
    } );

    it( 'throws exception for non-existent config key', function () {
        $values = [ 'key' => 'value' ];

        TicketConfigValidator::validateExistence( 'invalid', $values, 'test' );
    } )->throws( RuntimeException::class, 'Invalid test: invalid' );