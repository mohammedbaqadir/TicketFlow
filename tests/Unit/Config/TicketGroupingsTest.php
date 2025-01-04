<?php
    declare( strict_types = 1 );

    use App\Config\TicketGroupings;
    use Illuminate\Support\Facades\Config;

    describe( 'TicketGroupings', function () {
        beforeEach( function () {
            Config::set( 'tickets.groupings.index', [
                [
                    'title' => 'Test Group',
                    'status' => [ 'test-status' ],
                    'no_tickets_msg' => 'No tickets'
                ]
            ] );
        } );

        it( 'should load valid index groupings from configuration', function () {
            $groupings = TicketGroupings::getIndexGroupings();

            expect( $groupings )->toBeArray()
                ->and( $groupings )->toHaveCount( 1 )
                ->and( $groupings[0]['title'] )->toBe( 'Test Group' );
        } );

        it( 'should throw exception when configuration is missing', function () {
            Config::set( 'tickets.groupings.index', null );

            expect( fn() => TicketGroupings::getIndexGroupings() )
                ->toThrow( RuntimeException::class );
        } );
    } );