<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Services\AssessmentAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentAnalyticsController extends Controller
{
    public function show(
        Request $request,
        Assessment $assessment,
        AssessmentAnalyticsService $analytics
    ): View {
        $assessment->loadMissing('course.subject');

        abort_unless(
            $assessment->created_by === $request->user()->id
                && $assessment->course?->created_by === $request->user()->id,
            403
        );

        return view('teacher.assessments.analytics', [
            'assessment' => $assessment,
            'summary' => $analytics->assessmentSummary($assessment),
            'questions' => $analytics->questionAnalysis($assessment),
        ]);
    }
}
