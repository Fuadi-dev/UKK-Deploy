<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
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

// Schedule permanent deletion of soft-deleted records older than 30 days
Schedule::command('cleanup:soft-deleted')
    ->daily()
    ->at('00:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->name('cleanup-soft-deleted-records')
    ->emailOutputOnFailure('admin@example.com');

// ============================================================
// BACKUP SCHEDULES
// ============================================================

// Backup per jam (hanya database)
Schedule::command('backup:run --only-db')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->name('backup-database-hourly')
    ->onFailure(function () {
        Log::error('Hourly database backup failed');
    });

// Backup harian (full backup: database + files)
Schedule::command('backup:run')
    ->daily()
    ->at('02:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->name('backup-daily')
    ->onSuccess(function () {
        Log::info('Daily backup completed successfully');
    })
    ->onFailure(function () {
        Log::error('Daily backup failed');
    });

// Backup mingguan (setiap hari Minggu jam 3 pagi)
Schedule::command('backup:run')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->name('backup-weekly')
    ->onSuccess(function () {
        Log::info('Weekly backup completed successfully');
    });

// Backup bulanan (setiap tanggal 1 jam 4 pagi)
Schedule::command('backup:run')
    ->monthly()
    ->at('04:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->name('backup-monthly')
    ->onSuccess(function () {
        Log::info('Monthly backup completed successfully');
    });

// Cleanup backup lama (setiap hari jam 1 pagi)
Schedule::command('backup:clean')
    ->daily()
    ->at('01:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->name('cleanup-old-backups')
    ->onSuccess(function () {
        Log::info('Old backups cleaned successfully');
    });

// Monitor backup health (setiap hari jam 5 pagi)
Schedule::command('backup:monitor')
    ->daily()
    ->at('05:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->name('monitor-backups')
    ->onFailure(function () {
        Log::warning('Backup health check found issues');
    });
