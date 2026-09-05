<?php

namespace App\Providers;

use App\Models\AssessmentAttempt;
use App\Observers\AssessmentAttemptObserver;
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
        AssessmentAttempt::observe(AssessmentAttemptObserver::class);
    }
}
