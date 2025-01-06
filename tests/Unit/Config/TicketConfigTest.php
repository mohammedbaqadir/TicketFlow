<?php
    declare( strict_types = 1 );

    use App\Config\TicketConfig;
    use Illuminate\Support\Facades\Config;

    describe( 'TicketConfig', function () {
        // Setup default test configurations
        beforeEach( function () {
            Config::set( 'tickets.status', [
                'open' => 'OPEN',
                'in-progress' => 'IN PROGRESS',
                'resolved' => 'RESOLVED'
            ] );

            Config::set( 'tickets.priority', [
                'low' => 'LOW',
                'medium' => 'MEDIUM',
                'high' => 'HIGH'
            ] );

            Config::set( 'tickets.priority_timeout', [
                'low' => 8,
                'medium' => 4,
                'high' => 2
            ] );
        } );

        describe( 'status management', function () {
            it( 'should retrieve all configured statuses', function () {
                $statuses = TicketConfig::getStatuses();

                expect( $statuses )->toBeArray()
                    ->and( $statuses )->toHaveCount( 3 )
                    ->and( $statuses )->toHaveKey( 'open' )
                    ->and( $statuses['open'] )->toBe( 'OPEN' );
            } );

            it( 'should retrieve specific status label', function () {
                $label = TicketConfig::getStatusLabel( 'in-progress' );

                expect( $label )->toBe( 'IN PROGRESS' );
            } );

            it( 'should throw exception for invalid status when getting label', function () {
                expect( fn() => TicketConfig::getStatusLabel( 'invalid' ) )
                    ->toThrow( RuntimeException::class, 'Invalid status: invalid' );
            } );

            it( 'should correctly check status existence', function () {
                expect( TicketConfig::hasStatus( 'open' ) )->toBeTrue()
                    ->and( TicketConfig::hasStatus( 'invalid' ) )->toBeFalse();
            } );

            it( 'should handle missing status configuration', function () {
                Config::set( 'tickets.status', null );

                expect( fn() => TicketConfig::getStatuses() )
                    ->toThrow( RuntimeException::class );
            } );
        } );

        describe( 'priority management', function () {
            it( 'should retrieve all configured priorities', function () {
                $priorities = TicketConfig::getPriorities();

                expect( $priorities )->toBeArray()
                    ->and( $priorities )->toHaveCount( 3 )
                    ->and( $priorities )->toHaveKey( 'low' )
                    ->and( $priorities['low'] )->toBe( 'LOW' );
            } );

            it( 'should retrieve specific priority label', function () {
                $label = TicketConfig::getPriorityLabel( 'medium' );

                expect( $label )->toBe( 'MEDIUM' );
            } );

            it( 'should throw exception for invalid priority when getting label', function () {
                expect( fn() => TicketConfig::getPriorityLabel( 'invalid' ) )
                    ->toThrow( RuntimeException::class, 'Invalid priority: invalid' );
            } );

            it( 'should correctly check priority existence', function () {
                expect( TicketConfig::hasPriority( 'high' ) )->toBeTrue()
                    ->and( TicketConfig::hasPriority( 'invalid' ) )->toBeFalse();
            } );

            it( 'should handle missing priority configuration', function () {
                Config::set( 'tickets.priority', null );

                expect( fn() => TicketConfig::getPriorities() )
                    ->toThrow( RuntimeException::class );
            } );
        } );

        describe( 'timeout management', function () {
            it( 'should retrieve all configured timeouts', function () {
                $timeouts = TicketConfig::getPriorityTimeouts();

                expect( $timeouts )->toBeArray()
                    ->and( $timeouts )->toHaveCount( 3 )
                    ->and( $timeouts )->toHaveKey( 'low' )
                    ->and( $timeouts['low'] )->toBe( 8 );
            } );

            it( 'should retrieve specific priority timeout', function () {
                $timeout = TicketConfig::getTimeoutForPriority( 'medium' );

                expect( $timeout )->toBe( 4 );
            } );

            it( 'should throw exception for invalid priority in timeout lookup', function () {
                expect( fn() => TicketConfig::getTimeoutForPriority( 'invalid' ) )
                    ->toThrow( RuntimeException::class, 'Invalid priority: invalid' );
            } );

            it( 'should throw exception for missing timeout configuration', function () {
                Config::set( 'tickets.priority_timeout', null );

                expect( fn() => TicketConfig::getPriorityTimeouts() )
                    ->toThrow( RuntimeException::class );
            } );

            it( 'should throw exception for valid priority with missing timeout', function () {
                Config::set( 'tickets.priority_timeout', [ 'low' => 8 ] ); // Missing medium and high

                expect( fn() => TicketConfig::getTimeoutForPriority( 'high' ) )
                    ->toThrow( RuntimeException::class, 'No timeout configured for priority: high' );
            } );
        } );
    } );