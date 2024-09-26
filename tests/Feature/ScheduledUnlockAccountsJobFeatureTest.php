<?php
    declare( strict_types = 1 );

    use App\Jobs\UnlockAccountsJob;
    use App\Models\User;
    use App\Actions\Auth\AccountLockoutAction;

    test( 'it unlocks accounts after lockout duration', function () {
        $user = User::factory()->create( [
            'is_locked' => true,
            'lockout_time' => now()->subMinutes( config( 'lockout.lockout_duration' ) + 1 ),
        ] );

        $job = new UnlockAccountsJob();
        $job->handle( new AccountLockoutAction() );

        $this->assertDatabaseHas( 'users', [
            'id' => $user->id,
            'is_locked' => false,
        ] );
    } );