<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\View\View;

class CourseBuilderController extends Controller
{
    public function __invoke(Course $course): View
    {
        $course->load([
            'subject',
            'vocabularyTerms' => fn ($query) => $query
                ->orderBy('sort_order')
                ->orderBy('term'),
            'units' => fn ($unitQuery) => $unitQuery
                ->with([
                    'lessons' => fn ($lessonQuery) => $lessonQuery
                        ->with([
                            'mediaAsset',
                            'vocabularyTerms',
                            'practiceResources' => fn ($practiceQuery) => $practiceQuery
                                ->with([
                                    'assessment' => fn ($assessmentQuery) => $assessmentQuery
                                        ->withCount('questions'),
                                ])
                                ->orderBy('sort_order')
                                ->orderBy('title'),
                        ])
                        ->orderBy('sort_order')
                        ->orderBy('title'),
                ])
                ->orderBy('sort_order')
                ->orderBy('title'),
        ]);

        $lessons = $course->units->flatMap->lessons;
        $practiceResources = $lessons->flatMap->practiceResources;
        $assessments = $practiceResources
            ->map->assessment
            ->filter()
            ->values();

        return view('admin.courses.builder', [
            'course' => $course,
            'lessons' => $lessons,
            'practiceResources' => $practiceResources,
            'assessments' => $assessments,
            'totalUnits' => $course->units->count(),
            'publishedUnits' => $course->units->where('is_published', true)->count(),
            'totalLessons' => $lessons->count(),
            'publishedLessons' => $lessons->where('is_published', true)->count(),
            'practiceCount' => $practiceResources->where('kind', 'practice')->count(),
            'resourceCount' => $practiceResources->where('kind', 'resource')->count(),
            'vocabularyCount' => $course->vocabularyTerms->count(),
            'assessmentCount' => $assessments->count(),
            'publishedAssessmentCount' => $assessments->where('is_published', true)->count(),
        ]);
    }
}
