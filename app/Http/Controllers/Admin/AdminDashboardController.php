<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningProgress;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'learnerCount' => User::query()->where('role', 'learner')->count(),
            'adminCount' => User::query()->where('role', 'admin')->count(),
            'trackedCourses' => LearningProgress::query()->count(),
            'completedCourses' => LearningProgress::query()->whereNotNull('completed_at')->count(),
        ]);
    }
}
