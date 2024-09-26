<?php
    declare( strict_types = 1 );

    use Illuminate\Foundation\Inspiring;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Schedule;
    use App\Jobs\UnlockAccountsJob;


    Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
    Schedule::job( new UnlockAccountsJob() )->everyFiveMinutes();