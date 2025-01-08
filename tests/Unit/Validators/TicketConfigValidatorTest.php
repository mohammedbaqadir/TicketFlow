<?php
    declare( strict_types = 1 );

    use App\Validators\TicketConfigValidator;

// Grouping Validation Tests
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

// Status Validation Tests
    it( 'validates correct statuses structure', function () {
        $statuses = [
            'keys' => [ 'open', 'closed' ],
            'labels' => [ 'OPEN', 'CLOSED' ],
            'badges' => [
                'styles' => [ 'style1', 'style2' ],
                'icons' => [ 'icon1', 'icon2' ]
            ],
            'cards' => [
                'backgrounds' => [ 'bg1', 'bg2' ]
            ]
        ];

        expect( fn() => TicketConfigValidator::validateStatuses( $statuses ) )
            ->not->toThrow( RuntimeException::class );
    } );

    it( 'throws exception for missing status section', function () {
        $statuses = [
            'keys' => [ 'open', 'closed' ],
            // Missing labels section
            'badges' => [
                'styles' => [ 'style1', 'style2' ],
                'icons' => [ 'icon1', 'icon2' ]
            ]
        ];

        TicketConfigValidator::validateStatuses( $statuses );
    } )->throws( RuntimeException::class, "Statuses configuration must contain 'labels' section" );

    it( 'throws exception for mismatched status array lengths', function () {
        $statuses = [
            'keys' => [ 'open', 'closed' ],
            'labels' => [ 'OPEN' ], // One less than keys
            'badges' => [
                'styles' => [ 'style1', 'style2' ],
                'icons' => [ 'icon1', 'icon2' ]
            ],
            'cards' => [
                'backgrounds' => [ 'bg1', 'bg2' ]
            ]
        ];

        TicketConfigValidator::validateStatuses( $statuses );
    } )->throws( RuntimeException::class, 'Statuses: labels must have same number of elements as keys' );

// Priority Validation Tests
    it( 'validates correct priorities structure', function () {
        $priorities = [
            'keys' => [ 'low', 'high' ],
            'labels' => [ 'LOW', 'HIGH' ],
            'timeouts' => [ 8, 2 ],
            'badges' => [
                'styles' => [ 'style1', 'style2' ],
                'icons' => [ 'icon1', 'icon2' ]
            ]
        ];

        expect( fn() => TicketConfigValidator::validatePriorities( $priorities ) )
            ->not->toThrow( RuntimeException::class );
    } );

    it( 'throws exception for missing priority section', function () {
        $priorities = [
            'keys' => [ 'low', 'high' ],
            'labels' => [ 'LOW', 'HIGH' ],
            // Missing timeouts section
            'badges' => [
                'styles' => [ 'style1', 'style2' ],
                'icons' => [ 'icon1', 'icon2' ]
            ]
        ];

        TicketConfigValidator::validatePriorities( $priorities );
    } )->throws( RuntimeException::class, "Priorities configuration must contain 'timeouts' section" );

    it( 'throws exception for invalid timeout values', function () {
        $priorities = [
            'keys' => [ 'low', 'high' ],
            'labels' => [ 'LOW', 'HIGH' ],
            'timeouts' => [ 8, -2 ], // Negative timeout
            'badges' => [
                'styles' => [ 'style1', 'style2' ],
                'icons' => [ 'icon1', 'icon2' ]
            ]
        ];

        TicketConfigValidator::validatePriorities( $priorities );
    } )->throws( RuntimeException::class, 'Priority timeouts must contain positive integers' );

// Key Existence Validation
    it( 'validates existence of key in array', function () {
        $validKeys = [ 'key1', 'key2' ];

        expect( fn() => TicketConfigValidator::validateExistence( 'key1', $validKeys, 'test' ) )
            ->not->toThrow( RuntimeException::class );
    } );

    it( 'throws exception for non-existent key', function () {
        $validKeys = [ 'key1', 'key2' ];

        TicketConfigValidator::validateExistence( 'invalid', $validKeys, 'test' );
    } )->throws( RuntimeException::class, 'Invalid test: invalid' );

// String Array Validation
    it( 'throws exception for non-string values in array', function () {
        $statuses = [
            'keys' => [ 'open', 123 ], // Invalid numeric value
            'labels' => [ 'OPEN', 'CLOSED' ],
            'badges' => [
                'styles' => [ 'style1', 'style2' ],
                'icons' => [ 'icon1', 'icon2' ]
            ],
            'cards' => [
                'backgrounds' => [ 'bg1', 'bg2' ]
            ]
        ];

        TicketConfigValidator::validateStatuses( $statuses );
    } )->throws( RuntimeException::class, 'Status keys must contain non-empty strings' );