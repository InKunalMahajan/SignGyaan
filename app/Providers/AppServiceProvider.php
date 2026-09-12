<?php

namespace App\Providers;

use App\Services\LearnerDashboardData;
use App\Services\TeacherDashboardData;
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

        Route::middleware('web')
            ->group(base_path('routes/mastery.php'));

        View::composer('dashboard', function (BladeView $view): void {
            $data = $view->getData();
            $role = $data['role'] ?? null;

            if (! Auth::check()) {
                return;
            }

            if ($role === 'learner') {
                $view->setPath(resource_path('views/learner/dashboard.blade.php'));
                $view->with('learnerDashboard', app(LearnerDashboardData::class)->forUser(Auth::user()));

                return;
            }

            if ($role === 'teacher') {
                $view->setPath(resource_path('views/teacher/dashboard.blade.php'));
                $view->with('teacherDashboard', app(TeacherDashboardData::class)->forUser(Auth::user()));
            }
        });
    }
}
