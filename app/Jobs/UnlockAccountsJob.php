<?php
    declare( strict_types = 1 );

    namespace App\Jobs;

    use App\Models\User;
    use App\Actions\Auth\AccountLockoutAction;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Support\Facades\Log;

    class UnlockAccountsJob implements ShouldQueue
    {
        use Dispatchable;
        use InteractsWithQueue;
        use Queueable;
        use SerializesModels;

        /**
         * Execute the job.
         *
         * @param  AccountLockoutAction  $accountLockoutAction
         */
        public function handle( AccountLockoutAction $accountLockoutAction ) : void
        {
            User::where( 'is_locked', true )
                ->where( 'lockout_time', '<=', now()->subMinutes( config( 'lockout.lockout_duration' ) ) )
                ->chunkById( 100, function ( $users ) use ( $accountLockoutAction ) {
                    foreach ( $users as $user ) {
                        try {
                            $accountLockoutAction->unlockUser( $user );
                        } catch (\Exception $e) {
                            Log::error( "Failed to unlock user {$user->email}: {$e->getMessage()}" );
                        }
                    }
                } );
        }
    }