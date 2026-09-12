<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Services\LearnerDashboardData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContinueLearningController extends Controller
{
    public function __invoke(Request $request, LearnerDashboardData $dashboardData): RedirectResponse
    {
        $data = $dashboardData->forUser($request->user());

        return redirect()->to($data['continue_url']);
    }
}
