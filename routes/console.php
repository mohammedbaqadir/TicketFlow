<?php
    declare( strict_types = 1 );

    use Illuminate\Support\Facades\Schedule;
    use App\Jobs\UnlockAccountsJob;
    use Spatie\ScheduleMonitor\Models\MonitoredScheduledTaskLogItem;


    Schedule::job( new UnlockAccountsJob() )->everyFiveMinutes();
    Schedule::command( 'tickets:escalate-timed-out' )->everyMinute()->monitorName( 'Escalate Timed-Out Tickets' );
    Schedule::command( 'model:prune', [ '--model' => MonitoredScheduledTaskLogItem::class ] )->daily();