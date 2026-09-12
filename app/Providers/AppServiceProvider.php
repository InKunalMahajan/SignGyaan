<?php

namespace App\Providers;

use App\Services\LearnerDashboardData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;

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
        View::composer('dashboard', function (BladeView $view): void {
            $data = $view->getData();

            if (($data['role'] ?? null) !== 'learner' || ! Auth::check()) {
                return;
            }

            $dashboard = $data['dashboard'];
            $liveData = app(LearnerDashboardData::class)->forUser(Auth::user());

            $dashboard['stats'] = $liveData['stats'];
            $dashboard['updates'] = $liveData['updates'];
            $dashboard['live_data'] = true;

            $view->with('dashboard', $dashboard);
        });
    }
}
