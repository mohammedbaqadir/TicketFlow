<?php
    declare( strict_types = 1 );

    use App\Config\TicketConfig;
    use Illuminate\Support\Facades\Config;

    beforeEach( function () {
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
                'keys' => [ 'open', 'in-progress' ],
                'labels' => [ 'OPEN', 'IN PROGRESS' ],
                'badges' => [
                    'styles' => [
                        'bg-teal-300 dark:bg-teal-600',
                        'bg-amber-300 dark:bg-amber-600'
                    ],
                    'icons' => [
                        'heroicon-o-envelope-open',
                        'heroicon-o-play'
                    ]
                ],
                'cards' => [
                    'backgrounds' => [
                        'bg-teal-200 dark:bg-teal-700',
                        'bg-amber-200 dark:bg-amber-700'
                    ]
                ]
            ],
            'priorities' => [
                'keys' => [ 'low', 'high' ],
                'labels' => [ 'LOW', 'HIGH' ],
                'timeouts' => [ 8, 2 ],
                'badges' => [
                    'styles' => [
                        'bg-green-100 dark:bg-green-700',
                        'bg-red-100 dark:bg-red-700'
                    ],
                    'icons' => [
                        'heroicon-o-signal',
                        'heroicon-o-bell-alert'
                    ]
                ]
            ]
        ] );
    } );

// Grouping Tests
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

    it( 'throws exception for invalid index groupings', function () {
        Config::set( 'tickets.groupings.index', null );
        TicketConfig::getIndexGroupings();
    } )->throws( RuntimeException::class );

    it( 'throws exception for invalid my tickets groupings', function () {
        Config::set( 'tickets.groupings.my_tickets', null );
        TicketConfig::getMyTicketsGroupings();
    } )->throws( RuntimeException::class );

// Status Tests
    it( 'retrieves all statuses', function () {
        $statuses = TicketConfig::getStatuses();

        expect( $statuses )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'open', 'in-progress' ] )
            ->and( $statuses['open'] )->toBe( 'OPEN' )
            ->and( $statuses['in-progress'] )->toBe( 'IN PROGRESS' );
    } );

    it( 'retrieves status badge styles', function () {
        $styles = TicketConfig::getStatusBadgeStyles();

        expect( $styles )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'open', 'in-progress' ] )
            ->and( $styles['open'] )->toContain( 'bg-teal-300' );
    } );

    it( 'retrieves badge style for specific status', function () {
        $style = TicketConfig::getBadgeStyleForStatus( 'open' );
        expect( $style )->toContain( 'bg-teal-300' );
    } );

    it( 'throws exception for invalid status in badge style retrieval', function () {
        TicketConfig::getBadgeStyleForStatus( 'invalid' );
    } )->throws( RuntimeException::class );

    it( 'retrieves status icons', function () {
        $icons = TicketConfig::getStatusIcons();

        expect( $icons )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'open', 'in-progress' ] )
            ->and( $icons['open'] )->toBe( 'heroicon-o-envelope-open' );
    } );

    it( 'retrieves icon for specific status', function () {
        $icon = TicketConfig::getIconForStatus( 'open' );
        expect( $icon )->toBe( 'heroicon-o-envelope-open' );
    } );

    it( 'throws exception for invalid status in icon retrieval', function () {
        TicketConfig::getIconForStatus( 'invalid' );
    } )->throws( RuntimeException::class );

    it( 'retrieves status card backgrounds', function () {
        $backgrounds = TicketConfig::getStatusCardBackgrounds();

        expect( $backgrounds )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'open', 'in-progress' ] )
            ->and( $backgrounds['open'] )->toContain( 'bg-teal-200' );
    } );

    it( 'retrieves card background for specific status', function () {
        $background = TicketConfig::getCardBackgroundForStatus( 'open' );
        expect( $background )->toContain( 'bg-teal-200' );
    } );

    it( 'throws exception for invalid status in card background retrieval', function () {
        TicketConfig::getCardBackgroundForStatus( 'invalid' );
    } )->throws( RuntimeException::class );

    it( 'retrieves specific status label', function () {
        $label = TicketConfig::getStatusLabel( 'open' );
        expect( $label )->toBe( 'OPEN' );
    } );

    it( 'throws exception for invalid status label', function () {
        TicketConfig::getStatusLabel( 'invalid-status' );
    } )->throws( RuntimeException::class );

// Priority Tests
    it( 'retrieves all priorities', function () {
        $priorities = TicketConfig::getPriorities();

        expect( $priorities )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'low', 'high' ] )
            ->and( $priorities['low'] )->toBe( 'LOW' )
            ->and( $priorities['high'] )->toBe( 'HIGH' );
    } );

    it( 'retrieves priority badge styles', function () {
        $styles = TicketConfig::getPriorityBadgeStyles();

        expect( $styles )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'low', 'high' ] )
            ->and( $styles['low'] )->toContain( 'bg-green-100' );
    } );

    it( 'retrieves badge style for specific priority', function () {
        $style = TicketConfig::getBadgeStyleForPriority( 'low' );
        expect( $style )->toContain( 'bg-green-100' );
    } );

    it( 'throws exception for invalid priority in badge style retrieval', function () {
        TicketConfig::getBadgeStyleForPriority( 'invalid' );
    } )->throws( RuntimeException::class );

    it( 'retrieves priority icons', function () {
        $icons = TicketConfig::getPriorityIcons();

        expect( $icons )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'low', 'high' ] )
            ->and( $icons['low'] )->toBe( 'heroicon-o-signal' );
    } );

    it( 'retrieves icon for specific priority', function () {
        $icon = TicketConfig::getIconForPriority( 'low' );
        expect( $icon )->toBe( 'heroicon-o-signal' );
    } );

    it( 'throws exception for invalid priority in icon retrieval', function () {
        TicketConfig::getIconForPriority( 'invalid' );
    } )->throws( RuntimeException::class );

    it( 'retrieves specific priority label', function () {
        $label = TicketConfig::getPriorityLabel( 'high' );
        expect( $label )->toBe( 'HIGH' );
    } );

    it( 'throws exception for invalid priority label', function () {
        TicketConfig::getPriorityLabel( 'invalid-priority' );
    } )->throws( RuntimeException::class );

    it( 'retrieves priority timeouts', function () {
        $timeouts = TicketConfig::getPriorityTimeouts();

        expect( $timeouts )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->toHaveKeys( [ 'low', 'high' ] )
            ->and( $timeouts['low'] )->toBe( 8 )
            ->and( $timeouts['high'] )->toBe( 2 );
    } );

    it( 'retrieves specific priority timeout', function () {
        $timeout = TicketConfig::getTimeoutForPriority( 'low' );
        expect( $timeout )->toBe( 8 );
    } );

    it( 'throws exception for invalid priority timeout', function () {
        TicketConfig::getTimeoutForPriority( 'invalid-priority' );
    } )->throws( RuntimeException::class );