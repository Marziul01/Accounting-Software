<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:auto-monthly-export')
    ->monthlyOn(1, '03:00'); // Runs on the 1st of every month at 3:00 AM

Schedule::command('app:send-monthly-asset-notifications')
    ->monthlyOn(1, '02:00');

Schedule::command('app:send-monthly-liability-notifications')
    ->monthlyOn(1, '02:00');

Schedule::command('send:occasion-sms')->everyMinute();

Schedule::command('app:schedule-transfer')
    ->dailyAt('00:01');