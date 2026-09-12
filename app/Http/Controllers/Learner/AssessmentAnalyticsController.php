<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Services\AssessmentAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentAnalyticsController extends Controller
{
    public function history(Request $request, AssessmentAnalyticsService $analytics): View
    {
        $history = $analytics->learnerHistory($request->user());

        return view('learner.assessments.history', compact('history'));
    }
}
