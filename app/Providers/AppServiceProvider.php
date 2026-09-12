<?php

namespace App\Providers;

use App\Services\LearnerDashboardData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/teacher-assessments.php'));

        Route::middleware('web')
            ->group(base_path('routes/learner-assessments.php'));

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
