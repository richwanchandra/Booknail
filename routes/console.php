<?php

use App\Jobs\SendReminder2hJob;
use App\Jobs\SendReminderH1Job;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('booking:cancel-unpaid')->everyFiveMinutes();
Schedule::job(new SendReminderH1Job)->dailyAt('09:00');
Schedule::job(new SendReminder2hJob)->everyThirtyMinutes();
Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=60')->everyMinute();
