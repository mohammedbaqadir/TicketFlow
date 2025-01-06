<?php
    declare( strict_types = 1 );

    use App\Config\TicketConfig;
    use Illuminate\Support\Facades\Config;

    beforeEach( function () {
        // Reset config before each test
        Config::set( 'tickets', [
            'groupings' => [
                'index' => [
                    [
                        'title' => 'Pending Action',
                        'status' => [ 'in-progress' ],
                        'assignee_required' => true,
                        'no_tickets_msg' => 'No tickets'
                    ]
                ],
                'my_tickets' => [
                    [
                        'title' => 'Active Tickets',
                        'status' => [ 'open', 'in-progress' ],
                        'no_tickets_msg' => 'No active tickets'
                    ]
                ]
            ],
            'statuses' => [
                'open' => 'OPEN',
                'in-progress' => 'IN PROGRESS'
            ],
            'priorities' => [
                'low' => 'LOW',
                'high' => 'HIGH'
            ],
            'priority_timeouts' => [
                'low' => 8,
                'high' => 2
            ]
        ] );
    } );

    it( 'retrieves index groupings configuration', function () {
        $groupings = TicketConfig::getIndexGroupings();

        expect( $groupings )
            ->toBeArray()
            ->toHaveCount( 1 )
            ->and( $groupings[0] )
            ->toHaveKeys( [ 'title', 'status', 'assignee_required', 'no_tickets_msg' ] );
    } );

    it( 'retrieves my tickets groupings configuration', function () {
        $groupings = TicketConfig::getMyTicketsGroupings();

        expect( $groupings )
            ->toBeArray()
            ->toHaveCount( 1 )
            ->and( $groupings[0] )
            ->toHaveKeys( [ 'title', 'status', 'no_tickets_msg' ] );
    } );

    it( 'retrieves all statuses', function () {
        $statuses = TicketConfig::getStatuses();

        expect( $statuses )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'open', 'in-progress' ] );
    } );

    it( 'retrieves specific status label', function () {
        $label = TicketConfig::getStatusLabel( 'open' );

        expect( $label )->toBe( 'OPEN' );
    } );

    it( 'throws exception for invalid status label', function () {
        TicketConfig::getStatusLabel( 'invalid-status' );
    } )->throws( RuntimeException::class );

    it( 'retrieves all priorities', function () {
        $priorities = TicketConfig::getPriorities();

        expect( $priorities )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'low', 'high' ] );
    } );

    it( 'retrieves specific priority label', function () {
        $label = TicketConfig::getPriorityLabel( 'high' );

        expect( $label )->toBe( 'HIGH' );
    } );

    it( 'throws exception for invalid priority label', function () {
        TicketConfig::getPriorityLabel( 'invalid-priority' );
    } )->throws( RuntimeException::class );

    it( 'retrieves all priority timeouts', function () {
        $timeouts = TicketConfig::getPriorityTimeouts();

        expect( $timeouts )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'low', 'high' ] );
    } );

    it( 'retrieves specific priority timeout', function () {
        $timeout = TicketConfig::getTimeoutForPriority( 'low' );

        expect( $timeout )->toBe( 8 );
    } );

    it( 'throws exception for invalid priority timeout', function () {
        TicketConfig::getTimeoutForPriority( 'invalid-priority' );
    } )->throws( RuntimeException::class );