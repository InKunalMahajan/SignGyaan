<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LearningClass;
use App\Support\TeacherClassProgressSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassProgressController extends Controller
{
    public function show(
        Request $request,
        LearningClass $class,
        TeacherClassProgressSummary $progressSummary
    ): View {
        abort_unless($class->teacher_id === $request->user()->id, 403);

        $summary = $progressSummary->build($class);

        return view('teacher.classes.progress', [
            'class' => $summary['class'],
            'learnerProgress' => $summary['learner_progress'],
            'publishedLessonCount' => $summary['published_lesson_count'],
            'averagePercent' => $summary['average_percent'],
        ]);
    }
}
