<?php
    declare( strict_types = 1 );

    use App\Jobs\EscalateTicketJob;
    use App\Models\Ticket;
    use App\Actions\Ticket\EscalateTicketAction;
    use Illuminate\Support\Facades\Queue;
    use Illuminate\Support\Facades\Log;

// Unit Tests
    test( 'EscalateTicketJob can be instantiated', function () {
        $ticket = Ticket::factory()->create();
        $job = new EscalateTicketJob( $ticket );

        dump( 'Job instance:', $job );

        expect( $job )->toBeInstanceOf( EscalateTicketJob::class );
        expect( $job->ticket->id )->toBe( $ticket->id );
    } );

    test( 'EscalateTicketJob handles resolved tickets', function () {
        // Arrange
        $logSpy = Log::spy();
        $ticket = Ticket::factory()->create( [ 'status' => 'resolved' ] );
        $job = new EscalateTicketJob( $ticket );

        $escalateActionMock = Mockery::mock( EscalateTicketAction::class );
        $escalateActionMock->shouldNotReceive( 'execute' );

        dump( 'Ticket before job execution:', $ticket->toArray() );

        // Act
        $job->handle( $escalateActionMock );

        dump( 'Ticket after job execution:', $ticket->fresh()->toArray() );

        // Assert
        $logSpy->shouldHaveReceived( 'info' )
            ->with( "Ticket ID {$ticket->id} is already resolved. Skipping escalation." )
            ->once();

        expect( $ticket->fresh()->status )->toBe( 'resolved' );
        $escalateActionMock->shouldNotHaveReceived( 'execute' );
    } );


    test( 'EscalateTicketJob handles exceptions', function () {
        // Arrange
        $logSpy = Log::spy();
        $ticket = Ticket::factory()->create( [ 'status' => 'open' ] );
        $job = new EscalateTicketJob( $ticket );

        $escalateActionMock = Mockery::mock( EscalateTicketAction::class );
        $escalateActionMock->shouldReceive( 'execute' )
            ->andThrow( new \Exception( 'Test exception' ) );

        dump( 'Ticket before job execution:', $ticket->toArray() );

        // Act
        $job->handle( $escalateActionMock );

        dump( 'Ticket after job execution:', $ticket->fresh()->toArray() );

        // Assert
        $logSpy->shouldHaveReceived( 'error' )
            ->with( "Error escalating ticket ID {$ticket->id}: Test exception" )
            ->once();

        $escalateActionMock->shouldHaveReceived( 'execute' )->once();
        expect( $ticket->fresh()->status )->toBe( 'open' );
    } );


// Feature Tests
    test( 'EscalateTicketJob is dispatched with correct delay', function () {
        Queue::fake();

        $ticket = Ticket::factory()->create( [ 'status' => 'open' ] );
        $delay = now()->addHours( 2 );

        dump( 'Ticket before dispatching job:', $ticket->toArray() );
        dump( 'Delay:', $delay );

        EscalateTicketJob::dispatch( $ticket )->delay( $delay );

        dump( 'Dispatched jobs:', Queue::pushedJobs() );

        Queue::assertPushed( EscalateTicketJob::class, function ( $job ) use ( $ticket, $delay ) {
            return $job->ticket->id === $ticket->id
                && $job->delay->timestamp === $delay->timestamp;
        } );
    } );

    test( 'EscalateTicketJob updates ticket status when executed', function () {
        Queue::fake();

        $ticket = Ticket::factory()->create( [ 'status' => 'open' ] );

        dump( 'Ticket before job execution:', $ticket->toArray() );

        EscalateTicketJob::dispatch( $ticket );

        Queue::assertPushed( EscalateTicketJob::class, function ( $job ) use ( $ticket ) {
            $job->handle( new EscalateTicketAction() );
            return true;
        } );

        $ticket->refresh();

        dump( 'Ticket after job execution:', $ticket->toArray() );

        expect( $ticket->status )->toBe( 'escalated' );
    } );