<?php
    declare( strict_types = 1 );

    use App\Actions\Ticket\GroupTicketsAction;
    use App\Models\Ticket;
    use App\Models\User;

    beforeEach( function () {
        $this->action = new GroupTicketsAction();
        $this->user = User::factory()->state( [ 'role' => 'agent' ] )->create();

        $this->testConfig = [
            [
                'title' => 'In Progress',
                'status' => [ 'in-progress' ],
                'assignee_required' => true,
                'no_tickets_msg' => 'No tickets in progress'
            ],
            [
                'title' => 'Open',
                'status' => [ 'open' ],
                'assignee_required' => false,
                'no_tickets_msg' => 'No open tickets'
            ]
        ];
    } );

    describe( 'GroupTicketsAction', function () {
        it( 'should group tickets according to their status', function () {
            $tickets = collect( [
                Ticket::factory()
                    ->inProgress()
                    ->withAssignee( $this->user )
                    ->withRequestor()
                    ->create(),
                Ticket::factory()
                    ->open()
                    ->withRequestor()
                    ->create()
            ] );

            $result = $this->action->execute( $tickets, $this->testConfig, $this->user );

            expect( $result )->toHaveCount( 2 )
                ->and( $result[0]['tickets'] )->toHaveCount( 1 )
                ->and( $result[1]['tickets'] )->toHaveCount( 1 );
        } );

        it( 'should return empty groups when no tickets are provided', function () {
            $result = $this->action->execute( collect(), $this->testConfig, $this->user );

            expect( $result )->toHaveCount( 2 )
                ->and( $result[0]['tickets'] )->toBeEmpty()
                ->and( $result[1]['tickets'] )->toBeEmpty();
        } );

        it( 'should filter tickets based on assignee when required', function () {
            $otherUser = User::factory()->state( [ 'role' => 'agent' ] )->create();
            $tickets = collect( [
                Ticket::factory()
                    ->inProgress()
                    ->withAssignee( $this->user )
                    ->withRequestor()
                    ->create(),
                Ticket::factory()
                    ->inProgress()
                    ->withAssignee( $otherUser )
                    ->withRequestor()
                    ->create()
            ] );

            $result = $this->action->execute( $tickets, $this->testConfig, $this->user );

            expect( $result[0]['tickets'] )->toHaveCount( 1 )
                ->and( $result[0]['tickets']->first()->assignee_id )->toBe( $this->user->id );
        } );

        it( 'should return fallback grouping when configuration is invalid', function () {
            $tickets = collect( [
                Ticket::factory()
                    ->complete()
                    ->create()
            ] );
            $invalidConfig = [ [ 'invalid' => 'config' ] ];

            $result = $this->action->execute( $tickets, $invalidConfig, $this->user );

            expect( $result )->toHaveCount( 1 )
                ->and( $result[0]['title'] )->toBe( 'All Tickets' )
                ->and( $result[0]['tickets'] )->toHaveCount( 1 );
        } );

        it( 'should handle null user gracefully when assignee filtering is required', function () {
            $tickets = collect( [
                Ticket::factory()
                    ->inProgress()
                    ->withRequestor()
                    ->create()
            ] );

            $result = $this->action->execute( $tickets, $this->testConfig, null );

            expect( $result[0]['tickets'] )->toBeEmpty();
        } );
    } );