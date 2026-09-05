<?php

use App\Services\LearningReminderService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('learning:send-reminders {--days=3 : Days of inactivity before a reminder}', function (LearningReminderService $reminders) {
    $days = max(1, (int) $this->option('days'));
    $sent = $reminders->sendDueReminders($days);

    $this->info("Learning reminders sent: {$sent}");
})->purpose('Send in-app reminders for unfinished learning');

Schedule::command('learning:send-reminders')
    ->dailyAt('09:00')
    ->withoutOverlapping();
