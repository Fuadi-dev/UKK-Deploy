<?php

namespace App\Providers;

use App\Models\Card;
use App\Models\TimeLog;
use App\Models\User;
use App\Observers\CardObserver;
use App\Observers\TimeLogObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Card::observe(CardObserver::class);
        TimeLog::observe(TimeLogObserver::class);
        User::observe(UserObserver::class);
    }
}
