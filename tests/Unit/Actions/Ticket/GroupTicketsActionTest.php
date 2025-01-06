<?php
    declare( strict_types = 1 );

    use App\Actions\Ticket\GroupTicketsAction;
    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Support\Collection;

    beforeEach( function () {
        $this->action = new GroupTicketsAction();

        // Create test users that we'll need across multiple tests
        $this->agent = User::factory()->create( [ 'role' => 'agent' ] );
        $this->requestor = User::factory()->create( [ 'role' => 'employee' ] );
    } );

    it( 'groups tickets according to configuration', function () {
        // Create test tickets with proper relationships
        $tickets = Collection::make( [
            Ticket::factory()
                ->withRequestor( $this->requestor )
                ->withAssignee( $this->agent )
                ->inProgress()
                ->create(),
            Ticket::factory()
                ->withRequestor( $this->requestor )
                ->open()
                ->create()
        ] );

        $config = [
            [
                'title' => 'In Progress',
                'status' => [ 'in-progress' ],
                'assignee_required' => true,
                'no_tickets_msg' => 'No tickets in progress'
            ],
            [
                'title' => 'Unassigned',
                'status' => [ 'open' ],
                'assignee_required' => false,
                'no_tickets_msg' => 'No unassigned tickets'
            ]
        ];

        $result = $this->action->execute( $tickets, $config, $this->agent );

        expect( $result )
            ->toBeArray()
            ->toHaveCount( 2 )
            ->and( $result[0]['tickets'] )->toHaveCount( 1 )
            ->and( $result[1]['tickets'] )->toHaveCount( 1 );
    } );

    it( 'returns empty groups when no tickets exist', function () {
        $config = [
            [
                'title' => 'Test Group',
                'status' => [ 'open' ],
                'no_tickets_msg' => 'No tickets'
            ]
        ];

        $result = $this->action->execute( collect(), $config );

        expect( $result )
            ->toBeArray()
            ->toHaveCount( 1 )
            ->and( $result[0]['tickets'] )->toBeEmpty()
            ->and( $result[0]['no_tickets_msg'] )->toBe( 'No tickets' );
    } );

    it( 'filters tickets by assignee when required', function () {
        $otherAgent = User::factory()->create( [ 'role' => 'agent' ] );

        $tickets = Collection::make( [
            Ticket::factory()
                ->withRequestor( $this->requestor )
                ->withAssignee( $this->agent )
                ->inProgress()
                ->create(),
            Ticket::factory()
                ->withRequestor( $this->requestor )
                ->withAssignee( $otherAgent )
                ->inProgress()
                ->create()
        ] );

        $config = [
            [
                'title' => 'My Tickets',
                'status' => [ 'in-progress' ],
                'assignee_required' => true,
                'no_tickets_msg' => 'No assigned tickets'
            ]
        ];

        $result = $this->action->execute( $tickets, $config, $this->agent );

        expect( $result[0]['tickets'] )->toHaveCount( 1 )
            ->and( $result[0]['tickets']->first()->assignee_id )->toBe( $this->agent->id );
    } );

    it( 'returns fallback group on error', function () {
        $ticket = Ticket::factory()
            ->withRequestor( $this->requestor )
            ->create();

        $tickets = Collection::make( [ $ticket ] );

        // Invalid config to trigger error
        $config = [
            [
                'invalid' => 'config'
            ]
        ];

        $result = $this->action->execute( $tickets, $config );

        expect( $result )
            ->toBeArray()
            ->toHaveCount( 1 )
            ->and( $result[0]['title'] )->toBe( 'All Tickets' )
            ->and( $result[0]['tickets'] )->toHaveCount( 1 );
    } );

    it( 'correctly handles multiple status filters', function () {
        $tickets = Collection::make( [
            Ticket::factory()->withRequestor( $this->requestor )->open()->create(),
            Ticket::factory()->withRequestor( $this->requestor )->inProgress()->create(),
            Ticket::factory()->withRequestor( $this->requestor )->resolved()->create()
        ] );

        $config = [
            [
                'title' => 'Active Tickets',
                'status' => [ 'open', 'in-progress' ],
                'no_tickets_msg' => 'No active tickets'
            ]
        ];

        $result = $this->action->execute( $tickets, $config );

        expect( $result[0]['tickets'] )->toHaveCount( 2 );
    } );