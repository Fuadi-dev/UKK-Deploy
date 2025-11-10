<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\ProjectStatusService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule project status updates
Schedule::command('projects:update-status')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->emailOutputOnFailure('admin@example.com');

// Alternative: Schedule the service directly
Schedule::call(function () {
    ProjectStatusService::updateProjectAndUserStatus();
})->everyTenMinutes()
  ->name('update-project-status')
  ->withoutOverlapping();
