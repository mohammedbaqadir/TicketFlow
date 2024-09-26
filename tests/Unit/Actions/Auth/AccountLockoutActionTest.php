<?php
    declare( strict_types = 1 );

    use App\Actions\Auth\AccountLockoutAction;
    use App\Models\User;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Validation\ValidationException;

    beforeEach( function () {
        $this->user = User::factory()->make( [
            'email' => 'test@example.com',
            'lockout_count' => 0,
            'is_locked' => false,
            'lockout_time' => null,
        ] );

        Log::spy();
    } );

    test( 'it should lockout user after exceeding max attempts', function () {
        $maxAttempts = (int) ( config( 'lockout.max_attempts' ) );
        $this->user->lockout_count = $maxAttempts - 1; // Set one less than the max to test increment
        $this->user->is_locked = false;
        $this->user->lockout_time = null;
        $this->user->save(); // Save the user to the database

        $action = new AccountLockoutAction();
        $action->execute( $this->user );
        $this->user->refresh();

        dump( [
            'actual_lockout_count' => $this->user->lockout_count,
            'actual_is_locked' => $this->user->is_locked,
            'actual_lockout_time' => $this->user->lockout_time,
        ] );

        expect( $this->user->lockout_count )->toBe( $maxAttempts ); // Now it will be equal to $maxAttempts
        expect( $this->user->is_locked )->toBeTrue();
        expect( $this->user->lockout_time )->not->toBeNull();
        Log::shouldHaveReceived( 'warning' )->with( "User account locked: {$this->user->email}" );
    } );

    test( 'it should unlock user after lockout duration', function () {
        $this->user->is_locked = true;
        $this->user->lockout_time = now()->subMinutes( config( 'lockout.lockout_duration' ) + 1 );
        $this->user->save(); // Ensure the user is saved to the database

        $action = new AccountLockoutAction();

        dump( [
            'initial_is_locked' => $this->user->is_locked,
            'initial_lockout_time' => $this->user->lockout_time,
            'current_time' => now(),
        ] );

        $action->checkLockout( $this->user );

        $this->user->refresh();
        dump( [
            'after_check_lockout_is_locked' => $this->user->is_locked,
            'after_check_lockout_time' => $this->user->lockout_time,
            'after_check_lockout_count' => $this->user->lockout_count,
        ] );

        expect( $this->user->is_locked )->toBeFalse();
        expect( $this->user->lockout_time )->toBeNull();
        expect( $this->user->lockout_count )->toBe( 0 );
        Log::shouldHaveReceived( 'info' )->with( "User account unlocked: {$this->user->email}" );
    } );


    test( 'it should not unlock user if lockout time has not passed', function () {
        $this->user->is_locked = true;
        $this->user->lockout_time = now()->subMinutes( config( 'lockout.lockout_duration' ) - 1 );

        $action = new AccountLockoutAction();

        expect( fn() => $action->checkLockout( $this->user ) )
            ->toThrow( ValidationException::class )
            ->and( $this->user->is_locked )->toBeTrue();
    } );